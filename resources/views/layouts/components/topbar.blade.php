<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box">
                <a href="/" class="logo logo-light">
                    <span class="logo-sm">
                        <img class="img-fluid" src="{{ asset('assets/images/logo-sm.png') }}" alt="Logo Small">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="Logo" height="45">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                    {{-- LOGIC GAMBAR AVATAR --}}
                    <img class="rounded-circle header-profile-user" style="object-fit: cover;" {{-- Agar tidak gepeng
                        --}}
                        src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/images/users/avatar-2.jpg') }}"
                        alt="Header Avatar">

                    <span class="d-none d-xl-inline-block ml-1">
                        {{ auth()->user()->name }}
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">

                    {{-- LINK KE HALAMAN EDIT PROFILE --}}
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="ri-user-line align-middle mr-1"></i> Profile
                    </a>

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="ri-shut-down-line align-middle mr-1 text-danger"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="ri-settings-2-line"></i>
                </button>
            </div>
        </div>
    </div>
</header>