@extends('layouts.admin')

@section('header', 'Manajemen Berita')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Berita</h4>
        <p class="text-muted">Kelola semua berita pada platform WinniNews</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Berita
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
    </div>
</div>

<!-- Filter dan Pencarian -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.news.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari judul berita...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="category">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Terpublikasi</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
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

<!-- Status Info -->
@if(request('search') || request('category') || request('status'))
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-info-circle me-2"></i>
        <div>
            Menampilkan hasil untuk
            @if(request('search'))
                pencarian "<strong>{{ request('search') }}</strong>"
            @endif
            @if(request('category'))
                @if(request('search')) dan @endif
                kategori "<strong>{{ $categories->find(request('category'))->name }}</strong>"
            @endif
            @if(request('status'))
                @if(request('search') || request('category')) dan @endif
                status "<strong>{{ request('status') == 'published' ? 'Terpublikasi' : 'Draft' }}</strong>"
            @endif
            <a href="{{ route('admin.news.index') }}" class="ms-2 text-primary">
                <i class="bi bi-x-circle"></i> Hapus filter
            </a>
        </div>
    </div>
</div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="45%">Berita</th>
                        <th width="15%">Kategori</th>
                        <th width="15%">Status</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            <div class="d-flex">
                                <div class="me-3">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                        class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $item->title }}</h6>
                                    <div class="d-flex align-items-center small text-muted">
                                        <span class="me-2">
                                            <i class="bi bi-calendar me-1"></i>
                                            @if($item->published_at)
                                                {{ $item->published_at->format('d M Y') }}
                                            @else
                                                <span class="text-secondary">Draft</span>
                                            @endif
                                        </span>
                                        <span class="me-2">
                                            <i class="bi bi-eye me-1"></i>{{ $item->views_count ?? 0 }}
                                        </span>
                                        <span class="me-2">
                                            <i class="bi bi-chat-dots me-1"></i>{{ $item->comments_count ?? 0 }}
                                        </span>
                                    </div>
                                    @if($item->source && $item->source != 'WinniNews')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary small">
                                        <i class="bi bi-link-45deg me-1"></i>{{ $item->source }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info">
                                <i class="bi bi-tag me-1"></i>{{ $item->category->name }}
                            </span>
                        </td>
                        <td>
                            @if($item->status == 'published')
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="bi bi-globe2 me-1"></i>Terpublikasi
                            </span>
                            @else
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-pencil-square me-1"></i>Draft
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('news.show', $item) }}" class="btn btn-sm btn-outline-info" target="_blank" data-bs-toggle="tooltip" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}" data-bs-toggle="tooltip" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Modal Hapus untuk setiap item -->
                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus berita "<strong>{{ $item->title }}</strong>"?</p>
                                            <p class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Tindakan ini tidak dapat dibatalkan.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('admin.news.destroy', $item) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="py-5">
                                <i class="bi bi-newspaper display-1 text-muted"></i>
                                <p class="mt-3 mb-0">Tidak ada berita yang tersedia.</p>
                                @if(request('search') || request('category') || request('status'))
                                <p class="text-muted">Coba ubah filter pencarian Anda.</p>
                                <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    Tampilkan Semua Berita
                                </a>
                                @else
                                <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Berita Baru
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

    @if($news->count() > 0)
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan {{ $news->firstItem() ?? 0 }} - {{ $news->lastItem() ?? 0 }} dari {{ $news->total() }} berita
            </div>
            <div>
                {{ $news->withQueryString()->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Lanjutan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.news.index') }}" method="GET" id="advancedFilterForm">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Publikasi</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">Dari</span>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">Sampai</span>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Tampilan</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">Min</span>
                                    <input type="number" class="form-control" name="views_min" value="{{ request('views_min') }}" min="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">Max</span>
                                    <input type="number" class="form-control" name="views_max" value="{{ request('views_max') }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sumber</label>
                        <input type="text" class="form-control" name="source" value="{{ request('source') }}" placeholder="Cari berdasarkan sumber...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Urutkan Berdasarkan</label>
                        <select class="form-select" name="sort_by">
                            <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="most_viewed" {{ request('sort_by') == 'most_viewed' ? 'selected' : '' }}>Paling Banyak Dilihat</option>
                            <option value="most_commented" {{ request('sort_by') == 'most_commented' ? 'selected' : '' }}>Paling Banyak Dikomentari</option>
                            <option value="title_asc" {{ request('sort_by') == 'title_asc' ? 'selected' : '' }}>Judul (A-Z)</option>
                            <option value="title_desc" {{ request('sort_by') == 'title_desc' ? 'selected' : '' }}>Judul (Z-A)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.news.index') }}" class="btn btn-link text-secondary">Reset Filter</a>
                <button type="submit" form="advancedFilterForm" class="btn btn-primary">Terapkan Filter</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
