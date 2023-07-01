<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call(DatabaseSeederRole::class);
        $this->call(DatabaseSeederUser::class);
        $this->call(DatabaseSeederBrand::class);
        $this->call(DatabaseSeederCategory::class);
        $this->call(DatabaseSeederSubcategory::class);
    }

}
