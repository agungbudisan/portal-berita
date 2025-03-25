import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import ArticleCard from '@/Components/ArticleCard';

export default function Home({ featuredArticles, latestArticles, popularArticles }) {
    return (
        <MainLayout>
            <Head title="Home" />

            <div className="row">
                <div className="col-md-8">
                    <h4 className="border-bottom pb-2 mb-4">Berita Utama</h4>

                    {featuredArticles && featuredArticles.length > 0 ? (
                        <>
                            <div className="featured-article position-relative mb-4">
                                <img
                                    src={featuredArticles[0].url_to_image || '/img/placeholder.jpg'}
                                    alt={featuredArticles[0].title}
                                    className="w-100 rounded"
                                    style={{ height: '400px', objectFit: 'cover' }}
                                />
                                <div className="position-absolute bottom-0 start-0 w-100 p-4 text-white" style={{
                                    background: 'linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0))'
                                }}>
                                    <h3>{featuredArticles[0].title}</h3>
                                    <p>{featuredArticles[0].description}</p>
                                    <div className="d-flex justify-content-between align-items-center">
                                        <span className="badge bg-primary">
                                            {featuredArticles[0].category?.name || 'Uncategorized'}
                                        </span>
                                        <Link
                                            href={route('articles.show', featuredArticles[0].id)}
                                            className="btn btn-light btn-sm"
                                        >
                                            Baca Selengkapnya
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <div className="row g-4 mb-5">
                                {featuredArticles.slice(1).map(article => (
                                    <div className="col-md-6" key={article.id}>
                                        <ArticleCard article={article} />
                                    </div>
                                ))}
                            </div>
                        </>
                    ) : (
                        <div className="alert alert-info">Belum ada berita utama.</div>
                    )}

                    <h4 className="border-bottom pb-2 mb-4 mt-5">Berita Terbaru</h4>

                    <div className="row g-4">
                        {latestArticles && latestArticles.length > 0 ? (
                            latestArticles.map(article => (
                                <div className="col-md-4" key={article.id}>
                                    <ArticleCard article={article} />
                                </div>
                            ))
                        ) : (
                            <div className="col">
                                <div className="alert alert-info">Belum ada berita terbaru.</div>
                            </div>
                        )}
                    </div>
                </div>

                <div className="col-md-4">
                    <div className="card mb-4">
                        <div className="card-header">Berita Populer</div>
                        <div className="card-body p-0">
                            <ul className="list-group list-group-flush">
                                {popularArticles && popularArticles.length > 0 ? (
                                    popularArticles.map(article => (
                                        <li className="list-group-item py-3" key={article.id}>
                                            <div className="d-flex">
                                                <img
                                                    src={article.url_to_image || '/img/placeholder.jpg'}
                                                    alt={article.title}
                                                    className="me-3 rounded"
                                                    style={{ width: '60px', height: '60px', objectFit: 'cover' }}
                                                />
                                                <div>
                                                    <Link
                                                        href={route('articles.show', article.id)}
                                                        className="text-decoration-none fw-bold"
                                                    >
                                                        {article.title.length > 60 ? article.title.substring(0, 60) + '...' : article.title}
                                                    </Link>
                                                    <div className="d-flex justify-content-between align-items-center mt-1">
                                                        <small className="text-muted">
                                                            {new Date(article.published_at).toLocaleDateString()}
                                                        </small>
                                                        <span className="badge bg-secondary">{article.view_count} views</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    ))
                                ) : (
                                    <li className="list-group-item">Belum ada berita populer.</li>
                                )}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
