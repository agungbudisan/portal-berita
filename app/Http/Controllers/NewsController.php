<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function show(News $news)
    {
        $news->load(['category', 'approvedComments.user']);

        $relatedNews = News::where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(2)
            ->get();

        $popularNews = News::withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->take(3)
            ->get();

        $isBookmarked = Auth::check() ? $news->isBookmarkedByUser(Auth::user()) : false;

        return view('news.show', compact(
            'news',
            'relatedNews',
            'popularNews',
            'isBookmarked'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $news = News::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->with('category')
            ->latest('published_at')
            ->paginate(10);

        return view('news.search', compact('news', 'query'));
    }
}
