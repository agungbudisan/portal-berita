<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
      x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - {{ config('app.name', 'WinniNews') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Nunito:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Summernote WYSIWYG Editor -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

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

        .dashboard-main {
            flex: 1;
            margin-left: 250px;
            transition: all 0.3s ease;
            min-height: 100vh;
            background-color: var(--light-bg);
        }

        .sidebar-logo-container {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center; /* Changed from space-between to center */
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
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-left: 4px solid var(--primary-color);
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

        .table thead th {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
            padding-top: 1rem;
            padding-bottom: 1rem;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom-width: 1px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.03);
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
            box-shadow: 0 4px 6px rgba(var(--bs-primary-rgb), 0.2);
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #e54483;
            border-color: #e54483;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(var(--bs-primary-rgb), 0.25);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .alert {
            border-radius: var(--border-radius-md);
            border: none;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            animation: slideInDown 0.5s ease forwards;
            border-radius: var(--border-radius-md);
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: var(--bs-success);
        }

        .alert-danger {
            border-left-color: var(--bs-danger);
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Badge style improvisasi */
        .badge {
            padding: 0.5em 0.75em;
            border-radius: 30px;
            font-weight: 500;
        }

        /* Form styling */
        .form-control, .form-select {
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1rem;
            border-color: #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.1);
        }

        /* Content preview cards */
        .content-preview {
            height: 60px;
            overflow: hidden;
            position: relative;
        }

        .content-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(rgba(255,255,255,0), rgba(255,255,255,1));
        }

        /* Image preview component */
        .image-preview-container {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            border: 1px dashed #e2e8f0;
            margin-bottom: 1rem;
        }

        .image-preview-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Improved avatar */
        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }

        /* Custom file input */
        .form-control[type="file"] {
            padding: 0.375rem 0.75rem;
        }

        .form-control[type="file"]::file-selector-button {
            padding: 0.375rem 0.75rem;
            margin: -0.375rem -0.75rem;
            margin-inline-end: 0.75rem;
            border: 0;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease-in-out;
            background-color: var(--primary-color);
            color: white;
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
            background-color: rgba(65, 105, 225, 0.1);
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

        .dark .form-control, .dark .form-select {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--text-light);
        }

        .dark .form-control:focus, .dark .form-select:focus {
            border-color: var(--primary-color);
        }

        .dark .image-preview-container {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
        }

        .dark .content-preview::after {
            background: linear-gradient(rgba(30,41,59,0), rgba(30,41,59,1));
        }

        .dark .note-editor.note-frame {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
        }

        .dark .note-editor.note-frame .note-editing-area,
        .dark .note-editor.note-frame .note-statusbar {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        .dark .note-editor.note-frame .note-toolbar {
            background-color: var(--dark-bg);
            border-color: var(--dark-border);
        }

        .dark .modal-content {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--text-light);
        }

        .dark .modal-header, .dark .modal-footer {
            border-color: var(--dark-border);
        }

        .dark .pagination .page-item .page-link {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--text-light);
        }

        .dark .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .dark .alert {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        /* Pagination styling */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            border-radius: var(--border-radius-sm);
            margin: 0 2px;
            color: var(--text-dark);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Responsiveness enhancement */
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

            .dashboard-content {
                padding: 1rem;
            }

            .form-control-lg {
                font-size: 1rem;
                padding: 0.5rem 0.75rem;
            }
        }

        /* Breadcrumb styling */
        .breadcrumb {
            padding: 0.5rem 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-size: 1.2rem;
            line-height: 1;
            vertical-align: middle;
        }

        /* Loader */
        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid #FFF;
            border-bottom-color: var(--primary-color);
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Tooltip enhancement */
        .tooltip {
            font-family: 'Nunito', sans-serif;
            font-size: 0.75rem;
        }

        .tooltip .tooltip-inner {
            box-shadow: var(--shadow-sm);
            padding: 0.5rem 0.75rem;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        .dark ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .dark ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Footer styling */
        footer {
            transition: var(--transition-default);
        }

        .dark footer {
            background-color: var(--dark-card) !important;
            border-color: var(--dark-border) !important;
            color: var(--text-light);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js CDN as fallback -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="dashboard-sidebar"
             x-bind:class="{ 'open': sidebarOpen }">

            <div class="sidebar-logo-container">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    <div class="logo">Winni<span>News</span></div>
                </a>
                <!-- Tombol toggle sidebar dihapus pada desktop -->
            </div>

            <div class="sidebar-content">
                <div class="sidebar-section">
                    <div class="sidebar-section-title">MENU</div>
                    <ul class="nav flex-column dashboard-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}"
                               href="{{ route('admin.news.index') }}">
                                <i class="bi bi-newspaper"></i>
                                <span>Manajemen Berita</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                               href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-tag"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                               href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
                               href="{{ route('admin.comments.index') }}">
                                <i class="bi bi-chat-dots"></i>
                                <span>Komentar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.api-sources.*') ? 'active' : '' }}"
                               href="{{ route('admin.api-sources.index') }}">
                                <i class="bi bi-gear"></i>
                                <span>Pengaturan API</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">AKUN</div>
                    <ul class="nav flex-column dashboard-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main"
             x-bind:class="{ 'shifted': sidebarOpen }">

            <!-- Header -->
            <header class="dashboard-header">
                <div class="d-flex align-items-center">
                    <button type="button"
                            class="toggle-sidebar d-lg-none"
                            @click="sidebarOpen = !sidebarOpen">
                        <i class="bi" x-bind:class="sidebarOpen ? 'bi-x-lg' : 'bi-list'"></i>
                    </button>
                    <h5 class="header-title">@yield('header', 'Dashboard Admin')</h5>
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
                        <span class="user-name d-none d-md-block">{{ Auth::user()->name }}</span>
                        <div class="user-avatar" style="background-color: {{ ['#FF4B91', '#4169E1', '#FFC107', '#20C997'][array_rand(['#FF4B91', '#4169E1', '#FFC107', '#20C997'])] }};">
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

            <!-- Footer -->
            <footer class="mt-auto py-3 bg-white border-top">
                <div class="container-fluid">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div class="text-muted small">
                            &copy; {{ date('Y') }} WinniNews. All rights reserved.
                        </div>
                        <div class="d-flex mt-2 mt-md-0">
                            <div class="text-muted small me-3">
                                <i class="bi bi-clock me-1"></i> Server time: {{ now()->format('H:i:s') }}
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i> Version 1.0
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tooltips initialization for all elements with data-bs-toggle="tooltip" -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

    <!-- Global AJAX Setup for CSRF token -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Additional scripts for specific pages -->
    @stack('scripts')
</body>
</html>
