<?php

namespace App\Services\Contracts;

use App\Http\Requests\PIDMigrationRequest;
use Illuminate\Http\JsonResponse;

interface PIDMigrationServiceInterface
{
    public function migrateAll(PIDMigrationRequest $request): JsonResponse;

    public function migrateByCampaign(PIDMigrationRequest $request): JsonResponse;

    public function migrateByReview(PIDMigrationRequest $request): JsonResponse;
}
