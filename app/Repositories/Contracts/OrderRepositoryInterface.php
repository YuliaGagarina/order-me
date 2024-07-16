<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PIDMigrationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function getAllProductReviews(string $PID): array;

    public function getUsersRequestedBothProducts(PIDMigrationRequest $request): array;

    public function getReviewInfo(int $reviewId);

    public function updatePID(PIDMigrationRequest $request): void;

    public function updatePIDByCampaignId(PIDMigrationRequest $request): void;

    public function updatePIDByReviewId(PIDMigrationRequest $request): void;
}
