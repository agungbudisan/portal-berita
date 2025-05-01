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
            <div class="list-group mb-3">
                @foreach($recentBookmarks as $bookmark)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">
                            <a href="{{ route('news.show', $bookmark->news) }}" class="text-decoration-none text-dark">
                                {{ $bookmark->news->title }}
                            </a>
                        </h6>
                        <small class="text-muted">{{ $bookmark->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary">{{ $bookmark->news->category->name }}</span>
                        <form action="{{ route('bookmark.destroy', $bookmark->news) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-danger" title="Hapus bookmark">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
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
            <div class="mb-3">
                @foreach($recentComments as $comment)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">
                                Pada:
                                <a href="{{ route('news.show', $comment->news) }}" class="text-decoration-none">
                                    {{ $comment->news->title }}
                                </a>
                            </small>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="card-text mt-2">{{ $comment->content }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge {{ $comment->status === 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $comment->status === 'approved' ? 'Disetujui' : 'Menunggu Persetujuan' }}
                            </span>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2"
                                        onclick="editComment('{{ $comment->id }}', '{{ addslashes($comment->content) }}')">
                                    Edit
                                </button>
                                <form action="{{ route('comment.destroy', $comment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                Anda belum memberikan komentar. Berikan pendapat Anda pada berita yang Anda baca!
            </div>
        @endif
    </div>
</div>

<!-- Modal for editing comment -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCommentModalLabel">Edit Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-content" class="form-label">Komentar</label>
                        <textarea class="form-control" id="edit-content" name="content" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editComment(id, content) {
        document.getElementById('editCommentForm').action = "{{ url('comment') }}/" + id;
        document.getElementById('edit-content').value = content.replace(/\\'/g, "'");
        var modal = new bootstrap.Modal(document.getElementById('editCommentModal'));
        modal.show();
    }
</script>
@endsection
