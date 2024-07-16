<?php

namespace App\Repositories;

use App\Http\Requests\PIDMigrationRequest;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAllProductReviews(string $PID): array
    {
        return Order::where('product_id', $PID)
            ->pluck('review_id')
            ->toArray();

        return response()->json($reviews);
    }

    public function getUsersRequestedBothProducts(PIDMigrationRequest $request): array
    {
        return Order::select('user_id')
            ->whereIn('product_id', [$request->old_product_id, $request->new_product_id])
            ->groupBy('user_id')
            ->havingRaw('COUNT(product_id) > 1')
            ->pluck('user_id')
            ->toArray();
    }

    public function getReviewInfo(int $reviewId)
    {
        return Order::select('id', 'campaign_id', 'variation_id')
            ->where('review_id', $reviewId)
            ->first();
    }

    public function updatePID(PIDMigrationRequest $request): void
    {
        Order::where('product_id', $request->old_product_id)->update(['product_id' => $request->new_product_id]);
    }

    public function updatePIDByCampaignId(PIDMigrationRequest $request): void
    {
        Order::where('campaign_id', $request->campaign_id)
            ->where('product_id', $request->old_product_id)
            ->update(['product_id' => $request->new_product_id]);

        if (!empty($request->variations)) {
            foreach ($request->variations as $oldVariationId => $newVariationId) {
                Order::where('campaign_id', $request->campaign_id)
                    ->where('variation_id', $oldVariationId)
                    ->update(['variation_id' => $newVariationId]);
            }
        }
    }

    public function updatePIDByReviewId(PIDMigrationRequest $request): void
    {
        Order::where('review_id', $request->review_id)->update(['product_id' => $request->new_product_id]);

        if (!empty($request->variations)) {
            $oldVariationId = Order::where('review_id', $request->review_id)->value('variation_id');

            Order::where('review_id', $request->review_id)->update(['variation_id' => $request->variations[$oldVariationId]]);
        }
    }
}
