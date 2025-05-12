@extends('layouts.app')

@section('title', 'Semua Kategori')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Semua Kategori</h2>

        <!-- Trending Categories -->
        <div class="mb-5">
            <h4 class="mb-3">Kategori Populer</h4>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($trendingCategories as $category)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="card-title mb-0">{{ $category->name }}</h5>
                                <span class="badge bg-primary rounded-pill">{{ $category->news_count }}</span>
                            </div>
                            <p class="card-text text-muted small mb-3">Berita terkini seputar {{ $category->name }}</p>
                            <a href="{{ route('category.show', $category) }}" class="btn btn-sm btn-outline-primary d-block">Lihat Berita</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Alphabetical Categories Index -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h4 class="mb-0">Indeks Kategori</h4>
            </div>
            <div class="card-body">
                <!-- Alphabet Navigation -->
                <div class="mb-4 alphabet-nav">
                    @foreach(range('A', 'Z') as $letter)
                        @if(isset($categoriesByLetter[$letter]))
                            <a href="#letter-{{ $letter }}" class="btn btn-sm btn-outline-primary me-1 mb-2">{{ $letter }}</a>
                        @else
                            <span class="btn btn-sm btn-outline-secondary me-1 mb-2 disabled">{{ $letter }}</span>
                        @endif
                    @endforeach
                </div>

                <div class="row">
                    @foreach($categoriesByLetter as $letter => $letterCategories)
                        <div class="col-md-4 mb-4">
                            <h5 class="fw-bold" id="letter-{{ $letter }}">{{ $letter }}</h5>
                            <ul class="list-unstyled">
                                @foreach($letterCategories as $category)
                                <li class="mb-2 category-list-item">
                                    <a href="{{ route('category.show', $category) }}" class="d-flex justify-content-between align-items-center text-decoration-none">
                                        <span class="category-name">{{ $category->name }}</span>
                                        <span class="badge bg-light text-dark rounded-pill">{{ $category->news_count }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        @if($loop->iteration % 3 == 0)
                            </div><div class="row">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .category-list-item {
        transition: transform 0.2s ease;
    }

    .category-list-item:hover {
        transform: translateX(5px);
    }

    .category-list-item a {
        color: var(--text-dark);
        padding: 0.375rem 0;
        transition: color 0.2s ease;
    }

    .category-list-item:hover a {
        color: #FF4B91;
    }

    .category-name {
        font-weight: 500;
    }

    .alphabet-nav {
        position: sticky;
        top: 80px;
        z-index: 100;
        background: white;
        padding: 10px 0;
        border-radius: 0.5rem;
    }

    .dark .alphabet-nav {
        background: var(--dark-card);
    }

    .dark .badge.bg-light.text-dark {
        background-color: #334155 !important;
        color: var(--text-light) !important;
    }

    .dark .category-list-item a {
        color: var(--text-light);
    }
</style>
@endsection
