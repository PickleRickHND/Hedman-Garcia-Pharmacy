<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\ImageResizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FetchProductImages extends Command
{
    protected $signature = 'products:fetch-images
        {--force : Re-descarga la imagen incluso si el producto ya tiene una}
        {--placeholder : Omite la descarga remota y genera solo placeholders locales}';

    protected $description = 'Pobla la imagen de los productos buscando por nombre (Wikipedia) con fallback a un placeholder local';

    /** User-Agent con contacto, requerido por la politica de Wikimedia. */
    private const USER_AGENT = 'HedmanGarciaPharmacy/1.0 (https://hedmangarcia.local; contacto@hedmangarcia.local)';

    public function handle(): int
    {
        $query = Product::query();
        if (! $this->option('force')) {
            $query->whereNull('image_path');
        }

        $products = $query->get();
        if ($products->isEmpty()) {
            $this->info('No hay productos pendientes de imagen.');

            return self::SUCCESS;
        }

        $this->info("Procesando {$products->count()} producto(s)...");
        $downloaded = 0;
        $placeholders = 0;

        foreach ($products as $product) {
            // Limpia la imagen previa si se re-descarga.
            if ($this->option('force') && filled($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $raw = null;

            if (! $this->option('placeholder')) {
                $raw = $this->downloadFromWikipedia($this->searchTerm($product->name));
                // Respeta el rate limit de Wikimedia entre peticiones.
                usleep(1_200_000);
            }

            if ($raw === null) {
                $bytes = $this->makePlaceholder($product->name);
                $ext = 'png';
                $placeholders++;
                $this->line("  · {$product->name}: placeholder generado");
            } else {
                // Redimensiona para acotar peso (listados/POS/PDF).
                [$bytes, $ext] = ImageResizer::resize($raw);
                $downloaded++;
                $this->line("  · {$product->name}: imagen descargada");
            }

            // Nombre por id del producto: único y estable (evita colisiones de slug entre SKUs).
            $path = 'products/'.$product->id.'.'.$ext;
            Storage::disk('public')->put($path, $bytes);
            $product->update(['image_path' => $path]);
        }

        $this->newLine();
        $this->info("Listo. Descargadas: {$downloaded} · Placeholders: {$placeholders}");

        return self::SUCCESS;
    }

    /**
     * Deriva un termino de busqueda (principio activo) a partir del nombre del producto:
     * descarta dosis, unidades y formas farmaceuticas para mejorar el match.
     */
    private function searchTerm(string $name): string
    {
        $stop = ['mg', 'mcg', 'ml', 'gel', 'crema', 'jarabe', 'inhalador', 'tableta', 'tabletas',
            'capsula', 'capsulas', 'aerosol', 'sobre', 'frasco', 'tubo', 'oral', 'topica', 'adulto',
            'pediatrico', 'inhalada', 'solucion', 'suplemento'];

        $tokens = preg_split('/[\s+]+/', mb_strtolower($name)) ?: [];
        $clean = array_filter($tokens, function (string $t) use ($stop): bool {
            return ! preg_match('/\d/', $t)            // descarta tokens con numeros (500mg, 1%)
                && ! in_array($t, $stop, true)
                && mb_strlen($t) > 2;
        });

        $term = trim(implode(' ', $clean));

        return $term !== '' ? $term : $name;
    }

    /** Hosts permitidos para descargar imágenes (anti-SSRF). */
    private const ALLOWED_IMAGE_HOSTS = ['upload.wikimedia.org', 'commons.wikimedia.org'];

    /** Tamaño máximo de imagen a descargar (bytes). */
    private const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

    /**
     * Busca en Wikipedia (es, luego en) la imagen principal del articulo del termino.
     * Devuelve los bytes de una imagen válida, o null si no hay match seguro.
     */
    private function downloadFromWikipedia(string $term): ?string
    {
        foreach (['es', 'en'] as $lang) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders(['User-Agent' => self::USER_AGENT])
                    ->get("https://{$lang}.wikipedia.org/w/api.php", [
                        'action' => 'query',
                        'prop' => 'pageimages',
                        'piprop' => 'thumbnail',
                        'pithumbsize' => 600,
                        'generator' => 'search',
                        'gsrsearch' => $term,
                        'gsrlimit' => 1,
                        'gsrnamespace' => 0,
                        'format' => 'json',
                    ]);

                if (! $response->ok()) {
                    continue;
                }

                $pages = data_get($response->json(), 'query.pages', []);
                foreach ($pages as $page) {
                    $url = data_get($page, 'thumbnail.source');
                    if (! $this->isAllowedImageUrl($url)) {
                        continue;
                    }

                    $img = Http::timeout(20)
                        ->withOptions(['allow_redirects' => false])
                        ->withHeaders(['User-Agent' => self::USER_AGENT])
                        ->get($url);

                    if (! $img->ok()) {
                        continue;
                    }

                    $body = $img->body();
                    // No confiar en el Content-Type: validar que sean bytes de imagen real y acotar tamaño.
                    if (strlen($body) > 0 && strlen($body) <= self::MAX_IMAGE_BYTES
                        && @getimagesizefromstring($body) !== false) {
                        return $body;
                    }
                }
            } catch (Throwable) {
                // Red caida o respuesta invalida: caemos al siguiente idioma / placeholder.
                continue;
            }
        }

        return null;
    }

    /** Solo https hacia hosts de Wikimedia (bloquea SSRF a IPs internas/otros hosts). */
    private function isAllowedImageUrl(mixed $url): bool
    {
        if (! is_string($url) || $url === '') {
            return false;
        }
        $parts = parse_url($url);
        if (($parts['scheme'] ?? '') !== 'https') {
            return false;
        }
        $host = strtolower($parts['host'] ?? '');

        return in_array($host, self::ALLOWED_IMAGE_HOSTS, true);
    }

    /**
     * Genera un placeholder PNG (cuadro teal de marca con las iniciales del producto).
     */
    private function makePlaceholder(string $name): string
    {
        $size = 400;
        $img = imagecreatetruecolor($size, $size);
        $tmp = null;

        try {
            // Paleta de marca HG (teal).
            $bg = imagecolorallocate($img, 0x12, 0xA5, 0x94);
            $fg = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
            imagefilledrectangle($img, 0, 0, $size, $size, $bg);

            // Iniciales (hasta 2 letras) centradas con la fuente built-in mas grande.
            $initials = mb_strtoupper(mb_substr(preg_replace('/[^A-Za-zÁÉÍÓÚÑ ]/u', '', $name) ?: $name, 0, 1));
            $parts = preg_split('/\s+/', trim($name)) ?: [];
            if (count($parts) > 1) {
                $initials .= mb_strtoupper(mb_substr($parts[1], 0, 1));
            }

            $font = 5;
            $charW = imagefontwidth($font) * strlen($initials);
            $charH = imagefontheight($font);
            $scale = 6; // ampliamos la fuente built-in
            $tmp = imagecreatetruecolor(max(1, $charW), max(1, $charH));
            imagefilledrectangle($tmp, 0, 0, $charW, $charH, $bg);
            imagestring($tmp, $font, 0, 0, $initials, $fg);
            $bigW = $charW * $scale;
            $bigH = $charH * $scale;
            imagecopyresized($img, $tmp, (int) (($size - $bigW) / 2), (int) (($size - $bigH) / 2), 0, 0, $bigW, $bigH, $charW, $charH);

            ob_start();
            imagepng($img);

            return (string) ob_get_clean();
        } finally {
            // Libera siempre los recursos GD, incluso si imagepng lanza.
            if ($tmp !== false && $tmp !== null) {
                imagedestroy($tmp);
            }
            imagedestroy($img);
        }
    }
}
