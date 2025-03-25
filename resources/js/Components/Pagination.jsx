import React from 'react';
import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    if (links.length <= 3) {
        return null;
    }

    return (
        <nav aria-label="Page navigation">
            <ul className="pagination">
                {links.map((link, key) => (
                    <li key={key} className={`page-item ${link.active ? 'active' : ''} ${link.url ? '' : 'disabled'}`}>
                        <Link
                            className="page-link"
                            href={link.url ?? '#'}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    </li>
                ))}
            </ul>
        </nav>
    );
}
