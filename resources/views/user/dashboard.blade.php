@extends('layouts.user-dashboard')

@section('header', 'Dashboard Pengguna')

@section('content')
<!-- Welcome -->
<div class="card mb-4">
    <div class="card-body">
        <h5>Selamat Datang, {{ auth()->user()->name }}</h5>
        <p>Kelola bookmark dan komentar Anda di portal berita WinniNews.</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="text-muted">Bookmark Disimpan</h6>
            <h3>{{ $bookmarksCount }}</h3>
            <small class="text-muted">{{ $recentBookmarks->isNotEmpty() ? 'Terakhir disimpan: ' . $recentBookmarks->first()->created_at->diffForHumans() : 'Belum ada bookmark' }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="text-muted">Komentar Ditulis</h6>
            <h3>{{ $commentsCount }}</h3>
            <small class="text-muted">{{ $recentComments->isNotEmpty() ? 'Terakhir komentar: ' . $recentComments->first()->created_at->diffForHumans() : 'Belum ada komentar' }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="text-muted">Kategori Favorit</h6>
            <h3>{{ $favoriteCategory ? $favoriteCategory->name : 'Belum Ada' }}</h3>
            <small class="text-muted">Berdasarkan aktivitas membaca</small>
        </div>
    </div>
</div>

<!-- Bookmark Section -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Bookmark Terbaru</h5>
            <a href="{{ route('user.bookmarks') }}" class="btn btn-sm btn-link">Lihat Semua</a>
        </div>

        @if($recentBookmarks->isNotEmpty())
            <div x-data="{ bookmarks: [] }" x-init="
                bookmarks = {{ json_encode($recentBookmarks->map(function($bookmark) {
                    return [
                        'id' => $bookmark->id,
                        'news_id' => $bookmark->news_id,
                        'title' => $bookmark->news->title,
                        'category' => $bookmark->news->category->name,
                        'url' => route('news.show', $bookmark->news),
                        'created_at' => $bookmark->created_at->diffForHumans(),
                        'delete_url' => route('bookmark.destroy', $bookmark->news)
                    ];
                })) }}
            ">
                <div class="list-group mb-3">
                    <template x-for="bookmark in bookmarks" :key="bookmark.id">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <a :href="bookmark.url" class="text-decoration-none text-dark" x-text="bookmark.title"></a>
                                </h6>
                                <small class="text-muted" x-text="bookmark.created_at"></small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary" x-text="bookmark.category"></span>
                                <button @click="
                                    fetch(bookmark.delete_url, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(() => {
                                        bookmarks = bookmarks.filter(b => b.id !== bookmark.id);
                                    })
                                " class="btn btn-sm text-danger" title="Hapus bookmark">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                Anda belum menyimpan bookmark. Jelajahi berita dan simpan yang Anda sukai!
            </div>
        @endif
    </div>
</div>

<!-- Comments Section -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Komentar Terbaru Anda</h5>
            <a href="{{ route('user.comments') }}" class="btn btn-sm btn-link">Lihat Semua</a>
        </div>

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
                <div class="mb-3">
                    <template x-for="comment in comments" :key="comment.id">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between" x-data="{ showEdit: false, editContent: comment.content, submitting: false }">
                                    <small class="text-muted">
                                        Pada:
                                        <a :href="comment.news_url" class="text-decoration-none" x-text="comment.news_title"></a>
                                    </small>
                                    <small class="text-muted" x-text="comment.created_at"></small>
                                </div>
                                <template x-if="!showEdit">
                                    <p class="card-text mt-2" x-text="comment.content"></p>
                                </template>
                                <template x-if="showEdit">
                                    <div class="mt-2">
                                        <textarea class="form-control" x-model="editContent" rows="3"></textarea>
                                    </div>
                                </template>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge"
                                          :class="comment.status === 'approved' ? 'bg-success' : 'bg-warning text-dark'"
                                          x-text="comment.status === 'approved' ? 'Disetujui' : 'Menunggu Persetujuan'"></span>
                                    <div x-data="{ showEdit: false }">
                                        <template x-if="!showEdit">
                                            <div>
                                                <button @click="showEdit = true" class="btn btn-sm btn-outline-primary me-2">Edit</button>
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
                                                " class="btn btn-sm btn-outline-danger">Hapus</button>
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
                                                " class="btn btn-sm btn-primary me-2" :disabled="submitting">
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
                        </div>
                    </template>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                Anda belum memberikan komentar. Berikan pendapat Anda pada berita yang Anda baca!
            </div>
        @endif
    </div>
</div>
@endsection
