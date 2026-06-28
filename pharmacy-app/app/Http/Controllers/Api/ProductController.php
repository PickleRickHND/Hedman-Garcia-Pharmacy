<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

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
        $product->update($request->validated());

        return new ProductResource($product->load(['category', 'supplier']));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
