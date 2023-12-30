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
        DB::table('roles')->insert([['name' => 'admin']]);

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $permissions = DB::table('access_management')->pluck('id')->toArray();

        $data = [];
        
        foreach ($permissions as $permissionId) {
            $data[] = [
                'role_id' => $adminRoleId,
                'access_id' => $permissionId,
            ];
        }

        DB::table('roles_access_management')->insert($data);
    }

}
