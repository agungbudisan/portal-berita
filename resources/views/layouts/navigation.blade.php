<header class="bg-white shadow-sm">
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
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>
                    @foreach(\App\Models\Category::take(4)->get() as $category)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('category.show') && request()->category->id == $category->id ? 'active' : '' }}"
                           href="{{ route('category.show', $category) }}">{{ $category->name }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-3 text-end">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary me-2">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a>
                @else
                    <div class="dropdown">
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
