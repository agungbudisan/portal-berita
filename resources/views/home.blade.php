@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Hero News -->
        @if($featuredNews)
        <div class="hero-section">
            <div class="category-badge">{{ $featuredNews->category->name }}</div>
            <h3><a href="{{ route('news.show', $featuredNews) }}" class="text-decoration-none text-dark">{{ $featuredNews->title }}</a></h3>
            <p class="text-muted">{{ Str::limit(strip_tags($featuredNews->content), 150) }}</p>
            <small class="text-muted">{{ $featuredNews->published_at->diffForHumans() }}</small>
        </div>
        @endif

        <!-- News Grid -->
        <h5 class="wgt-title">Berita Terbaru</h5>
        <div class="row">
            @foreach($latestNews as $news)
            <div class="col-md-6 mb-4">
                @include('components.news-card', ['news' => $news])
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Search -->
        <div class="mb-4">
            <h5 class="wgt-title">Pencarian</h5>
            <form action="{{ route('news.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Cari berita..." value="{{ request('query') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>
        </div>

        <!-- Popular News -->
        <div class="mb-4">
            <h5 class="wgt-title">Berita Populer</h5>
            <div class="list-group">
                @foreach($popularNews as $news)
                <a href="{{ route('news.show', $news) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $news->title }}</h6>
                    </div>
                    <small class="text-muted">{{ $news->published_at->diffForHumans() }}</small>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Categories -->
        <div class="mb-4">
            <h5 class="wgt-title">Kategori</h5>
            <div class="list-group">
                @foreach($categories as $category)
                <a href="{{ route('category.show', $category) }}" class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $category->name }}
                    <span class="badge bg-primary rounded-pill">{{ $category->news_count }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Login Prompt for Guest -->
        @include('components.guest-login-prompt')
    </div>
</div>
@endsection
