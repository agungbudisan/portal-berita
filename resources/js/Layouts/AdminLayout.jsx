import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function AdminLayout({ children, title }) {
    const { auth, flash } = usePage().props;
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

    const toggleSidebar = () => {
        setSidebarCollapsed(!sidebarCollapsed);
    };

    return (
        <div className="d-flex">
            {/* Sidebar */}
            <nav id="sidebar" className={`bg-dark text-white ${sidebarCollapsed ? 'collapsed' : ''}`} style={{
                width: sidebarCollapsed ? '80px' : '250px',
                minHeight: '100vh',
                transition: 'width 0.3s'
            }}>
                <div className="p-3 border-bottom border-secondary">
                    <h3 className={sidebarCollapsed ? 'd-none' : ''}>Admin Panel</h3>
                    {sidebarCollapsed && <h3 className="text-center">A</h3>}
                </div>

                <ul className="nav flex-column p-3">
                    <li className="nav-item">
                        <Link
                            href={route('admin.dashboard')}
                            className={`nav-link text-white ${route().current('admin.dashboard') ? 'active bg-primary' : ''}`}
                        >
                            <i className="fas fa-tachometer-alt me-2"></i>
                            {!sidebarCollapsed && <span>Dashboard</span>}
                        </Link>
                    </li>

                    <li className="nav-item">
                        <Link
                            href={route('admin.news-api.index')}
                            className={`nav-link text-white ${route().current('admin.news-api.*') ? 'active bg-primary' : ''}`}
                        >
                            <i className="fas fa-rss me-2"></i>
                            {!sidebarCollapsed && <span>News API</span>}
                        </Link>
                    </li>

                    <li className="nav-item">
                        <Link
                            href={route('admin.saved-articles.index')}
                            className={`nav-link text-white ${route().current('admin.saved-articles.*') ? 'active bg-primary' : ''}`}
                        >
                            <i className="fas fa-newspaper me-2"></i>
                            {!sidebarCollapsed && <span>Artikel</span>}
                        </Link>
                    </li>

                    <li className="nav-item">
                        <Link
                            href={route('admin.categories.index')}
                            className={`nav-link text-white ${route().current('admin.categories.*') ? 'active bg-primary' : ''}`}
                        >
                            <i className="fas fa-tags me-2"></i>
                            {!sidebarCollapsed && <span>Kategori</span>}
                        </Link>
                    </li>

                    <li className="nav-item">
                        <Link
                            href={route('admin.comments.index')}
                            className={`nav-link text-white ${route().current('admin.comments.*') ? 'active bg-primary' : ''}`}
                        >
                            <i className="fas fa-comments me-2"></i>
                            {!sidebarCollapsed && <span>Komentar</span>}
                        </Link>
                    </li>

                    <li className="nav-item">
                        <Link
                            href={route('admin.users.index')}
                            className={`nav-link text-white ${route().current('admin.users.*') ? 'active bg-primary' : ''}`}
                        >
                            <i className="fas fa-users me-2"></i>
                            {!sidebarCollapsed && <span>Pengguna</span>}
                        </Link>
                    </li>

                    <li className="nav-item mt-3">
                        <Link
                            href={route('home')}
                            className="nav-link text-white"
                        >
                            <i className="fas fa-home me-2"></i>
                            {!sidebarCollapsed && <span>Kembali ke Portal</span>}
                        </Link>
                    </li>
                </ul>
            </nav>

            {/* Main Content */}
            <div className="flex-grow-1">
                <header className="bg-white shadow p-3">
                    <div className="d-flex justify-content-between align-items-center">
                        <button
                            className="btn btn-outline-secondary"
                            onClick={toggleSidebar}
                        >
                            <i className="fas fa-bars"></i>
                        </button>

                        <div className="dropdown">
                            <button className="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                {auth.user?.name || 'User'}
                            </button>
                            <ul className="dropdown-menu dropdown-menu-end">
                                <li>
                                    <Link
                                        href={route('user.profile')}
                                        className="dropdown-item"
                                    >
                                        Profil
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
                        </div>
                    </div>
                </header>

                <main className="p-4">
                    {flash.success && (
                        <div className="alert alert-success alert-dismissible fade show">
                            {flash.success}
                            <button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    )}

                    {flash.error && (
                        <div className="alert alert-danger alert-dismissible fade show">
                            {flash.error}
                            <button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    )}

                    <h1 className="mb-4">{title}</h1>

                    {children}
                </main>
            </div>
        </div>
    );
}
