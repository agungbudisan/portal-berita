<header class="bg-white shadow-sm" x-bind:class="{ 'bg-dark': darkMode }">
    <div class="container">
        <div class="row align-items-center py-3">
            <div class="col-md-3">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="logo">Winni<span>News</span></div>
                </a>
            </div>
            <div class="col-md-6">
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
            <div class="col-md-3 text-end">
                <!-- Dark Mode Toggle -->
                <button class="btn btn-sm me-2"
                        x-on:click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                        x-bind:class="darkMode ? 'btn-light' : 'btn-dark'">
                    <i class="bi" x-bind:class="darkMode ? 'bi-sun' : 'bi-moon'"></i>
                </button>

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
</header>

<style>
/* Some enhanced styles that keep your original structure */
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
</style>
