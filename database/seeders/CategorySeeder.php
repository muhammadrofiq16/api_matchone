<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Matcha', 'slug' => 'matcha'],
            ['name' => 'Snack', 'slug' => 'snack'],
            ['name' => 'Coffee', 'slug' => 'coffee'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
