<?php

namespace App\Services\Contracts;

use App\Http\Requests\PIDMigrationRequest;

interface ProductServiceInterface
{
    public function completeProductWithVariations(PIDMigrationRequest $request): array;
}
