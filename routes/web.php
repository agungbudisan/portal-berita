<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsApiController;
use App\Http\Controllers\Admin\SavedArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\UserController;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/category/{slug}', [ArticleController::class, 'byCategory'])->name('articles.category');
Route::get('/search', [ArticleController::class, 'search'])->name('articles.search');

// Comment routes (requires authentication)
Route::post('/articles/{article}/comments', [CommentController::class, 'store'])
    ->middleware(['auth'])
    ->name('comments.store');

// Bookmark routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/articles/{article}/bookmark', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
});

// User profile routes (requires authentication)
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/reading-history', [UserProfileController::class, 'readingHistory'])->name('reading-history');
    Route::get('/comments', [UserProfileController::class, 'comments'])->name('comments');
});

// Admin routes (requires admin role)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'debug', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard'); // Perbaiki ini
    Route::get('/news-api', [NewsApiController::class, 'index'])->name('news-api.index'); // Perbaiki ini
    Route::post('/news-api/save', [NewsApiController::class, 'saveArticle'])->name('news-api.save'); // Perbaiki ini

    // Saved Articles
    Route::resource('saved-articles', SavedArticleController::class);
    Route::put('/saved-articles/{savedArticle}/toggle-publish', [SavedArticleController::class, 'togglePublish'])->name('saved-articles.toggle-publish');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Comments
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::put('/comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::put('/comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    Route::get('/test-news-api', function() {
        $service = app(App\Services\NewsApiService::class);
        return $service->getHeadlines(['country' => 'id']);
    })->middleware('auth', 'admin');

    Route::get('/admin-test', function() {
        return 'Anda memiliki akses admin!';
    })->middleware(['auth', 'admin']);
});

require __DIR__.'/auth.php';
