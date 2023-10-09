<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 20kg Beef
        $beefIngredient = Ingredient::create([
            'name' => 'Beef',
            'initial_stock' => 20_000,
            'current_stock' => 20_000,
        ]);

        // 5kg Cheese
        $cheeseIngredient = Ingredient::create([
            'name' => 'Cheese',
            'initial_stock' => 5_000,
            'current_stock' => 5_000,
        ]);

        // 1kg Onion
        $onionIngredient = Ingredient::create([
            'name' => 'Onion',
            'initial_stock' => 1_000,
            'current_stock' => 1_000,
        ]);

        $product = Product::create([
            'name' => 'Beef Burger',
            'price' => '110',
        ]);

        // 150g Beef
        // 30g Cheese
        // 20g Onion
        $product->ingredients()->attach([
            $beefIngredient->id => ['amount' => 150],
            $cheeseIngredient->id => ['amount' => 30],
            $onionIngredient->id => ['amount' => 20],
        ]);
    }
}
