@extends('layouts.admin')

@section('header', 'Edit Kategori')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Kategori</h5>
                <div>
                    <a href="{{ route('category.show', $category) }}" class="btn btn-sm btn-outline-info me-1" target="_blank">
                        <i class="bi bi-eye me-1"></i>Lihat
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="slug" class="form-label fw-bold">Slug</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link"></i></span>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="Auto-generated if empty">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i>Perhatian: Mengubah slug dapat memengaruhi URL yang sudah ada
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi singkat tentang kategori (opsional)">{{ old('description', $category->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category Info Card -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Informasi Kategori
                            </h6>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted small">Jumlah Berita:</div>
                                        <strong>{{ $category->news_count }} berita</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted small">Dibuat pada:</div>
                                        <strong>{{ $category->created_at->format('d F Y') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <button type="button" class="btn btn-outline-danger w-100"
                                    onclick="confirmDelete()"
                                    {{ $category->news_count > 0 ? 'disabled' : '' }}>
                                <i class="bi bi-trash me-1"></i>Hapus Kategori
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-1"></i>Perbarui Kategori
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Hidden Delete Form -->
                <form id="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
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
                <p>Apakah Anda yakin ingin menghapus kategori "<strong>{{ $category->name }}</strong>"?</p>

                @if($category->news_count > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Kategori ini memiliki <strong>{{ $category->news_count }}</strong> berita terkait. Hapus atau pindahkan berita tersebut terlebih dahulu.
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Kategori ini tidak memiliki berita terkait dan aman untuk dihapus.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitDelete()" {{ $category->news_count > 0 ? 'disabled' : '' }}>
                    <i class="bi bi-trash me-1"></i>Hapus Kategori
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    // Function to convert name to slug
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    // Update slug when name changes (only if slug is empty or matches the original slugified name)
    nameInput.addEventListener('input', function() {
        const currentSlug = slugInput.value;
        const originalSlugifiedName = slugify(nameInput.defaultValue);

        // Only auto-update if slug is empty or matches the original slugified name
        if (!currentSlug || currentSlug === originalSlugifiedName) {
            slugInput.value = slugify(this.value);
        }
    });
});

function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
    deleteModal.show();
}

function submitDelete() {
    document.getElementById('delete-form').submit();
}
</script>
@endsection
