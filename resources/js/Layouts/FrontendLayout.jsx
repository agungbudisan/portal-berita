// resources/js/Layouts/FrontendLayout.jsx
import React, { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { Dropdown } from 'react-bootstrap';

const FrontendLayout = ({ children }) => {
    const { auth, categories, flash } = usePage().props;
    const [searchQuery, setSearchQuery] = useState('');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('articles.search'), { q: searchQuery });
    };

    return (
        <div id="app">
            <header>
                <nav className="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                    <div className="container">
                        <Link className="navbar-brand" href={route('home')}>
                            Portal Berita
                        </Link>
                        <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span className="navbar-toggler-icon"></span>
                        </button>

                        <div className="collapse navbar-collapse" id="navbarSupportedContent">
                            {/* Left Side Of Navbar */}
                            <ul className="navbar-nav me-auto">
                                <li className="nav-item">
                                    <Link className={`nav-link ${route().current('home') ? 'active' : ''}`} href={route('home')}>
                                        Home
                                    </Link>
                                </li>
                                <li className="nav-item dropdown">
                                    <a className="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Kategori
                                    </a>
                                    <ul className="dropdown-menu" aria-labelledby="navbarDropdown">
                                        {categories.map(category => (
                                            <li key={category.id}>
                                                <Link className="dropdown-item" href={route('articles.category', category.slug)}>
                                                    {category.name}
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </li>
                            </ul>

                            {/* Search Form */}
                            <form className="d-flex me-auto" onSubmit={handleSearch}>
                                <input
                                    className="form-control me-2"
                                    type="search"
                                    placeholder="Cari berita..."
                                    aria-label="Search"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                                <button className="btn btn-outline-success" type="submit">Cari</button>
                            </form>

                            {/* Right Side Of Navbar */}
                            <ul className="navbar-nav ms-auto">
                                {!auth.user ? (
                                    <>
                                        <li className="nav-item">
                                            <Link className="nav-link" href={route('login')}>Login</Link>
                                        </li>
                                        <li className="nav-item">
                                            <Link className="nav-link" href={route('register')}>Register</Link>
                                        </li>
                                    </>
                                ) : (
                                    <>
                                        {auth.user.role === 'admin' && (
                                            <li className="nav-item">
                                                <Link className="nav-link" href={route('admin.dashboard')}>Dashboard Admin</Link>
                                            </li>
                                        )}
                                        <li className="nav-item">
                                            <Link className={`nav-link ${route().current('bookmarks.index') ? 'active' : ''}`} href={route('bookmarks.index')}>
                                                Bookmark
                                            </Link>
                                        </li>
                                        <li className="nav-item dropdown">
                                            <Dropdown>
                                                <Dropdown.Toggle variant="link" className="nav-link dropdown-toggle">{auth.user.name}</Dropdown.Toggle>
                                                <Dropdown.Menu>
                                                    <Dropdown.Item href={route('user.profile')}>Profil Saya</Dropdown.Item>
                                                    <Dropdown.Item href={route('user.reading-history')}>Riwayat Bacaan</Dropdown.Item>
                                                    <Dropdown.Item href={route('user.comments')}>Komentar Saya</Dropdown.Item>
                                                    <Dropdown.Divider />
                                                    <Dropdown.Item
                                                        onClick={() => router.post(route('logout'))}
                                                    >
                                                        Logout
                                                    </Dropdown.Item>
                                                </Dropdown.Menu>
                                            </Dropdown>
                                        </li>
                                    </>
                                )}
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <main className="py-4">
                <div className="container">
                    {flash.success && (
                        <div className="alert alert-success alert-dismissible fade show" role="alert">
                            {flash.success}
                            <button type="button" className="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    )}

                    {flash.error && (
                        <div className="alert alert-danger alert-dismissible fade show" role="alert">
                            {flash.error}
                            <button type="button" className="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    )}

                    {children}
                </div>
            </main>

            <footer className="bg-dark text-white py-4 mt-4">
                <div className="container">
                    <div className="row">
                        <div className="col-md-4">
                            <h5>Portal Berita</h5>
                            <p>Portal berita terkini dengan sumber terpercaya.</p>
                        </div>
                        <div className="col-md-4">
                            <h5>Kategori</h5>
                            <ul className="list-unstyled">
                                {categories.slice(0, 5).map(category => (
                                    <li key={category.id}>
                                        <Link href={route('articles.category', category.slug)} className="text-white">
                                            {category.name}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div className="col-md-4">
                            <h5>Link</h5>
                            <ul className="list-unstyled">
                                <li><Link href={route('home')} className="text-white">Home</Link></li>
                                {!auth.user ? (
                                    <>
                                        <li><Link href={route('login')} className="text-white">Login</Link></li>
                                        <li><Link href={route('register')} className="text-white">Register</Link></li>
                                    </>
                                ) : (
                                    <>
                                        <li><Link href={route('user.profile')} className="text-white">Profil</Link></li>
                                        <li><Link href={route('bookmarks.index')} className="text-white"></Link></li>
                                    </>
                                )}
                            </ul>
                        </div>
                    </div>
                    <hr />
                    <div className="text-center">
                        <p>&copy; {new Date().getFullYear()} Portal Berita. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
};

export default FrontendLayout;
