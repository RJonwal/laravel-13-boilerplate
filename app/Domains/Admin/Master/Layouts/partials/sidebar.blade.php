<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="javascript::void(0);" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!--- Sidemenu -->
        <x-sidebar-menu />
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>