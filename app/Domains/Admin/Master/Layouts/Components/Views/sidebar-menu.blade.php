<ul class="side-nav">
    @foreach($menus as $menu)
        @if(!$menu['permission'] || auth()->user()->can($menu['permission']))
            <li class="side-nav-item {{ $menu['active'] ? 'menuitem-active' : '' }}">
                <a href="{{ route($menu['route']) }}" class="side-nav-link {{ $menu['active'] ? 'active' : '' }}">
                    <i class="{{ $menu['icon'] }}"></i>
                    <span>{{ $menu['title'] }}</span>
                </a>
            </li>
        @endif
    @endforeach
            
    <li class="side-nav-item">
        <a href="javascript:void(0);" class="side-nav-link userLogoutBtn" data-href="{{ route('auth.logout') }}">
            <i class="ri-logout-box-line"></i>
            <span>{{ trans('global.logout') }}</span>
        </a>
    </li>
</ul>