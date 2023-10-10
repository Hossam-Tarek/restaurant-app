<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class OrderService
{

    /**
     * Create a new order for the provided user.
     *
     * @param User $user
     * @param array $productsData
     * @return Order
     * @throws \Exception
     */
    public function createOrder(User $user, array $productsData): Order
    {
        $productsData = collect($productsData);
        $products = Product::whereIn('id', $productsData->pluck('product_id'))->with('ingredients')->get();

        $order = Order::create(['user_id' => $user->id]);

        foreach ($productsData as $productData) {
            $product = $products->where('id', $productData['product_id'])->first();

            $this->createOrderProduct($order, $product, $productData['quantity']);
        }

        $order->total = $order->products->map(function ($product) {
            return $product->pivot->subtotal;
        })->sum();

        $order->save();

        return $order;
    }

    /**
     * Create an order_product record.
     *
     * @param Order $order
     * @param Product $product
     * @param int $quantity
     * @return void
     * @throws \Exception
     */
    private function createOrderProduct(Order $order, Product $product, int $quantity): void
    {
        $this->reduceStock($product, $quantity);

        $order->products()->attach($product->id, [
            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity,
        ]);
    }

    /**
     * Reduces the stock of each ingredient used within the product.
     *
     * @param Product $product
     * @param int $quantity
     * @return void
     * @throws \Exception
     */
    private function reduceStock(Product $product, int $quantity): void
    {
        foreach ($product->ingredients as $ingredient) {
            $totalAmount = $ingredient->pivot->amount * $quantity;
            if ($totalAmount > $ingredient->current_stock) {
                throw new \Exception('Insufficient stock for product: ' . $product->name);
            }
            $ingredient->current_stock -= $totalAmount;
            if ($ingredient->current_stock <= ($ingredient->integer / 2) && !$ingredient->merchant_notified) {
                $ingredient->merchant_notified = 1;
                // TODO: Notify merchant that the stock is about to end.
            }

            $ingredient->save();
        }
    }
}
