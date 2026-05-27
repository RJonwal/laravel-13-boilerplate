<?php

namespace Database\Seeders;

use App\Domains\Core\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Empty the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $updateDate = $createDate = date('Y-m-d H:i:s');
        $permissions = [
            
            // roles
                [
                    'name'       => 'role_access',
                    'title'      => 'Access',
                    'route_name' => 'roles',
                    'module_key' => 'role',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'role_create',
                    'title'      => 'Create',
                    'route_name' => 'roles',
                    'module_key' => 'role',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'role_edit',
                    'title'      => 'Edit',
                    'route_name' => 'roles',
                    'module_key' => 'role',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'role_show',
                    'title'      => 'Show',
                    'route_name' => 'roles',
                    'module_key' => 'role',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'role_delete',
                    'title'      => 'Delete',
                    'route_name' => 'roles',
                    'module_key' => 'role',
                    'type'       => 'backend',
                    'status'     => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],

            // staff
                [
                    'name'       => 'staff_access',
                    'title'      => 'Access',
                    'route_name' => 'system-users',
                    'module_key' => 'staff',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'staff_create',
                    'title'      => 'Create',
                    'route_name' => 'system-users',
                    'module_key' => 'staff',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'staff_edit',
                    'title'      => 'Edit',
                    'route_name' => 'system-users',
                    'module_key' => 'staff',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'staff_show',
                    'title'      => 'Show',
                    'route_name' => 'system-users',
                    'module_key' => 'staff',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'staff_delete',
                    'title'      => 'Delete',
                    'route_name' => 'system-users',
                    'module_key' => 'staff',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'staff_status',
                    'title'      => 'Status',
                    'route_name' => 'system-users',
                    'module_key' => 'staff',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'staff_change_password',
                    'title'      => 'Change Password',
                    'route_name' => 'system-users',
                    'module_key' => 'staff',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],

            // setting
                [
                    'name'       => 'setting_access',
                    'title'      => 'Access',
                    'route_name' =>'settings',
                    'module_key' => 'setting',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'setting_edit',
                    'title'      => 'Edit',
                    'route_name' =>'settings',
                    'module_key' => 'setting',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
                [
                    'name'       => 'setting_show',
                    'title'      => 'Show',
                    'route_name' => 'settings',
                    'module_key' => 'setting',
                    'type'       => 'backend',
                    'status'        => 1,
                    'created_at' => $createDate,
                    'updated_at' => $updateDate,
                ],
        ];
        Permission::insert($permissions);
    }
}
