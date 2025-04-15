<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin'])->except(['show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with('category', 'author')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.articles.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = Auth::id();

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // If published and no published_at is set, use current timestamp
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        // For public view, only show published articles
        if (!$article->is_published && !Auth::check() && Auth::user()->is_admin) {
            abort(404);
        }

        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }

            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // If published and no published_at is set, use current timestamp
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Delete image if exists
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus');
    }
}
