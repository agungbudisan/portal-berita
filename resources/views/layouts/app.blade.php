<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WinniNews') }} - @yield('title', 'Portal Berita Terkini')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        .category-badge {
            background-color: #ff6b9a;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .wgt-title {
            font-size: 18px;
            border-bottom: 2px solid #ff6b9a;
            padding-bottom: 8px;
            margin-bottom: 15px;
            display: inline-block;
        }

        .logo {
            font-weight: bold;
            color: #ff6b9a;
        }

        .logo span {
            color: #4169E1;
        }

        .news-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 20px;
            height: 100%;
        }

        .news-card-img {
            height: 180px;
            background-color: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            background-size: cover;
            background-position: center;
        }

        .bookmark-btn {
            color: #ff6b9a;
            border: none;
            background: none;
            cursor: pointer;
        }

        .nav-link.active {
            background-color: #ff6b9a !important;
            color: white !important;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #dee2e6;
            margin-top: 20px;
        }

        .hero-section {
            background-color: #e9ecef;
            padding: 30px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .sidebar {
            background-color: #f8f9fa;
            padding: 15px;
            border-right: 1px solid #dee2e6;
        }

        .dashboard-sidebar {
            background-color: #212529;
            color: white;
            padding: 15px;
            min-height: 100vh;
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
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.navigation')

    <main class="container my-4">
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
    </main>

    @include('layouts.footer')

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
