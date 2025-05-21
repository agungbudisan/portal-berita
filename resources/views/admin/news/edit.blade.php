@extends('layouts.admin')

@section('header', 'Edit Berita')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Berita</h5>
        <div>
            <a href="{{ route('news.show', $news) }}" class="btn btn-sm btn-outline-info me-2" target="_blank">
                <i class="bi bi-eye me-1"></i>Lihat Berita
            </a>
            <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <label for="title" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $news->title) }}" required>
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
                                    <option value="{{ $category->id }}" {{ (old('category_id', $news->category_id) == $category->id) ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('source') is-invalid @enderror" id="source" name="source" value="{{ old('source', $news->source) }}">
                            @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote @error('content') is-invalid @enderror" id="content" name="content" rows="12" required>{{ old('content', $news->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Terakhir diperbarui:</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history me-2 text-muted"></i>
                            <span>{{ $news->updated_at->format('d F Y, H:i') }} ({{ $news->updated_at->diffForHumans() }})</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Gambar Utama</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div id="image-preview-container">
                                    <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="img-fluid rounded"
                                        id="image-preview" style="max-height: 200px; width: auto;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Ubah Gambar</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" onchange="previewImage()">
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Unggah gambar baru untuk mengganti yang ada (opsional)
                                </small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image_url" class="form-label">URL Gambar (opsional, untuk berita dari API)</label>
                                <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url', $news->image_url) }}">
                                @error('image_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Status Publikasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusPublished" value="published"
                                           {{ old('status', $news->status) == 'published' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusPublished">
                                        <i class="bi bi-globe2 me-1 text-success"></i>Dipublikasikan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusDraft" value="draft"
                                           {{ old('status', $news->status) == 'draft' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusDraft">
                                        <i class="bi bi-save me-1 text-warning"></i>Draft
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-save2 me-1"></i>Perbarui
                                </button>

                                <button type="button" class="btn btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteNewsModal">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($news->published_at)
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informasi Publikasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-check me-2 text-success"></i>
                                <div>
                                    <div class="small text-muted">Tanggal Publikasi</div>
                                    <div>{{ $news->published_at->format('d F Y, H:i') }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <i class="bi bi-eye me-2 text-primary"></i>
                                <div>
                                    <div class="small text-muted">Dilihat</div>
                                    <div>{{ $news->views_count ?? 0 }} kali</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-pencil-square me-2 text-warning"></i>
                            <div>
                                <div class="small text-muted">Tanggal Publikasi</div>
                                <div>Draft - Belum dipublikasikan</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteNewsModal" tabindex="-1" aria-labelledby="deleteNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteNewsModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus berita "<strong>{{ $news->title }}</strong>"?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.news.destroy', $news) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Berita</button>
                </form>
            </div>
        </div>
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
