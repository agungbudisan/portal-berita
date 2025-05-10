@extends('layouts.user-dashboard')

@section('header', 'Komentar Saya')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom border-3 border-primary py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-chat-left-text-fill me-2 text-primary"></i>Komentar Saya
            </h5>
            <span class="badge bg-primary rounded-pill">{{ $comments->total() }}</span>
        </div>
    </div>

    <div class="card-body p-4">
        @if($comments->isEmpty())
            <div class="text-center py-5">
                <div class="display-1 text-muted mb-4">
                    <i class="bi bi-chat-quote"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Komentar</h4>
                <p class="text-muted mb-4">Anda belum memberikan komentar. Berikan pendapat Anda pada berita yang Anda baca!</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="bi bi-newspaper me-1"></i> Jelajahi Berita
                </a>
            </div>
        @else
            <!-- Filter and tabs -->
            <div class="row mb-4">
                <div class="col-md-7 mb-3 mb-md-0">
                    <ul class="nav nav-pills" id="commentsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-comments-tab" data-bs-toggle="pill"
                                    data-bs-target="#all-comments" type="button" role="tab"
                                    aria-controls="all-comments" aria-selected="true">Semua</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="approved-comments-tab" data-bs-toggle="pill"
                                    data-bs-target="#approved-comments" type="button" role="tab"
                                    aria-controls="approved-comments" aria-selected="false">Disetujui</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-comments-tab" data-bs-toggle="pill"
                                    data-bs-target="#pending-comments" type="button" role="tab"
                                    aria-controls="pending-comments" aria-selected="false">Menunggu</button>
                        </li>
                    </ul>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari komentar..." id="searchComment">
                        <button class="btn btn-outline-primary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Comments List -->
            <div class="tab-content" id="commentsTabContent">
                <!-- All Comments Tab -->
                <div class="tab-pane fade show active" id="all-comments" role="tabpanel" aria-labelledby="all-comments-tab">
                    <div class="comment-list">
                        @foreach($comments as $comment)
                        <div class="comment-item card mb-3 border-0 shadow-sm hover-card" data-status="{{ $comment->status }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="comment-article mb-0">
                                        <a href="{{ route('news.show', $comment->news) }}" class="text-decoration-none text-dark">
                                            {{ $comment->news->title }}
                                        </a>
                                    </h5>
                                    <span class="badge {{ $comment->status === 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $comment->status === 'approved' ? 'Disetujui' : 'Menunggu Persetujuan' }}
                                    </span>
                                </div>

                                <div class="comment-content card-text p-3 bg-light rounded mb-3">
                                    {{ $comment->content }}
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i> {{ $comment->created_at->format('d M Y, H:i') }}
                                    </small>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCommentModal"
                                                onclick="editComment('{{ $comment->id }}', '{{ addslashes($comment->content) }}')">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteCommentModal"
                                                onclick="setDeleteCommentId('{{ $comment->id }}')">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Approved Comments Tab -->
                <div class="tab-pane fade" id="approved-comments" role="tabpanel" aria-labelledby="approved-comments-tab">
                    <div class="comment-list">
                        @php $hasApproved = false; @endphp
                        @foreach($comments as $comment)
                            @if($comment->status === 'approved')
                                @php $hasApproved = true; @endphp
                                <div class="comment-item card mb-3 border-0 shadow-sm hover-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="comment-article mb-0">
                                                <a href="{{ route('news.show', $comment->news) }}" class="text-decoration-none text-dark">
                                                    {{ $comment->news->title }}
                                                </a>
                                            </h5>
                                            <span class="badge bg-success">Disetujui</span>
                                        </div>

                                        <div class="comment-content card-text p-3 bg-light rounded mb-3">
                                            {{ $comment->content }}
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> {{ $comment->created_at->format('d M Y, H:i') }}
                                            </small>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editCommentModal"
                                                        onclick="editComment('{{ $comment->id }}', '{{ addslashes($comment->content) }}')">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>

                                                <button class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteCommentModal"
                                                        onclick="setDeleteCommentId('{{ $comment->id }}')">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(!$hasApproved)
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-emoji-smile fs-1 mb-3"></i>
                                    <p>Belum ada komentar yang disetujui.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pending Comments Tab -->
                <div class="tab-pane fade" id="pending-comments" role="tabpanel" aria-labelledby="pending-comments-tab">
                    <div class="comment-list">
                        @php $hasPending = false; @endphp
                        @foreach($comments as $comment)
                            @if($comment->status === 'pending')
                                @php $hasPending = true; @endphp
                                <div class="comment-item card mb-3 border-0 shadow-sm hover-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="comment-article mb-0">
                                                <a href="{{ route('news.show', $comment->news) }}" class="text-decoration-none text-dark">
                                                    {{ $comment->news->title }}
                                                </a>
                                            </h5>
                                            <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                        </div>

                                        <div class="comment-content card-text p-3 bg-light rounded mb-3">
                                            {{ $comment->content }}
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> {{ $comment->created_at->format('d M Y, H:i') }}
                                            </small>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editCommentModal"
                                                        onclick="editComment('{{ $comment->id }}', '{{ addslashes($comment->content) }}')">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>

                                                <button class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteCommentModal"
                                                        onclick="setDeleteCommentId('{{ $comment->id }}')">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(!$hasPending)
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-emoji-smile fs-1 mb-3"></i>
                                    <p>Tidak ada komentar yang menunggu persetujuan.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal for editing comment -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
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
                        <textarea class="form-control" id="edit-content" name="content" rows="5" required></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i> Komentar yang diedit perlu disetujui ulang oleh admin
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for deleting comment -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCommentModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                </div>
                <p>Apakah Anda yakin ingin menghapus komentar ini?</p>
                <p class="small text-muted">Tindakan ini tidak dapat dibatalkan</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteCommentForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Hover effect for comment cards */
.hover-card {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    border-left: 3px solid var(--primary-color, #FF4B91);
}

/* Custom nav pills styling */
.nav-pills .nav-link {
    border-radius: 50px;
    padding: 0.5rem 1.25rem;
    color: var(--text-dark);
}

.nav-pills .nav-link.active {
    background-color: var(--primary-color, #FF4B91);
}

/* For dark mode compatibility */
.dark .card,
.dark .modal-content {
    background-color: var(--dark-card) !important;
    color: var(--text-light) !important;
}

.dark .card-header,
.dark .modal-header,
.dark .modal-footer {
    background-color: var(--dark-card) !important;
    border-color: var(--dark-border) !important;
}

.dark .nav-pills .nav-link {
    color: var(--text-light);
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

.dark .form-control {
    background-color: var(--dark-bg);
    border-color: var(--dark-border);
    color: var(--text-light);
}

/* Pagination */
.pagination {
    --bs-pagination-active-bg: var(--primary-color);
    --bs-pagination-active-border-color: var(--primary-color);
}
</style>

<script>
// Set up comment ID for delete modal
function setDeleteCommentId(id) {
    document.getElementById('deleteCommentForm').action = "{{ url('comment') }}/" + id;
}

// Edit comment function
function editComment(id, content) {
    document.getElementById('editCommentForm').action = "{{ url('comment') }}/" + id;
    document.getElementById('edit-content').value = content.replace(/\\'/g, "'");
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchComment');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const activeTab = document.querySelector('.tab-pane.active');

            activeTab.querySelectorAll('.comment-item').forEach(item => {
                const title = item.querySelector('.comment-article').textContent.toLowerCase();
                const content = item.querySelector('.comment-content').textContent.toLowerCase();

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
