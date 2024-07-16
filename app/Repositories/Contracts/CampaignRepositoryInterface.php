<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PIDMigrationRequest;
use Illuminate\Database\Eloquent\Collection;

interface CampaignRepositoryInterface
{
    public function updatePID(PIDMigrationRequest $request);

    public function updatePIDByCampaignId(PIDMigrationRequest $request);

    public function getProducts(int $campaignId): Collection;

    public function getCampaignIdsContainingProduct(string $PID): array;

    public function isProductInAnyCampaign(string $PID): bool;
}
