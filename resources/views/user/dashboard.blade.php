@extends('layouts.user-dashboard')

@section('header', 'Dashboard Pengguna')

@section('content')
<!-- Welcome Card -->
<div class="card border-0 shadow-sm welcome-card mb-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="row g-0">
            <div class="col-lg-8">
                <div class="p-4">
                    <h4 class="welcome-title mb-1">Selamat datang, {{ auth()->user()->name }}</h4>
                    <p class="text-muted mb-3">Kelola aktivitas Anda di portal berita WinniNews</p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('user.bookmarks') }}" class="btn btn-primary">
                            <i class="bi bi-bookmark me-1"></i> Bookmark Saya
                        </a>
                        <a href="{{ route('user.comments') }}" class="btn btn-outline-primary">
                            <i class="bi bi-chat-left-text me-1"></i> Komentar Saya
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-newspaper me-1"></i> Jelajahi Berita
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="welcome-image h-100" style="background-color: #FF4B9120;">
                    <div class="d-flex align-items-center justify-content-center h-100 p-4">
                        <img src="{{ asset('images/dashboard-illustration.svg') }}" class="img-fluid" alt="Dashboard Illustration"
                             onerror="this.onerror=null; this.src=''; this.parentElement.innerHTML = '<div class=\'display-1 text-primary\'><i class=\'bi bi-person-circle\'></i></div>';">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 stat-card bookmark-stat">
            <div class="card-body position-relative p-4">
                <div class="stat-icon position-absolute end-0 top-0 m-3 text-primary opacity-25">
                    <i class="bi bi-bookmark-star"></i>
                </div>
                <div class="stat-content">
                    <h6 class="text-uppercase text-muted small mb-3">Bookmark Disimpan</h6>
                    <h2 class="mb-2 stat-value">{{ $bookmarksCount }}</h2>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                             style="width: {{ min(100, $bookmarksCount * 5) }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ $recentBookmarks->isNotEmpty() ? 'Terakhir disimpan: ' . $recentBookmarks->first()->created_at->diffForHumans() : 'Belum ada bookmark' }}
                    </small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end p-3">
                <a href="{{ route('user.bookmarks') }}" class="btn btn-sm btn-link text-primary p-0">
                    Lihat semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 stat-card comment-stat">
            <div class="card-body position-relative p-4">
                <div class="stat-icon position-absolute end-0 top-0 m-3 text-primary opacity-25">
                    <i class="bi bi-chat-quote"></i>
                </div>
                <div class="stat-content">
                    <h6 class="text-uppercase text-muted small mb-3">Komentar Ditulis</h6>
                    <h2 class="mb-2 stat-value">{{ $commentsCount }}</h2>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                             style="width: {{ min(100, $commentsCount * 10) }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ $recentComments->isNotEmpty() ? 'Terakhir komentar: ' . $recentComments->first()->created_at->diffForHumans() : 'Belum ada komentar' }}
                    </small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end p-3">
                <a href="{{ route('user.comments') }}" class="btn btn-sm btn-link text-primary p-0">
                    Lihat semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 stat-card category-stat">
            <div class="card-body position-relative p-4">
                <div class="stat-icon position-absolute end-0 top-0 m-3 text-primary opacity-25">
                    <i class="bi bi-tag"></i>
                </div>
                <div class="stat-content">
                    <h6 class="text-uppercase text-muted small mb-3">Kategori Favorit</h6>
                    @if($favoriteCategory)
                        <h2 class="mb-2 stat-value">{{ $favoriteCategory->name }}</h2>
                        <a href="{{ route('category.show', $favoriteCategory) }}" class="badge bg-primary mb-3 text-decoration-none">
                            Lihat Berita
                        </a>
                    @else
                        <h3 class="mb-2 stat-value text-muted">Belum Ada</h3>
                        <div class="mb-3">-</div>
                    @endif
                    <small class="text-muted">Berdasarkan aktivitas membaca</small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end p-3">
                <a href="{{ route('home') }}" class="btn btn-sm btn-link text-primary p-0">
                    Jelajahi kategori <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Tabs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4">
        <ul class="nav nav-tabs card-header-tabs" id="activityTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="bookmarks-tab" data-bs-toggle="tab"
                        data-bs-target="#bookmarks-tab-pane" type="button" role="tab"
                        aria-controls="bookmarks-tab-pane" aria-selected="true">
                    <i class="bi bi-bookmark me-1"></i> Bookmark Terbaru
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comments-tab" data-bs-toggle="tab"
                        data-bs-target="#comments-tab-pane" type="button" role="tab"
                        aria-controls="comments-tab-pane" aria-selected="false">
                    <i class="bi bi-chat-left-text me-1"></i> Komentar Terbaru
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="activityTabsContent">
            <!-- Bookmarks Tab -->
            <div class="tab-pane fade show active" id="bookmarks-tab-pane" role="tabpanel" aria-labelledby="bookmarks-tab" tabindex="0">
                @if($recentBookmarks->isNotEmpty())
                    <div x-data="{ bookmarks: [] }" x-init="
                        bookmarks = {{ json_encode($recentBookmarks->map(function($bookmark) {
                            return [
                                'id' => $bookmark->id,
                                'news_id' => $bookmark->news_id,
                                'title' => $bookmark->news->title,
                                'content' => Str::limit(strip_tags($bookmark->news->content), 80),
                                'image' => $bookmark->news->image ? asset('storage/'.$bookmark->news->image) : asset('images/placeholder.jpg'),
                                'category' => $bookmark->news->category->name,
                                'url' => $bookmark->news->source_url ? $bookmark->news->source_url : route('news.show', $bookmark->news),
                                'external' => (bool)$bookmark->news->source_url,
                                'created_at' => $bookmark->created_at->diffForHumans(),
                                'delete_url' => route('bookmark.destroy', $bookmark->news)
                            ];
                        })) }}
                    ">
                        <div class="row g-4">
                            <template x-for="bookmark in bookmarks" :key="bookmark.id">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                        <div class="row g-0 h-100">
                                            <div class="col-4">
                                                <div class="rounded-start h-100 bg-light" x-bind:style="'background-image: url(' + bookmark.image + '); background-size: cover; background-position: center;'"></div>
                                            </div>
                                            <div class="col-8">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-2" style="line-height: 1.4;">
                                                        <a x-bind:href="bookmark.url"
                                                           class="text-decoration-none text-dark"
                                                           x-bind:target="bookmark.external ? '_blank' : ''">
                                                            <span x-text="bookmark.title"></span>
                                                            <i class="bi bi-box-arrow-up-right small text-muted" x-show="bookmark.external"></i>
                                                        </a>
                                                    </h6>
                                                    <p class="card-text small text-muted mb-2" x-text="bookmark.content"></p>
                                                    <div class="d-flex justify-content-between align-items-end">
                                                        <span class="badge bg-primary" x-text="bookmark.category"></span>
                                                        <button @click.prevent="
                                                            if (confirm('Hapus bookmark ini?')) {
                                                                fetch(bookmark.delete_url, {
                                                                    method: 'DELETE',
                                                                    headers: {
                                                                        'Content-Type': 'application/json',
                                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                    }
                                                                })
                                                                .then(() => {
                                                                    bookmarks = bookmarks.filter(b => b.id !== bookmark.id);
                                                                });
                                                            }
                                                        " class="btn btn-sm text-danger" title="Hapus bookmark">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="text-center mt-4" x-show="bookmarks.length === 0">
                            <div class="text-muted py-4">
                                <i class="bi bi-bookmark fs-1 mb-3"></i>
                                <p>Semua bookmark telah dihapus.</p>
                                <a href="{{ route('home') }}" class="btn btn-sm btn-primary">Jelajahi Berita</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-bookmark fs-1 mb-3"></i>
                            <p>Anda belum menyimpan bookmark.</p>
                            <p class="small">Jelajahi berita dan simpan yang Anda sukai!</p>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-primary">Jelajahi Berita</a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Comments Tab -->
            <div class="tab-pane fade" id="comments-tab-pane" role="tabpanel" aria-labelledby="comments-tab" tabindex="0">
                @if($recentComments->isNotEmpty())
                    <div x-data="{ comments: [] }" x-init="
                        comments = {{ json_encode($recentComments->map(function($comment) {
                            return [
                                'id' => $comment->id,
                                'content' => $comment->content,
                                'news_title' => $comment->news->title,
                                'news_url' => route('news.show', $comment->news),
                                'created_at' => $comment->created_at->diffForHumans(),
                                'status' => $comment->status,
                                'edit_url' => route('comment.update', $comment),
                                'delete_url' => route('comment.destroy', $comment)
                            ];
                        })) }}
                    ">
                        <div class="comments-container">
                            <template x-for="comment in comments" :key="comment.id">
                                <div class="card mb-3 border-0 shadow-sm hover-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between" x-data="{ showEdit: false, editContent: comment.content, submitting: false }">
                                            <small class="text-muted">
                                                <i class="bi bi-newspaper me-1"></i>
                                                <a x-bind:href="comment.news_url" class="text-decoration-none" x-text="comment.news_title"></a>
                                            </small>
                                            <div class="d-flex align-items-center">
                                                <span class="badge me-2"
                                                      x-bind:class="comment.status === 'approved' ? 'bg-success' : 'bg-warning text-dark'"
                                                      x-text="comment.status === 'approved' ? 'Disetujui' : 'Menunggu'"></span>
                                                <small class="text-muted" x-text="comment.created_at"></small>
                                            </div>
                                        </div>

                                        <template x-if="!showEdit">
                                            <div class="mt-3 bg-light p-3 rounded">
                                                <p class="card-text mb-0" x-text="comment.content"></p>
                                            </div>
                                        </template>

                                        <template x-if="showEdit">
                                            <div class="mt-3">
                                                <textarea class="form-control" x-model="editContent" rows="3"></textarea>
                                            </div>
                                        </template>

                                        <div class="d-flex justify-content-end mt-3">
                                            <template x-if="!showEdit">
                                                <div>
                                                    <button @click="showEdit = true" class="btn btn-sm btn-outline-primary me-2">
                                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                                    </button>
                                                    <button @click="
                                                        if (confirm('Yakin ingin menghapus komentar ini?')) {
                                                            fetch(comment.delete_url, {
                                                                method: 'DELETE',
                                                                headers: {
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                }
                                                            })
                                                            .then(() => {
                                                                comments = comments.filter(c => c.id !== comment.id);
                                                            });
                                                        }
                                                    " class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash me-1"></i> Hapus
                                                    </button>
                                                </div>
                                            </template>

                                            <template x-if="showEdit">
                                                <div>
                                                    <button @click="
                                                        submitting = true;
                                                        fetch(comment.edit_url, {
                                                            method: 'PUT',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            },
                                                            body: JSON.stringify({ content: editContent })
                                                        })
                                                        .then(response => {
                                                            if (response.ok) {
                                                                comment.content = editContent;
                                                                comment.status = 'pending';
                                                                showEdit = false;
                                                            }
                                                        })
                                                        .finally(() => {
                                                            submitting = false;
                                                        });
                                                    " class="btn btn-sm btn-primary me-2" x-bind:disabled="submitting">
                                                        <span x-show="submitting">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        </span>
                                                        <span x-show="!submitting">Simpan</span>
                                                    </button>
                                                    <button @click="showEdit = false" class="btn btn-sm btn-secondary">Batal</button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="text-center mt-4" x-show="comments.length === 0">
                            <div class="text-muted py-4">
                                <i class="bi bi-chat-quote fs-1 mb-3"></i>
                                <p>Semua komentar telah dihapus.</p>
                                <a href="{{ route('home') }}" class="btn btn-sm btn-primary">Baca Berita</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-chat-quote fs-1 mb-3"></i>
                            <p>Anda belum memberikan komentar.</p>
                            <p class="small">Berikan pendapat Anda pada berita yang Anda baca!</p>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-primary">Baca Berita</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Welcome Card Styling */
.welcome-card {
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    overflow: hidden;
}

.welcome-title {
    font-weight: 600;
}

.welcome-image {
    transition: all 0.3s ease;
}

.welcome-card:hover .welcome-image {
    transform: scale(1.05);
}

/* Stat Cards */
.stat-card {
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.stat-icon {
    font-size: 4rem;
}

.stat-value {
    font-weight: 600;
}

.bookmark-stat:hover {
    border-top: 3px solid #FF4B91;
}

.comment-stat:hover {
    border-top: 3px solid #FF4B91;
}

.category-stat:hover {
    border-top: 3px solid #FF4B91;
}

/* Tabs Styling */
.nav-tabs .nav-link {
    color: var(--text-dark);
    border: none;
    padding: 0.75rem 1.25rem;
    font-weight: 500;
    position: relative;
}

.nav-tabs .nav-link.active {
    color: var(--primary-color, #FF4B91);
    background-color: transparent;
    border: none;
}

.nav-tabs .nav-link.active::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background-color: var(--primary-color, #FF4B91);
}

.nav-tabs .nav-link:hover {
    border: none;
    color: var(--primary-color, #FF4B91);
}

/* Hover Cards */
.hover-card {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    border-left: 3px solid #FF4B91;
}

/* For dark mode compatibility */
.dark .card,
.dark .card-header {
    background-color: var(--dark-card) !important;
    color: var(--text-light) !important;
    border-color: var(--dark-border) !important;
}

.dark .bg-light {
    background-color: var(--dark-bg) !important;
}

.dark .welcome-image {
    background-color: rgba(255, 75, 145, 0.1) !important;
}

.dark .nav-tabs .nav-link {
    color: var(--text-light);
}

.dark .text-dark {
    color: var(--text-light) !important;
}

.dark .text-muted {
    color: var(--text-muted-dark) !important;
}

.dark .btn-outline-secondary {
    color: var(--text-light);
    border-color: var(--dark-border);
}
</style>
@endsection
