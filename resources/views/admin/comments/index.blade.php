@extends('layouts.admin')

@section('header', 'Manajemen Komentar')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h4 class="mb-1">Komentar Pengguna</h4>
        <p class="text-muted">Kelola dan moderasi komentar pada berita</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
    </div>
</div>

<!-- Filter Collapse -->
<div class="collapse mb-4" id="filterCollapse">
    <div class="card shadow-sm">
        <div class="card-body bg-light">
            <form action="{{ route('admin.comments.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari isi komentar...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="sort">
                        <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="user">
                        <option value="">Semua Pengguna</option>
                        @foreach(\App\Models\User::has('comments')->select('id', 'name')->get() as $commentUser)
                            <option value="{{ $commentUser->id }}" {{ request('user') == $commentUser->id ? 'selected' : '' }}>
                                {{ $commentUser->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Total Komentar</h6>
                    <h3 class="mb-0">{{ $commentsCount ?? $comments->total() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-chat-dots text-primary fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Pending</h6>
                    <h3 class="mb-0">{{ $pendingCount ?? 0 }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-hourglass-split text-warning fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Approved</h6>
                    <h3 class="mb-0">{{ $approvedCount ?? 0 }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-check-circle text-success fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Komentar Hari Ini</h6>
                    <h3 class="mb-0">{{ $todayCount ?? 0 }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-check text-info fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-0">
        <ul class="nav nav-pills nav-justified bg-light">
            <li class="nav-item">
                <a class="nav-link rounded-0 {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
                    <i class="bi bi-chat-text me-1"></i>Semua
                    <span class="badge bg-white text-primary ms-1">{{ $commentsCount ?? $comments->total() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0 {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'pending']) }}">
                    <i class="bi bi-hourglass-split me-1"></i>Pending
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingCount ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0 {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'approved']) }}">
                    <i class="bi bi-check-circle me-1"></i>Approved
                    <span class="badge bg-success ms-1">{{ $approvedCount ?? 0 }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">
                @if($status === 'all')
                    Semua Komentar
                @elseif($status === 'pending')
                    <i class="bi bi-hourglass-split text-warning me-1"></i>Komentar Pending
                @elseif($status === 'approved')
                    <i class="bi bi-check-circle text-success me-1"></i>Komentar Approved
                @endif
            </h5>

            @if($status === 'pending' && $comments->count() > 0)
            <div>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bulkApproveModal">
                    <i class="bi bi-check-all me-1"></i>Approve Semua
                </button>
            </div>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Pengguna</th>
                        <th width="20%">Berita</th>
                        <th width="30%">Komentar</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                    <tr>
                        <td>{{ $comment->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $comment->user->name }}</h6>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ $comment->user->role }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('news.show', $comment->news) }}" class="d-block text-truncate text-decoration-none" style="max-width: 200px;" target="_blank" data-bs-toggle="tooltip" title="{{ $comment->news->title }}">
                                <i class="bi bi-newspaper me-1 text-muted"></i>
                                {{ $comment->news->title }}
                            </a>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>{{ $comment->news->published_at ? $comment->news->published_at->format('d M Y') : 'Draft' }}
                            </small>
                        </td>
                        <td>
                            <div class="p-2 bg-light rounded">
                                {{ $comment->content }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span data-bs-toggle="tooltip" title="{{ $comment->created_at->format('d M Y, H:i:s') }}">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                                <span class="badge {{ $comment->status === 'approved' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }} mt-2">
                                    <i class="bi {{ $comment->status === 'approved' ? 'bi-check-circle-fill' : 'bi-hourglass-split' }} me-1"></i>
                                    {{ $comment->status }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($comment->status === 'pending')
                                <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Approve">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif

                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $comment->id }}, '{{ addslashes(Str::limit($comment->content, 50)) }}')"
                                        data-bs-toggle="tooltip"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <form id="delete-form-{{ $comment->id }}" action="{{ route('admin.comments.destroy', $comment) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-chat-square-text display-6 text-muted"></i>
                                <p class="mt-3 mb-0">Tidak ada komentar yang tersedia</p>
                                @if($status !== 'all')
                                <a href="{{ route('admin.comments.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    Lihat Semua Komentar
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($comments->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan {{ $comments->firstItem() ?? 0 }} - {{ $comments->lastItem() ?? 0 }} dari {{ $comments->total() }} komentar
            </div>
            <div>
                {{ $comments->appends(['status' => $status])->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCommentModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus komentar ini?</p>
                <div class="p-3 bg-light rounded mb-3">
                    <p class="mb-0" id="commentContentToDelete"></p>
                </div>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Tindakan ini tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="submitDelete()">
                    <i class="bi bi-trash me-1"></i>Hapus Komentar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Approve Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1" aria-labelledby="bulkApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkApproveModalLabel">Approve Semua Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyetujui semua komentar yang masih dalam status pending?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Tindakan ini akan menyetujui {{ $pendingCount ?? 0 }} komentar sekaligus.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.comments.approve-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-all me-1"></i>Approve Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// For delete confirmation
let commentIdToDelete = null;

function confirmDelete(id, content) {
    commentIdToDelete = id;
    document.getElementById('commentContentToDelete').textContent = content;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
    deleteModal.show();
}

function submitDelete() {
    if (commentIdToDelete) {
        document.getElementById(`delete-form-${commentIdToDelete}`).submit();
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
