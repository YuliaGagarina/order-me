<?php

namespace App\Http\Controllers;

use App\Http\Requests\PIDMigrationRequest;
use App\Services\Contracts\PIDMigrationServiceInterface;
use Illuminate\Http\JsonResponse;

class PIDMigrationController extends Controller
{
    private readonly PIDMigrationServiceInterface $migrationService;

    public function __construct(
        PIDMigrationServiceInterface $migrationService,
    ) {
        $this->migrationService = $migrationService;
    }

    public function explore(string $PID): JsonResponse
    {
        return $this->migrationService->exploreProduct($PID);
    }

    public function getAllProducts()
    {
        return $this->migrationService->getAllProducts();
    }

    public function migratePID(PIDMigrationRequest $request)
    {
        if (!$request->validated()) {
            return response('invalid data', 400);
        }

        $migrationModifier = !empty($request->migration_modifier) ? 'By' . $request->migration_modifier : 'All' ;
        $migrationMethod = 'migrate' . $migrationModifier;

        return $this->migrationService->$migrationMethod($request);
    }
}
