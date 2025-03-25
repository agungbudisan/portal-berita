import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Pagination from '@/Components/Pagination';

export default function Bookmarks({ bookmarks }) {
    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus bookmark ini?')) {
            router.delete(route('bookmarks.destroy', id), {
                preserveState: true
            });
        }
    };

    return (
        <MainLayout title="Bookmark Saya">
            <Head title="Bookmark Saya" />

            {bookmarks.data.length > 0 ? (
                <>
                    <div className="row">
                        {bookmarks.data.map(bookmark => (
                            <div className="col-md-6 col-lg-4 mb-4" key={bookmark.id}>
                                <div className="card h-100">
                                    <img
                                        src={bookmark.saved_article.url_to_image || '/img/placeholder.jpg'}
                                        className="card-img-top"
                                        alt={bookmark.saved_article.title}
                                        style={{ height: '180px', objectFit: 'cover' }}
                                    />
                                    <div className="card-body">
                                        <div className="d-flex justify-content-between align-items-start mb-2">
                                            <span className="badge bg-primary">
                                                {bookmark.saved_article.category?.name || 'Uncategorized'}
                                            </span>
                                            <small className="text-muted">
                                                {new Date(bookmark.saved_article.published_at).toLocaleDateString()}
                                            </small>
                                        </div>
                                        <h5 className="card-title">
                                            {bookmark.saved_article.title.length > 60
                                                ? bookmark.saved_article.title.substring(0, 60) + '...'
                                                : bookmark.saved_article.title}
                                        </h5>
                                        <p className="card-text">
                                            {bookmark.saved_article.description?.length > 100
                                                ? bookmark.saved_article.description.substring(0, 100) + '...'
                                                : bookmark.saved_article.description}
                                        </p>
                                    </div>
                                    <div className="card-footer bg-transparent d-flex justify-content-between">
                                        <Link
                                            href={route('articles.show', bookmark.saved_article.id)}
                                            className="btn btn-sm btn-primary"
                                        >
                                            Baca Artikel
                                        </Link>
                                        <button
                                            className="btn btn-sm btn-outline-danger"
                                            onClick={() => handleDelete(bookmark.id)}
                                        >
                                            Hapus Bookmark
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="d-flex justify-content-center mt-4">
                        <Pagination links={bookmarks.links} />
                    </div>
                </>
            ) : (
                <div className="alert alert-info">
                    <p className="mb-0">Anda belum memiliki bookmark. Mulai baca artikel dan tambahkan ke bookmark Anda!</p>
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
