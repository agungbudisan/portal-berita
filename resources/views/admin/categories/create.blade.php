@extends('layouts.admin')

@section('header', 'Tambah Kategori Baru')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Tambah Kategori</h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama kategori">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i>Slug akan dibuat secara otomatis dari nama kategori
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi singkat tentang kategori (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Preview</label>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center rounded me-3"
                                     id="categoryColorPreview"
                                     style="width: 36px; height: 36px; background-color: #FF4B91; color: white;">
                                    <i class="bi bi-tag-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0" id="categoryNamePreview">Nama Kategori</h6>
                                    <small class="text-muted" id="categorySlugPreview">nama-kategori</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-1"></i>Simpan Kategori
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const namePreview = document.getElementById('categoryNamePreview');
    const slugPreview = document.getElementById('categorySlugPreview');
    const colorPreview = document.getElementById('categoryColorPreview');

    // Function to convert name to slug
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    // Function to generate color from string
    function stringToColor(str) {
        if (!str || str.length === 0) return '#FF4B91';

        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }

        const colors = [
            '#FF4B91', '#4169E1', '#20C997', '#FFC107',
            '#6610f2', '#fd7e14', '#20c997', '#e83e8c'
        ];

        return colors[Math.abs(hash) % colors.length];
    }

    // Update preview when name changes
    nameInput.addEventListener('input', function() {
        const name = this.value.trim();
        namePreview.textContent = name || 'Nama Kategori';
        slugPreview.textContent = slugify(name) || 'nama-kategori';
        colorPreview.style.backgroundColor = stringToColor(name);
    });
});
</script>
@endsection
