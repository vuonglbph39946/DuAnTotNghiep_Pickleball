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
        // Tạo 20 categories mẫu
        Category::factory(20)->create();

        // Tạo một số categories có parent (subcategories)
        $parentCategories = Category::take(5)->get();
        foreach ($parentCategories as $parent) {
            Category::factory(3)->create([
                'parent_id' => $parent->id,
            ]);
        }
    }
}
