@extends('layouts.admin')

@section('header', 'Manajemen Komentar')

@section('content')
<div class="mb-4">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">Semua</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'pending']) }}">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'approved']) }}">Approved</a>
        </li>
    </ul>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pengguna</th>
                        <th>Berita</th>
                        <th>Komentar</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                    <tr>
                        <td>{{ $comment->id }}</td>
                        <td>{{ $comment->user->name }}</td>
                        <td>
                            <a href="{{ route('news.show', $comment->news) }}" target="_blank">
                                {{ Str::limit($comment->news->title, 30) }}
                            </a>
                        </td>
                        <td>{{ Str::limit($comment->content, 50) }}</td>
                        <td>{{ $comment->created_at->format('d-M-Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $comment->status === 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $comment->status }}
                            </span>
                        </td>
                        <td>
                            @if($comment->status === 'pending')
                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-outline-success me-1">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus komentar ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada komentar yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $comments->appends(['status' => $status])->links() }}
        </div>
    </div>
</div>
@endsection
