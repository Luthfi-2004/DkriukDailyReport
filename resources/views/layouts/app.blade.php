<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    {{-- Mengambil title dari child view, default 'ERP System' --}}
    <title>@yield('title', 'ERP System') | AICC</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Head --}}
    @include('layouts.components.head')

    {{-- Stack Styles untuk CSS spesifik per halaman --}}
    @stack('styles')
</head>

<body data-sidebar="dark">
    <div id="layout-wrapper">
        {{-- Topbar --}}
        @include('layouts.components.topbar')

        {{-- Sidebar --}}
        @include('layouts.components.sidebar')

        {{-- Content --}}
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    {{-- Breadcrumb / Page Title (Opsional) --}}
                    @if(isset($title))
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">{{ $title }}</h4>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ISI KONTEN UTAMA --}}
                    @yield('content')
                </div>
            </div>

            {{-- Footer --}}
            @include('layouts.components.footer')
        </div>
        {{-- End Content --}}

        {{-- Rightbar --}}
        @include('layouts.components.rightbar')

        <div class="rightbar-overlay"></div>
    </div>

    {{-- Scripts --}}
    @include('layouts.components.scripts')
    
    {{-- Stack Scripts untuk JS spesifik per halaman --}}
    @stack('scripts')
</body>
</html>