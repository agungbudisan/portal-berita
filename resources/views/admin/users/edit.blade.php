@extends('layouts.admin')

@section('header', 'Edit Pengguna')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="user" {{ (old('role', $user->role) === 'user') ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection
