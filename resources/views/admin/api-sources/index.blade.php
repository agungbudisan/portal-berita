@extends('layouts.admin')

@section('header', 'Manajemen API Berita')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Daftar Sumber API</h4>
    <a href="{{ route('admin.api-sources.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Sumber API
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Last Sync</th>
                        <th>Jumlah Berita</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apiSources as $api)
                    <tr>
                        <td>{{ $api->id }}</td>
                        <td>{{ $api->name }}</td>
                        <td>{{ Str::limit($api->url, 30) }}</td>
                        <td>
                            <span class="badge {{ $api->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ $api->status }}
                            </span>
                        </td>
                        <td>{{ $api->last_sync ? $api->last_sync->format('d-M-Y H:i') : 'Belum Pernah' }}</td>
                        <td>{{ $api->news_count }}</td>
                        <td>
                            <a href="{{ route('admin.api-sources.edit', $api) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.api-sources.refresh', $api) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success me-1">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.api-sources.destroy', $api) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus sumber API ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada sumber API yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
