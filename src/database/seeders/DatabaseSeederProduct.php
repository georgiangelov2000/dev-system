<?php

namespace Database\Seeders;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeederProduct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()->count(1050)->create();
    }
}
