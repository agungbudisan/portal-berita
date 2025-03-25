// resources/js/Pages/Admin/Comments/Index.jsx
import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ comments, filters }) {
    const [searchQuery, setSearchQuery] = useState(filters?.search || '');
    const [selectedStatus, setSelectedStatus] = useState(filters?.status || '');

    const handleSearch = (e) => {
        e.preventDefault();

        router.get(route('admin.comments.index'), {
            search: searchQuery,
            status: selectedStatus
        }, {
            preserveState: true
        });
    };

    const handleApprove = (id) => {
        router.put(route('admin.comments.approve', id), {}, {
            preserveState: true
        });
    };

    const handleReject = (id) => {
        router.put(route('admin.comments.reject', id), {}, {
            preserveState: true
        });
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
            router.delete(route('admin.comments.destroy', id), {
                preserveState: true
            });
        }
    };

    return (
        <AdminLayout title="Moderasi Komentar">
            <Head title="Moderasi Komentar" />

            <div className="card mb-4">
                <div className="card-header">Filter Komentar</div>
                <div className="card-body">
                    <form onSubmit={handleSearch}>
                        <div className="row g-3">
                            <div className="col-md-6">
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Cari komentar..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                            </div>
                            <div className="col-md-4">
                                <select
                                    className="form-select"
                                    value={selectedStatus}
                                    onChange={(e) => setSelectedStatus(e.target.value)}
                                >
                                    <option value="">Semua Status</option>
                                    <option value="approved">Disetujui</option>
                                    <option value="pending">Menunggu Approval</option>
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

            <div className="card">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Artikel</th>
                                    <th>Komentar</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {comments.data && comments.data.length > 0 ? (
                                    comments.data.map(comment => (
                                        <tr key={comment.id}>
                                            <td>{comment.user.name}</td>
                                            <td>
                                                <Link href={route('articles.show', comment.saved_article_id)} target="_blank">
                                                    {comment.saved_article.title.length > 30
                                                        ? comment.saved_article.title.substring(0, 30) + '...'
                                                        : comment.saved_article.title}
                                                </Link>
                                            </td>
                                            <td style={{ maxWidth: '300px' }}>{comment.content}</td>
                                            <td>
                                                {comment.is_approved ? (
                                                    <span className="badge bg-success">Disetujui</span>
                                                ) : (
                                                    <span className="badge bg-warning text-dark">Menunggu</span>
                                                )}
                                            </td>
                                            <td>{new Date(comment.created_at).toLocaleDateString()}</td>
                                            <td>
                                                {!comment.is_approved && (
                                                    <button
                                                        className="btn btn-sm btn-success me-1"
                                                        onClick={() => handleApprove(comment.id)}
                                                    >
                                                        Approve
                                                    </button>
                                                )}
                                                {comment.is_approved && (
                                                    <button
                                                        className="btn btn-sm btn-warning me-1"
                                                        onClick={() => handleReject(comment.id)}
                                                    >
                                                        Reject
                                                    </button>
                                                )}
                                                <button
                                                    className="btn btn-sm btn-danger"
                                                    onClick={() => handleDelete(comment.id)}
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="6" className="text-center py-3">Tidak ada komentar</td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div className="card-footer">
                    <Pagination links={comments.links} />
                </div>
            </div>
        </AdminLayout>
    );
}
