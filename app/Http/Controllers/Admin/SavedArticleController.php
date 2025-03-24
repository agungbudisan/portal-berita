<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavedArticle;
use App\Models\Category;
use Inertia\Inertia;

class SavedArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = SavedArticle::with('category', 'user');

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest()->paginate(15);
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status'])
        ]);
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();

        return Inertia::render('Admin/Articles/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'required|url',
            'url_to_image' => 'nullable|url',
            'source_name' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'boolean'
        ]);

        SavedArticle::create([
            'article_id' => 'manual-' . time(),
            'title' => $request->title,
            'description' => $request->description,
            'url' => $request->url,
            'url_to_image' => $request->url_to_image,
            'source_name' => $request->source_name ?: 'Manual Entry',
            'published_at' => $request->published_at ?: now(),
            'content' => $request->content,
            'category_id' => $request->category_id,
            'user_id' => $request->user()->id,
            'is_published' => $request->is_published ?? false,
        ]);

        return redirect()->route('admin.saved-articles.index')
            ->with('success', 'Artikel berhasil dibuat');
    }

    public function edit(SavedArticle $savedArticle)
    {
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();

        return Inertia::render('Admin/Articles/Edit', [
            'article' => $savedArticle,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, SavedArticle $savedArticle)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'required|url',
            'url_to_image' => 'nullable|url',
            'source_name' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'boolean'
        ]);

        $savedArticle->update([
            'title' => $request->title,
            'description' => $request->description,
            'url' => $request->url,
            'url_to_image' => $request->url_to_image,
            'source_name' => $request->source_name,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'is_published' => $request->is_published ?? false,
        ]);

        return redirect()->route('admin.saved-articles.index')
            ->with('success', 'Artikel berhasil diperbarui');
    }

    public function destroy(SavedArticle $savedArticle)
    {
        $savedArticle->delete();

        return redirect()->route('admin.saved-articles.index')
            ->with('success', 'Artikel berhasil dihapus');
    }

    public function togglePublish(SavedArticle $savedArticle)
    {
        $savedArticle->update([
            'is_published' => !$savedArticle->is_published
        ]);

        return redirect()->back()
            ->with('success', 'Status publikasi artikel berhasil diubah');
    }
}
