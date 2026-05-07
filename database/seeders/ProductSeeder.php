<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Matcha products
            [
                'category_id' => 1,
                'name' => 'Iced Matcha Latte',
                'description' => 'Creamy matcha latte with ice',
                'price' => 45000,
                'image' => 'matcha-latte.jpg',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Hot Matcha Latte',
                'description' => 'Warm creamy matcha latte',
                'price' => 40000,
                'image' => 'matcha-hot.jpg',
                'is_available' => true,
            ],
            // Snack products
            [
                'category_id' => 2,
                'name' => 'Chocolate Croissant',
                'description' => 'French butter croissant with chocolate',
                'price' => 35000,
                'image' => 'croissant.jpg',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Almond Biscotti',
                'description' => 'Crunchy almond biscotti',
                'price' => 25000,
                'image' => 'biscotti.jpg',
                'is_available' => true,
            ],
            // Coffee products
            [
                'category_id' => 3,
                'name' => 'Espresso',
                'description' => 'Strong double shot espresso',
                'price' => 25000,
                'image' => 'espresso.jpg',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Iced Americano',
                'description' => 'Cold americano with ice',
                'price' => 28000,
                'image' => 'americano.jpg',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Cappuccino',
                'description' => 'Espresso with steamed milk and foam',
                'price' => 35000,
                'image' => 'cappuccino.jpg',
                'is_available' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
