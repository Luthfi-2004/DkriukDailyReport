@extends('layouts.auth.app')

@section('title', 'Login System')

@section('content')
    {{-- Container Fluid P-0 agar full width tanpa margin --}}
    <div class="container-fluid p-0">
        <div class="row no-gutters">

            {{-- BAGIAN KIRI: FORM LOGIN (40%) --}}
            <div class="col-lg-4">
                <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100 bg-white">
                    <div class="w-100">
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <div>
                                    <div class="text-center mb-5">
                                        <a href="#" class="logo">
                                            <img src="{{ asset('assets/images/logo.png') }}" alt="logo" height="60"
                                                class="mx-auto d-block">
                                        </a>
                                        <h4 class="font-size-20 font-weight-bold mt-4 text-dark">Selamat Datang!</h4>
                                        <p class="text-muted">Masuk untuk mengelola operasional.</p>
                                    </div>

                                    {{-- Flash Messages --}}
                                    @if (session('session_expired') || request('expired'))
                                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            Sesi habis, silakan login ulang.
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ $errors->first() }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login.attempt') }}" class="form-horizontal mt-4"
                                        id="loginForm" autocomplete="off">
                                        @csrf

                                        <div class="form-group auth-form-group-custom mb-4">
                                            <i class="ri-user-2-line auti-custom-input-icon"></i>
                                            <label for="email" class="font-weight-semibold">Email</label>
                                            {{-- Ganti name="usr" jadi name="email" --}}
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" placeholder="Masukkan email" autofocus>
                                        </div>

                                        <div class="form-group auth-form-group-custom mb-4">
                                            <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                            <label for="password" class="font-weight-semibold">Password</label>
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Masukkan password">
                                        </div>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="remember"
                                                name="remember" value="1">
                                            <label class="custom-control-label" for="remember">Ingat Saya</label>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <button
                                                class="btn btn-primary btn-block waves-effect waves-light font-weight-bold shadow-sm"
                                                type="submit" id="loginBtn" style="padding: 12px;">
                                                <span class="default-text">MASUK SISTEM</span>
                                                <div class="loading-text" style="display:none;">
                                                    <span class="spinner-border spinner-border-sm mr-1" role="status"
                                                        aria-hidden="true"></span>
                                                    Memproses...
                                                </div>
                                            </button>
                                        </div>
                                    </form>

                                    <div class="mt-5 text-center">
                                        <p class="mb-0 text-muted font-size-12">© {{ date('Y') }} AICC System.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN KANAN: GAMBAR (60%) --}}
            <div class="col-lg-8">
                <div class="authentication-bg">
                    <div class="bg-overlay"></div>
                </div>
            </div>

        </div>
    </div>
@endsection

{{-- CSS KHUSUS LOGIN --}}
@push('styles')
    <style>
        /* Reset body background agar tidak bentrok dengan class auth-body-bg bawaan layout */
        body.auth-body-bg {
            background-image: none !important;
            background-color: #fff !important;
        }

        /* Gambar Kanan */
        .authentication-bg {
            /* Ganti URL gambar di sini */
            background-image: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(18, 20, 35, 0.5);
            /* Gelapkan gambar 50% */
        }

        /* Input Styling */
        .auth-form-group-custom input {
            border: 1px solid #eef0f3;
            height: 50px;
            padding-left: 45px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .auth-form-group-custom input:focus {
            border-color: #556ee6;
            box-shadow: none;
            background-color: #fff;
        }

        .auth-form-group-custom .auti-custom-input-icon {
            line-height: 50px;
            left: 15px;
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .authentication-bg {
                display: none;
            }

            .col-lg-4 {
                width: 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
@endpush

{{-- JS KHUSUS LOGIN (Token Handler) --}}
@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');

            async function fetchToken() {
                try {
                    // Pastikan route ini ada di web.php: Route::get('/csrf-token', ...)->name('csrf.token.public');
                    // Kalau belum ada, pakai cara manual reload aja
                    const r = await fetch("{{ route('csrf.token.public') }}");
                    const j = await r.json();

                    document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', j.token);
                    form.querySelector('input[name="_token"]').value = j.token;
                } catch (e) { console.log('Token refresh failed'); }
            }

            window.addEventListener('focus', () => { fetchToken().catch(() => { }); });

            form?.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (btn) {
                    btn.disabled = true;
                    btn.querySelector('.default-text').style.display = 'none';
                    btn.querySelector('.loading-text').style.display = 'inline-block';
                }
                try {
                    await fetchToken();
                    form.submit();
                } catch {
                    location.reload();
                }
            });
        })();
    </script>
@endpush