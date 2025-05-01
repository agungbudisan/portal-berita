@extends('layouts.admin')

@section('header', 'Manajemen Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Daftar Kategori</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Kategori
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
                        <th>Slug</th>
                        <th>Jumlah Berita</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->news_count }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('category.show', $category) }}" class="btn btn-sm btn-outline-secondary me-1" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada kategori yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
