@extends('layouts.admin')

@section('header', 'Dashboard Admin')

@section('content')
<!-- Stats dengan animasi dan improve visual -->
<div class="row mb-4">
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="stat-card h-100" style="border-left-color: #FF4B91;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Total Berita</h6>
                    <h3 class="mb-0">{{ $newsCount }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-newspaper text-primary fs-1 opacity-25"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success">+12% </span>
                <span class="text-muted small ms-2"> dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="stat-card h-100" style="border-left-color: #4169E1;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Total Pengguna</h6>
                    <h3 class="mb-0">{{ $userCount }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people text-info fs-1 opacity-25"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success">+8% </span>
                <span class="text-muted small ms-2"> dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="stat-card h-100" style="border-left-color: #FFC107;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Total Komentar</h6>
                    <h3 class="mb-0">{{ $commentCount }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-chat-dots text-warning fs-1 opacity-25"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success">+15% </span>
                <span class="text-muted small ms-2"> dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="stat-card h-100" style="border-left-color: #20C997;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Berita Hari Ini</h6>
                    <h3 class="mb-0">{{ $todayNewsCount }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-check text-success fs-1 opacity-25"></i>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center">
                @if($todayNewsCount > 0)
                <span class="badge bg-success bg-opacity-10 text-success">+{{ $todayNewsCount }} </span>
                <span class="text-muted small ms-2"> hari ini</span>
                @else
                <span class="badge bg-danger bg-opacity-10 text-danger">0 </span>
                <span class="text-muted small ms-2"> hari ini</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- API Status dengan card yang lebih menarik -->
<div class="card mb-4 shadow-sm" x-data="{ loading: {} }">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Status API</h5>
            <a href="{{ route('admin.api-sources.index') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Kelola API
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
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
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2 bg-light rounded p-2">
                                    <i class="bi bi-rss text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $api->name }}</h6>
                                    <small class="text-muted">{{ Str::limit($api->url, 30) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $api->status == 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                <i class="bi {{ $api->status == 'active' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                {{ $api->status == 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            @if($api->last_sync)
                                <span data-bs-toggle="tooltip" title="{{ $api->last_sync->format('d-M-Y H:i') }}">
                                    {{ $api->last_sync->diffForHumans() }}
                                </span>
                            @else
                                <span class="text-muted">Belum Pernah</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $api->news_count }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button @click="
                                    loading[{{ $api->id }}] = true;
                                    fetch('{{ route('admin.api-sources.refresh', $api) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            window.location.reload();
                                        } else {
                                            alert('Error: ' + data.message);
                                        }
                                    })
                                    .finally(() => {
                                        loading[{{ $api->id }}] = false;
                                    });
                                " class="btn btn-sm btn-outline-primary" x-bind:disabled="loading[{{ $api->id }}]">
                                    <span x-show="loading[{{ $api->id }}]">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </span>
                                    <span x-show="!loading[{{ $api->id }}]">
                                        <i class="bi bi-arrow-repeat me-1"></i>Refresh
                                    </span>
                                </button>
                                <a href="{{ route('admin.api-sources.edit', $api) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Comments dengan card yang lebih ditingkatkan -->
<div class="row">
    <div class="col-lg-7">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-chat-dots me-2 text-primary"></i>Komentar Terbaru</h5>
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Komentar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentComments as $comment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $comment->user->name }}</h6>
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="small text-muted mb-1" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                            <i class="bi bi-newspaper me-1"></i>{{ $comment->news->title }}
                                        </div>
                                        <div>{{ Str::limit($comment->content, 40) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $comment->status == 'approved' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                                        <i class="bi {{ $comment->status == 'approved' ? 'bi-check-circle-fill' : 'bi-hourglass-split' }} me-1"></i>
                                        {{ $comment->status == 'approved' ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($comment->status == 'pending')
                                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check-circle me-1"></i>Approve
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-chat-square-dots display-6 d-block mb-3 text-primary opacity-50"></i>
                                        <p>Tidak ada komentar terbaru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Preview dengan card yang lebih baik -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Pengguna Terbaru</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Info</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" style="background-color:
                                            {{ ['#FF4B91', '#4169E1', '#FFC107', '#20C997'][array_rand(['#FF4B91', '#4169E1', '#FFC107', '#20C997'])] }}">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'info' }} bg-opacity-10 text-{{ $user->role == 'admin' ? 'danger' : 'info' }}">
                                            {{ $user->role }}
                                        </span>
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-calendar-date me-1"></i>
                                            Bergabung {{ $user->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-people display-6 d-block mb-3 text-primary opacity-50"></i>
                                        <p>Tidak ada pengguna terbaru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inisialisasi tooltip -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
