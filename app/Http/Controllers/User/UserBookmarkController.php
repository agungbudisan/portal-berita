<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = Bookmark::with('news.category')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.bookmarks', compact('bookmarks'));
    }
}
