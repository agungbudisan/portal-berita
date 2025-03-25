import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Profile({ auth, reading_history, comments }) {
    return (
        <MainLayout title="Profil Saya">
            <Head title="Profil Saya" />

            <div className="row">
                <div className="col-md-4">
                    <div className="card mb-4">
                        <div className="card-header">Informasi Profil</div>
                        <div className="card-body">
                            <div className="text-center mb-3">
                                <div className="avatar bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style={{ width: "100px", height: "100px", fontSize: "2.5rem" }}>
                                    {auth.user.name.charAt(0).toUpperCase()}
                                </div>
                                <h3>{auth.user.name}</h3>
                                <p className="text-muted">{auth.user.email}</p>
                            </div>

                            <div className="d-grid gap-2">
                                <Link href={route('user.profile.edit')} className="btn btn-primary">
                                    Edit Profil
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div className="card">
                        <div className="card-header">Menu</div>
                        <div className="list-group list-group-flush">
                            <Link href={route('bookmarks.index')} className="list-group-item list-group-item-action">
                                <i className="fas fa-bookmark me-2"></i> Bookmark Saya
                            </Link>
                            <Link href={route('user.reading-history')} className="list-group-item list-group-item-action">
                                <i className="fas fa-history me-2"></i> Riwayat Bacaan
                            </Link>
                            <Link href={route('user.comments')} className="list-group-item list-group-item-action">
                                <i className="fas fa-comments me-2"></i> Komentar Saya
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="col-md-8">
                    <div className="card mb-4">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <span>Riwayat Bacaan Terbaru</span>
                            <Link href={route('user.reading-history')} className="btn btn-sm btn-outline-primary">
                                Lihat Semua
                            </Link>
                        </div>
                        <div className="card-body p-0">
                            {reading_history && reading_history.length > 0 ? (
                                <div className="list-group list-group-flush">
                                    {reading_history.map(history => (
                                        <Link
                                            key={history.id}
                                            href={route('articles.show', history.saved_article.id)}
                                            className="list-group-item list-group-item-action"
                                        >
                                            <div className="d-flex w-100 justify-content-between">
                                                <h5 className="mb-1">{history.saved_article.title}</h5>
                                                <small className="text-muted">
                                                    {new Date(history.read_at).toLocaleDateString()}
                                                </small>
                                            </div>
                                            <p className="mb-1 text-muted">{history.saved_article.category?.name || 'Uncategorized'}</p>
                                        </Link>
                                    ))}
                                </div>
                            ) : (
                                <p className="p-3 m-0">Anda belum memiliki riwayat bacaan.</p>
                            )}
                        </div>
                    </div>

                    <div className="card">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <span>Komentar Terbaru</span>
                            <Link href={route('user.comments')} className="btn btn-sm btn-outline-primary">
                                Lihat Semua
                            </Link>
                        </div>
                        <div className="card-body p-0">
                            {comments && comments.length > 0 ? (
                                <div className="list-group list-group-flush">
                                    {comments.map(comment => (
                                        <div key={comment.id} className="list-group-item">
                                            <div className="d-flex w-100 justify-content-between">
                                                <h5 className="mb-1">
                                                    <Link href={route('articles.show', comment.saved_article.id)}>
                                                        {comment.saved_article.title}
                                                    </Link>
                                                </h5>
                                                <small className="text-muted">
                                                    {new Date(comment.created_at).toLocaleDateString()}
                                                </small>
                                            </div>
                                            <p className="mb-1">{comment.content}</p>
                                            <small className="text-muted d-block mt-1">
                                                Status: {comment.is_approved ?
                                                    <span className="text-success">Disetujui</span> :
                                                    <span className="text-warning">Menunggu Persetujuan</span>
                                                }
                                            </small>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="p-3 m-0">Anda belum membuat komentar.</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
