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
                    <div class="rounded" style="height: 240px; background-image: url('{{ $featuredNews->image_url ? $featuredNews->image_url : asset('images/placeholder.jpg') }}'); background-size: cover; background-position: center;"></div>
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
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Pencarian</h5>
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
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Berita Populer</h5>
            <div class="list-group list-group-flush">
                @foreach($popularNews as $index => $popular)
                <a href="{{ $popular->source_url ? $popular->source_url : route('news.show', $popular) }}"
                class="list-group-item list-group-item-action border-0 py-3 px-0"
                {{ $popular->source_url ? 'target="_blank"' : '' }}>
                    <div class="row g-0">
                        <div class="col-2 d-flex align-items-center">
                            <span class="badge rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                style="width: 30px; height: 30px;">{{ $index + 1 }}</span>
                        </div>
                        <div class="col-10">
                            <h6 class="mb-1 text-truncate-2">
                                {{ $popular->title }}
                                @if($popular->source_url)
                                    <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                @endif
                            </h6>
                            <div class="d-flex align-items-center small">
                                <span class="text-muted me-2">
                                    <i class="bi bi-eye me-1"></i> {{ number_format($popular->views_count) }}
                                </span>
                                <span class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    @if($popular->published_at)
                                        {{ $popular->published_at->diffForHumans() }}
                                    @else
                                        Baru saja
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Kategori</h5>
            <div class="list-group list-group-flush">
                @foreach(\App\Models\Category::withCount(['news' => function($query) {
                    $query->where('status', 'published')->whereNotNull('published_at');
                }])->orderBy('news_count', 'desc')->take(5)->get() as $category)
                <a href="{{ route('category.show', $category) }}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2 px-0">
                    <span>
                        <i class="bi bi-tag-fill me-2 text-muted"></i>
                        {{ $category->name }}
                    </span>
                    <span class="badge bg-secondary rounded-pill">
                        {{ $category->news_count }}
                    </span>
                </a>
                @endforeach
                <div class="text-center mt-2">
                    <a href="{{ route('category.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Kategori
                    </a>
                </div>
            </div>
        </div>

        <!-- Login Prompt for Guest -->
        @include('components.guest-login-prompt')
    </div>
</div>
<style>
    .text-truncate-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>
@endsection
