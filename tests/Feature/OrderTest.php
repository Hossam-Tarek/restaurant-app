<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_not_logged_in_user_create_order()
    {
        $product = $this->createValidProduct();

        $response = $this->postJson('api/v1/orders', [
            "products" => [
                'product_id' => $product->id,
                'quantity' => 1
            ],
        ]);

        $response->assertStatus(401);
    }

    public function test_create_order_with_valid_data()
    {
        $product = $this->createValidProduct();
        $ingredients = $product->ingredients;

        $response = $this->actingAs($this->user)->postJson('api/v1/orders', [
            "products" => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ],
            ]
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'data' => [
                "user_id" => $this->user->id,
                "total" => $product->price,
                "products" => [
                    [
                        "id" => 1,
                        "name" => $product->name,
                        "quantity" => 1,
                        "subtotal" => $product->price
                    ]
                ],
            ],
        ]);
        $updatedIngredients = Ingredient::whereIn('id', $ingredients->pluck('id'))->get();

        foreach ($updatedIngredients as $updatedIngredient) {
            $ingredient = $ingredients->where('id', $updatedIngredient->id)->first();
            $this->assertEquals($updatedIngredient->current_stock,
                $ingredient->current_stock - $ingredient->pivot->amount);
        }
    }

    public function test_create_order_with_insufficient_stock()
    {
        $product = $this->createValidProduct();
        $ingredients = $product->ingredients;

        $response = $this->actingAs($this->user)->postJson('api/v1/orders', [
            "products" => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10000
                ],
            ]
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Insufficient stock for product: ' . $product->name,
        ]);
    }

    public function test_create_order_with_invalid_product_data()
    {
        $response = $this->actingAs($this->user)->postJson('api/v1/orders', [
            "products" => [
                [
                    'quantity' => 1
                ],
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_create_order_with_invalid_data()
    {
        $response = $this->actingAs($this->user)->postJson('api/v1/orders', [
            "products" => [
                [
                    'quantity' => 1
                ],
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_create_order_with_invalid_quantity()
    {
        $product = $this->createValidProduct();

        $response = $this->actingAs($this->user)->postJson('api/v1/orders', [
            "products" => [
                [
                    'product_id' => $product->id,
                    'quantity' => 0
                ],
            ]
        ]);

        $response->assertStatus(422);
    }

    private function createValidProduct()
    {
        $beefIngredient = Ingredient::create([
            'name' => 'Beef',
            'initial_stock' => 20_000,
            'current_stock' => 20_000,
        ]);

        $cheeseIngredient = Ingredient::create([
            'name' => 'Cheese',
            'initial_stock' => 5_000,
            'current_stock' => 5_000,
        ]);

        $product = Product::create([
            'name' => 'Beef Burger',
            'price' => '110',
        ]);

        $product->ingredients()->attach([
            $beefIngredient->id => ['amount' => 100],
            $cheeseIngredient->id => ['amount' => 100],
        ]);

        return $product;
    }
}
