<?php

namespace App\Services;

use App\Http\Requests\PIDMigrationRequest;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\PIDMigrationServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PIDMigrationService implements PIDMigrationServiceInterface
{
    private readonly ProductServiceInterface $productService;
    private readonly CampaignRepositoryInterface $campaignRepository;
    private readonly OrderRepositoryInterface $orderRepository;
    private readonly ProductRepositoryInterface $productRepository;

    public function __construct(
        ProductServiceInterface $productService,
        CampaignRepositoryInterface $campaignRepository,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
    ) {
        $this->productService = $productService;
        $this->campaignRepository = $campaignRepository;
        $this->orderRepository =  $orderRepository;
        $this->productRepository = $productRepository;
    }


    public function getAllProducts()
    {
        return $this->productRepository->getProductsWithVariationsAndAttributes();
    }

    public function exploreProduct(string $PID): JsonResponse
    {
        $campaigns = $this->campaignRepository->getCampaignIdsContainingProduct($PID);
        $reviews = $this->orderRepository->getAllProductReviews($PID);

        $result = [
            'Product ID' => $PID,
            'Campaigns' => $campaigns,
            'Reviews' => $reviews,
        ];

        return response()->json($result);
    }

    /**
     * Replaces all occurrences of the old product ID with the new one in
     * tables: products, product_variations, campaign_products, orders
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function migrateAll(PIDMigrationRequest $request): JsonResponse
    {
        try {
            $this->verifyInputData($request);
        } catch (RequestedBothProducts_Exception $e) {
            return response()->json([
                'error' => 'Exception',
                'members' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Exception',
                'message' => $e->getMessage(),
            ], 401);
        }

        try {
            DB::beginTransaction();

            $this->productRepository->updatePID($request);
            $this->productRepository->delete($request->old_product_id);

            $this->campaignRepository->updatePID($request);
            $this->orderRepository->updatePID($request);

            DB::commit();

            return response()->json(['success'], 200);
        } catch (\Exception) {
            DB::rollBack();

            return response()->json(['error'], 500);
        }
    }

    /**
     * Replaces all occurrences of the old product ID with the new one in
     * the only one campaign. Impacts tables: products, product_variations,
     * campaign_products, orders
     *
     * @param PIDMigrationRequest $request
     * @return JsonResponse
     */
    public function migrateByCampaign(PIDMigrationRequest $request): JsonResponse
    {
        try {
            $this->verifyInputData($request);
        } catch (RequestedBothProducts_Exception $e) {
            return response()->json([
                'error' => 'Exception',
                'members' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Exception',
                'message' => $e->getMessage(),
            ], 401);
        }

        try {
            DB::beginTransaction();

            $variations = $this->productService->completeProductWithVariations($request);

            if (count($variations) > 0) {
                $request->variations = $variations;
            }

            $this->campaignRepository->updatePIDByCampaignId($request);
            $this->orderRepository->updatePIDByCampaignId($request);

            DB::commit();

            return response()->json(['success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([$e->getMessage()], 500);
        }
    }

    /**
     * Replaces the old product ID with the new one in the only one row
     * in orders table. Refills the products in the campaign_products table
     * accordingly in the campaign the products with the specified review
     * was requested from.
     *
     * @param PIDMigrationRequest $request
     * @return JsonResponse
     */
    public function migrateByReview(PIDMigrationRequest $request): JsonResponse
    {
        try {
            $this->verifyInputData($request);
        } catch (RequestedBothProducts_Exception $e) {
            return response()->json([
                'error' => 'Exception',
                'members' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Exception',
                'message' => $e->getMessage(),
            ], 401);
        }

        try {
            DB::beginTransaction();

            $variations = $this->productService->completeProductWithVariations($request);

            if (count($variations) > 0) {
                $request->variations = $variations;
            }

            $this->refillCampaignProducts($request);
            $this->orderRepository->updatePIDByReviewId($request);

            DB::commit();

            return response( )->json(['success'], 200);
        } catch (\Exception) {
            DB::rollBack();

            return response()->json(['error'], 500);
        }
    }

    private function verifyInputData(PIDMigrationRequest $request): void
    {
        $oldPIDExists = $this->campaignRepository->isProductInAnyCampaign($request->old_product_id);

        if (!$oldPIDExists) {
            throw new \Exception('Product ' . $request->old_product_id . ' was not added to any campaign');
        }

        $users = $this->orderRepository->getUsersRequestedBothProducts($request);

        if (count($users) > 0) {
            throw new RequestedBothProducts_Exception(json_encode($users));
        }
    }

    private function refillCampaignProducts(PIDMigrationRequest $request): void
    {
        $order = $this->orderRepository->getReviewInfo($request->review_id);
        $campaignProducts = $this->campaignRepository->getProducts($order->campaign_id);

        $newProductAdded = false;
        $newProducts = new Collection();
        foreach ($campaignProducts as $row) {
            if ($row->product_id == $request->new_product_id) {
                $row->quantity = $row->quantity == 0 ? 0 : $row->quantity++;
                $row->initial_quantity++;

                $newProductAdded = true;
            } elseif ($row->product_id == $request->old_product_id) {
                if ($row->initial_quantity == 0) {
                    $row->delete();
                    continue;
                }
                $newProduct = $row->replicate();
                $newProducts->push($newProduct);

                $row->quantity = $row->quantity > 0 ? $row->quantity-- : 0;
                $row->initial_quantity = $row->initial_quantity--;
            }

            $row->save();
        }

        if (!$newProductAdded) {
            foreach ($newProducts as $newRow) {
                unset($newRow->id);
                $newRow->product_id = $request->new_product_id;
                $newRow->quantity = $newRow->quantity == 0 ? $newRow->quantity : 1;
                $newRow->initial_quantity = 1;
                $newRow->variation_id = empty($order->variation_id) ?? $request->variations[$order->variation_id];

                $newRow->save();
            }
        }
    }
}
