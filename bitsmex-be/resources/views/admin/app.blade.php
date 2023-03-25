<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base_url" content="{!! url('/') !!}">
    <link rel="icon" type="image/png" href="{{ $options['site_logo'] }}"/>
    <title>@yield('title') {!! $options['seo_separator'] . ' ' . $options['title_website'] !!} Admin</title>
    <link rel="shortcut icon" href="{!! $options['favicon'] !!}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('contents/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/admin/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/admin/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/admin/css/vendor.bundle.addons.css') }}">
    <link rel="stylesheet" type="text/css" href="{!! asset('contents/admin/css/minify.min.css') !!}">
    <link href="{{ asset('contents/libs/payment-fonts/css/paymentfont.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('contents/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/admin/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/alertify/css/alertify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('contents/alertify/css/themes/default.min.css') }}">
    @stack('css')
    <script src="{{ asset('contents/admin/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('contents/admin/js/vendor.bundle.addons.js') }}"></script>
    <script src="{{ asset('contents/admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('contents/admin/js/misc.js') }}"></script>
    <script type="text/javascript" src="{!! asset('contents/admin/js/admin.js') !!}"></script>
</head>
<body>
    <div class="container-scroller">
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
                <a class="navbar-brand brand-logo" href="{{ url('/') }}">
                    <img src="{{ $options['site_logo'] }}" alt="Logo" />
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
                    <img src="{{ $options['site_logo'] }}" alt="Logo" />
                </a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center">
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item dropdown d-none d-xl-inline-block">
                        <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                            <span class="profile-text">Hello, {!! Auth::user()->username !!}</span>
                            <img class="img-xs rounded-circle" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}" alt="Avatar">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item" style="cursor: pointer">{{ __('Logout') }}</button>
                            </form>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <!-- <li class="nav-item nav-profile">
                        <div class="nav-link">
                            <div class="user-wrapper">
                                <div class="profile-image">
                                    <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}" alt="Avatar">
                                </div>
                                <div class="text-wrapper">
                                    <p class="profile-name">{{ Auth::user()->last_name }}</p>
                                    <div>
                                        @php
                                            $roles = (array) @json_decode(Auth::user()->roles);
                                            $roles = array_filter($roles);
                                            $role = @count($roles) > 0 ? $roles[0] : 'user';
                                        @endphp
                                        <small class="designation text-muted">{{ @config('roles.' . $role) }}</small>
                                        <span class="status-indicator online"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li> -->
                    @if(count(config('admin_menu')) > 0)
                    @php
                        $admin_menu = config('admin_menu');
                        $admin_menu = array_values(Arr::sort($admin_menu, function ($value) {
                            return $value['priority'];
                        }));
                        $url_parse = explode('/', $_SERVER['REQUEST_URI']);
                        $check_admin = [];
                    @endphp
                    @foreach($admin_menu as $key => $menu)
                    <li class="nav-item admin_nav_li {{ (@$url_parse[2] ==  $menu['name']) ? 'active' : '' }}">
                        @php
                            if(Auth::user()->permission != 'supper-admin') {
                                $role = DB::table('roles')->where('slug', Auth::user()->permission)->first();
                                $modules = [];
                                if(!is_null($role)) {
                                    $modules = DB::table('permissions')->where('id_role', $role->id)->pluck('slug_module')->toArray();
                                }
                                $check_admin[$key] = $menu['name'];
                            }
                            $has_sub_menu = false;
                            if(isset($menu['sub']) && count($menu['sub']) > 0){
                                $has_sub_menu = true;
                            }
                            $expanded = (@$url_parse[2] ==  $menu['name']) ? 'true' : 'false'
                        @endphp
                        @if(Auth::user()->permission == 'supper-admin')
                        <a class="nav-link" {!! $has_sub_menu == true ? 'data-toggle="collapse" href="#menu_' . $menu['name'] . '" aria-expanded="'.$expanded.'"' : ' href="' . $menu['url'] . '"' !!}>
                            <i class="menu-icon {!! $menu['icon'] !!}"></i>
                            <span class="menu-title">{!! __($menu['title']) !!}</span>
                            @if($has_sub_menu == true)
                            <i class="menu-arrow"></i>
                            @endif
                        </a>
                        @elseif(in_array($menu['name'], $modules))
                        <a class="nav-link" {!! $has_sub_menu == true ? 'data-toggle="collapse" href="#menu_' . $menu['name'] . '" aria-expanded="'.$expanded.'"' : ' href="' . $menu['url'] . '"' !!}>
                            <i class="menu-icon {!! $menu['icon'] !!}"></i>
                            <span class="menu-title">{!! __($menu['title']) !!}</span>
                            @if($has_sub_menu == true)
                            <i class="menu-arrow"></i>
                            @endif
                        </a>
                        @endif
                        @if($has_sub_menu)
                        @php
                            $menu['sub'] = array_values(Arr::sort($menu['sub'], function ($value) {
                                return $value['priority'];
                            }));
                        @endphp
                        <div class="collapse {{ (@$url_parse[2] ==  $menu['name']) ? 'show' : '' }}" id="menu_{{ $menu['name'] }}">
                            <ul class="nav flex-column sub-menu">
                                @foreach($menu['sub'] as $sub)
                                <li class="nav-item">
                                    <a class="nav-link {{ (@$url_parse[3] ==  $sub['name']) ? 'active' : '' }}" href="{!! $sub['url'] !!}">{!! __($sub['title']) !!}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </li>
                    @endforeach
                    @endif
                </ul>
            </nav>
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                <footer class="footer">
                    <div class="container-fluid clearfix">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">
                            Copyright © 2020 {{ request()->getHost() }}. All rights reserved.
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <input type="hidden" id="_token" value="{!! csrf_token() !!}">
    <input type="hidden" id="current_url" value="{!! url()->current() !!}">
    @if(isset($active_url))
    <input type="hidden" id="active_url" value="{!! $active_url !!}">
    @else
    <input type="hidden" id="active_url" value="{!! url()->current() !!}">
    @endif
    <input type="hidden" id="nav_collapse_url" value="{!! url('/admin/nav-collapse') !!}">
    <input type="hidden" id="nav_mobile_collapse_url" value="{!! url('/admin/nav-mobile-collapse') !!}">
    
    <script src="{{ asset('contents/js/socket.io.js') }}"></script>
    <script>
        var socket = io("/trading");
    </script>    
    <script src="{{ asset('contents/admin/js/app.js') }}"></script>
    <script src="{{ asset('contents/admin/js/chart/Chart.min.js') }}"></script>
    <script src="{{ asset('contents/admin/js/custom.js') }}"></script>
    <script src="{{ asset('contents/js/clipboard.js') }}"></script>
    <script src="{{ asset('contents/js/functions.js') }}"></script>
    <script src="{{ asset('contents/alertify/alertify.min.js') }}"></script>
    
    @stack('js')
</body>
</html>