@extends('layouts.app')

@section('title', 'Semua Berita')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Semua Berita</li>
                </ol>
            </nav>

            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-2 mb-0">
                    @if(request('category'))
                        {{ $categories->where('id', request('category'))->first()->name ?? 'Berita' }}
                    @else
                        Semua Berita
                    @endif
                </h1>
                <div>
                    <button class="btn btn-sm btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterSidebar">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </div>

            <!-- Active Filters -->
            @if(request()->anyFilled(['category', 'date_from', 'date_to', 'sort']))
            <div class="alert alert-light mb-4">
                <div class="d-flex align-items-center">
                    <div><i class="bi bi-funnel me-2"></i> Filter aktif:</div>
                    <div class="ms-3">
                        @if(request('category'))
                            <span class="badge bg-primary me-2">
                                Kategori: {{ $categories->where('id', request('category'))->first()->name ?? '' }}
                            </span>
                        @endif

                        @if(request('date_from') || request('date_to'))
                            <span class="badge bg-primary me-2">
                                Tanggal:
                                {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') : '' }}
                                {{ (request('date_from') && request('date_to')) ? '-' : '' }}
                                {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') : '' }}
                            </span>
                        @endif

                        @if(request('sort'))
                            <span class="badge bg-primary me-2">
                                Urutan:
                                @switch(request('sort'))
                                    @case('oldest')
                                        Terlama
                                        @break
                                    @case('most_viewed')
                                        Paling Banyak Dilihat
                                        @break
                                    @case('most_commented')
                                        Paling Banyak Dikomentari
                                        @break
                                    @default
                                        Terbaru
                                @endswitch
                            </span>
                        @endif
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Hapus Filter
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- News Grid -->
            <div class="row g-4 mb-4">
                @forelse($news as $item)
                <div class="col-md-6 col-lg-4">
                    @include('components.news-card', ['news' => $item])
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-newspaper display-1 text-muted"></i>
                    <h3 class="mt-3">Belum ada berita</h3>
                    <p class="text-muted">
                        @if(request()->anyFilled(['category', 'date_from', 'date_to', 'sort']))
                            Tidak ada berita yang sesuai dengan filter. Coba ubah filter atau <a href="{{ route('news.index') }}">lihat semua berita</a>.
                        @else
                            Belum ada berita yang tersedia saat ini. Silakan kunjungi kembali nanti.
                        @endif
                    </p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mb-4">
                {{ $news->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Desktop Filter -->
            <div class="card shadow-sm mb-4 d-none d-lg-block">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i> Filter Berita</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('news.index') }}" method="GET">
                        <!-- Kategori -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->news_count }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rentang Tanggal -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Rentang Tanggal</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="Dari">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengurutan -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="most_viewed" {{ request('sort') == 'most_viewed' ? 'selected' : '' }}>Paling Banyak Dilihat</option>
                                <option value="most_commented" {{ request('sort') == 'most_commented' ? 'selected' : '' }}>Paling Banyak Dikomentari</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Filter Offcanvas -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="filterSidebar" aria-labelledby="filterSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="filterSidebarLabel"><i class="bi bi-funnel me-2"></i> Filter Berita</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form action="{{ route('news.index') }}" method="GET">
                        <!-- Kategori -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->news_count }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rentang Tanggal -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Rentang Tanggal</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengurutan -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="most_viewed" {{ request('sort') == 'most_viewed' ? 'selected' : '' }}>Paling Banyak Dilihat</option>
                                <option value="most_commented" {{ request('sort') == 'most_commented' ? 'selected' : '' }}>Paling Banyak Dikomentari</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Subscribe Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body bg-primary bg-opacity-10 rounded">
                    <h5 class="card-title fw-bold"><i class="bi bi-envelope-paper me-2"></i> Berlangganan Newsletter</h5>
                    <p class="card-text small">Dapatkan update berita terbaru langsung ke email Anda</p>
                    <div x-data="{ email: '', submitted: false, valid: false }">
                        <div class="input-group shadow-sm">
                            <input
                                type="email"
                                class="form-control"
                                placeholder="Email Anda"
                                x-model="email"
                                @input="valid = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(email)">
                            <button
                                class="btn btn-primary"
                                type="button"
                                x-bind:disabled="!valid || submitted"
                                @click="
                                    submitted = true;
                                    setTimeout(() => {
                                        alert('Terima kasih telah berlangganan newsletter kami!');
                                        email = '';
                                        submitted = false;
                                    }, 1000);
                                ">
                                <span x-show="submitted">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </span>
                                <span x-show="!submitted">Subscribe</span>
                            </button>
                        </div>
                        <small x-show="email && !valid" class="text-danger">
                            Email tidak valid
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Text truncate for lists */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Card hover effects */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
}

/* Breadcrumb styling */
.breadcrumb-item + .breadcrumb-item::before {
    content: "\F231";
    font-family: "bootstrap-icons";
    font-size: 0.7rem;
    vertical-align: middle;
}

/* Widget title */
.wgt-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Dark mode compatibility */
.dark .bg-white,
.dark .card,
.dark .list-group-item {
    background-color: var(--dark-card) !important;
    color: var(--text-light) !important;
    border-color: var(--dark-border) !important;
}

.dark .bg-light,
.dark .alert-light,
.dark .btn-outline-secondary {
    background-color: var(--dark-bg) !important;
    color: var(--text-light) !important;
    border-color: var(--dark-border) !important;
}

.dark .text-muted {
    color: var(--text-muted-dark) !important;
}

.dark .text-dark {
    color: var(--text-light) !important;
}

.dark .bg-primary.bg-opacity-10 {
    background-color: rgba(65, 105, 225, 0.2) !important;
}

.dark .form-control,
.dark .form-select,
.dark .input-group-text {
    background-color: var(--dark-card);
    border-color: var(--dark-border);
    color: var(--text-light);
}

.dark .offcanvas {
    background-color: var(--dark-bg);
    color: var(--text-light);
}
</style>
@endsection
