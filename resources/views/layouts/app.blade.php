<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('js')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('/img/logosss.png') }}" type="image/x-icon">
    @yield('css')
    <style>
        :root {
            --primary-green: #10b981;
            --primary-green-dark: #059669;
            --primary-green-light: #d1fae5;
            --accent-green: #047857;
        }

        body {
            background-color: #f0fdf4;
            font-family: 'Nunito', sans-serif;
        }

        .navbar-brand {
            font-family: poppins-semibold, sans-serif;
            letter-spacing: .5px;
            color: #065f46 !important;
            font-weight: 700;
        }

        .navbar .nav-item .nav-link {
            font-family: poppins-regular, sans-serif;
            letter-spacing: .3px;
        }

        .card-title, .card-text {
            font-family: poppins-regular, sans-serif;
            letter-spacing: .3px;
        }

        .nav-link {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: #10b981;
        }

        .nav-link.active {
            position: relative;
            color: #059669 !important;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 100%;
            height: 3px;
            border-radius: 2px;
            background-color: #10b981;
        }

        /* Override Bootstrap blue with Fresh Green */
        .bg-primary {
            background-color: #10b981 !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .btn-primary {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.25);
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #059669 !important;
            border-color: #059669 !important;
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.35);
        }

        .btn-outline-primary {
            color: #10b981 !important;
            border-color: #10b981 !important;
        }

        .btn-outline-primary:hover {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }

        .border-primary {
            border-color: #10b981 !important;
        }

        .text-primary {
            color: #10b981 !important;
        }

        .avatar-img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #a7f3d0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            background-color: #ecfdf5;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

    </style>
</head>
<body>
    <div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm border-bottom" style="border-color: #e5e7eb !important;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" >
                <i class="fas fa-vote-yea mr-2 text-success"></i> E - Pilketos
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto align-items-center">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="#">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <!-- Gambar profil di depan dengan Random Avatar berbasis Nama Siswa -->
                                @auth
                                <img src="{{ Auth::user()->avatar_url }}"
                                     alt="Profile Avatar"
                                     class="avatar-img mr-2"
                                     onerror="this.onerror=null; this.src='https://api.dicebear.com/7.x/bottts/svg?seed={{ urlencode(Auth::user()->nama_panjang ?? Auth::user()->username) }}';">
                                @endauth

                                <!-- Nama pengguna dan peran -->
                                <div class="text-left">
                                @auth
                                @if (Auth::user()->role == 'admin')
                                    <span style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ Auth::user()->username }}</span><br>
                                    <small class="badge badge-success px-2 py-0" style="font-size: 11px; background-color: #10b981;">{{ ucfirst(Auth::user()->role) }}</small>
                                @endif
                                @if (Auth::user()->role == 'siswa')
                                    <span style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ !empty(Auth::user()->nama_panjang) ? Auth::user()->nama_panjang : Auth::user()->username }}</span><br>
                                    <small class="badge badge-success px-2 py-0" style="font-size: 11px; background-color: #10b981;">{{ ucfirst(Auth::user()->role) }} {{ Auth::user()->kelas ? '• ' . Auth::user()->kelas : '' }}</small>
                                @endif
                                @endauth
                                </div>
                                <!-- Dropdown caret -->
                                <span class="caret ml-2"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    @auth
    @if (Auth::user()->role == 'admin')
    <hr style="border: 0px solid #ccc; margin: 1.5px 0;">
    <nav class="navbar navbar-expand-md bg-white shadow-sm">
        <div class="container">
            <div class="header text-center mb-1">
                <ul class="navbar-nav ml-auto mt-1">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link {{ Route::is('kandidat') ? 'active' : '' }}" href="{{ route('kandidat') }}">
                            <i class="fas fa-users mr-1"></i> Kandidat
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link {{ Route::is('listSiswa') ? 'active' : '' }}" href="{{ route('listSiswa') }}">
                            <i class="fas fa-user-check mr-1"></i> Pemilih
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link {{ request()->is('hasilVote') ? 'active' : '' }}" href="/hasilVote">
                            <i class="fas fa-chart-bar mr-1"></i> Hasil
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endif
    @endauth

        <main class="py-4">
            @yield('content')
        </main>
    </div>


</body>
</html>
