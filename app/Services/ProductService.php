<?php

namespace App\Services;

use App\Http\Requests\PIDMigrationRequest;
use App\Models\ProductVariation;
use App\Models\UserProduct;
use App\Models\VariationAttribute;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;

class ProductService implements ProductServiceInterface
{
    private readonly ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function completeProductWithVariations(PIDMigrationRequest $request): array
    {
        $oldVariations = $this->productRepository->getAllProductVariations($request->old_product_id);

        if ($oldVariations->isEmpty()) {
            return [];
        }

        $variationsMap = [];

        $newVariations = $this->productRepository->getAllProductVariations($request->new_product_id);
        if ($newVariations->isEmpty()) {
            foreach ($oldVariations as $oldVariation) {
                $variationsMap[$oldVariation->id] = $this->replicateVariation($oldVariation, $request->new_product_id);
            }

            return $variationsMap;
        }

        foreach ($oldVariations as $oldVariation) {
            $variationsMap[$oldVariation->id] = null;

            $oldVariationAttributes = $oldVariation->attributes->map(function($attribute) {
                return [
                    'name' => $attribute->name,
                    'value' => $attribute->value
                ];
            })->toArray();

            foreach ($newVariations as $newVariation) {
                $newVariationAttributes = $newVariation->attributes->map(function($attribute) {
                    return [
                        'name' => $attribute->name,
                        'value' => $attribute->value
                    ];
                })->toArray();

                if ($oldVariationAttributes == $newVariationAttributes) {
                    $variationsMap[$oldVariation->id] = $newVariation->id;
                }
            }

            if (empty($variationsMap[$oldVariation->id])) {
                $variationsMap[$oldVariation->id] = $this->replicateVariation($oldVariation, $request->new_product_id);
            }
        }

        return $variationsMap;
    }

    private function replicateVariation(ProductVariation $oldVariation, string $newPID)
    {
        $newVariation = $oldVariation->replicate();
        $newVariation->product_id = $newPID;
        $newVariation->save();
        $newVariationId = $newVariation->id;

        foreach ($oldVariation->attributes as $attribute) {
            $newAttribute = $attribute->replicate();
            $newAttribute->variation_id = $newVariationId;
            $newAttribute->save();
        }

        return $newVariationId;
    }
}
