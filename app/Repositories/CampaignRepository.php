<?php

namespace App\Repositories;

use App\Http\Requests\PIDMigrationRequest;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CampaignRepository implements CampaignRepositoryInterface
{
    public function __construct(Campaign $campaign, CampaignProduct $campaignProduct)
    {

    }
    public function updatePID(PIDMigrationRequest $request)
    {
        CampaignProduct::where('product_id', $request->old_product_id)
            ->update(['product_id' => $request->new_product_id]);
    }

    public function updatePIDByCampaignId(PIDMigrationRequest $request)
    {
        CampaignProduct::where('campaign_id', $request->campaign_id)
            ->where('product_id', $request->old_product_id)
            ->update(['product_id' => $request->new_product_id]);

        if (!empty($request->variations)) {
            foreach ($request->variations as $oldVariationId => $newVariationId) {
                CampaignProduct::where('campaign_id', $request->campaign_id)
                    ->where('variation_id', $oldVariationId)
                    ->update(['variation_id' => $newVariationId]);
            }
        }
    }

    public function getProducts(int $campaignId): Collection
    {
        return CampaignProduct::where('campaign_id', $campaignId)->get();
    }

    public function getCampaignIdsContainingProduct(string $PID): array
    {
        return CampaignProduct::where('product_id', $PID)
            ->distinct()
            ->pluck('campaign_id')
            ->toArray();
    }

    public function isProductInAnyCampaign(string $PID): bool
    {
        return count(CampaignProduct::where('product_id', $PID)->get()) > 0;
    }
}
