<?php

namespace App\Domains\Admin\Master\Layouts\Components;

use Illuminate\View\Component;

class SidebarMenu extends Component
{
    public array $menus;

    public function __construct()
    {
        $this->menus = [
            [
                'title' => trans('cruds.menus.dashboard'),
                'icon' => 'ri-dashboard-fill',
                'route' => 'admin.dashboard',
                'active' => request()->is('dashboard'),
                'permission' => null,
            ],
            [
                'title' => trans('cruds.menus.customer'),
                'icon' => 'ri-team-line',
                'route' => 'customers.index',
                'active' => request()->routeIs('customers.*'),
                'permission' => 'customer_access',
            ],
            [
                'title' => trans('cruds.menus.ledger'),
                'icon' => 'ri-list-unordered',
                'route' => 'ledgers.index',
                'active' => request()->routeIs('ledgers.*'),
                'permission' => 'ledger_access',
            ],
            [
                'title' => trans('cruds.menus.staff'),
                'icon' => 'ri-user-settings-line',
                'route' => 'system-users.index',
                'active' => request()->is('system-users*'),
                'permission' => 'staff_access',
            ],
            [
                'title' => trans('cruds.menus.device'),
                'icon' => 'ri-computer-line',
                'route' => 'devices.index',
                'active' => request()->is('devices'),
                'permission' => 'device_access',
            ],
            [
                'title' => trans('cruds.menus.role'),
                'icon' => 'ri-shield-user-line',
                'route' => 'roles.index',
                'active' => request()->routeIs('roles.*'),
                'permission' => 'role_access',
            ],
            [
                'title' => trans('cruds.menus.day_book'),
                'icon' => 'ri-database-2-line',
                'route' => 'day-books.index',
                'active' => request()->is('day-books*'),
                'permission' => 'city_access',
            ],
            [
                'title' => trans('cruds.menus.city'),
                'icon' => 'ri-map-pin-line',
                'route' => 'cities.index',
                'active' => request()->is('cities*'),
                'permission' => 'city_access',
            ],
            [
                'title' => trans('cruds.menus.deleted_receipt'),
                'icon' => 'ri-delete-bin-line',
                'route' => 'deleted-receipts.index',
                'active' => request()->is('deleted-receipts*'),
                'permission' => 'deleted_receipt_access',
            ],
            [
                'title' => trans('cruds.menus.setting'),
                'icon' => 'ri-settings-line',
                'route' => 'settings.index',
                'active' => request()->routeIs('settings.*'),
                'permission' => 'setting_access',
            ],
        ];
    }

    public function render()
    {
        return view('Components::sidebar-menu');
    }
}
