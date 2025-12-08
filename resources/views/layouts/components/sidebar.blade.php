<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                {{-- 1. DASHBOARD (Pusat Informasi) --}}
                <li class="menu-title">Menu Utama</li>

                <li class="{{ request()->routeIs('*.dashboard') ? 'mm-active' : '' }}">
                    @php
                        $dashRoute = '#';
                        if (auth()->user()->isSuperAdmin())
                            $dashRoute = route('super.dashboard');
                        elseif (auth()->user()->isAdmin())
                            $dashRoute = route('admin.dashboard');
                        elseif (auth()->user()->isUser())
                            $dashRoute = route('user.dashboard');
                    @endphp
                    <a href="{{ $dashRoute }}" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- 2. GROUP: SUPER ADMIN (Konfigurasi Sistem) --}}
                @if(auth()->user()->isSuperAdmin())
                    <li class="menu-title">Super Admin</li>

                    {{-- Master Barang --}}
                    <li class="{{ request()->routeIs('master-items.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('master-items.index') }}" class="waves-effect">
                            <i class="ri-database-2-line"></i>
                            <span>Master Barang</span>
                        </a>
                    </li>

                    {{-- Manajemen User --}}
                    <li class="{{ request()->routeIs('manage-users.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('manage-users.index') }}" class="waves-effect">
                            <i class="ri-user-settings-line"></i>
                            <span>Kelola User</span>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->isUser() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <li class="menu-title">Operasional</li>

                    {{-- Menu Gabungan --}}
                    <li class="{{ request()->routeIs('reports.index') ? 'mm-active' : '' }}">
                        <a href="{{ route('reports.index') }}" class="waves-effect">
                            <i class="ri-file-list-3-line"></i>
                            <span>Laporan Harian</span> {{-- Nama baru yang lebih umum --}}
                        </a>
                    </li>
                @endif

                {{-- 4. GROUP: ANALISIS (Laporan Global) --}}
                {{-- Hanya untuk level Manajerial (Admin & Super Admin) --}}
                @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <li class="menu-title">Analisis & Laporan</li>

                    {{-- Rekapitulasi Lengkap --}}
                    <li class="{{ request()->routeIs('admin.reports.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('admin.reports.index') }}" class="waves-effect">
                            <i class="ri-pie-chart-2-line"></i>
                            <span>Rekapitulasi Total</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</div>