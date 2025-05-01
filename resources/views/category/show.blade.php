@extends('layouts.app')

@section('title', 'Kategori: ' . $category->name)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Category Title -->
        <div class="mb-4">
            <h3>Kategori: {{ $category->name }}</h3>
            <p class="text-muted">Temukan berita terbaru seputar {{ strtolower($category->name) }}</p>
        </div>

        <!-- News Grid with Infinite Scroll -->
        <div x-data="{
            page: 1,
            loading: false,
            noMoreContent: {{ $news->hasMorePages() ? 'false' : 'true' }},
            newsItems: [],
            init() {
                // Store current news items
                this.storeCurrentItems();

                // Set up scroll listener
                window.addEventListener('scroll', () => {
                    if (!this.loading && !this.noMoreContent &&
                        window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
                        this.loadMore();
                    }
                });
            },
            storeCurrentItems() {
                const currentItems = document.querySelectorAll('.news-article');
                currentItems.forEach(item => {
                    this.newsItems.push(item.outerHTML);
                });
            },
            async loadMore() {
                this.loading = true;
                this.page++;

                try {
                    const response = await fetch(`{{ route('category.show', $category) }}?page=${this.page}`);
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newItems = doc.querySelectorAll('.news-article');

                    if (newItems.length === 0) {
                        this.noMoreContent = true;
                    } else {
                        newItems.forEach(item => {
                            this.newsItems.push(item.outerHTML);
                        });
                    }
                } catch (error) {
                    console.error('Error loading more articles:', error);
                } finally {
                    this.loading = false;
                }
            }
        }">
            <div class="row original-content">
                @foreach($news as $item)
                <div class="col-md-6 mb-4 news-article">
                    @include('components.news-card', ['news' => $item])
                </div>
                @endforeach
            </div>

            <div class="row appended-content" x-html="newsItems.join('')"></div>

            <div x-show="loading" class="text-center my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div x-show="noMoreContent" class="text-center my-4">
                <p>Tidak ada lagi berita untuk ditampilkan</p>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Search -->
        <div class="mb-4">
            <h5 class="wgt-title">Pencarian</h5>
            <div x-data="{ query: '' }">
                <form action="{{ route('news.search') }}" method="GET">
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
                            Cari
                        </button>
                    </div>
                    <small x-show="query.length > 0 && query.length < 3" class="text-danger">
                        Minimal 3 karakter
                    </small>
                </form>
            </div>
        </div>

        <!-- Popular News in Category -->
        <div class="mb-4">
            <h5 class="wgt-title">Berita Populer di Kategori Ini</h5>
            <div class="list-group">
                @foreach($popularInCategory as $news)
                <a href="{{ route('news.show', $news) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $news->title }}</h6>
                    </div>
                    <small class="text-muted">{{ $news->published_at->diffForHumans() }}</small>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Other Categories -->
        <div class="mb-4">
            <h5 class="wgt-title">Kategori Lainnya</h5>
            <div class="list-group">
                @foreach($otherCategories as $otherCategory)
                <a href="{{ route('category.show', $otherCategory) }}" class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $otherCategory->name }}
                    <span class="badge bg-primary rounded-pill">{{ $otherCategory->news_count }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
