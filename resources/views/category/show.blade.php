@extends('layouts.app')

@section('title', 'Kategori: ' . $category->name)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Category Header -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <div class="d-flex align-items-center mb-2">
                <div class="category-badge me-2">Kategori</div>
                <nav aria-label="breadcrumb" class="ms-auto">
                    <ol class="breadcrumb bg-transparent p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    </ol>
                </nav>
            </div>
            <h2 class="mb-2">{{ $category->name }}</h2>
            <p class="text-muted mb-0">Temukan berita terbaru seputar {{ strtolower($category->name) }}</p>
        </div>

        <!-- News Grid with Infinite Scroll -->
        <div x-data="{
            page: 1,
            loading: false,
            noMoreContent: {{ $news->hasMorePages() ? 'false' : 'true' }},
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
                        const container = document.querySelector('.appended-content');
                        newItems.forEach(item => {
                            container.appendChild(item.cloneNode(true));
                        });
                    }
                } catch (error) {
                    console.error('Error loading more articles:', error);
                } finally {
                    this.loading = false;
                }
            },
            init() {
                // Set up scroll listener
                window.addEventListener('scroll', () => {
                    if (!this.loading && !this.noMoreContent &&
                        window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
                        this.loadMore();
                    }
                });
            }
        }">
            <!-- Original Content -->
            <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                @foreach($news as $item)
                <div class="col news-article">
                    @include('components.news-card', ['news' => $item])
                </div>
                @endforeach
            </div>

            <!-- Appended Content through Infinite Scroll -->
            <div class="row row-cols-1 row-cols-md-2 g-4 appended-content"></div>

            <!-- Loading Indicator -->
            <div x-show="loading" class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat berita lainnya...</p>
            </div>

            <!-- End of Content Notification -->
            <div x-show="noMoreContent" class="text-center my-5 py-3 bg-light rounded shadow-sm">
                <i class="bi bi-check-circle text-primary" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">Anda telah melihat semua berita di kategori ini</p>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Search -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Pencarian</h5>
            <div x-data="{ query: '' }">
                <form action="{{ route('news.search') }}" method="GET">
                    <div class="input-group shadow-sm">
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
                    <small x-show="query.length > 0 && query.length < 3" class="text-danger mt-2 d-block">
                        <i class="bi bi-exclamation-circle me-1"></i> Minimal 3 karakter
                    </small>
                </form>
            </div>
        </div>

        {{-- <!-- Popular News in Category -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Populer di {{ $category->name }}</h5>
            <div class="list-group list-group-flush">
                @foreach($popularInCategory as $index => $newsItem)
                <a href="{{ $newsItem->source_url ? $newsItem->source_url : route('news.show', $newsItem) }}"
                   class="list-group-item list-group-item-action border-0 py-3 px-0"
                   {{ $newsItem->source_url ? 'target="_blank"' : '' }}>
                    <div class="row g-0">
                        <div class="col-2 text-center">
                            <span class="badge rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto"
                                  style="width: 28px; height: 28px; font-size: 0.8rem;">{{ $index + 1 }}</span>
                        </div>
                        <div class="col-10">
                            <h6 class="mb-1 text-truncate-2">
                                {{ $newsItem->title }}
                                @if($newsItem->source_url)
                                    <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                @endif
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i> {{ $newsItem->published_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Other Categories -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Kategori Lainnya</h5>
            <div class="row g-2">
                @foreach($otherCategories as $otherCategory)
                <div class="col-6">
                    <a href="{{ route('category.show', $otherCategory) }}"
                       class="card text-decoration-none border-0 shadow-sm h-100">
                        <div class="card-body p-3 text-center">
                            <h6 class="card-title mb-1">{{ $otherCategory->name }}</h6>
                            <span class="badge bg-primary rounded-pill">{{ $otherCategory->news_count }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('category.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Lihat Semua Kategori
                </a>
            </div>
        </div> --}}

        <!-- Newsletter Widget -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-primary bg-opacity-10 rounded">
                <h5 class="card-title fw-bold"><i class="bi bi-envelope-paper me-2"></i> Berlangganan Update</h5>
                <p class="card-text small">Dapatkan berita terbaru dari kategori {{ $category->name }} langsung ke email Anda</p>
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
                                    alert('Terima kasih telah berlangganan update kategori ini!');
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

<style>
/* Enhanced Styles */
.wgt-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.category-badge {
    background-color: #FF4B91;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Text truncate for title */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Dark mode compatibility */
.dark .bg-white,
.dark .card,
.dark .list-group-item {
    background-color: var(--dark-card) !important;
    color: var(--text-light) !important;
    border-color: var(--dark-border) !important;
}

.dark .bg-light {
    background-color: var(--dark-bg) !important;
    color: var(--text-light) !important;
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
</style>
@endsection
