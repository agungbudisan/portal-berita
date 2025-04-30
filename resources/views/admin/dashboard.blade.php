@extends('layouts.admin')

@section('header', 'Dashboard Admin')

@section('content')
<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6 class="text-muted">Total Berita</h6>
            <h3>{{ $newsCount }}</h3>
            <div class="d-flex align-items-center">
                <span class="text-success small">+12% </span>
                <span class="text-muted small"> dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6 class="text-muted">Total Pengguna</h6>
            <h3>{{ $userCount }}</h3>
            <div class="d-flex align-items-center">
                <span class="text-success small">+8% </span>
                <span class="text-muted small"> dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6 class="text-muted">Total Komentar</h6>
            <h3>{{ $commentCount }}</h3>
            <div class="d-flex align-items-center">
                <span class="text-success small">+15% </span>
                <span class="text-muted small"> dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6 class="text-muted">Berita Hari Ini</h6>
            <h3>{{ $todayNewsCount }}</h3>
            <div class="d-flex align-items-center">
                @if($todayNewsCount > 0)
                <span class="text-success small">+{{ $todayNewsCount }} </span>
                <span class="text-muted small"> hari ini</span>
                @else
                <span class="text-danger small">0 </span>
                <span class="text-muted small"> hari ini</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- API Status -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Status API</h5>
            <a href="{{ route('admin.api-sources.index') }}" class="btn btn-sm btn-primary">Kelola API</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama API</th>
                    <th>Status</th>
                    <th>Last Sync</th>
                    <th>Berita</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($apiSources as $api)
                <tr>
                    <td>{{ $api->name }}</td>
                    <td>
                        <span class="badge {{ $api->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ $api->status == 'active' ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $api->last_sync ? $api->last_sync->format('d-M-Y H:i') : 'Belum Pernah' }}</td>
                    <td>{{ $api->news_count }}</td>
                    <td>
                        <form action="{{ route('admin.api-sources.refresh', $api) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">Refresh</button>
                        </form>
                        <a href="{{ route('admin.api-sources.edit', $api) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Comments -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Komentar Terbaru</h5>
            <a href="{{ route('admin.comments.index') }}" class="btn btn-sm btn-link">Lihat Semua</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Berita</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentComments as $comment)
                <tr>
                    <td>{{ $comment->user->name }}</td>
                    <td>{{ Str::limit($comment->news->title, 30) }}</td>
                    <td>{{ Str::limit($comment->content, 50) }}</td>
                    <td>{{ $comment->created_at->format('d-M-Y') }}</td>
                    <td>
                        <span class="badge {{ $comment->status == 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $comment->status == 'approved' ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td>
                        @if($comment->status == 'pending')
                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-outline-success me-1">Approve</button>
                        </form>
                        @endif
                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada komentar terbaru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- User Management Preview -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Pengguna Terbaru</h5>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link">Lihat Semua</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Tipe</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->created_at->format('d-M-Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada pengguna terbaru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
