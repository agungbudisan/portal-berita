<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $news = $category->news()
            ->latest('published_at')
            ->paginate(10);

        $popularInCategory = $category->news()
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->take(3)
            ->get();

        $otherCategories = Category::where('id', '!=', $category->id)
            ->withCount('news')
            ->orderBy('news_count', 'desc')
            ->take(4)
            ->get();

        return view('category.show', compact(
            'category',
            'news',
            'popularInCategory',
            'otherCategories'
        ));
    }
}
