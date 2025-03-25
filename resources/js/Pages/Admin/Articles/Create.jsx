import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Create({ categories }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        content: '',
        url: '',
        url_to_image: '',
        source_name: '',
        category_id: '',
        is_published: true
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('admin.saved-articles.store'));
    };

    return (
        <AdminLayout title="Tambah Artikel">
            <Head title="Tambah Artikel" />

            <div className="card">
                <div className="card-header">
                    <div className="d-flex justify-content-between align-items-center">
                        <span>Form Artikel</span>
                        <Link href={route('admin.saved-articles.index')} className="btn btn-sm btn-secondary">
                            Kembali
                        </Link>
                    </div>
                </div>
                <div className="card-body">
                    <form onSubmit={handleSubmit}>
                        <div className="mb-3">
                            <label htmlFor="title" className="form-label">Judul Artikel <span className="text-danger">*</span></label>
                            <input
                                type="text"
                                className={`form-control ${errors.title ? 'is-invalid' : ''}`}
                                id="title"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                required
                            />
                            {errors.title && <div className="invalid-feedback">{errors.title}</div>}
                        </div>

                        <div className="mb-3">
                            <label htmlFor="description" className="form-label">Deskripsi</label>
                            <textarea
                                className={`form-control ${errors.description ? 'is-invalid' : ''}`}
                                id="description"
                                rows="3"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                            ></textarea>
                            {errors.description && <div className="invalid-feedback">{errors.description}</div>}
                        </div>

                        <div className="mb-3">
                            <label htmlFor="content" className="form-label">Konten</label>
                            <textarea
                                className={`form-control ${errors.content ? 'is-invalid' : ''}`}
                                id="content"
                                rows="6"
                                value={data.content}
                                onChange={(e) => setData('content', e.target.value)}
                            ></textarea>
                            {errors.content && <div className="invalid-feedback">{errors.content}</div>}
                            <div className="form-text">HTML diperbolehkan untuk format konten</div>
                        </div>

                        <div className="mb-3">
                            <label htmlFor="url" className="form-label">URL <span className="text-danger">*</span></label>
                            <input
                                type="url"
                                className={`form-control ${errors.url ? 'is-invalid' : ''}`}
                                id="url"
                                value={data.url}
                                onChange={(e) => setData('url', e.target.value)}
                                required
                            />
                            {errors.url && <div className="invalid-feedback">{errors.url}</div>}
                            <div className="form-text">URL ke artikel asli</div>
                        </div>

                        <div className="mb-3">
                            <label htmlFor="url_to_image" className="form-label">URL Gambar</label>
                            <input
                                type="url"
                                className={`form-control ${errors.url_to_image ? 'is-invalid' : ''}`}
                                id="url_to_image"
                                value={data.url_to_image}
                                onChange={(e) => setData('url_to_image', e.target.value)}
                            />
                            {errors.url_to_image && <div className="invalid-feedback">{errors.url_to_image}</div>}
                            <div className="form-text">URL ke gambar feature artikel</div>
                        </div>

                        <div className="mb-3">
                            <label htmlFor="source_name" className="form-label">Nama Sumber</label>
                            <input
                                type="text"
                                className={`form-control ${errors.source_name ? 'is-invalid' : ''}`}
                                id="source_name"
                                value={data.source_name}
                                onChange={(e) => setData('source_name', e.target.value)}
                            />
                            {errors.source_name && <div className="invalid-feedback">{errors.source_name}</div>}
                        </div>

                        <div className="mb-3">
                            <label htmlFor="category_id" className="form-label">Kategori <span className="text-danger">*</span></label>
                            <select
                                className={`form-select ${errors.category_id ? 'is-invalid' : ''}`}
                                id="category_id"
                                value={data.category_id}
                                onChange={(e) => setData('category_id', e.target.value)}
                                required
                            >
                                <option value="">Pilih Kategori</option>
                                {categories.map(category => (
                                    <option key={category.id} value={category.id}>
                                        {category.name}
                                    </option>
                                ))}
                            </select>
                            {errors.category_id && <div className="invalid-feedback">{errors.category_id}</div>}
                        </div>

                        <div className="mb-3 form-check">
                            <input
                                type="checkbox"
                                className="form-check-input"
                                id="is_published"
                                checked={data.is_published}
                                onChange={(e) => setData('is_published', e.target.checked)}
                            />
                            <label className="form-check-label" htmlFor="is_published">Publikasikan</label>
                        </div>

                        <div className="d-flex justify-content-end">
                            <button type="submit" className="btn btn-primary" disabled={processing}>
                                {processing ? 'Menyimpan...' : 'Simpan Artikel'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
