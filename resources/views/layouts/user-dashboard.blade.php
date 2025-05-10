<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarCollapsed: window.innerWidth < 768 }"
      x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>User Dashboard - {{ config('app.name', 'WinniNews') }}</title>

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
            --sidebar-bg: #1e293b;
            --sidebar-accent: #0f172a;
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
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .dashboard-sidebar {
            background-color: var(--sidebar-bg);
            color: var(--text-light);
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .dashboard-sidebar.collapsed {
            width: 60px;
        }

        .dashboard-main {
            flex: 1;
            margin-left: 250px;
            transition: all 0.3s ease;
            min-height: 100vh;
            background-color: var(--light-bg);
        }

        .dashboard-main.expanded {
            margin-left: 60px;
        }

        .sidebar-logo-container {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-content {
            padding: 1.25rem;
        }

        .sidebar-section {
            margin-bottom: 1.5rem;
        }

        .sidebar-section-title {
            color: var(--text-muted-dark);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.75rem;
            padding-left: 0.5rem;
            transition: all 0.3s ease;
        }

        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            transition: var(--transition-default);
            white-space: nowrap;
        }

        .logo:hover {
            transform: scale(1.03);
        }

        .logo span {
            color: var(--secondary-color);
        }

        .dashboard-menu .nav-link {
            color: var(--text-light);
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 0.375rem;
            display: flex;
            align-items: center;
            transition: var(--transition-default);
            white-space: nowrap;
        }

        .dashboard-menu .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(3px);
        }

        .dashboard-menu .nav-link.active {
            background-color: var(--primary-color) !important;
            color: white !important;
            box-shadow: 0 4px 6px rgba(255, 75, 145, 0.2);
        }

        .dashboard-menu .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            transition: var(--transition-default);
        }

        .dashboard-menu .nav-link span {
            transition: opacity 0.3s ease;
        }

        .collapsed .sidebar-section-title,
        .collapsed .nav-link span,
        .collapsed .logo span {
            opacity: 0;
            visibility: hidden;
        }

        .collapsed .nav-link {
            padding: 0.75rem;
            justify-content: center;
        }

        .collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.25rem;
        }

        .dashboard-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition-default);
        }

        .header-title {
            font-weight: 600;
            font-size: 1.25rem;
            margin: 0;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            color: var(--text-dark);
            cursor: pointer;
            font-size: 1.25rem;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            border-radius: var(--border-radius-sm);
            transition: var(--transition-default);
        }

        .toggle-sidebar:hover {
            background-color: var(--light-bg);
        }

        .toggle-dark {
            background: none;
            border: none;
            color: var(--text-dark);
            cursor: pointer;
            font-size: 1.25rem;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            border-radius: var(--border-radius-sm);
            transition: var(--transition-default);
        }

        .toggle-dark:hover {
            background-color: var(--light-bg);
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-name {
            margin-right: 0.75rem;
            font-weight: 500;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .dashboard-content {
            padding: 1.5rem;
        }

        .stat-card {
            background-color: white;
            border-radius: var(--border-radius-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-default);
            border: 1px solid #e2e8f0;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-card-icon {
            width: 50px;
            height: 50px;
            background-color: var(--accent-color);
            color: var(--primary-color);
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-card-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin: 0;
        }

        .card {
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid #e2e8f0;
            transition: var(--transition-default);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            color: var(--text-muted);
            border-bottom-width: 1px;
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

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }

        .alert {
            border-radius: var(--border-radius-md);
            border: none;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }

        .logout-button {
            color: var(--text-light);
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius-sm);
            margin-top: 1rem;
            display: flex;
            align-items: center;
            transition: var(--transition-default);
            background-color: rgba(220, 53, 69, 0.2);
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            white-space: nowrap;
        }

        .logout-button:hover {
            background-color: rgba(220, 53, 69, 0.3);
            transform: translateX(3px);
        }

        .logout-button i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .collapsed .logout-button {
            padding: 0.75rem;
            justify-content: center;
        }

        .collapsed .logout-button i {
            margin-right: 0;
        }

        .collapsed .logout-button span {
            opacity: 0;
            visibility: hidden;
        }

        /* Dark Mode Styles */
        .dark {
            background-color: var(--dark-bg);
            color: var(--text-light);
        }

        .dark .dashboard-sidebar {
            background-color: var(--dark-bg);
            border-right: 1px solid var(--dark-border);
        }

        .dark .sidebar-logo-container {
            border-bottom: 1px solid var(--dark-border);
        }

        .dark .dashboard-header {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
        }

        .dark .stat-card,
        .dark .card {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--text-light);
        }

        .dark .card-header {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--text-light);
        }

        .dark .toggle-sidebar,
        .dark .toggle-dark {
            color: var(--text-light);
        }

        .dark .toggle-sidebar:hover,
        .dark .toggle-dark:hover {
            background-color: var(--dark-bg);
        }

        .dark .stat-card-icon {
            background-color: rgba(255, 75, 145, 0.2);
        }

        .dark .table {
            color: var(--text-light);
        }

        .dark .table th {
            color: var(--text-muted-dark);
        }

        .dark .table td,
        .dark .table th {
            border-color: var(--dark-border);
        }

        .dark .header-title {
            color: var(--text-light);
        }

        .dark .user-name {
            color: var(--text-light);
        }

        /* Responsive styles */
        @media (max-width: 991.98px) {
            .dashboard-sidebar {
                width: 250px;
                left: -250px;
            }

            .dashboard-sidebar.open {
                left: 0;
            }

            .dashboard-main {
                margin-left: 0;
            }

            .dashboard-main.shifted {
                margin-left: 250px;
            }
        }

        @media (max-width: 767.98px) {
            .dashboard-main.shifted {
                margin-left: 0;
                transform: translateX(250px);
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js CDN as fallback -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="dashboard-wrapper" x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <div class="dashboard-sidebar"
             x-bind:class="{ 'collapsed': sidebarCollapsed && !sidebarOpen, 'open': sidebarOpen }">

            <div class="sidebar-logo-container">
                <a href="{{ route('user.dashboard') }}" class="text-decoration-none">
                    <div class="logo">Winni<span>News</span></div>
                </a>
                <button type="button"
                        class="d-none d-lg-block btn btn-sm text-white bg-transparent border-0"
                        @click="sidebarCollapsed = !sidebarCollapsed">
                    <i class="bi" x-bind:class="sidebarCollapsed ? 'bi-chevron-right' : 'bi-chevron-left'"></i>
                </button>
            </div>

            <div class="sidebar-content">
                <div class="sidebar-section">
                    <div class="sidebar-section-title">MENU</div>
                    <ul class="nav flex-column dashboard-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"
                               href="{{ route('user.dashboard') }}">
                                <i class="bi bi-grid"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.bookmarks') ? 'active' : '' }}"
                               href="{{ route('user.bookmarks') }}">
                                <i class="bi bi-bookmark"></i>
                                <span>Bookmark</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.comments') ? 'active' : '' }}"
                               href="{{ route('user.comments') }}">
                                <i class="bi bi-chat-dots"></i>
                                <span>Komentar Saya</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mt-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main"
             x-bind:class="{
                'expanded': sidebarCollapsed && !sidebarOpen,
                'shifted': sidebarOpen
             }">

            <!-- Header -->
            <header class="dashboard-header">
                <div class="d-flex align-items-center">
                    <button type="button"
                            class="toggle-sidebar d-lg-none"
                            @click="sidebarOpen = !sidebarOpen">
                        <i class="bi" x-bind:class="sidebarOpen ? 'bi-x-lg' : 'bi-list'"></i>
                    </button>
                    <h5 class="header-title">@yield('header', 'Dashboard Pengguna')</h5>
                </div>

                <div class="d-flex align-items-center">
                    <button type="button"
                            class="toggle-dark"
                            @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                            x-bind:title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                        <i class="bi" x-bind:class="darkMode ? 'bi-sun' : 'bi-moon'"></i>
                    </button>

                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary me-3">
                        <i class="bi bi-house-door me-1"></i> Portal
                    </a>

                    <div class="user-profile">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="dashboard-content">
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
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
