import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Pagination from '@/Components/Pagination';

export default function ReadingHistory({ history }) {
    return (
        <MainLayout title="Riwayat Bacaan">
            <Head title="Riwayat Bacaan" />

            {history.data.length > 0 ? (
                <>
                    <div className="list-group mb-4">
                        {history.data.map(item => (
                            <Link
                                key={item.id}
                                href={route('articles.show', item.saved_article.id)}
                                className="list-group-item list-group-item-action"
                            >
                                <div className="d-flex w-100 justify-content-between align-items-center">
                                    <div className="d-flex">
                                        <img
                                            src={item.saved_article.url_to_image || '/img/placeholder.jpg'}
                                            alt={item.saved_article.title}
                                            className="me-3 rounded"
                                            style={{ width: '100px', height: '70px', objectFit: 'cover' }}
                                        />
                                        <div>
                                            <h5 className="mb-1">{item.saved_article.title}</h5>
                                            <p className="mb-1 text-muted">
                                                <span className="badge bg-primary me-2">
                                                    {item.saved_article.category?.name || 'Uncategorized'}
                                                </span>
                                                <small>
                                                    Sumber: {item.saved_article.source_name || 'Tidak diketahui'}
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                    <small className="text-muted">
                                        Dibaca pada {new Date(item.read_at).toLocaleString()}
                                    </small>
                                </div>
                            </Link>
                        ))}
                    </div>

                    <div className="d-flex justify-content-center">
                        <Pagination links={history.links} />
                    </div>
                </>
            ) : (
                <div className="alert alert-info">
                    <p className="mb-0">Anda belum memiliki riwayat bacaan. Mulai baca artikel untuk melihat riwayat Anda!</p>
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
