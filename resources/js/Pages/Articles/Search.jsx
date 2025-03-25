import React from 'react';
import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import ArticleCard from '@/Components/ArticleCard';
import Pagination from '@/Components/Pagination';

export default function Search({ articles, search }) {
    return (
        <MainLayout title={`Hasil Pencarian: ${search}`}>
            <Head title={`Pencarian: ${search}`} />

            <div className="alert alert-info mb-4">
                Hasil pencarian untuk: <strong>{search}</strong>
            </div>

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
                    Tidak ada artikel yang ditemukan dengan kata kunci tersebut.
                </div>
            )}
        </MainLayout>
    );
}
