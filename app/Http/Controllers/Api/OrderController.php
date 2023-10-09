<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Resources\Api\OrderResource;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(OrderRequest $request, OrderService $orderService)
    {
        try {
            DB::beginTransaction();
            $order = $orderService->createOrder(auth()->user(), $request->input('products'));
            DB::commit();

            return ApiHelper::success(new OrderResource($order), 'Order created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiHelper::failure([], $e->getMessage());
        }
    }
}
