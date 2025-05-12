@extends('layouts.admin')

@section('header', 'Manajemen Pengguna')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Pengguna</h4>
        <p class="text-muted">Kelola pengguna dan admin pada sistem</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('register') }}" class="btn btn-primary" target="_blank">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
    </div>
</div>

<!-- Filter Collapse -->
<div class="collapse mb-4" id="filterCollapse">
    <div class="card shadow-sm">
        <div class="card-body bg-light">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="role">
                        <option value="">Semua Role</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="sort">
                        <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
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
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Total Pengguna</h6>
                    <h3 class="mb-0">{{ $users->total() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people text-primary fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Admin</h6>
                    <h3 class="mb-0">{{ $adminCount ?? $users->where('role', 'admin')->count() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-shield-lock text-danger fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Pengguna</h6>
                    <h3 class="mb-0">{{ $userCount ?? $users->where('role', 'user')->count() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-person text-info fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">User Baru Bulan Ini</h6>
                    <h3 class="mb-0">{{ $newUsersThisMonth ?? 0 }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-plus text-success fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengguna</h5>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="25%">Pengguna</th>
                        <th width="25%">Email</th>
                        <th width="15%">Status</th>
                        <th width="15%">Bergabung</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} text-white d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-weight: 500;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    @if(auth()->id() === $user->id)
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-person-check me-1"></i>Anda
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-envelope text-muted me-2"></i>
                                {{ $user->email }}
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} bg-opacity-10 text-{{ $user->role === 'admin' ? 'danger' : 'primary' }} px-3 py-2">
                                <i class="bi {{ $user->role === 'admin' ? 'bi-shield-lock' : 'bi-person' }} me-1"></i>
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <div data-bs-toggle="tooltip" title="{{ $user->created_at->format('d M Y, H:i') }}">
                                {{ $user->created_at->format('d M Y') }}
                                <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <button type="button"
                                       class="btn btn-sm btn-outline-info"
                                       data-bs-toggle="modal"
                                       data-bs-target="#userStatsModal{{ $user->id }}"
                                       title="Statistik">
                                    <i class="bi bi-bar-chart"></i>
                                </button>

                                @if(auth()->id() !== $user->id)
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                        data-bs-toggle="tooltip"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </div>

                            <!-- User Stats Modal -->
                            <div class="modal fade" id="userStatsModal{{ $user->id }}" tabindex="-1" aria-labelledby="userStatsModalLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="userStatsModalLabel{{ $user->id }}">Statistik {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-4">
                                                <div class="col-6">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-chat-dots display-5 text-primary mb-2"></i>
                                                            <h5>{{ $user->comments_count ?? $user->comments()->count() }}</h5>
                                                            <p class="mb-0 text-muted">Komentar</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-bookmark display-5 text-primary mb-2"></i>
                                                            <h5>{{ $user->bookmarks_count ?? $user->bookmarks()->count() }}</h5>
                                                            <p class="mb-0 text-muted">Bookmark</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <h6>Informasi Pengguna</h6>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                        <span>Email</span>
                                                        <span class="text-muted">{{ $user->email }}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                        <span>Role</span>
                                                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">{{ $user->role }}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                        <span>Bergabung</span>
                                                        <span class="text-muted">{{ $user->created_at->format('d F Y') }}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                        <span>Terakhir Login</span>
                                                        <span class="text-muted">{{ $user->last_login_at ?? 'Tidak ada data' }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-people display-6 text-muted"></i>
                                <p class="mt-3 mb-0">Tidak ada pengguna yang tersedia.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
            </div>
            <div>
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengguna <strong id="userNameToDelete"></strong>?</p>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <strong>Perhatian!</strong> Tindakan ini akan:
                        <ul class="mb-0 mt-1">
                            <li>Menghapus semua data pengguna</li>
                            <li>Menghapus semua komentar pengguna</li>
                            <li>Menghapus bookmark pengguna</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="submitDelete()">
                    <i class="bi bi-trash me-1"></i>Hapus Pengguna
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// For delete confirmation
let userIdToDelete = null;

function confirmDelete(id, name) {
    userIdToDelete = id;
    document.getElementById('userNameToDelete').textContent = name;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    deleteModal.show();
}

function submitDelete() {
    if (userIdToDelete) {
        document.getElementById(`delete-form-${userIdToDelete}`).submit();
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
