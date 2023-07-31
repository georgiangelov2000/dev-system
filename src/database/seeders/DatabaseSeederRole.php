<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeederRole extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('roles')->insert(
                [
                    [
                        'name' => 'admin',
                        'access' => 1, 
                    ],
                    [
                        'name' => 'driver',
                        'access' => 2,
                    ]
                ]
        );
    }

}
