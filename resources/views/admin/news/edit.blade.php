@extends('layouts.admin')

@section('header', 'Edit Berita')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Judul Berita</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $news->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori</label>
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

            <div class="mb-3">
                <label for="content" class="form-label">Konten Berita</label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content', $news->content) }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="source" class="form-label">Sumber Berita</label>
                <input type="text" class="form-control @error('source') is-invalid @enderror" id="source" name="source" value="{{ old('source', $news->source) }}">
                @error('source')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Gambar</label>
                @if($news->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                <small class="text-muted">Unggah gambar baru untuk mengganti gambar saat ini (opsional)</small>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Berita</button>
            </div>
        </form>
    </div>
</div>
@endsection
