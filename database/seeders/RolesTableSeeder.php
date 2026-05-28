<?php

namespace Database\Seeders;

use App\Domains\Core\Role\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {

        // Empty the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = [
            [
                'name' => 'Super Admin',
            ],
            [
                'name' => 'Customer',
            ]
        ];
         foreach($roles as $key=>$role){
            Role::create($role);
        }
    }
}
