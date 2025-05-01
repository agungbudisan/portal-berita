<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $bookmarksCount = Bookmark::where('user_id', $user->id)->count();
        $commentsCount = Comment::where('user_id', $user->id)->count();

        // Menentukan kategori favorit
        $favoriteCategory = Category::select('categories.*')
            ->join('news', 'categories.id', '=', 'news.category_id')
            ->join('bookmarks', 'news.id', '=', 'bookmarks.news_id')
            ->where('bookmarks.user_id', $user->id)
            ->groupBy('categories.id')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        // Bookmark terbaru
        $recentBookmarks = Bookmark::with('news.category')
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        // Komentar terbaru
        $recentComments = Comment::with('news')
            ->where('user_id', $user->id)
            ->latest()
            ->take(2)
            ->get();

        return view('user.dashboard', compact(
            'bookmarksCount',
            'commentsCount',
            'favoriteCategory',
            'recentBookmarks',
            'recentComments'
        ));
    }
}
