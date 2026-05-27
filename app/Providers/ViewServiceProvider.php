<?php

namespace App\Providers;

use App\Domains\Admin\Master\Layouts\Components\SidebarMenu;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Components
        Blade::component('sidebar-menu', SidebarMenu::class);

        // Admin layout and partials blade files
        View::addNamespace('Layouts', base_path('app/Domains/Admin/Master/Layouts'));
        View::addNamespace('Components', base_path('app/Domains/Admin/Master/Layouts/Components/Views'));
        View::addNamespace('Auth', base_path('app/Domains/Admin/Auth/Views'));

        View::addNamespace('Dashboard', base_path('app/Domains/Admin/Dashboard/Views'));
        View::addNamespace('Setting', base_path('app/Domains/Admin/Setting/Views'));
        View::addNamespace('Role', base_path('app/Domains/Admin/Role/Views'));
        View::addNamespace('Staff', base_path('app/Domains/Admin/Staff/Views'));
    }
}
