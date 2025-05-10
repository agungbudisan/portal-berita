<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-bind:class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WinniNews') }}</title>

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
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                transition: var(--transition-default);
            }

            h1, h2, h3, h4, h5, h6, .logo {
                font-family: 'Montserrat', sans-serif;
                font-weight: 600;
            }

            .auth-container {
                width: 100%;
                max-width: 420px;
                padding: 0 15px;
            }

            .auth-card {
                background-color: white;
                border-radius: var(--border-radius-lg);
                box-shadow: var(--shadow-md);
                overflow: hidden;
                transition: var(--transition-default);
            }

            .auth-header {
                padding: 1.5rem;
                text-align: center;
                background-color: var(--primary-color);
                color: white;
            }

            .auth-body {
                padding: 2rem;
            }

            .auth-logo {
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
            }

            .auth-logo span {
                color: var(--secondary-color);
            }

            .form-group {
                margin-bottom: 1.25rem;
            }

            .form-control {
                padding: 0.75rem 1rem;
                border-radius: var(--border-radius-sm);
                border: 1px solid #e2e8f0;
                transition: var(--transition-default);
            }

            .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.25rem rgba(255, 75, 145, 0.25);
            }

            .btn {
                border-radius: var(--border-radius-sm);
                padding: 0.625rem 1.25rem;
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

            .auth-footer {
                text-align: center;
                margin-top: 1.5rem;
                color: var(--text-muted);
            }

            .toggle-dark {
                position: absolute;
                top: 1rem;
                right: 1rem;
                width: 2.5rem;
                height: 2.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.2);
                cursor: pointer;
                color: white;
                transition: var(--transition-default);
            }

            .toggle-dark:hover {
                background-color: rgba(255, 255, 255, 0.3);
            }

            .home-link {
                display: inline-block;
                margin-bottom: 1rem;
                color: var(--text-dark);
                text-decoration: none;
                font-weight: 500;
                transition: var(--transition-default);
            }

            .home-link:hover {
                color: var(--primary-color);
            }

            /* Dark Mode Styles */
            .dark {
                background-color: var(--dark-bg);
                color: var(--text-light);
            }

            .dark .auth-card {
                background-color: var(--dark-card);
                border-color: var(--dark-border);
            }

            .dark .form-control {
                background-color: var(--dark-bg);
                border-color: var(--dark-border);
                color: var(--text-light);
            }

            .dark .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.25rem rgba(255, 75, 145, 0.25);
            }

            .dark .home-link {
                color: var(--text-light);
            }

            .dark .toggle-dark {
                background-color: rgba(0, 0, 0, 0.3);
            }

            .dark .text-dark {
                color: var(--text-light) !important;
            }

            .dark .auth-footer {
                color: var(--text-muted-dark);
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js CDN as fallback -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body x-bind:class="{ 'dark': darkMode }">
        <div class="position-relative auth-container">
            <div class="toggle-dark"
                 x-on:click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                 x-bind:title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                <i class="bi" x-bind:class="darkMode ? 'bi-sun' : 'bi-moon'"></i>
            </div>

            <a href="{{ route('home') }}" class="home-link">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>

            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">Winni<span>News</span></div>
                    <p class="mb-0">Portal Berita Terkini dan Terpercaya</p>
                </div>

                <div class="auth-body">
                    {{ $slot }}
                </div>
            </div>

            <div class="auth-footer">
                <small>&copy; {{ date('Y') }} WinniNews. All rights reserved.</small>
            </div>
        </div>

        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
