<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeederAccessManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $access_management = config('access_management.access_data');

        DB::table('access_management')->insert($access_management);

    }
}
