@extends('layouts.admin')

@section('header', 'Manajemen Kategori')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Kategori</h4>
        <p class="text-muted">Kelola kategori untuk berita dan artikel</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
    </div>
</div>

<!-- Filter Collapse -->
<div class="collapse mb-4" id="filterCollapse">
    <div class="card shadow-sm">
        <div class="card-body bg-light">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="sort">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                        <option value="most_used" {{ request('sort') == 'most_used' ? 'selected' : '' }}>Paling Banyak Digunakan</option>
                        <option value="least_used" {{ request('sort') == 'least_used' ? 'selected' : '' }}>Paling Sedikit Digunakan</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">Min. Berita</span>
                        <input type="number" class="form-control" name="min_news" value="{{ request('min_news') }}" min="0" placeholder="0">
                    </div>
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
                    <h6 class="text-muted">Total Kategori</h6>
                    <h3 class="mb-0">{{ $categories->total() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-tag text-primary fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Kategori Aktif</h6>
                    <h3 class="mb-0">{{ $categories->where('news_count', '>', 0)->count() }}</h3>
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
                    <h6 class="text-muted">Kategori Kosong</h6>
                    <h3 class="mb-0">{{ $categories->where('news_count', 0)->count() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-x-circle text-danger fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Kategori Terpopuler</h6>
                    <h3 class="mb-0">
                        @if($mostUsedCategory = $categories->sortByDesc('news_count')->first())
                            {{ Str::limit($mostUsedCategory->name, 15) }}
                        @else
                            -
                        @endif
                    </h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-trophy text-warning fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kategori</h5>
            <div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tambah
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="25%">Kategori</th>
                        <th width="25%">Slug</th>
                        <th width="15%">Berita</th>
                        <th width="15%">Dibuat</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @php
                                    $colors = [
                                        '#FF4B91', '#4169E1', '#20C997', '#FFC107',
                                        '#6610f2', '#fd7e14', '#20c997', '#e83e8c'
                                    ];

                                    $colorIndex = ($category->id - 1) % count($colors);
                                    $categoryColor = $colors[$colorIndex >= 0 ? $colorIndex : 0];
                                @endphp

                                <div class="d-flex align-items-center justify-content-center rounded me-3"
                                     style="width: 36px; height: 36px; background-color: {{ $categoryColor }}; color: white;">
                                    <i class="bi bi-tag-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $category->name }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="bg-light text-dark p-1 rounded small">{{ $category->slug }}</code>
                        </td>
                        <td>
                            <a href="{{ route('admin.news.index', ['category' => $category->id]) }}" class="badge bg-{{ $category->news_count > 0 ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ $category->news_count > 0 ? 'primary' : 'secondary' }} text-decoration-none px-3 py-2">
                                <i class="bi bi-newspaper me-1"></i>
                                {{ number_format($category->news_count) }}
                            </a>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $category->created_at->format('d M Y') }}
                                <span class="d-block">{{ $category->created_at->diffForHumans() }}</span>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('category.show', $category) }}" class="btn btn-sm btn-outline-info" target="_blank" data-bs-toggle="tooltip" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}', {{ $category->news_count }})"
                                        data-bs-toggle="tooltip"
                                        title="Hapus"
                                        {{ $category->news_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div>
                                <i class="bi bi-tag display-6 text-muted"></i>
                                <p class="mt-3 mb-0">Tidak ada kategori yang tersedia.</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Kategori Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-end">
            {{ $categories->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="categoryNameToDelete"></strong>?</p>
                <div id="categoryHasNews" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Kategori ini memiliki <strong id="categoryNewsCount"></strong> berita terkait. Hapus atau pindahkan berita tersebut terlebih dahulu.
                </div>
                <div id="categoryEmpty" class="alert alert-info d-none">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Kategori ini tidak memiliki berita terkait dan aman untuk dihapus.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="submitDelete()">
                    <i class="bi bi-trash me-1"></i>Hapus Kategori
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// For delete confirmation
let categoryIdToDelete = null;

function confirmDelete(id, name, newsCount) {
    categoryIdToDelete = id;
    document.getElementById('categoryNameToDelete').textContent = name;

    const hasNewsAlert = document.getElementById('categoryHasNews');
    const emptyAlert = document.getElementById('categoryEmpty');
    const deleteBtn = document.getElementById('confirmDeleteBtn');

    if (newsCount > 0) {
        hasNewsAlert.classList.remove('d-none');
        emptyAlert.classList.add('d-none');
        deleteBtn.disabled = true;
        document.getElementById('categoryNewsCount').textContent = newsCount;
    } else {
        hasNewsAlert.classList.add('d-none');
        emptyAlert.classList.remove('d-none');
        deleteBtn.disabled = false;
    }

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
    deleteModal.show();
}

function submitDelete() {
    if (categoryIdToDelete) {
        document.getElementById(`delete-form-${categoryIdToDelete}`).submit();
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
