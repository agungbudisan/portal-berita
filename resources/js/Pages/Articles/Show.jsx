import React from 'react';
import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import BookmarkButton from '@/Components/Bookmark/BookmarkButton';
import CommentSection from '@/Components/Comments/CommentSection';
import { Link } from '@inertiajs/react';

export default function Show({ article, related_articles, isBookmarked, auth }) {
    return (
        <MainLayout>
            <Head title={article.title} />

            <div className="row">
                <div className="col-lg-8">
                    <article>
                        <h1 className="mb-3">{article.title}</h1>

                        <div className="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span className="badge bg-primary me-2">
                                    {article.category?.name || 'Uncategorized'}
                                </span>
                                <small className="text-muted">
                                    {new Date(article.published_at).toLocaleDateString('id-ID', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                    })}
                                </small>
                            </div>

                            {auth.user && (
                                <BookmarkButton articleId={article.id} isBookmarked={isBookmarked} />
                            )}
                        </div>

                        {article.url_to_image && (
                            <div className="mb-4">
                                <img
                                    src={article.url_to_image}
                                    alt={article.title}
                                    className="img-fluid rounded w-100"
                                    style={{ maxHeight: '500px', objectFit: 'cover' }}
                                />
                                {article.source_name && (
                                    <small className="text-muted d-block text-end mt-1">
                                        Sumber: {article.source_name}
                                    </small>
                                )}
                            </div>
                        )}

                        {article.description && (
                            <div className="lead mb-4">
                                {article.description}
                            </div>
                        )}

                        <div className="article-content mb-5">
                            {article.content ? (
                                <div dangerouslySetInnerHTML={{ __html: article.content }} />
                            ) : (
                                <p>Baca artikel lengkap di sumber aslinya.</p>
                            )}

                            {article.url && (
                                <a
                                    href={article.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="btn btn-primary mt-3"
                                >
                                    Baca Artikel di Sumber Asli
                                </a>
                            )}
                        </div>

                        <CommentSection articleId={article.id} comments={article.comments || []} auth={auth} />
                    </article>
                </div>

                <div className="col-lg-4">
                    <div className="card mb-4">
                        <div className="card-header">Artikel Terkait</div>
                        <div className="card-body">
                            {related_articles && related_articles.length > 0 ? (
                                related_articles.map(related => (
                                    <div className="mb-3" key={related.id}>
                                        {related.url_to_image && (
                                            <img
                                                src={related.url_to_image}
                                                alt={related.title}
                                                className="img-fluid mb-2 rounded w-100"
                                                style={{ height: '120px', objectFit: 'cover' }}
                                            />
                                        )}
                                        <h6>
                                            <Link
                                                href={route('articles.show', related.id)}
                                                className="text-decoration-none"
                                            >
                                                {related.title}
                                            </Link>
                                        </h6>
                                        <p className="text-muted small mb-0">
                                            {new Date(related.published_at).toLocaleDateString()}
                                        </p>
                                    </div>
                                ))
                            ) : (
                                <p className="text-muted mb-0">Tidak ada artikel terkait.</p>
                            )}
                        </div>
                    </div>

                    <div className="card">
                        <div className="card-header">Bagikan Artikel</div>
                        <div className="card-body">
                            <div className="d-flex justify-content-around">
                                <a
                                    href={`https://www.facebook.com/sharer/sharer.php?u=${window.location.href}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="btn btn-primary"
                                >
                                    <i className="fab fa-facebook-f"></i>
                                </a>
                                <a
                                    href={`https://twitter.com/intent/tweet?url=${window.location.href}&text=${article.title}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="btn btn-info text-white"
                                >
                                    <i className="fab fa-twitter"></i>
                                </a>
                                <a
                                    href={`https://wa.me/?text=${article.title} ${window.location.href}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="btn btn-success"
                                >
                                    <i className="fab fa-whatsapp"></i>
                                </a>
                                <a
                                    href={`mailto:?subject=${article.title}&body=Check out this article: ${window.location.href}`}
                                    className="btn btn-secondary"
                                >
                                    <i className="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
