@extends('layouts.admin')

@section('header', 'Edit Sumber API')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.api-sources.update', $apiSource) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nama API</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $apiSource->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="url" class="form-label">URL API</label>
                <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $apiSource->url) }}" required>
                <small class="text-muted">Contoh: https://newsapi.org/v2/top-headlines</small>
                @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="api_key" class="form-label">API Key</label>
                <input type="text" class="form-control @error('api_key') is-invalid @enderror" id="api_key" name="api_key" value="{{ old('api_key', $apiSource->api_key) }}">
                <small class="text-muted">Kosongkan jika tidak memerlukan API key</small>
                @error('api_key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="active" {{ (old('status', $apiSource->status) === 'active') ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ (old('status', $apiSource->status) === 'inactive') ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.api-sources.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Sumber API</button>
            </div>
        </form>
    </div>
</div>
@endsection
