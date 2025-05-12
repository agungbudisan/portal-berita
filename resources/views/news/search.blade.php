@extends('layouts.app')

@section('title', 'Hasil Pencarian: ' . $query)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Search Header -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="search-icon me-3">
                    <i class="bi bi-search text-primary" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h2 class="mb-1">Hasil Pencarian</h2>
                    <p class="mb-0 text-muted">
                        <span class="badge bg-light text-dark me-2">Kata kunci: "{{ $query }}"</span>
                        <span class="badge bg-primary">{{ $news->total() }} hasil</span>
                    </p>
                </div>
            </div>

            <!-- Quick Search Form -->
            <div class="mt-2">
                <form action="{{ route('news.search') }}" method="GET" class="d-flex">
                    <input type="text" name="query" class="form-control shadow-sm me-2" placeholder="Cari berita lainnya..." value="{{ $query }}">
                    <button class="btn btn-primary shadow-sm" type="submit">
                        <i class="bi bi-search me-md-2"></i>
                        <span class="d-none d-md-inline">Cari</span>
                    </button>
                </form>
            </div>
        </div>

        @if($news->count() > 0)
            <!-- News List -->
            <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                @foreach($news as $item)
                <div class="col">
                    @include('components.news-card', ['news' => $item])
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                <nav aria-label="Page navigation" class="shadow-sm">
                    {{ $news->appends(['query' => $query])->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        @else
            <!-- No Results Found -->
            <div class="bg-white rounded shadow-sm p-5 text-center mb-4">
                <div class="mb-3">
                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                </div>
                <h4>Tidak ada hasil yang ditemukan</h4>
                <p class="text-muted mb-4">Tidak ada berita yang sesuai dengan kata kunci "{{ $query }}".</p>

                <div class="mx-auto" style="max-width: 400px;">
                    <h6 class="text-start mb-3">Tips Pencarian:</h6>
                    <ul class="text-start text-muted">
                        <li>Periksa ejaan kata kunci Anda</li>
                        <li>Coba gunakan kata kunci yang lebih umum</li>
                        <li>Coba cari dengan kategori tertentu</li>
                        <li>Gunakan kata kunci yang lebih pendek</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary me-2">
                        <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                    </a>
                    <a href="#" onclick="history.back(); return false;" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Recent Searches (Dummy) -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Pencarian Terbaru</h5>
            <div class="d-flex flex-wrap gap-2">
                @php
                    // This could be implemented with real data in a future feature
                    $dummySearches = ['politik', 'ekonomi', 'olahraga', 'kesehatan', 'teknologi'];
                @endphp

                @foreach($dummySearches as $search)
                <a href="{{ route('news.search', ['query' => $search]) }}"
                   class="badge bg-light text-dark text-decoration-none p-2">
                    <i class="bi bi-clock-history me-1 small"></i> {{ $search }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Kategori</h5>
            <div class="row g-2 mb-3">
                @foreach(\App\Models\Category::withCount('news')->orderBy('news_count', 'desc')->take(6)->get() as $category)
                <div class="col-6">
                    <a href="{{ route('category.show', $category) }}" class="btn btn-outline-primary w-100 position-relative">
                        {{ $category->name }}
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                            {{ $category->news_count }}
                        </span>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="text-center">
                <a href="{{ route('category.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Lihat Semua Kategori
                </a>
            </div>
        </div>

        <!-- Popular Tags (Dummy) -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Tag Populer</h5>
            <div class="d-flex flex-wrap gap-2">
                @php
                    // This could be implemented with real tag data in the future
                    $dummyTags = [
                        'politik' => 42, 'ekonomi' => 38, 'pendidikan' => 24,
                        'kesehatan' => 21, 'teknologi' => 19, 'olahraga' => 17,
                        'hiburan' => 15, 'sosial' => 13, 'sains' => 10
                    ];
                @endphp

                @foreach($dummyTags as $tag => $count)
                <a href="{{ route('news.search', ['query' => $tag]) }}"
                   class="badge {{ $tag === strtolower($query) ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none p-2"
                   style="font-size: {{ 0.7 + ($count / 50) * 0.3 }}rem;">
                    #{{ $tag }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Login Prompt for Guest -->
        @include('components.guest-login-prompt')
    </div>
</div>

<style>
/* Enhanced Styles */
.wgt-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Dark mode compatibility */
.dark .bg-white,
.dark .card,
.dark .badge.bg-light {
    background-color: var(--dark-card) !important;
    color: var(--text-light) !important;
    border-color: var(--dark-border) !important;
}

.dark .bg-light {
    background-color: var(--dark-bg) !important;
}

.dark .text-dark {
    color: var(--text-light) !important;
}

.dark .text-muted {
    color: var(--text-muted-dark) !important;
}

.dark .border-primary {
    border-color: var(--primary-color) !important;
}

.dark .btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.dark .btn-outline-primary:hover {
    background-color: var(--primary-color);
    color: white;
}

/* Pagination styling */
.pagination {
    --bs-pagination-active-bg: var(--primary-color);
    --bs-pagination-active-border-color: var(--primary-color);
}
</style>
@endsection
