<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PIDMigrationRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface ProductRepositoryInterface
{
    public function getProduct(string $PID): ?Product;

    public function getProductsWithVariationsAndAttributes(): JsonResponse;

    public function getAllProductVariations(string $PID): Collection;

    public function delete(string $PID): void;

    public function updatePID(PIDMigrationRequest $request): void;
}
