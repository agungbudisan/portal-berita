<?php

namespace App\Http\Controllers;

use App\Models\SavedNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class BookmarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bookmarks = SavedNews::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.bookmarks.index', [
            'bookmarks' => $bookmarks
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source' => 'required|string|max:255',
            'source_url' => 'required|url',
            'image_url' => 'nullable|url',
            'api_id' => 'nullable|string',
            'published_at' => 'required|date'
        ]);

        $bookmark = SavedNews::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'source_url' => $validated['source_url']
            ],
            array_merge($validated, ['user_id' => Auth::id()])
        );

        return redirect()->back()->with('success', 'Berita berhasil disimpan ke bookmark');
    }

    public function destroy(SavedNews $bookmark)
    {
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $bookmark->delete();

        return redirect()->route('user.bookmarks.index')
            ->with('success', 'Bookmark berhasil dihapus');
    }
}
