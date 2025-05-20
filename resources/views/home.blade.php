@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Hero News -->
        @if($featuredNews)
        <div class="hero-section">
            <div class="row">
                <div class="col-md-5">
                    <div class="rounded" style="height: 240px; background-image: url('{{ $featuredNews->image_url ? $featurednews->image_url : asset('images/placeholder.jpg') }}'); background-size: cover; background-position: center;"></div>
                </div>
                <div class="col-md-7">
                    <div class="category-badge">{{ $featuredNews->category->name }}</div>
                    <h3 class="mb-3">
                        <a href="{{ route('news.show', $featuredNews) }}" class="text-decoration-none text-dark">
                            {{ $featuredNews->title }}
                        </a>
                    </h3>
                    <p class="text-muted">{{ Str::limit(strip_tags($featuredNews->content), 150) }}</p>
                    <div class="d-flex align-items-center mt-3">
                        <i class="bi bi-clock me-1"></i>
                        <small class="text-muted">
                            @if($featuredNews->published_at)
                                {{ $featuredNews->published_at->diffForHumans() }}
                            @else
                                Baru saja
                            @endif
                        </small>
                        @if($featuredNews->source)
                            <small class="text-muted ms-2">
                                <span class="mx-1">•</span>
                                <i class="bi bi-newspaper me-1"></i>
                                {{ $featuredNews->source }}
                            </small>
                        @endif

                        @auth
                            <div class="ms-auto" x-data="{ isBookmarked: {{ $featuredNews->isBookmarkedByUser(auth()->user()) ? 'true' : 'false' }} }">
                                <button type="button"
                                        class="bookmark-btn"
                                        @click="
                                            $dispatch('bookmark-toggle');
                                            isBookmarked = !isBookmarked;
                                            fetch('{{ $featuredNews->isBookmarkedByUser(auth()->user())
                                                ? route('bookmark.destroy', $featuredNews)
                                                : route('bookmark.store', $featuredNews) }}', {
                                                method: isBookmarked ? 'POST' : 'DELETE',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                }
                                            });
                                        "
                                        x-bind:title="isBookmarked ? 'Hapus dari bookmark' : 'Simpan ke bookmark'">
                                    <i class="bi" x-bind:class="isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark'"></i>
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- News Grid -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="wgt-title mb-0">Berita Terbaru</h5>
            <a href="{{ route('news.index') }}" class="text-decoration-none small">Lihat Semua <i class="bi bi-chevron-right"></i></a>
        </div>

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
                {{ $latestNews->links() }}
            </ul>
        </nav>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Search -->
        <div class="sidebar-widget">
            <h5 class="wgt-title mb-3">Pencarian</h5>
            <div x-data="{ query: '' }">
                <form action="{{ route('news.search') }}" method="GET" class="search-form">
                    <div class="input-group">
                        <input type="text"
                               name="query"
                               x-model="query"
                               class="form-control"
                               placeholder="Cari berita..."
                               value="{{ request('query') }}">
                        <button class="btn btn-primary"
                                type="submit"
                                x-bind:disabled="query.length < 3">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <small x-show="query.length > 0 && query.length < 3" class="text-danger mt-1 d-block">
                        Minimal 3 karakter
                    </small>
                </form>
            </div>
        </div>

        <!-- Popular News -->
        <div class="sidebar-widget">
            <h5 class="wgt-title mb-3">Berita Populer</h5>
            <div class="list-group list-group-flush">
                @foreach($popularNews as $index => $news)
                <a href="{{ route('news.show', $news) }}" class="list-group-item list-group-item-action px-0">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3 text-primary fw-bold">
                            #{{ $index + 1 }}
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $news->title }}</h6>
                            <div class="d-flex align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    @if($news->published_at)
                                        {{ $news->published_at->diffForHumans() }}
                                    @else
                                        Baru saja
                                    @endif
                                </small>
                                @if($news->source)
                                    <small class="text-muted ms-2">
                                        <span class="mx-1">•</span> {{ $news->source }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Categories -->
        <div class="sidebar-widget">
            <h5 class="wgt-title mb-3">Kategori</h5>
            <div class="list-group list-group-flush">
                @foreach($categories as $category)
                <a href="{{ route('category.show', $category) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0">
                    <span>
                        <i class="bi bi-tag me-2"></i>
                        {{ $category->name }}
                    </span>
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
