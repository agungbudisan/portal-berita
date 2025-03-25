import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Edit({ user }) {
    const { data, setData, put, processing, errors } = useForm({
        name: user.name || '',
        email: user.email || '',
        role: user.role || 'user'
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('admin.users.update', user.id));
    };

    return (
        <AdminLayout title="Edit Pengguna">
            <Head title="Edit Pengguna" />

            <div className="card">
                <div className="card-header">
                    <div className="d-flex justify-content-between align-items-center">
                        <span>Edit Pengguna: {user.name}</span>
                        <Link href={route('admin.users.index')} className="btn btn-sm btn-secondary">
                            Kembali
                        </Link>
                    </div>
                </div>
                <div className="card-body">
                    <form onSubmit={handleSubmit}>
                        <div className="mb-3">
                            <label htmlFor="name" className="form-label">Nama <span className="text-danger">*</span></label>
                            <input
                                type="text"
                                className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                required
                            />
                            {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                        </div>

                        <div className="mb-3">
                            <label htmlFor="email" className="form-label">Email <span className="text-danger">*</span></label>
                            <input
                                type="email"
                                className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                id="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                            />
                            {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                        </div>

                        <div className="mb-3">
                            <label htmlFor="role" className="form-label">Role <span className="text-danger">*</span></label>
                            <select
                                className={`form-select ${errors.role ? 'is-invalid' : ''}`}
                                id="role"
                                value={data.role}
                                onChange={(e) => setData('role', e.target.value)}
                                required
                            >
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            {errors.role && <div className="invalid-feedback">{errors.role}</div>}
                        </div>

                        <div className="d-flex justify-content-end">
                            <button type="submit" className="btn btn-primary" disabled={processing}>
                                {processing ? 'Menyimpan...' : 'Update Pengguna'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
