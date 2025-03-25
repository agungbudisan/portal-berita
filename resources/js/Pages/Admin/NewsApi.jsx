import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function NewsApi({ apiArticles = [], categories, filters, error }) {
    const [searchQuery, setSearchQuery] = useState(filters.q || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.category || '');
    const [isLoading, setIsLoading] = useState(false);

    const handleSearch = (e) => {
        e.preventDefault();
        setIsLoading(true);

        router.get(route('admin.news-api.index'), {
            q: searchQuery,
            category: selectedCategory,
            fetch: true
        }, {
            preserveState: true,
            onFinish: () => setIsLoading(false),
            onError: (errors) => {
                console.error('Error fetching news:', errors);
                setIsLoading(false);
            }
        });
    };

    const handleSaveArticle = (article) => {
        if (!article.title || !article.url || !selectedCategory) {
            alert('Judul, URL, dan kategori harus diisi.');
            return;
        }

        router.post(route('admin.news-api.save'), {
            article_id: article.source.id ? `${article.source.id}-${Date.now()}` : `newsapi-${Date.now()}`,
            title: article.title,
            description: article.description,
            url: article.url,
            url_to_image: article.urlToImage,
            source_name: article.source.name,
            published_at: article.publishedAt,
            content: article.content,
            category_id: selectedCategory || categories[0]?.id || null,
            is_published: true
        }, {
            onError: (errors) => {
                console.error('Error saving article:', errors);
            }
        });
    };

    return (
        <AdminLayout title="News API">
            <Head title="News API" />

            <div className="card mb-4">
                <div className="card-header">Cari Berita dari News API</div>
                <div className="card-body">
                    <form onSubmit={handleSearch}>
                        <div className="row mb-3">
                            <div className="col-md-5">
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Kata kunci pencarian..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                            </div>
                            <div className="col-md-5">
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
                            <div className="col-md-2">
                                <button type="submit" className="btn btn-primary w-100" disabled={isLoading}>
                                    {isLoading ? (
                                        <><span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...</>
                                    ) : (
                                        'Cari'
                                    )}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {error && (
                <div className="alert alert-danger mb-4">
                    <strong>Error:</strong> {error}
                    <p>
                        Pastikan API key telah dikonfigurasi dengan benar. Periksa file .env atau tabel api_configs.
                    </p>
                </div>
            )}

            {apiArticles.length > 0 ? (
                <div className="row">
                    {apiArticles.map((article, index) => (
                        <div className="col-md-4 mb-4" key={index}>
                            <div className="card h-100">
                                <img
                                    src={article.urlToImage || 'https://via.placeholder.com/300x200?text=No+Image'}
                                    className="card-img-top"
                                    alt={article.title}
                                    style={{ height: '200px', objectFit: 'cover' }}
                                />
                                <div className="card-body">
                                    <h5 className="card-title">{article.title}</h5>
                                    <p className="card-text text-muted mb-2">
                                        <small>
                                            {article.source.name} | {new Date(article.publishedAt).toLocaleDateString()}
                                        </small>
                                    </p>
                                    <p className="card-text">
                                        {article.description?.length > 150
                                            ? article.description.substring(0, 150) + '...'
                                            : article.description}
                                    </p>
                                </div>
                                <div className="card-footer d-flex justify-content-between">
                                    <a href={article.url} target="_blank" rel="noopener noreferrer" className="btn btn-sm btn-info">
                                        Lihat Sumber
                                    </a>
                                    <button
                                        className="btn btn-sm btn-success"
                                        onClick={() => handleSaveArticle(article)}
                                    >
                                        Simpan Artikel
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                !isLoading && !error && (
                    <div className="alert alert-info">
                        {filters.q || filters.category
                            ? 'Tidak ada artikel yang ditemukan. Coba kata kunci lain atau kategori yang berbeda.'
                            : 'Gunakan formulir pencarian di atas untuk menemukan berita dari News API.'}
                    </div>
                )
            )}
        </AdminLayout>
    );
}
