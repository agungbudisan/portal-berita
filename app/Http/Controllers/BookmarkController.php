<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function store(News $news)
    {
        $user = Auth::user();

        // Cek apakah sudah ada bookmark untuk berita ini
        $exists = Bookmark::where('user_id', $user->id)
            ->where('news_id', $news->id)
            ->exists();

        if (!$exists) {
            Bookmark::create([
                'user_id' => $user->id,
                'news_id' => $news->id,
            ]);
        }

        return back()->with('success', 'Berita telah disimpan ke bookmark Anda.');
    }

    public function destroy(News $news)
    {
        $user = Auth::user();

        Bookmark::where('user_id', $user->id)
            ->where('news_id', $news->id)
            ->delete();

        return back()->with('success', 'Berita telah dihapus dari bookmark Anda.');
    }
}
