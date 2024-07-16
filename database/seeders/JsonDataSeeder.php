<?php

namespace Database\Seeders;

use App\Models\CampaignProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class JsonDataSeeder extends Seeder
{
    public function run()
    {
        $json = File::get(database_path('seeders/seedData.json'));
        $data = json_decode($json, true);

        foreach ($data['products'] as $productData) {
            $product = Product::create([
                'product_id' => fake()->unique()->uuid,
                'vendor_id' => $productData['vendor_id'],
                'title' => fake()->sentence(3),
                'description' => fake()->paragraph,
                'image_url' => fake()->imageUrl(),
                'upc' => fake()->ean13,
                'quantity' => fake()->numberBetween(0, 100),
                'quantity_updated_at' => now(),
                'fmv' => fake()->randomFloat(2, 10, 100),
                'nominated' => fake()->boolean,
                'nominated_date_start' => fake()->dateTimeThisYear,
                'nominated_date_end' => fake()->dateTimeThisYear,
            ]);

            if (!empty($productData['inCampaign'])) {
                CampaignProduct::create([
                    'campaign_id' => $productData['campaign_id'],
                    'product_id' => $product->product_id,
                    'vendor_id' => $productData['vendor_id'],
                    'variation_id' => null,
                    'quantity' => $productData['quantity'],
                    'initial_quantity' => $productData['initial_quantity'],
                    'fmv' => $product->fmv,
                ]);

                if (empty($productData['variations'])) {
                    foreach ($productData['users'] as $userId) {
                        Order::create([
                            'user_id' => $userId,
                            'product_id' => $product->product_id,
                            'campaign_id' => $productData['campaign_id'],
                            'review_id' => fake()->unique()->numerify('##########'),
                            'variation_id' => null,
                        ]);
                    }
                }
            }

            if (!empty($productData['variations'])) {
                foreach ($productData['variations'] as $variationData) {
                    $variation = ProductVariation::create([
                        'product_id' => $product->product_id,
                        'vendor_id' => $productData['vendor_id'],
                        'quantity' => fake()->numberBetween(0, 50),
                        'image_url' => fake()->imageUrl(),
                        'upc' => fake()->ean13,
                    ]);

                    foreach ($variationData['attributes'] as $attribute) {
                        ProductVariationAttribute::create([
                            'variation_id' => $variation->id,
                            'name' => $attribute['name'],
                            'value' => $attribute['value'],
                        ]);
                    }

                    if (!empty($productData['inCampaign'])) {
                        CampaignProduct::create([
                            'campaign_id' => $productData['campaign_id'],
                            'product_id' => $product->product_id,
                            'vendor_id' => $productData['vendor_id'],
                            'variation_id' => $variation->id,
                            'quantity' => $variationData['quantity'],
                            'initial_quantity' => $variationData['initial_quantity'],
                            'fmv' => $product->fmv,
                        ]);

                        foreach ($variationData['users'] as $userId) {
                            Order::create([
                                'user_id' => $userId,
                                'product_id' => $product->product_id,
                                'campaign_id' => $productData['campaign_id'],
                                'review_id' => fake()->unique()->numerify('##########'),
                                'variation_id' =>$variation->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
