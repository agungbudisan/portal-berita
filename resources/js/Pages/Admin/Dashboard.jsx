import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Dashboard({ stats, recent_articles, popular_articles }) {
    return (
        <AdminLayout title="Dashboard Admin">
            <Head title="Dashboard Admin" />

            <div className="row g-4 mb-5">
                <div className="col-md-3">
                    <div className="card bg-primary text-white">
                        <div className="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 className="mb-0">Total Artikel</h6>
                                <h2 className="mb-0">{stats.articles}</h2>
                            </div>
                            <div>
                                <i className="fas fa-newspaper fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3">
                    <div className="card bg-success text-white">
                        <div className="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 className="mb-0">Artikel Terpublikasi</h6>
                                <h2 className="mb-0">{stats.published_articles}</h2>
                            </div>
                            <div>
                                <i className="fas fa-check-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3">
                    <div className="card bg-info text-white">
                        <div className="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 className="mb-0">Total Pengguna</h6>
                                <h2 className="mb-0">{stats.users}</h2>
                            </div>
                            <div>
                                <i className="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3">
                    <div className="card bg-warning text-dark">
                        <div className="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 className="mb-0">Komentar Menunggu</h6>
                                <h2 className="mb-0">{stats.pending_comments}</h2>
                            </div>
                            <div>
                                <i className="fas fa-comments fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="row">
                <div className="col-lg-8">
                    <div className="card mb-4">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <span>Artikel Terbaru</span>
                            <Link href={route('admin.saved-articles.index')} className="btn btn-sm btn-primary">
                                Lihat Semua
                            </Link>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Status</th>
                                            <th>Views</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {recent_articles && recent_articles.length > 0 ? (
                                            recent_articles.map(article => (
                                                <tr key={article.id}>
                                                    <td>{article.title.length > 40 ? article.title.substring(0, 40) + '...' : article.title}</td>
                                                    <td>{article.category?.name || 'Uncategorized'}</td>
                                                    <td>
                                                        {article.is_published ? (
                                                            <span className="badge bg-success">Published</span>
                                                        ) : (
                                                            <span className="badge bg-warning">Draft</span>
                                                        )}
                                                    </td>
                                                    <td>{article.view_count}</td>
                                                    <td>
                                                        <Link href={route('admin.saved-articles.edit', article.id)} className="btn btn-sm btn-primary me-1">
                                                            Edit
                                                        </Link>
                                                        <Link href={route('articles.show', article.id)} className="btn btn-sm btn-info" target="_blank">
                                                            View
                                                        </Link>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="5" className="text-center py-3">Tidak ada artikel</td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-lg-4">
                    <div className="card">
                        <div className="card-header">Artikel Populer</div>
                        <div className="card-body p-0">
                            <div className="list-group list-group-flush">
                                {popular_articles && popular_articles.length > 0 ? (
                                    popular_articles.map(article => (
                                        <Link
                                            key={article.id}
                                            href={route('articles.show', article.id)}
                                            className="list-group-item list-group-item-action"
                                            target="_blank"
                                        >
                                            <div className="d-flex w-100 justify-content-between">
                                                <h6 className="mb-1">{article.title.length > 40 ? article.title.substring(0, 40) + '...' : article.title}</h6>
                                                <span className="badge bg-info">{article.view_count}</span>
                                            </div>
                                            <small className="text-muted">{article.category?.name || 'Uncategorized'}</small>
                                        </Link>
                                    ))
                                ) : (
                                    <div className="p-3">Belum ada artikel populer</div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
