import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ articles, categories, filters }) {
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.category || '');
    const [selectedStatus, setSelectedStatus] = useState(filters.status || '');

    const handleSearch = (e) => {
        e.preventDefault();

        router.get(route('admin.saved-articles.index'), {
            search: searchQuery,
            category: selectedCategory,
            status: selectedStatus
        }, {
            preserveState: true
        });
    };

    const handleTogglePublish = (id, isPublished) => {
        router.put(route('admin.saved-articles.toggle-publish', id), {}, {
            preserveState: true
        });
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
            router.delete(route('admin.saved-articles.destroy', id), {
                preserveState: true
            });
        }
    };

    return (
        <AdminLayout title="Manajemen Artikel">
            <Head title="Manajemen Artikel" />

            <div className="card mb-4">
                <div className="card-header">Filter Artikel</div>
                <div className="card-body">
                    <form onSubmit={handleSearch}>
                        <div className="row g-3">
                            <div className="col-md-4">
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Cari berdasarkan judul..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                            </div>
                            <div className="col-md-3">
                                <select
                                    className="form-select"
                                    value={selectedCategory}
                                    onChange={(e) => setSelectedCategory(e.target.value)}
                                >
                                    <option value="">Semua Kategori</option>
                                    {categories.map(category => (
                                        <option key={category.id} value={category.id}>
                                            {category.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-3">
                                <select
                                    className="form-select"
                                    value={selectedStatus}
                                    onChange={(e) => setSelectedStatus(e.target.value)}
                                >
                                    <option value="">Semua Status</option>
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div className="col-md-2">
                                <button type="submit" className="btn btn-primary w-100">
                                    <i className="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div className="d-flex justify-content-between align-items-center mb-4">
                <h5>Total: {articles.total} artikel</h5>
                <Link href={route('admin.saved-articles.create')} className="btn btn-success">
                    <i className="fas fa-plus me-1"></i> Tambah Artikel
                </Link>
            </div>

            <div className="card">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Tanggal Publikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {articles.data.length > 0 ? (
                                    articles.data.map(article => (
                                        <tr key={article.id}>
                                            <td>
                                                <div style={{maxWidth: '300px'}}>
                                                    {article.title.length > 50 ? article.title.substring(0, 50) + '...' : article.title}
                                                </div>
                                            </td>
                                            <td>{article.category?.name || 'Uncategorized'}</td>
                                            <td>
                                                {article.is_published ? (
                                                    <span className="badge bg-success">Published</span>
                                                ) : (
                                                    <span className="badge bg-warning text-dark">Draft</span>
                                                )}
                                            </td>
                                            <td>{article.view_count}</td>
                                            <td>{new Date(article.published_at).toLocaleDateString()}</td>
                                            <td>
                                                <button
                                                    className={`btn btn-sm ${article.is_published ? 'btn-warning' : 'btn-success'} me-1`}
                                                    onClick={() => handleTogglePublish(article.id, article.is_published)}
                                                >
                                                    {article.is_published ? 'Unpublish' : 'Publish'}
                                                </button>
                                                <Link href={route('admin.saved-articles.edit', article.id)} className="btn btn-sm btn-primary me-1">
                                                    Edit
                                                </Link>
                                                <Link href={route('articles.show', article.id)} className="btn btn-sm btn-info me-1" target="_blank">
                                                    View
                                                </Link>
                                                <button
                                                    className="btn btn-sm btn-danger"
                                                    onClick={() => handleDelete(article.id)}
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="6" className="text-center py-3">Tidak ada artikel ditemukan</td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div className="card-footer">
                    <Pagination links={articles.links} />
                </div>
            </div>
        </AdminLayout>
    );
}
