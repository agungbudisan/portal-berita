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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Nunito:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #FF4B91;
            --secondary-color: #4169E1;
            --accent-color: #FFD9E8;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-border: #334155;
            --light-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #f1f5f9;
            --text-muted: #64748b;
            --text-muted-dark: #94a3b8;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --border-radius-sm: 0.375rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 0.75rem;
            --transition-default: all 0.3s ease;
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--text-dark);
            background-color: var(--light-bg);
            line-height: 1.6;
            transition: var(--transition-default);
        }

        h1, h2, h3, h4, h5, h6, .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        .category-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition-default);
        }

        .category-badge:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .wgt-title {
            font-size: 1.25rem;
            position: relative;
            padding-bottom: 0.75rem;
            margin-bottom: 1.25rem;
            display: inline-block;
            font-weight: 600;
        }

        .wgt-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px;
        }

        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            transition: var(--transition-default);
        }

        .logo:hover {
            transform: scale(1.03);
        }

        .logo span {
            color: var(--secondary-color);
        }

        .news-card {
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius-md);
            overflow: hidden;
            margin-bottom: 1.25rem;
            height: 100%;
            transition: var(--transition-default);
            box-shadow: var(--shadow-sm);
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .news-card-img {
            height: 200px;
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .news-card-img::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 50%, rgba(0,0,0,0.7) 100%);
            opacity: 0;
            transition: var(--transition-default);
        }

        .news-card:hover .news-card-img::before {
            opacity: 1;
        }

        .news-card-body {
            padding: 1.25rem;
        }

        .bookmark-btn {
            color: var(--primary-color);
            border: none;
            background: none;
            cursor: pointer;
            transition: var(--transition-default);
        }

        .bookmark-btn:hover {
            transform: scale(1.2);
        }

        /* Navigation Styles */
        .navbar {
            padding: 0.75rem 0;
            transition: var(--transition-default);
        }

        .navbar-brand {
            padding: 0;
            margin-right: 2rem;
        }

        .navbar-nav .nav-link {
            position: relative;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--transition-default);
            color: var(--text-dark);
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            background-color: transparent !important;
            font-weight: 600;
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0.75rem;
            right: 0.75rem;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px 3px 0 0;
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon {
            width: 1.25em;
            height: 1.25em;
        }

        .toggle-dark {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition-default);
            background-color: #f1f5f9;
        }

        .toggle-dark:hover {
            background-color: #e2e8f0;
        }

        .user-dropdown .dropdown-toggle {
            border-radius: var(--border-radius-sm);
            transition: var(--transition-default);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-dropdown .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        .user-dropdown .dropdown-menu {
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow-md);
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0;
        }

        .user-dropdown .dropdown-item {
            padding: 0.5rem 1.25rem;
            transition: var(--transition-default);
        }

        .user-dropdown .dropdown-item:hover {
            background-color: var(--accent-color);
        }

        .footer {
            background-color: white;
            padding: 3rem 0 1.5rem;
            border-top: 1px solid #e2e8f0;
            margin-top: 3rem;
            transition: var(--transition-default);
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 1.25rem;
            font-size: 1.1rem;
        }

        .footer ul li {
            margin-bottom: 0.5rem;
        }

        .footer ul li a {
            color: var(--text-muted);
            transition: var(--transition-default);
        }

        .footer ul li a:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .footer address {
            color: var(--text-muted);
            font-style: normal;
        }

        .hero-section {
            background-color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: var(--transition-default);
            border: 1px solid #e2e8f0;
        }

        .hero-section:hover {
            box-shadow: var(--shadow-md);
        }

        .hero-section h3 {
            margin-bottom: 1rem;
        }

        .hero-section h3 a {
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition-default);
        }

        .hero-section h3 a:hover {
            color: var(--primary-color);
        }

        .sidebar-widget {
            background-color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius-md);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e2e8f0;
            transition: var(--transition-default);
        }

        .search-form .input-group {
            box-shadow: var(--shadow-sm);
            border-radius: var(--border-radius-sm);
            overflow: hidden;
        }

        .search-form .form-control {
            border-right: none;
            padding: 0.75rem 1rem;
        }

        .search-form .btn {
            border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
            padding: 0.75rem 1.25rem;
        }

        .list-group-item {
            padding: 0.75rem 1.25rem;
            border-left: none;
            border-right: none;
            transition: var(--transition-default);
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item:hover {
            background-color: var(--accent-color);
        }

        .alert {
            border-radius: var(--border-radius-md);
            border: none;
            box-shadow: var(--shadow-sm);
        }

        .btn {
            border-radius: var(--border-radius-sm);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: var(--transition-default);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #e54483;
            border-color: #e54483;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(229, 68, 131, 0.2);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(229, 68, 131, 0.2);
        }

        .pagination {
            margin-top: 2rem;
        }

        .pagination .page-link {
            border-radius: var(--border-radius-sm);
            margin: 0 0.25rem;
            color: var(--primary-color);
            transition: var(--transition-default);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Responsive styles */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: white;
                border-radius: var(--border-radius-md);
                padding: 1rem;
                margin-top: 1rem;
                box-shadow: var(--shadow-md);
            }

            .navbar-nav {
                margin-bottom: 1rem;
            }

            .nav-link {
                padding: 0.5rem 0.75rem;
            }

            .hero-section {
                padding: 1.5rem;
            }

            .news-card-img {
                height: 180px;
            }
        }

        /* Dark Mode Styles */
        .dark {
            background-color: var(--dark-bg);
            color: var(--text-light);
        }

        .dark .card,
        .dark .sidebar-widget,
        .dark .footer,
        .dark .hero-section,
        .dark .navbar-collapse {
            background-color: var(--dark-card) !important;
            color: var(--text-light) !important;
            border-color: var(--dark-border) !important;
        }

        .dark .bg-white, .dark .bg-light {
            background-color: var(--dark-card) !important;
            color: var(--text-light) !important;
        }

        .dark .text-dark {
            color: var(--text-light) !important;
        }

        .dark .navbar {
            background-color: var(--dark-card) !important;
        }

        .dark .navbar-toggler-icon {
            filter: invert(1);
        }

        .dark .navbar-light .navbar-nav .nav-link {
            color: var(--text-light);
        }

        .dark .navbar-light .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }

        .dark .text-muted {
            color: var(--text-muted-dark) !important;
        }

        .dark .form-control {
            background-color: var(--dark-bg);
            border-color: var(--dark-border);
            color: var(--text-light);
        }

        .dark .news-card {
            border-color: var(--dark-border);
            background-color: var(--dark-card);
        }

        .dark a:not(.btn) {
            color: #93c5fd;
        }

        .dark .hero-section h3 a {
            color: var(--text-light);
        }

        .dark .nav-link {
            color: var(--text-light);
        }

        .dark .list-group-item {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--text-light);
        }

        .dark .list-group-item:hover {
            background-color: rgba(255, 75, 145, 0.1);
        }

        .dark .dropdown-menu {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
        }

        .dark .dropdown-item {
            color: var(--text-light);
        }

        .dark .dropdown-item:hover {
            background-color: rgba(255, 75, 145, 0.1);
            color: var(--text-light);
        }

        .dark .toggle-dark {
            background-color: var(--dark-card);
        }

        .dark .toggle-dark:hover {
            background-color: var(--dark-bg);
        }

        .dark hr, .dark .dropdown-divider {
            border-color: var(--dark-border);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js CDN as fallback -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-bind:class="{ 'dark': darkMode }">
    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Main Content -->
    <main class="container py-4">
        @if (session()->has('success'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-show="show"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="alert alert-success alert-dismissible fade show"
                 role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
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
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
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
