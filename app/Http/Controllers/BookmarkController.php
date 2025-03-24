<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedArticle;
use App\Models\Bookmark;
use Inertia\Inertia;

class BookmarkController extends Controller
{

    public function index(Request $request)
    {
        $bookmarks = Bookmark::with('savedArticle', 'savedArticle.category')
                            ->where('user_id', $request->user()->id)
                            ->latest()
                            ->paginate(10);

        return Inertia::render('User/Bookmarks', [
            'bookmarks' => $bookmarks
        ]);
    }

    public function store(Request $request, $articleId)
    {
        $article = SavedArticle::findOrFail($articleId);

        $bookmark = Bookmark::firstOrNew([
            'user_id' => $request->user()->id,
            'saved_article_id' => $article->id,
        ]);

        if (!$bookmark->exists) {
            $bookmark->save();
            return redirect()->back()->with('success', 'Artikel berhasil disimpan ke bookmark.');
        }

        return redirect()->back()->with('info', 'Artikel sudah ada di bookmark Anda.');
    }

    public function destroy(Request $request, Bookmark $bookmark)
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403);
        }

        $bookmark->delete();

        return redirect()->back()->with('success', 'Bookmark berhasil dihapus.');
    }
}
