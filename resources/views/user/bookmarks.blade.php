@extends('layouts.user-dashboard')

@section('header', 'Bookmark Saya')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom border-3 border-primary py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-bookmark-fill me-2 text-primary"></i>Daftar Bookmark
            </h5>
            <span class="badge bg-primary rounded-pill">{{ $bookmarks->total() }}</span>
        </div>
    </div>

    <div class="card-body p-4">
        @if($bookmarks->isEmpty())
            <div class="text-center py-5">
                <div class="display-1 text-muted mb-4">
                    <i class="bi bi-bookmark"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Bookmark</h4>
                <p class="text-muted mb-4">Anda belum menyimpan bookmark. Jelajahi berita dan simpan yang Anda sukai!</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="bi bi-newspaper me-1"></i> Jelajahi Berita
                </a>
            </div>
        @else
            <!-- Filter and Search (Optional) -->
            <div class="row mb-4">
                <div class="col">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari bookmark..." id="searchBookmark">
                        <button class="btn btn-outline-primary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bookmark List -->
            <div class="bookmark-list" x-data="{ items: [] }" x-init="
                items = [...document.querySelectorAll('.bookmark-item')];

                // Search functionality
                document.getElementById('searchBookmark').addEventListener('input', (e) => {
                    const value = e.target.value.toLowerCase();
                    items.forEach(item => {
                        const title = item.querySelector('.bookmark-title').textContent.toLowerCase();
                        const content = item.querySelector('.bookmark-content').textContent.toLowerCase();
                        if (title.includes(value) || content.includes(value)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            ">
                @foreach($bookmarks as $bookmark)
                <div class="bookmark-item card mb-3 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="row g-0">
                            <!-- Left: Image or Placeholder -->
                            <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
                                <div class="news-image position-relative h-100 rounded">
                                    <div class="bookmark-image h-100 rounded" style="background-image: url('{{ $bookmark->news->image ? asset('storage/'.$bookmark->news->image) : asset('images/placeholder.jpg') }}'); background-size: cover; background-position: center; min-height: 120px;">
                                        @if(!$bookmark->news->image)
                                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-light rounded">
                                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <span class="badge bg-primary">{{ $bookmark->news->category->name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Content -->
                            <div class="col-md-9 col-lg-10 ps-md-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="bookmark-title card-title mb-0">
                                        @if($bookmark->news->source_url)
                                            <a href="{{ $bookmark->news->source_url }}" class="text-decoration-none text-dark" target="_blank">
                                                {{ $bookmark->news->title }} <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('news.show', $bookmark->news) }}" class="text-decoration-none text-dark">
                                                {{ $bookmark->news->title }}
                                            </a>
                                        @endif
                                    </h5>
                                    <div x-data="{ showConfirm: false }">
                                        <button @click="showConfirm = true"
                                                x-show="!showConfirm"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Hapus bookmark">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <div x-show="showConfirm" class="d-inline-block">
                                            <form action="{{ route('bookmark.destroy', $bookmark->news) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger me-1">Ya</button>
                                            </form>
                                            <button @click="showConfirm = false" class="btn btn-sm btn-secondary">Tidak</button>
                                        </div>
                                    </div>
                                </div>

                                <p class="bookmark-content card-text text-muted mb-2 small">
                                    {{ Str::limit(strip_tags($bookmark->news->content), 120) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i> Disimpan: {{ $bookmark->created_at->format('d M Y, H:i') }}
                                    </div>
                                    <div>
                                        <a href="{{ $bookmark->news->source_url ? $bookmark->news->source_url : route('news.show', $bookmark->news) }}"
                                           class="btn btn-sm btn-primary"
                                           {{ $bookmark->news->source_url ? 'target="_blank"' : '' }}>
                                            <i class="bi bi-book me-1"></i> Baca
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $bookmarks->links() }}
            </div>
        @endif
    </div>
</div>

<style>
/* Hover effect for bookmark cards */
.hover-card {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    border-left: 3px solid var(--primary-color, #FF4B91);
}

/* Image styling */
.bookmark-image {
    transition: all 0.3s ease;
}

.hover-card:hover .bookmark-image {
    transform: scale(1.05);
}

/* For dark mode compatibility */
.dark .card {
    background-color: var(--dark-card) !important;
    color: var(--text-light) !important;
}

.dark .card-header {
    background-color: var(--dark-card) !important;
    border-color: var(--primary-color) !important;
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

/* Pagination */
.pagination {
    --bs-pagination-active-bg: var(--primary-color);
    --bs-pagination-active-border-color: var(--primary-color);
}
</style>

<script>
// Simple search enhancement
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchBookmark');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.bookmark-item').forEach(item => {
                const title = item.querySelector('.bookmark-title').textContent.toLowerCase();
                const content = item.querySelector('.bookmark-content').textContent.toLowerCase();

                if (title.includes(searchTerm) || content.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection
