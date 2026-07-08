
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('vendor/micss/modern.css') }}">

    @yield('adminlte_css_pre')
    @stack('css')
    @yield('css')
</head>
<body class="modern-auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    @if (config('adminlte.auth_logo.enabled', false))
                        <img src="{{ asset(config('adminlte.auth_logo.img.path')) }}"
                             alt="{{ config('adminlte.auth_logo.img.alt') }}"
                             @if (config('adminlte.auth_logo.img.class', null))
                                class="{{ config('adminlte.auth_logo.img.class') }}"
                             @endif
                             @if (config('adminlte.auth_logo.img.width', null))
                                width="{{ config('adminlte.auth_logo.img.width') }}"
                             @endif
                             @if (config('adminlte.auth_logo.img.height', null))
                                height="{{ config('adminlte.auth_logo.img.height') }}"
                             @endif>
                    @else
                        <img src="{{ asset(config('adminlte.logo_img')) }}" alt="{{ config('adminlte.logo_img_alt') }}" height="50">
                    @endif
                </div>
                <h1 class="auth-title">{{ config('adminlte.title', 'Sistema de Trazabilidad Documental') }}</h1>
                <p class="auth-subtitle">Sistema de Trazabilidad Documental</p>
            </div>

            <div class="auth-body">
                @hasSection('auth_header')
                    <div class="auth-section-header">
                        @yield('auth_header')
                    </div>
                @endif

                @yield('auth_body')
            </div>

            @hasSection('auth_footer')
                <div class="auth-footer">
                    @yield('auth_footer')
                </div>
            @endif
        </div>

        <div class="auth-footer-text">
            &copy; {{ date('Y') }} {{ config('adminlte.title', 'Sistema de Trazabilidad Documental') }}. Todos los derechos reservados.
        </div>
    </div>

    @yield('adminlte_js')
    @stack('js')
    @yield('js')
</body>
</html>
