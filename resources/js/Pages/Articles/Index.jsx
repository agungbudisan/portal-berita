import React from 'react';
import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import ArticleCard from '@/Components/ArticleCard';
import Pagination from '@/Components/Pagination';

export default function Index({ articles, filters }) {
    return (
        <MainLayout title="Semua Artikel">
            <Head title="Artikel" />

            {filters.search && (
                <div className="alert alert-info mb-4">
                    Hasil pencarian untuk: <strong>{filters.search}</strong>
                </div>
            )}

            {filters.category && (
                <div className="alert alert-info mb-4">
                    Kategori: <strong>{filters.category}</strong>
                </div>
            )}

            {articles.data.length > 0 ? (
                <>
                    <div className="row g-4">
                        {articles.data.map(article => (
                            <div className="col-md-4 col-lg-3" key={article.id}>
                                <ArticleCard article={article} />
                            </div>
                        ))}
                    </div>

                    <div className="mt-4 d-flex justify-content-center">
                        <Pagination links={articles.links} />
                    </div>
                </>
            ) : (
                <div className="alert alert-warning">
                    Tidak ada artikel yang ditemukan.
                </div>
            )}
        </MainLayout>
    );
}
