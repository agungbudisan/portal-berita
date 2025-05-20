@extends('layouts.admin')

@section('header', 'Tambah Berita Baru')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Tambah Berita</h5>
        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <label for="title" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul berita yang menarik">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="source" class="form-label">Sumber Berita</label>
                            <input type="text" class="form-control @error('source') is-invalid @enderror" id="source" name="source" value="{{ old('source', 'WinniNews') }}" placeholder="Masukkan sumber berita">
                            @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote @error('content') is-invalid @enderror" id="content" name="content" rows="12" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Gambar Utama</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3" id="image-preview-container">
                                <img src="{{ asset('images/placeholder.jpg') }}" alt="Preview" class="img-fluid rounded"
                                     id="image-preview" style="max-height: 200px; width: auto;">
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Pilih Gambar</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" onchange="previewImage()">
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Rekomendasi: Gambar dengan rasio 16:9, maksimal 2MB
                                </small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image_url" class="form-label">URL Gambar (opsional, untuk berita dari API)</label>
                                <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url') }}">
                                @error('image_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Publikasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusPublished" value="published" checked>
                                    <label class="form-check-label" for="statusPublished">
                                        <i class="bi bi-globe2 me-1 text-success"></i>Publikasikan langsung
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusDraft" value="draft">
                                    <label class="form-check-label" for="statusDraft">
                                        <i class="bi bi-save me-1 text-warning"></i>Simpan sebagai draft
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save2 me-2"></i>Simpan Berita
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script untuk preview gambar -->
<script>
    function previewImage() {
        const input = document.getElementById('image');
        const preview = document.getElementById('image-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Inisialisasi Summernote
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            placeholder: 'Tulis konten berita lengkap di sini...',
            callbacks: {
                onImageUpload: function(files) {
                    // Implementasi upload gambar ke server bisa ditambahkan di sini
                    for(let i = 0; i < files.length; i++) {
                        uploadImage(files[i], this);
                    }
                }
            }
        });
    });

    // Fungsi upload gambar (implementasi contoh)
    function uploadImage(file, editor) {
        let formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route('admin.upload.image') }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $(editor).summernote('insertImage', response.url);
            },
            error: function(error) {
                console.error('Error uploading image:', error);
                alert('Gagal mengunggah gambar. Silakan coba lagi.');
            }
        });
    }
</script>
@endsection
