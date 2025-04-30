@extends('layouts.admin')

@section('header', 'Manajemen Berita')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Daftar Berita</h4>
    <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Berita
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Sumber</th>
                        <th>Tanggal Publikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ Str::limit($item->title, 40) }}</td>
                        <td>{{ $item->category->name }}</td>
                        <td>{{ $item->source }}</td>
                        <td>{{ $item->published_at->format('d-M-Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('news.show', $item) }}" class="btn btn-sm btn-outline-secondary me-1" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada berita yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $news->links() }}
        </div>
    </div>
</div>
@endsection
