@extends('layouts.user-dashboard')

@section('title', 'Edit Profil')

@section('header', 'Pengaturan Profil')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Profile Update Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Informasi Profil</h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('user.profile.update') }}" class="mt-2">
                    @csrf
                    @method('patch')

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label fw-semibold">Nama</label>
                                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required autocomplete="username">

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Verification Status -->
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="col-12">
                                <div class="alert alert-warning d-flex align-items-center py-2" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        <span>Email Anda belum diverifikasi.</span>
                                        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-decoration-none">
                                                Klik di sini untuk mengirim ulang email verifikasi.
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Save Changes Button -->
                    <div class="d-flex justify-content-end mt-4">
                        @if (session('status') === 'profile-updated')
                            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                 class="text-success me-3 d-flex align-items-center">
                                <i class="bi bi-check-circle me-1"></i> Profil berhasil diperbarui.
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('password.update') }}" class="mt-2">
                    @csrf
                    @method('put')

                    <div class="row g-3">
                        <!-- Current Password -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                <div class="input-group">
                                    <input id="current_password" name="current_password" type="password"
                                        class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                        autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    @error('current_password', 'updatePassword')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <input id="password" name="password" type="password"
                                        class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                        autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    @error('password', 'updatePassword')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="form-control" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Password Button -->
                    <div class="d-flex justify-content-end mt-4">
                        @if (session('status') === 'password-updated')
                            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                 class="text-success me-3 d-flex align-items-center">
                                <i class="bi bi-check-circle me-1"></i> Password berhasil diperbarui.
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key me-1"></i> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- User Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div class="avatar mx-auto mb-3" style="width: 100px; height: 100px; background-color: #FF4B91; font-size: 2.5rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold">{{ $user->name }}</h5>
                <p class="text-muted">{{ $user->email }}</p>
                <div class="d-flex justify-content-center">
                    <span class="badge bg-primary">{{ $user->isAdmin() ? 'Administrator' : 'Member' }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between text-muted small">
                    <div>
                        <div>Bergabung Sejak</div>
                        <div class="fw-bold">{{ $user->created_at->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div>Status</div>
                        <div class="fw-bold text-success">Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account Card -->
        <div class="card shadow-sm border-danger-subtle">
            <div class="card-header bg-danger bg-opacity-10 text-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <h5 class="mb-0">Zona Berbahaya</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Hapus Akun</h6>
                <p class="small text-muted">Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.</p>

                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                    <i class="bi bi-trash me-1"></i> Hapus Akun
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus Akun</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                </div>
                <p>Apakah Anda yakin ingin menghapus akun Anda?</p>
                <p class="fw-bold">Tindakan ini tidak dapat dibatalkan dan semua data Anda akan dihapus secara permanen.</p>

                <form method="post" action="{{ route('user.profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('delete')

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="modal_password" name="password" type="password" class="form-control" placeholder="Masukkan password Anda untuk konfirmasi" required>

                        @error('password', 'userDeletion')
                            <div class="text-danger mt-1 small">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteAccountForm').submit()">
                    Hapus Akun Saya
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(id) {
        var input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }

    // Show modal if there are validation errors
    @if ($errors->userDeletion->any())
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        });
    @endif
</script>
@endpush
@endsection
