@extends('layouts.admin')

@section('header', 'Edit Pengguna')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-gear me-2 text-primary"></i>Edit Pengguna</h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }} mx-auto mb-3 d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px; border-radius: 50%; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} mt-2">
                        {{ $user->role }}
                    </span>

                    @if(auth()->id() === $user->id)
                    <div class="mt-2">
                        <span class="badge bg-info bg-opacity-10 text-info">
                            <i class="bi bi-person-check me-1"></i>Ini adalah akun Anda
                        </span>
                    </div>
                    @endif
                </div>

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield"></i></span>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                <option value="user" {{ (old('role', $user->role) === 'user') ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(auth()->id() === $user->id)
                            <div class="alert alert-warning mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Anda tidak dapat mengubah role akun Anda sendiri.
                            </div>
                            <input type="hidden" name="role" value="{{ $user->role }}">
                        @endif
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Biarkan kosong jika tidak ingin mengubah password.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <!-- User Info Card -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Informasi Pengguna
                            </h6>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted small">Bergabung sejak:</div>
                                        <strong>{{ $user->created_at->format('d F Y') }}</strong>
                                        <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="text-muted small">Aktivitas:</div>
                                        <div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary me-1">
                                                {{ $user->comments()->count() }} Komentar
                                            </span>
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $user->bookmarks()->count() ?? 0 }} Bookmark
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-2 mb-md-0">
                            @if(auth()->id() !== $user->id)
                            <button type="button" class="btn btn-outline-danger w-100" onclick="confirmDelete()">
                                <i class="bi bi-trash me-1"></i>Hapus Pengguna
                            </button>
                            @else
                            <button type="button" class="btn btn-outline-secondary w-100" disabled>
                                <i class="bi bi-shield-lock me-1"></i>Tidak dapat menghapus akun sendiri
                            </button>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-1"></i>Perbarui Pengguna
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Hidden Delete Form -->
                <form id="delete-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
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
                <p>Apakah Anda yakin ingin menghapus pengguna "<strong>{{ $user->name }}</strong>"?</p>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <strong>Perhatian!</strong> Tindakan ini akan:
                        <ul class="mb-0 mt-1">
                            <li>Menghapus semua data pengguna</li>
                            <li>Menghapus semua komentar pengguna ({{ $user->comments()->count() }} komentar)</li>
                            <li>Menghapus bookmark pengguna ({{ $user->bookmarks()->count() ?? 0 }} bookmark)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitDelete()">
                    <i class="bi bi-trash me-1"></i>Hapus Pengguna
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const togglePasswordBtn = document.getElementById('togglePassword');

    togglePasswordBtn.addEventListener('click', function() {
        // Toggle type for password
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        confirmPasswordInput.setAttribute('type', type);

        // Toggle icon
        togglePasswordBtn.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });
});

function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    deleteModal.show();
}

function submitDelete() {
    document.getElementById('delete-form').submit();
}
</script>
@endsection
