<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Redimensiona imágenes con GD para acotar peso (listados, POS y PDF de factura).
 * Mantiene la relación de aspecto y reduce solo si la imagen excede el máximo.
 */
class ImageResizer
{
    /**
     * @return array{0: string, 1: string} [bytes redimensionados, extensión]
     */
    public static function resize(string $bytes, int $max = 500): array
    {
        $src = @imagecreatefromstring($bytes);
        if ($src === false) {
            // No es una imagen decodificable: devolver original como jpg por defecto.
            return [$bytes, 'jpg'];
        }

        try {
            $width = imagesx($src);
            $height = imagesy($src);
            $hasAlpha = self::hasAlpha($bytes);

            // Si ya cabe, re-encodear igual para normalizar formato y peso.
            $scale = min(1.0, $max / max($width, $height));
            $newW = max(1, (int) round($width * $scale));
            $newH = max(1, (int) round($height * $scale));

            $dst = imagecreatetruecolor($newW, $newH);
            if ($hasAlpha) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

            ob_start();
            if ($hasAlpha) {
                imagepng($dst, null, 6);
                $ext = 'png';
            } else {
                imagejpeg($dst, null, 82);
                $ext = 'jpg';
            }
            $out = (string) ob_get_clean();

            imagedestroy($dst);

            return [$out, $ext];
        } finally {
            imagedestroy($src);
        }
    }

    private static function hasAlpha(string $bytes): bool
    {
        // PNG/WEBP/GIF pueden tener transparencia; JPEG nunca.
        return str_starts_with($bytes, "\x89PNG")
            || str_starts_with($bytes, 'GIF')
            || (strlen($bytes) > 12 && substr($bytes, 8, 4) === 'WEBP');
    }
}
