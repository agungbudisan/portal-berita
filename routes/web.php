<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsManagementController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\CommentManagementController;
use App\Http\Controllers\Admin\ApiSourceController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserBookmarkController;
use App\Http\Controllers\User\UserCommentController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/search', [NewsController::class, 'search'])->name('news.search');

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    // Comments
    Route::post('/news/{news}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
    Route::put('/comment/{comment}', [CommentController::class, 'update'])->name('comment.update');

    // Bookmarks
    Route::post('/news/{news}/bookmark', [BookmarkController::class, 'store'])->name('bookmark.store');
    Route::delete('/bookmark/{news}', [BookmarkController::class, 'destroy'])->name('bookmark.destroy');

    // User Dashboard
    Route::prefix('dashboard')->name('user.')->group(function () {
        Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookmarks', [UserBookmarkController::class, 'index'])->name('bookmarks');
        Route::get('/comments', [UserCommentController::class, 'index'])->name('comments');
    });
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // News Management
    Route::resource('news', NewsManagementController::class);
    Route::post('/upload/image', [NewsManagementController::class, 'uploadImage'])
    ->name('upload.image');

    // Category Management
    Route::resource('categories', CategoryManagementController::class);

    // User Management
    Route::resource('users', UserManagementController::class);

    // Comment Moderation
    Route::get('/comments', [CommentManagementController::class, 'index'])->name('comments.index');
    Route::get('/comments/approve-all', [CommentManagementController::class, 'approveAll'])->name('comments.approve-all');
    Route::put('/comments/{comment}/approve', [CommentManagementController::class, 'approve'])->name('comments.approve');
    Route::delete('/comments/{comment}', [CommentManagementController::class, 'destroy'])->name('comments.destroy');

    // API Management
    Route::resource('api-sources', ApiSourceController::class);
    Route::post('api-sources/{apiSource}/refresh', [ApiSourceController::class, 'refresh'])->name('api-sources.refresh');
    Route::post('api-sources/refresh-all', [ApiSourceController::class, 'refreshAll'])->name('api-sources.refresh-all');
    Route::post('api-sources/test-connection', [ApiSourceController::class, 'testConnection'])->name('api-sources.test-connection');
});

// Profile Routes (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
