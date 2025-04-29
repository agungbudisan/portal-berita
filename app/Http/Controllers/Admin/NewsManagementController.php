<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsManagementController extends Controller
{
    public function index()
    {
        $news = News::with('category')
            ->latest()
            ->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'source' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $slug = Str::slug($validated['title']);
        $uniqueSlug = $this->getUniqueSlug($slug);

        $news = new News([
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'source' => $validated['source'] ?? 'WinniNews',
            'published_at' => now(),
        ]);

        if ($request->hasFile('image')) {
            $news->image = $request->file('image')->store('news', 'public');
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        $categories = Category::all();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'source' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        // Update slug hanya jika judul berubah
        if ($news->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $uniqueSlug = $this->getUniqueSlug($slug, $news->id);
            $news->slug = $uniqueSlug;
        }

        $news->title = $validated['title'];
        $news->content = $validated['content'];
        $news->category_id = $validated['category_id'];
        $news->source = $validated['source'] ?? $news->source;

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            $news->image = $request->file('image')->store('news', 'public');
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        // Hapus gambar jika ada
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    private function getUniqueSlug($slug, $ignoreId = null)
    {
        $originalSlug = $slug;
        $count = 1;

        $query = News::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = News::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }
}
