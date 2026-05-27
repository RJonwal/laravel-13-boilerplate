<?php

namespace Database\Seeders;

use App\Domains\Core\Permission\Models\Permission;
use App\Domains\Core\Role\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permission')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = Role::all();
        
        
        foreach ($roles as $role) {
            switch ($role->id) {
                case 1:
                    $allPermissions = Permission::get();
                    $role->permissions()->sync($allPermissions);
                    break;

                default:
                    break;
            }
        }
    }
}
