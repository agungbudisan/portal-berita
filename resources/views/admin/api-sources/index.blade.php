@extends('layouts.admin')

@section('header', 'Manajemen API Berita')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Sumber API Berita</h4>
        <p class="text-muted mb-0">Kelola dan sinkronkan berita dari berbagai sumber API</p>
    </div>
    <a href="{{ route('admin.api-sources.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Sumber API
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Total API</h6>
                    <h3 class="mb-0">{{ $apiSources->count() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-hdd-network text-primary fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">API Aktif</h6>
                    <h3 class="mb-0">{{ $apiSources->where('status', 'active')->count() }}</h3>
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
                    <h6 class="text-muted">Total Berita API</h6>
                    <h3 class="mb-0">{{ $apiSources->sum('news_count') }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-newspaper text-info fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Terakhir Sync</h6>
                    <h3 class="mb-0">
                        @if($lastSync = $apiSources->max('last_sync'))
                            {{ \Carbon\Carbon::parse($lastSync)->diffForHumans() }}
                        @else
                            -
                        @endif
                    </h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-arrow-repeat text-warning fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Sumber API</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary me-2"
                        id="refreshAllBtn"
                        onclick="confirmRefreshAll()">
                    <i class="bi bi-arrow-repeat me-1"></i>Refresh Semua
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-toggle="collapse"
                        data-bs-target="#filterCollapse"
                        aria-expanded="false">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card-body border-bottom bg-light">
            <form action="{{ route('admin.api-sources.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama atau URL...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="sort">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="most_news" {{ request('sort') == 'most_news' ? 'selected' : '' }}>Terbanyak Berita</option>
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

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">API</th>
                        <th width="25%">URL</th>
                        <th width="10%">Status</th>
                        <th width="15%">Last Sync</th>
                        <th width="10%">Berita</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apiSources as $api)
                    <tr>
                        <td>{{ $api->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-hdd-network text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $api->name }}</h6>
                                    <small class="text-muted">{{ $api->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-link-45deg text-muted me-2"></i>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $api->url }}">
                                    {{ $api->url }}
                                    @if($api->api_key)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">
                                            <i class="bi bi-key-fill"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $api->status === 'active' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $api->status === 'active' ? 'success' : 'danger' }} px-3 py-2">
                                <i class="bi bi-{{ $api->status === 'active' ? 'check-circle' : 'x-circle' }}-fill me-1"></i>
                                {{ $api->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            @if($api->last_sync)
                                <div data-bs-toggle="tooltip" title="{{ $api->last_sync->format('d M Y, H:i:s') }}">
                                    <i class="bi bi-calendar-check text-success me-1"></i>
                                    {{ $api->last_sync->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-muted">
                                    <i class="bi bi-dash-circle me-1"></i>
                                    Belum Pernah
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                <i class="bi bi-newspaper me-1"></i>
                                {{ number_format($api->news_count ?? 0) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button"
                                        class="btn btn-sm btn-outline-success"
                                        onclick="refreshApi({{ $api->id }}, '{{ $api->name }}', event)"
                                        id="refreshBtn{{ $api->id }}">
                                    <i class="bi bi-arrow-repeat" id="refreshIcon{{ $api->id }}"></i>
                                </button>
                                <a href="{{ route('admin.api-sources.edit', $api) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $api->id }}, '{{ $api->name }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Form untuk delete (tersembunyi) -->
                            <form id="delete-form-{{ $api->id }}" action="{{ route('admin.api-sources.destroy', $api) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <!-- Form untuk refresh (tersembunyi) -->
                            <form id="refresh-form-{{ $api->id }}" action="{{ route('admin.api-sources.refresh', $api) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-hdd-network display-4 text-muted"></i>
                                <p class="mt-3 mb-0">Belum ada sumber API yang ditambahkan.</p>
                                <p class="text-muted">Tambahkan sumber API untuk mulai mengambil berita secara otomatis.</p>
                                <a href="{{ route('admin.api-sources.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Sumber API
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($apiSources->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-end">
            {{ $apiSources->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Konfirmasi Refresh All Modal -->
<div class="modal fade" id="refreshAllModal" tabindex="-1" aria-labelledby="refreshAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refreshAllModalLabel">Konfirmasi Refresh Semua API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyinkronkan semua API yang aktif? Proses ini mungkin membutuhkan waktu yang lama tergantung pada jumlah sumber API.</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <span>Hanya API dengan status <strong>Active</strong> yang akan disinkronkan.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.api-sources.refresh-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat me-1"></i>Refresh Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Konfirmasi Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus sumber API "<strong id="apiNameToDelete"></strong>"?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Tindakan ini tidak dapat dibatalkan dan semua berita yang diambil dari API ini mungkin tidak dapat diakses.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="submitDelete()">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Variables to track delete operation
let apiIdToDelete = null;

// Function to handle refresh API
function refreshApi(id, name, event) {
    event.preventDefault();

    // Show spinner
    const refreshBtn = document.getElementById(`refreshBtn${id}`);
    const refreshIcon = document.getElementById(`refreshIcon${id}`);

    refreshBtn.disabled = true;
    refreshIcon.className = 'spinner-border spinner-border-sm';

    // Submit the form
    document.getElementById(`refresh-form-${id}`).submit();
}

// Function to show refresh all confirmation modal
function confirmRefreshAll() {
    var refreshAllModal = new bootstrap.Modal(document.getElementById('refreshAllModal'));
    refreshAllModal.show();
}

// Function to show delete confirmation modal
function confirmDelete(id, name) {
    apiIdToDelete = id;
    document.getElementById('apiNameToDelete').textContent = name;

    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Function to submit delete form
function submitDelete() {
    if (apiIdToDelete) {
        document.getElementById(`delete-form-${apiIdToDelete}`).submit();
    }
}
</script>
@endsection
