<header class="bg-white shadow-sm" x-bind:class="{ 'bg-dark': darkMode }">
    <div class="container">
        <div class="row align-items-center py-3">
            <!-- Logo Column -->
            <div class="col-6 col-md-3">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="logo">Winni<span>News</span></div>
                </a>
            </div>

            <!-- Navigation Column -->
            <div class="col-md-6 d-none d-md-block">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i> Beranda
                        </a>
                    </li>

                    <!-- Top 3 Categories -->
                    @php
                        $topCategories = \App\Models\Category::take(3)->get();
                    @endphp

                    @foreach($topCategories as $category)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('category.show') && isset(request()->category) && request()->category->id == $category->id ? 'active' : '' }}"
                           href="{{ route('category.show', $category) }}">{{ $category->name }}</a>
                    </li>
                    @endforeach

                    <!-- Categories Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid-3x3-gap me-1"></i> Lainnya
                        </a>

                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            @php
                                $otherCategories = \App\Models\Category::whereNotIn('id', $topCategories->pluck('id')->toArray())->take(10)->get();
                            @endphp

                            @foreach($otherCategories as $category)
                            <li>
                                <a class="dropdown-item" href="{{ route('category.show', $category) }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach

                            @if(count($otherCategories) > 0)
                                <li><hr class="dropdown-divider"></li>
                            @endif

                            <li>
                                <a class="dropdown-item" href="{{ route('category.index') }}">
                                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Lihat Semua Kategori
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- User Controls Column -->
            <div class="col-6 col-md-3 text-end">
                <!-- Dark Mode Toggle -->
                <button class="btn btn-sm me-1 me-md-2"
                        x-on:click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                        x-bind:class="darkMode ? 'btn-light' : 'btn-dark'">
                    <i class="bi" x-bind:class="darkMode ? 'bi-sun' : 'bi-moon'"></i>
                </button>

                <!-- Mobile Menu Button -->
                <button class="btn btn-sm btn-outline-primary d-md-none ms-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavMenu" aria-controls="mobileNavMenu">
                    <i class="bi bi-list"></i>
                </button>

                <!-- Desktop User Menu -->
                <div class="d-none d-md-inline-block">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary me-2">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a>
                    @else
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.bookmarks') }}">Bookmark</a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.comments') }}">Komentar Saya</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Navigation Menu (Off-canvas) -->
<div class="offcanvas offcanvas-end mobile-nav-menu" tabindex="-1" id="mobileNavMenu" aria-labelledby="mobileNavMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileNavMenuLabel">Menu Navigasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Mobile User Controls -->
        <div class="mb-4">
            @guest
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i> Register
                    </a>
                </div>
            @else
                <div class="user-info mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="user-avatar me-2">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-person-gear me-1"></i> Edit Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>

        <!-- Main Navigation -->
        <div class="mb-4">
            <h6 class="nav-section-title">Navigasi Utama</h6>
            <ul class="nav flex-column nav-mobile">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door me-2"></i> Beranda
                    </a>
                </li>

                @if(Auth::check())
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.bookmarks') }}">
                                <i class="bi bi-bookmark me-2"></i> Bookmark
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.comments') }}">
                                <i class="bi bi-chat-dots me-2"></i> Komentar Saya
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>

        <!-- Categories -->
        <div>
            <h6 class="nav-section-title">Kategori</h6>
            <ul class="nav flex-column nav-mobile">
                <!-- Main Categories -->
                @foreach($topCategories as $category)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('category.show') && isset(request()->category) && request()->category->id == $category->id ? 'active' : '' }}"
                       href="{{ route('category.show', $category) }}">
                        <i class="bi bi-tag me-2"></i> {{ $category->name }}
                    </a>
                </li>
                @endforeach

                <!-- Other Categories -->
                @foreach($otherCategories as $category)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('category.show', $category) }}">
                        <i class="bi bi-tag me-2"></i> {{ $category->name }}
                    </a>
                </li>
                @endforeach

                <!-- View All Categories -->
                <li class="nav-item">
                    <a class="nav-link view-all" href="{{ route('category.index') }}">
                        <i class="bi bi-grid-3x3-gap-fill me-2"></i> Lihat Semua Kategori
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
/* Base styles for logo */
.logo {
    font-weight: 700;
    font-size: 1.5rem;
    color: #ff6b9a;
    transition: transform 0.3s ease;
}

.logo:hover {
    transform: scale(1.05);
}

.logo span {
    color: #4169E1;
}

/* Desktop Navigation styles */
.nav .nav-link {
    position: relative;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: color 0.2s ease;
}

.nav .nav-link:hover {
    color: #ff6b9a;
}

.nav .nav-link.active {
    color: #ff6b9a !important;
    font-weight: 600;
}

.nav .nav-link.active::after {
    content: '';
    position: absolute;
    left: 1rem;
    right: 1rem;
    bottom: 0;
    height: 3px;
    background-color: #ff6b9a;
    border-radius: 1.5px;
}

/* Dropdown styles */
.dropdown-menu {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 0.5rem;
}

.dropdown-item {
    padding: 0.5rem 1.25rem;
    transition: background-color 0.2s ease;
}

.dropdown-item:hover {
    background-color: rgba(255, 107, 154, 0.1);
    color: #ff6b9a;
}

/* Dark mode dropdown styles */
.dark .dropdown-menu {
    background-color: #1e293b;
    border-color: #334155;
}

.dark .dropdown-item {
    color: rgba(255, 255, 255, 0.85);
}

.dark .dropdown-item:hover {
    background-color: rgba(255, 107, 154, 0.15);
    color: #ff6b9a;
}

.dark .dropdown-divider {
    border-color: #334155;
}

/* Mobile Navigation Menu */
.mobile-nav-menu {
    width: 280px;
}

.dark .mobile-nav-menu {
    background-color: #1e293b;
    color: #f1f5f9;
}

.dark .mobile-nav-menu .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.nav-section-title {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 0.75rem;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.5rem;
}

.dark .nav-section-title {
    color: #94a3b8;
    border-color: #334155;
}

.nav-mobile .nav-link {
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
}

.nav-mobile .nav-link:hover,
.nav-mobile .nav-link:focus {
    background-color: rgba(255, 107, 154, 0.1);
    color: #ff6b9a;
}

.dark .nav-mobile .nav-link:hover,
.dark .nav-mobile .nav-link:focus {
    background-color: rgba(255, 107, 154, 0.15);
}

.nav-mobile .nav-link.active {
    color: #ff6b9a;
    font-weight: 600;
    background-color: rgba(255, 107, 154, 0.05);
}

.dark .nav-mobile .nav-link.active {
    background-color: rgba(255, 107, 154, 0.1);
}

.nav-mobile .view-all {
    color: #4169E1;
    font-weight: 500;
}

.dark .nav-mobile .view-all {
    color: #93c5fd;
}

/* User avatar for mobile menu */
.user-avatar {
    width: 40px;
    height: 40px;
    background-color: #ff6b9a;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
}

.user-name {
    font-weight: 600;
    font-size: 1.1rem;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .logo {
        font-size: 1.25rem;
    }
}

/* Dark mode offcanvas */
.dark .offcanvas {
    background-color: #1e293b;
    color: #f1f5f9;
}

.dark .offcanvas-header {
    border-bottom-color: #334155;
}

.dark .offcanvas-title {
    color: #f1f5f9;
}
</style>
