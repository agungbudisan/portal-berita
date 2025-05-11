@extends('layouts.admin')

@section('header', 'Tambah Sumber API')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Tambah Sumber API</h5>
                <a href="{{ route('admin.api-sources.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.api-sources.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Nama API <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama sumber API">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Contoh: NewsAPI, Guardian API, dll.</small>
                    </div>

                    <div class="mb-4">
                        <label for="url" class="form-label fw-bold">URL API <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" required placeholder="https://api.example.com/news">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>Masukkan URL lengkap termasuk protokol (https://)
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="api_key" class="form-label fw-bold">API Key</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="text" class="form-control @error('api_key') is-invalid @enderror" id="api_key" name="api_key" value="{{ old('api_key') }}" placeholder="Masukkan API key jika diperlukan">
                            <button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            <i class="bi bi-shield-lock me-1"></i>Kosongkan jika API tidak memerlukan API key
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="params" class="form-label fw-bold">Parameter Tambahan</label>
                        <div id="paramsContainer">
                            <div class="row mb-2 param-row">
                                <div class="col-5">
                                    <input type="text" class="form-control form-control-sm" name="param_keys[]" placeholder="Nama parameter">
                                </div>
                                <div class="col-5">
                                    <input type="text" class="form-control form-control-sm" name="param_values[]" placeholder="Nilai parameter">
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-param" style="display: none;"><i class="bi bi-dash-circle"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addParam">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Parameter
                        </button>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>Parameter tambahan akan ditambahkan ke URL saat mengambil data (contoh: country=us, category=business)
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" checked>
                            <label class="form-check-label" for="statusActive">
                                <i class="bi bi-check-circle me-1 text-success"></i>Active
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive">
                            <label class="form-check-label" for="statusInactive">
                                <i class="bi bi-x-circle me-1 text-danger"></i>Inactive
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            API inactive tidak akan diproses saat refresh otomatis
                        </small>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <a href="{{ route('admin.api-sources.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-1"></i>Simpan Sumber API
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
    // Toggle API Key visibility
    const apiKeyInput = document.getElementById('api_key');
    const toggleBtn = document.getElementById('toggleApiKey');

    toggleBtn.addEventListener('click', function() {
        const type = apiKeyInput.getAttribute('type') === 'password' ? 'text' : 'password';
        apiKeyInput.setAttribute('type', type);
        toggleBtn.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });

    // Set initial type to password
    apiKeyInput.setAttribute('type', 'password');

    // Add parameter functionality
    const addParamBtn = document.getElementById('addParam');
    const paramsContainer = document.getElementById('paramsContainer');

    addParamBtn.addEventListener('click', function() {
        const paramRow = document.createElement('div');
        paramRow.className = 'row mb-2 param-row';
        paramRow.innerHTML = `
            <div class="col-5">
                <input type="text" class="form-control form-control-sm" name="param_keys[]" placeholder="Nama parameter">
            </div>
            <div class="col-5">
                <input type="text" class="form-control form-control-sm" name="param_values[]" placeholder="Nilai parameter">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-param"><i class="bi bi-dash-circle"></i></button>
            </div>
        `;
        paramsContainer.appendChild(paramRow);

        // Show all remove buttons when we have more than one row
        const removeButtons = document.querySelectorAll('.remove-param');
        if (removeButtons.length > 1) {
            removeButtons.forEach(btn => btn.style.display = 'block');
        }

        // Add event listener to the new remove button
        paramRow.querySelector('.remove-param').addEventListener('click', function() {
            paramRow.remove();

            // Hide remove button on the last remaining row
            const remainingRemoveButtons = document.querySelectorAll('.remove-param');
            if (remainingRemoveButtons.length === 1) {
                remainingRemoveButtons[0].style.display = 'none';
            }
        });
    });
});
</script>
@endsection
