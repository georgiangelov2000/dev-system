<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeederCategory extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        $categories = [
            [
                'name' => 'Sporting Goods',
                'description' => "Products related to sporting goods, such as equipment or apparel for sports and outdoor activities."
            ],
            [
                'name' => 'Beauty and Personal Care',
                'description' => "Products related to beauty and personal care, such as skincare, makeup, or haircare products."
            ],
            [
                'name' => 'Home Goods',
                'description' => "Products related to home goods, such as furniture, appliances, or decor"
            ],
            [
                'name' => 'Industrial and Construction',
                "description" => "Products related to industrial and construction, such as building materials or heavy machinery."
            ],
            [
                'name' => 'Food and Beverage',
                "description" => "Products related to food and beverage, such as groceries, snacks, or drinks."
            ],
            [
                'name' => 'Electronics',
                'description' => "Products related to electronics, such as TVs, computers, or smartphones."
            ],
            [
                "name" => "Medical and Healthcare",
                "description" => "Products related to medical and healthcare, such as medical devices or pharmaceuticals"
            ],
            [
                "name" => "Apparel and Accessories",
                "description" => "Products related to clothing and accessories, such as shoes, jewelry, or handbags."
            ],
            [
                "name" => "Automotive",
                "description" => "Products related to automotive, such as car parts, accessories, or maintenance products."
            ],
        ];
        
        DB::table('categories')->insert($categories);
    }

}
