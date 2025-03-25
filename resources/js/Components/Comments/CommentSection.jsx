import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';

export default function CommentSection({ articleId, comments = [], auth }) {
    const [content, setContent] = useState('');
    const [replyContent, setReplyContent] = useState('');
    const [replyingTo, setReplyingTo] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setIsSubmitting(true);

        router.post(route('comments.store', articleId), {
            content,
            parent_id: null
        }, {
            onSuccess: () => {
                setContent('');
                setIsSubmitting(false);
            },
            onError: () => setIsSubmitting(false)
        });
    };

    const handleReplySubmit = (e) => {
        e.preventDefault();
        setIsSubmitting(true);

        router.post(route('comments.store', articleId), {
            content: replyContent,
            parent_id: replyingTo
        }, {
            onSuccess: () => {
                setReplyContent('');
                setReplyingTo(null);
                setIsSubmitting(false);
            },
            onError: () => setIsSubmitting(false)
        });
    };

    return (
        <div className="comment-section mt-5">
            <h4 className="mb-4">Komentar ({comments.length})</h4>

            {auth.user ? (
                <div className="card mb-4">
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="mb-3">
                                <label htmlFor="comment" className="form-label">Tinggalkan komentar</label>
                                <textarea
                                    className="form-control"
                                    id="comment"
                                    rows="3"
                                    value={content}
                                    onChange={(e) => setContent(e.target.value)}
                                    required
                                ></textarea>
                            </div>
                            <button
                                type="submit"
                                className="btn btn-primary"
                                disabled={isSubmitting}
                            >
                                {isSubmitting ? 'Mengirim...' : 'Kirim Komentar'}
                            </button>
                        </form>
                    </div>
                </div>
            ) : (
                <div className="alert alert-info mb-4">
                    Silakan <Link href={route('login')}>login</Link> untuk memberikan komentar.
                </div>
            )}

            {comments.length > 0 ? (
                <div className="comments-container">
                    {comments.map(comment => (
                        <div className="comment mb-4 p-3 bg-light rounded" key={comment.id}>
                            <div className="d-flex">
                                <div className="flex-shrink-0">
                                    <div className="avatar bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style={{ width: "50px", height: "50px" }}>
                                        {comment.user.name.charAt(0).toUpperCase()}
                                    </div>
                                </div>
                                <div className="flex-grow-1 ms-3">
                                    <h6 className="mb-1">{comment.user.name}</h6>
                                    <p className="text-muted small mb-2">{new Date(comment.created_at).toLocaleString()}</p>
                                    <p>{comment.content}</p>

                                    {auth.user && (
                                        <button
                                            className="btn btn-sm btn-outline-secondary"
                                            onClick={() => setReplyingTo(comment.id)}
                                        >
                                            Balas
                                        </button>
                                    )}

                                    {replyingTo === comment.id && (
                                        <div className="mt-3">
                                            <form onSubmit={handleReplySubmit}>
                                                <div className="mb-3">
                                                    <textarea
                                                        className="form-control"
                                                        rows="2"
                                                        value={replyContent}
                                                        onChange={(e) => setReplyContent(e.target.value)}
                                                        required
                                                    ></textarea>
                                                </div>
                                                <div className="d-flex">
                                                    <button
                                                        type="submit"
                                                        className="btn btn-sm btn-primary me-2"
                                                        disabled={isSubmitting}
                                                    >
                                                        {isSubmitting ? 'Mengirim...' : 'Kirim Balasan'}
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm btn-outline-secondary"
                                                        onClick={() => setReplyingTo(null)}
                                                    >
                                                        Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    )}

                                    {comment.replies && comment.replies.length > 0 && (
                                        <div className="replies mt-3">
                                            {comment.replies.map(reply => (
                                                <div className="reply mt-3 ps-3 border-start" key={reply.id}>
                                                    <div className="d-flex">
                                                        <div className="flex-shrink-0">
                                                            <div className="avatar bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center" style={{ width: "40px", height: "40px" }}>
                                                                {reply.user.name.charAt(0).toUpperCase()}
                                                            </div>
                                                        </div>
                                                        <div className="flex-grow-1 ms-3">
                                                            <h6 className="mb-1">{reply.user.name}</h6>
                                                            <p className="text-muted small mb-2">{new Date(reply.created_at).toLocaleString()}</p>
                                                            <p>{reply.content}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-muted">Belum ada komentar untuk artikel ini.</p>
            )}
        </div>
    );
}
