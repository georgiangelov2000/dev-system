<?php

namespace Database\Seeders;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::factory()->count(1050)->create();
    }
}
