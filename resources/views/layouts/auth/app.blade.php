<!doctype html>
<html lang="en">
<head>
    <title>{{ $title ?? 'SandLab' }}</title>
    {{-- Pastikan path components ini sesuai dengan struktur folder kamu --}}
    {{-- Kalau tadi kita sepakat di layouts.components, sesuaikan ya --}}
    @include('layouts.components.head') 

    {{-- WAJIB: Agar CSS khusus login bisa masuk --}}
    @stack('styles')
</head>

<body class="auth-body-bg">
    
    @yield('content')
    
    {{-- Scripts Utama --}}
    @include('layouts.components.scripts')

    {{-- WAJIB: Agar JS token/login bisa masuk --}}
    @stack('scripts')
</body>
</html>