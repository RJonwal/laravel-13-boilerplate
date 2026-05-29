<?php

namespace Database\Seeders;

use App\Domains\Core\Setting\Models\Setting;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->delete();
        $settings = [
            [
                'key'           => 'site_title',
                'value'         => 'Juste',
                'type'          => 'text',
                'display_name'  => 'Site Title',
                'group'         => 'web',
                'details'       => null,
                'status'        => 1,
                'position'      => 1,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
            [
                'key'           => 'site_logo',
                'value'         => null,
                'type'          => 'image',
                'details'       => null,
                'display_name'  => 'Site Logo',
                'group'         => 'web',
                'status'        => 1,
                'position'      => 2,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
            [
                'key'           => 'auth_logo',
                'value'         => null,
                'type'          => 'image',
                'details'       => null,
                'display_name'  => 'Auth Page Logo',
                'group'         => 'web',
                'status'        => 1,
                'position'      => 2,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
            [
                'key'           => 'favicon',
                'value'         => null,
                'type'          => 'image',
                'details'       => null,
                'display_name'  =>'Favicon Icon',
                'group'         => 'web',
                'status'        => 1,
                'position'      => 3,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
            [
                'key'           => 'privacy_policy',
                'value'         => null,
                'type'          => 'text',
                'details'       => null,
                'display_name'  => 'Privacy Policy',
                'group'         => 'content',
                'status'        => 1,
                'position'      => 2,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
            [
                'key'           => 'terms_conditions',
                'value'         => null,
                'type'          => 'text',
                'details'       => null,
                'display_name'  => 'Term & Condition',
                'group'         => 'content',
                'status'        => 1,
                'position'      => 6,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
             [
                'key'           => 'support_email',
                'value'         => 'support@gmail.com',
                'type'          => 'text',
                'display_name'  => 'Email',
                'group'         => 'support',
                'details'       => null,
                'status'        => 1,
                'position'      => 1,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
            [
                'key'           => 'support_contact',
                'value'         => '1234567890',
                'type'          => 'text',
                'display_name'  => 'Phone',
                'group'         => 'support',
                'details'       => null,
                'status'        => 1,
                'position'      => 2,
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'    => 1,
            ],
        ];
        Setting::insert($settings);
    }
}