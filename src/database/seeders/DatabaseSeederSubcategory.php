<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeederSubcategory extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data = [
            [
                "name" => "Computers",
            ],
            [
                "name" => "Tablets",
            ],
            [
                "name" => "Smartphones",
            ],
            [
                "name" => "TVs",
            ],
            [
                "name" => "Audio equipment",
            ],
            [
                "name" => "Cameras",
            ],
            [
                "name" => "Gaming consoles",
            ],
            [
                "name" => "Furniture",
            ],
            [
                "name" => "Appliances",
            ],
            [
                "name" => "Home decor",
            ],
            [
                "name" => "Bedding and linens",
            ],
            [
                "name" => "Kitchenware and utensils",
            ],
            [
                "name" => "Lighting",
            ],
            [
                "name" => "Men's clothing",
            ],
            [
                "name" => "Women's clothing",
            ],
            [
                "name" => "Children's clothing",
            ],
            [
                "name" => "Appliances",
            ],
            [
                "name" => "Shoes",
            ],
            [
                "name" => "Jewelry",
            ],
            [
                "name" => "Handbags and purses",
            ],
            [
                "name" => "Skincare",
            ],
            [
                "name" => "Makeup",
            ],
            [
                "name" => "Haircare",
            ],
            [
                "name" => "Fragrances",
            ],
            [
                "name" => "Personal hygiene",
            ],
            [
                "name" => "Groceries",
            ],
            [
                "name" => "Snacks and confectionery",
            ],
            [
                "name" => "Beverages",
            ],
            [
                "name" => "Pet food and treats",
            ],
            [
                "name" => "Building materials",
            ],
            [
                "name" => "Heavy machinery",
            ],
            [
                "name" => "Safety equipment",
            ],
            [
                "name" => "Hand tools and power tools",
            ],
            [
                "name" => "Plumbing and electrical supplies",
            ],
            [
                "name" => "Medical devices",
            ],
            [
                "name" => "Pharmaceuticals",
            ],
            [
                "name" => "Personal protective equipment",
            ],
            [
                "name" => "First aid supplies",
            ],
            [
                "name" => "Athletic apparel",
            ],
            [
                "name" => "Outdoor gear and accessories",
            ],
            [
                "name" => "Team sports equipment",
            ],
        ];
        
        $categoryIds = DB::table('categories')->pluck('id')->all();
        
        $subcategoriesWithCategoryId = [];
        
        foreach ($data as $subcategory) {
            $categoryId = $categoryIds[array_rand($categoryIds)];
            $subcategory['category_id'] = $categoryId;
            $subcategoriesWithCategoryId[] = $subcategory;
        }
        
        DB::table('subcategories')->insert($subcategoriesWithCategoryId);

        

    }

}
