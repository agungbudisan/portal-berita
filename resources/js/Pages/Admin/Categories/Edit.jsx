import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Edit({ category }) {
    const { data, setData, put, processing, errors } = useForm({
        name: category.name || '',
        description: category.description || '',
        display_order: category.display_order || '0',
        is_active: category.is_active
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('admin.categories.update', category.id));
    };

    return (
        <AdminLayout title="Edit Kategori">
            <Head title="Edit Kategori" />

            <div className="card">
                <div className="card-header">
                    <div className="d-flex justify-content-between align-items-center">
                        <span>Edit Kategori: {category.name}</span>
                        <Link href={route('admin.categories.index')} className="btn btn-sm btn-secondary">
                            Kembali
                        </Link>
                    </div>
                </div>
                <div className="card-body">
                    <form onSubmit={handleSubmit}>
                        <div className="mb-3">
                            <label htmlFor="name" className="form-label">Nama Kategori <span className="text-danger">*</span></label>
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
                            <label htmlFor="description" className="form-label">Deskripsi</label>
                            <textarea
                                className={`form-control ${errors.description ? 'is-invalid' : ''}`}
                                id="description"
                                rows="3"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                            ></textarea>
                            {errors.description && <div className="invalid-feedback">{errors.description}</div>}
                        </div>

                        <div className="mb-3">
                            <label htmlFor="display_order" className="form-label">Urutan Tampilan</label>
                            <input
                                type="number"
                                className={`form-control ${errors.display_order ? 'is-invalid' : ''}`}
                                id="display_order"
                                value={data.display_order}
                                onChange={(e) => setData('display_order', e.target.value)}
                            />
                            {errors.display_order && <div className="invalid-feedback">{errors.display_order}</div>}
                            <div className="form-text">Urutan kategori saat ditampilkan (angka kecil ditampilkan lebih dulu)</div>
                        </div>

                        <div className="mb-3 form-check">
                            <input
                                type="checkbox"
                                className="form-check-input"
                                id="is_active"
                                checked={data.is_active}
                                onChange={(e) => setData('is_active', e.target.checked)}
                            />
                            <label className="form-check-label" htmlFor="is_active">Aktif</label>
                        </div>

                        <div className="d-flex justify-content-end">
                            <button type="submit" className="btn btn-primary" disabled={processing}>
                                {processing ? 'Menyimpan...' : 'Update Kategori'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
