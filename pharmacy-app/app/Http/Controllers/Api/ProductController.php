<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\ImageResizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Listado paginado con filtros (reusa los scopes del modelo Product).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with(['category', 'supplier'])
            ->search($request->string('search'))
            ->byCategory($request->integer('category_id') ?: null)
            ->when($request->boolean('low_stock'), fn ($q) => $q->lowStock())
            ->when($request->boolean('expiring_soon'), fn ($q) => $q->expiringSoon())
            ->when($request->boolean('expired'), fn ($q) => $q->expired())
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image')->get());
        }

        $product = Product::create($data);

        return (new ProductResource($product->load(['category', 'supplier'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['category', 'supplier']));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            // Reemplaza la imagen previa para no dejar archivos huerfanos.
            if (filled($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $this->storeImage($request->file('image')->get());
        }

        $product->update($data);

        return new ProductResource($product->load(['category', 'supplier']));
    }

    public function destroy(Product $product): JsonResponse
    {
        // SoftDelete: el archivo se conserva por si hay restore; se limpia en forceDelete (modelo Product).
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * Redimensiona (acota peso para listados/POS/PDF) y guarda la imagen con un
     * nombre único en el disco public. Devuelve la ruta relativa.
     */
    private function storeImage(string $bytes): string
    {
        [$resized, $ext] = ImageResizer::resize($bytes);
        $path = 'products/'.Str::uuid()->toString().'.'.$ext;
        Storage::disk('public')->put($path, $resized);

        return $path;
    }
}
