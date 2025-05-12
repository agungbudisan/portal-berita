@extends('layouts.admin')

@section('header', 'Edit Sumber API')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Sumber API</h5>
                <div>
                    <form action="{{ route('admin.api-sources.refresh', $apiSource) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success me-1">
                            <i class="bi bi-arrow-repeat me-1"></i>Refresh Data
                        </button>
                    </form>
                    <a href="{{ route('admin.api-sources.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.api-sources.update', $apiSource) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Nama API <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $apiSource->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="url" class="form-label fw-bold">URL API <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $apiSource->url) }}" required>
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
                            <input type="text" class="form-control @error('api_key') is-invalid @enderror" id="api_key" name="api_key" value="{{ old('api_key', $apiSource->api_key) }}">
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
                            @if($apiSource->params && count($apiSource->params))
                                @foreach($apiSource->params as $key => $value)
                                <div class="row mb-2 param-row">
                                    <div class="col-5">
                                        <input type="text" class="form-control form-control-sm" name="param_keys[]" value="{{ $key }}" placeholder="Nama parameter">
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control form-control-sm" name="param_values[]" value="{{ $value }}" placeholder="Nilai parameter">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-param"><i class="bi bi-dash-circle"></i></button>
                                    </div>
                                </div>
                                @endforeach
                            @else
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
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addParam">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Parameter
                        </button>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>Parameter tambahan akan ditambahkan ke URL saat mengambil data
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" {{ (old('status', $apiSource->status) === 'active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusActive">
                                <i class="bi bi-check-circle me-1 text-success"></i>Active
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" {{ (old('status', $apiSource->status) === 'inactive') ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusInactive">
                                <i class="bi bi-x-circle me-1 text-danger"></i>Inactive
                            </label>
                        </div>
                    </div>

                    <!-- API Info -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Informasi API
                            </h6>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted small">Terakhir disinkronkan:</div>
                                        <strong>
                                            @if($apiSource->last_sync)
                                                {{ $apiSource->last_sync->format('d F Y, H:i') }}
                                                <span class="text-muted">({{ $apiSource->last_sync->diffForHumans() }})</span>
                                            @else
                                                <span class="text-muted">Belum pernah</span>
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted small">Jumlah berita yang diambil:</div>
                                        <strong>{{ $apiSource->news_count ?? 0 }} berita</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteApiModal">
                                <i class="bi bi-trash me-1"></i>Hapus Sumber API
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-1"></i>Perbarui Sumber API
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteApiModal" tabindex="-1" aria-labelledby="deleteApiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteApiModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus sumber API "<strong>{{ $apiSource->name }}</strong>"?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Tindakan ini tidak dapat dibatalkan dan semua berita yang diambil dari API ini mungkin tidak dapat diakses.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.api-sources.destroy', $apiSource) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Sumber API</button>
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

    // Initialize remove buttons visibility
    const initialRemoveButtons = document.querySelectorAll('.remove-param');
    if (initialRemoveButtons.length > 1) {
        initialRemoveButtons.forEach(btn => btn.style.display = 'block');
    } else if (initialRemoveButtons.length === 1) {
        initialRemoveButtons[0].style.display = 'none';
    }

    // Add event listeners to existing remove buttons
    initialRemoveButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            btn.closest('.param-row').remove();

            // Update remove buttons visibility
            const remainingRemoveButtons = document.querySelectorAll('.remove-param');
            if (remainingRemoveButtons.length === 1) {
                remainingRemoveButtons[0].style.display = 'none';
            } else {
                remainingRemoveButtons.forEach(btn => btn.style.display = 'block');
            }
        });
    });

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
