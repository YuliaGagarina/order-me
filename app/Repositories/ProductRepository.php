<?php

namespace App\Repositories;

use App\Http\Requests\PIDMigrationRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ProductRepository implements ProductRepositoryInterface
{
    private $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getProductsWithVariationsAndAttributes(): JsonResponse
    {
        $products = $this->model->with(['variations.attributes'])->get();

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'product_id' => $product->product_id,
                'vendor_id'  => $product->vendor_id,
                'variations' => $product->variations->map(function ($variation) {
                    return [
                        'variation_id' => $variation->id,
                        'attributes' => $variation->attributes->map(function ($attribute) {
                            return [
                                'name' => $attribute->name,
                                'value' => $attribute->value,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ];
        }

        return response()->json($result);
    }

    public function getProduct(string $PID): ?Product
    {
        return Product::where('product_id', $PID)->first();
    }

    public function getAllProductVariations(string $PID): Collection
    {
        return ProductVariation::where('product_id', $PID)->with('attributes')->get();
    }

    public function delete(string $PID): void
    {
        $product = $this->getProduct($PID);

        if ($product) {
            $product->delete();
        }
    }

    public function updatePID(PIDMigrationRequest $request): void
    {
        $product = $this->getProduct($request->old_product_id);

        if ($product) {
            $product->variations()->update(['product_id' => $request->new_product_id]);
        }
    }
}
