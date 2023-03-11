<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeederBrand extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $brands = [
            [
                'name' => 'GFC',
            ],
            [
                'name' => 'HP',
            ],
            [
                'name' => 'APPLE',
            ],
            [
                'name' => 'Xiami',
            ],
            [
                'name' => 'Panasonic',
            ],
            [
                'name' => 'Huawei',
            ],
            [
                'name' => 'Samsung',
            ],
            [
                'name' => 'Canon',
            ],
            [
                'name' => 'Nike',
            ],
            [
                'name' => 'Adidas',
            ],
            [
                'name' => 'Toyota',
            ],
            [
                'name' => 'Honda',
            ],
            [
                'name' => 'Nescafé',
            ],
            [
                'name' => 'Audi',
            ],
            [
                'name' => 'Ford',
            ],
            [
                'name' => 'Gillette',
            ],
            [
                'name' => 'Nissan',
            ],
            [
                'name' => 'Nestlé',
            ],
            [
                'name' => 'Danone',
            ],
            [
                'name' => 'Colgate',
            ],
            [
                'name' => 'Canon',
            ],
            [
                'name' => 'Lego',
            ],
            [
                'name' => 'Cartier',
            ],
            [
                'name' => 'Panasonic',
            ],
            [
                'name' => 'Burberry',
            ],
            [
                'name' => 'Johnnie Walker ',
            ],
        ];

        DB::table('brands')->insert($brands);
    }

}
