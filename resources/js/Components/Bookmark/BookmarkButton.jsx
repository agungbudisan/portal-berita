import React, { useState } from 'react';
import { router } from '@inertiajs/react';

export default function BookmarkButton({ articleId, isBookmarked = false }) {
    const [bookmarked, setBookmarked] = useState(isBookmarked);
    const [isLoading, setIsLoading] = useState(false);

    const toggleBookmark = () => {
        setIsLoading(true);
        if (bookmarked) {
            // Find bookmark ID and delete it
            router.delete(route('bookmarks.destroy', articleId), {
                preserveScroll: true,
                onSuccess: () => {
                    setBookmarked(false);
                    setIsLoading(false);
                },
                onError: () => setIsLoading(false)
            });
        } else {
            router.post(route('bookmarks.store', articleId), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    setBookmarked(true);
                    setIsLoading(false);
                },
                onError: () => setIsLoading(false)
            });
        }
    };

    return (
        <button
            className={`btn ${bookmarked ? 'btn-warning' : 'btn-outline-warning'}`}
            onClick={toggleBookmark}
            disabled={isLoading}
        >
            <i className={`fas ${bookmarked ? 'fa-bookmark' : 'fa-bookmark'}`}></i>
            {' '}
            {isLoading ? 'Loading...' : (bookmarked ? 'Tersimpan' : 'Simpan')}
        </button>
    );
}
