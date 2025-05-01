@extends('layouts.user-dashboard')

@section('header', 'Komentar Saya')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Daftar Komentar</h5>

        @if($comments->isEmpty())
            <div class="alert alert-info">
                <p class="mb-0">Anda belum memberikan komentar. Berikan pendapat Anda pada berita yang Anda baca!</p>
            </div>
        @else
            <div class="mb-4">
                @foreach($comments as $comment)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>
                                <a href="{{ route('news.show', $comment->news) }}" class="text-decoration-none text-dark">
                                    {{ $comment->news->title }}
                                </a>
                            </h5>
                            <span class="badge {{ $comment->status === 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $comment->status === 'approved' ? 'Disetujui' : 'Menunggu Persetujuan' }}
                            </span>
                        </div>
                        <p class="card-text">{{ $comment->content }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $comment->created_at->format('d M Y, H:i') }}</small>
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

            <div class="d-flex justify-content-center">
                {{ $comments->links() }}
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
