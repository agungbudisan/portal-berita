import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Pagination from '@/Components/Pagination';

export default function Comments({ comments }) {
    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
            router.delete(route('comments.destroy', id), {
                preserveState: true
            });
        }
    };

    return (
        <MainLayout title="Komentar Saya">
            <Head title="Komentar Saya" />

            {comments.data.length > 0 ? (
                <>
                    <div className="card">
                        <div className="card-body p-0">
                            <div className="list-group list-group-flush">
                                {comments.data.map(comment => (
                                    <div key={comment.id} className="list-group-item">
                                        <div className="d-flex justify-content-between align-items-start mb-2">
                                            <h5 className="mb-0">
                                                <Link href={route('articles.show', comment.saved_article.id)}>
                                                    {comment.saved_article.title}
                                                </Link>
                                            </h5>
                                            <span className={`badge ${comment.is_approved ? 'bg-success' : 'bg-warning text-dark'}`}>
                                                {comment.is_approved ? 'Disetujui' : 'Menunggu Persetujuan'}
                                            </span>
                                        </div>
                                        <p className="mb-2">{comment.content}</p>
                                        <div className="d-flex justify-content-between align-items-center">
                                            <small className="text-muted">
                                                {new Date(comment.created_at).toLocaleString()}
                                            </small>
                                            <div>
                                                <Link
                                                    href={route('articles.show', comment.saved_article.id)}
                                                    className="btn btn-sm btn-outline-primary me-2"
                                                >
                                                    Lihat Artikel
                                                </Link>
                                                <button
                                                    className="btn btn-sm btn-outline-danger"
                                                    onClick={() => handleDelete(comment.id)}
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="d-flex justify-content-center mt-4">
                        <Pagination links={comments.links} />
                    </div>
                </>
            ) : (
                <div className="alert alert-info">
                    <p className="mb-0">Anda belum memberikan komentar pada artikel apapun.</p>
                    <div className="mt-3">
                        <Link href={route('articles.index')} className="btn btn-primary">
                            Jelajahi Artikel
                        </Link>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
