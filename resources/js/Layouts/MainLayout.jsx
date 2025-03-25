import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import NavLink from '@/Components/NavLink';

export default function MainLayout({ children, title }) {
    const { auth, categories, flash } = usePage().props;
    const [searchQuery, setSearchQuery] = useState('');

    const handleSearch = (e) => {
        e.preventDefault();
        window.location.href = route('articles.search') + '?q=' + searchQuery;
    };

    return (
        <div className="min-vh-100 d-flex flex-column">
            <header>
                <nav className="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div className="container">
                        <Link href={route('home')} className="navbar-brand">
                            Portal Berita
                        </Link>

                        <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                            <span className="navbar-toggler-icon"></span>
                        </button>

                        <div className="collapse navbar-collapse" id="mainNavbar">
                            <ul className="navbar-nav me-auto">
                                <li className="nav-item">
                                    <NavLink active={route().current('home')} href={route('home')}>
                                        Home
                                    </NavLink>
                                </li>

                                <li className="nav-item">
                                    <NavLink active={route().current('articles.index')} href={route('articles.index')}>
                                        Artikel
                                    </NavLink>
                                </li>

                                <li className="nav-item dropdown">
                                    <a className="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        Kategori
                                    </a>
                                    <ul className="dropdown-menu">
                                        {categories.map(category => (
                                            <li key={category.id}>
                                                <Link
                                                    href={route('articles.category', category.slug)}
                                                    className="dropdown-item"
                                                >
                                                    {category.name}
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </li>
                            </ul>

                            <form className="d-flex me-auto" onSubmit={handleSearch}>
                                <input
                                    className="form-control me-2"
                                    type="search"
                                    placeholder="Cari berita..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                                <button className="btn btn-outline-success" type="submit">Cari</button>
                            </form>

                            <ul className="navbar-nav">
                                {auth.user ? (
                                    <li className="nav-item dropdown">
                                        <a className="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                            {auth.user.name}
                                        </a>
                                        <ul className="dropdown-menu dropdown-menu-end">
                                            {auth.user.role === 'admin' && (
                                                <li>
                                                    <Link
                                                        href={route('admin.dashboard')}
                                                        className="dropdown-item"
                                                    >
                                                        Dashboard Admin
                                                    </Link>
                                                </li>
                                            )}

                                            <li>
                                                <Link
                                                    href={route('user.profile')}
                                                    className="dropdown-item"
                                                >
                                                    Profil Saya
                                                </Link>
                                            </li>

                                            <li>
                                                <Link
                                                    href={route('bookmarks.index')}
                                                    className="dropdown-item"
                                                >
                                                    Bookmark
                                                </Link>
                                            </li>

                                            <li>
                                                <Link
                                                    href={route('user.reading-history')}
                                                    className="dropdown-item"
                                                >
                                                    Riwayat Bacaan
                                                </Link>
                                            </li>

                                            <li><hr className="dropdown-divider" /></li>

                                            <li>
                                                <Link
                                                    href={route('logout')}
                                                    method="post"
                                                    as="button"
                                                    className="dropdown-item"
                                                >
                                                    Logout
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                ) : (
                                    <>
                                        <li className="nav-item">
                                            <Link
                                                href={route('login')}
                                                className="nav-link"
                                            >
                                                Login
                                            </Link>
                                        </li>
                                        <li className="nav-item">
                                            <Link
                                                href={route('register')}
                                                className="nav-link"
                                            >
                                                Register
                                            </Link>
                                        </li>
                                    </>
                                )}
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <main className="flex-grow-1 py-4">
                {flash.success && (
                    <div className="container">
                        <div className="alert alert-success alert-dismissible fade show">
                            {flash.success}
                            <button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                )}

                {flash.error && (
                    <div className="container">
                        <div className="alert alert-danger alert-dismissible fade show">
                            {flash.error}
                            <button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                )}

                <div className="container">
                    {title && <h1 className="mb-4">{title}</h1>}
                    {children}
                </div>
            </main>

            <footer className="bg-dark text-white py-4 mt-auto">
                <div className="container">
                    <div className="row">
                        <div className="col-md-4 mb-3">
                            <h5>Portal Berita</h5>
                            <p>Portal berita terkini dengan sumber terpercaya.</p>
                        </div>

                        <div className="col-md-4 mb-3">
                            <h5>Kategori</h5>
                            <ul className="list-unstyled">
                                {categories.slice(0, 5).map(category => (
                                    <li key={category.id}>
                                        <Link
                                            href={route('articles.category', category.slug)}
                                            className="text-white text-decoration-none"
                                        >
                                            {category.name}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <div className="col-md-4 mb-3">
                            <h5>Link</h5>
                            <ul className="list-unstyled">
                                <li>
                                    <Link
                                        href={route('home')}
                                        className="text-white text-decoration-none"
                                    >
                                        Home
                                    </Link>
                                </li>
                                {auth.user ? (
                                    <>
                                        <li>
                                            <Link
                                                href={route('user.profile')}
                                                className="text-white text-decoration-none"
                                            >
                                                Profil
                                            </Link>
                                        </li>
                                        <li>
                                            <Link
                                                href={route('bookmarks.index')}
                                                className="text-white text-decoration-none"
                                            >
                                                Bookmark
                                            </Link>
                                        </li>
                                    </>
                                ) : (
                                    <>
                                        <li>
                                            <Link
                                                href={route('login')}
                                                className="text-white text-decoration-none"
                                            >
                                                Login
                                            </Link>
                                        </li>
                                        <li>
                                            <Link
                                                href={route('register')}
                                                className="text-white text-decoration-none"
                                            >
                                                Register
                                            </Link>
                                        </li>
                                    </>
                                )}
                            </ul>
                        </div>
                    </div>

                    <hr className="border-light" />

                    <div className="text-center">
                        <p className="mb-0">&copy; {new Date().getFullYear()} Portal Berita. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
