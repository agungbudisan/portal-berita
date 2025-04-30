<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>User Dashboard - {{ config('app.name', 'WinniNews') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        .dashboard-sidebar {
            background-color: #212529;
            color: white;
            padding: 15px;
            min-height: 100vh;
        }

        .logo {
            font-weight: bold;
            color: #ff6b9a;
        }

        .logo span {
            color: #4169E1;
        }

        .dashboard-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 8px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .dashboard-menu .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .dashboard-menu .nav-link.active {
            background-color: #ff6b9a !important;
            color: white !important;
        }

        .stat-card {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-2 dashboard-sidebar">
            <div class="logo text-center mb-4">Winni<span>News</span></div>

            <p class="text-muted small mb-2">MENU</p>
            <ul class="nav flex-column dashboard-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                        <i class="bi bi-grid me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.bookmarks') ? 'active' : '' }}" href="{{ route('user.bookmarks') }}">
                        <i class="bi bi-bookmark me-2"></i> Bookmark
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.comments') ? 'active' : '' }}" href="{{ route('user.comments') }}">
                        <i class="bi bi-chat-dots me-2"></i> Komentar Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i> Profil
                    </a>
                </li>
            </ul>

            <div class="mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-danger w-100 text-start border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 bg-light">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center p-3 bg-white border-bottom">
                <h5 class="m-0">@yield('header', 'Dashboard Pengguna')</h5>
                <div class="d-flex align-items-center">
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary me-3">Kembali ke Portal</a>
                    <span class="me-3">{{ Auth::user()->name }}</span>
                    <div style="width: 40px; height: 40px; background-color: #dee2e6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-4">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
