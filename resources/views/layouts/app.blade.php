<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-bind:class="{ 'dark': darkMode }">
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

        /* Dark Mode Styles */
        .dark {
            background-color: #121212;
            color: #e1e1e1;
        }

        .dark .card, .dark .sidebar, .dark .footer, .dark .hero-section {
            background-color: #1e1e1e !important;
            color: #e1e1e1 !important;
            border-color: #333 !important;
        }

        .dark .bg-white, .dark .bg-light {
            background-color: #1e1e1e !important;
            color: #e1e1e1 !important;
        }

        .dark .text-dark {
            color: #e1e1e1 !important;
        }

        .dark .navbar, .dark .header {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
        }

        .dark .form-control {
            background-color: #2d2d2d;
            border-color: #444;
            color: #e1e1e1;
        }

        .dark .news-card {
            border-color: #333;
            background-color: #1e1e1e;
        }

        .dark .text-muted {
            color: #aaa !important;
        }

        .dark a:not(.btn) {
            color: #6d9fee;
        }

        .dark .list-group-item {
            background-color: #1e1e1e;
            border-color: #333;
            color: #e1e1e1;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js CDN as fallback -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-bind:class="{ 'bg-dark text-light': darkMode }">
    @include('layouts.navigation')

    <main class="container my-4">
        @if (session()->has('success'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-show="show"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="alert alert-success alert-dismissible fade show"
                 role="alert">
                {{ session('success') }}
                <button type="button" @click="show = false" class="btn-close" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-show="show"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="alert alert-danger alert-dismissible fade show"
                 role="alert">
                {{ session('error') }}
                <button type="button" @click="show = false" class="btn-close" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    @include('layouts.footer')

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
