import React from 'react';
import { Link } from '@inertiajs/react';

export default function ArticleCard({ article }) {
    return (
        <div className="card h-100 shadow-sm hover:shadow-md transition-shadow duration-200">
            <img
                src={article.url_to_image || '/img/placeholder.jpg'}
                className="card-img-top"
                alt={article.title}
                style={{ height: '180px', objectFit: 'cover' }}
            />
            <div className="card-body d-flex flex-column">
                <div className="d-flex justify-content-between align-items-center mb-2">
                    <span className="badge bg-primary">
                        {article.category?.name || 'Uncategorized'}
                    </span>
                    <small className="text-muted">
                        {new Date(article.published_at).toLocaleDateString('id-ID')}
                    </small>
                </div>
                <h5 className="card-title">{article.title}</h5>
                <p className="card-text flex-grow-1">
                    {article.description && article.description.length > 100
                        ? article.description.substring(0, 100) + '...'
                        : article.description}
                </p>
                <Link
                    href={route('articles.show', article.id)}
                    className="btn btn-sm btn-outline-primary mt-2"
                >
                    Baca Selengkapnya
                </Link>
            </div>
        </div>
    );
}
