import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Index({ categories }) {
    return (
        <AdminLayout title="Manajemen Kategori">
            <Head title="Manajemen Kategori" />

            <div className="mb-4">
                <Link href={route('admin.categories.create')} className="btn btn-primary">
                    <i className="fas fa-plus me-2"></i> Tambah Kategori
                </Link>
            </div>

            <div className="card">
                <div className="card-header">Daftar Kategori</div>
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Slug</th>
                                    <th>Artikel</th>
                                    <th>Status</th>
                                    <th>Urutan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {categories.length > 0 ? (
                                    categories.map(category => (
                                        <tr key={category.id}>
                                            <td>{category.name}</td>
                                            <td>{category.slug}</td>
                                            <td>{category.saved_articles_count || 0}</td>
                                            <td>
                                                {category.is_active ? (
                                                    <span className="badge bg-success">Aktif</span>
                                                ) : (
                                                    <span className="badge bg-danger">Nonaktif</span>
                                                )}
                                            </td>
                                            <td>{category.display_order}</td>
                                            <td>
                                                <Link href={route('admin.categories.edit', category.id)} className="btn btn-sm btn-primary me-1">
                                                    Edit
                                                </Link>
                                                <Link
                                                    href={route('admin.categories.destroy', category.id)}
                                                    method="delete"
                                                    as="button"
                                                    className="btn btn-sm btn-danger"
                                                    onClick={(e) => {
                                                        if (!confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                                                            e.preventDefault();
                                                        }
                                                    }}
                                                >
                                                    Hapus
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="6" className="text-center py-3">Belum ada kategori</td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
