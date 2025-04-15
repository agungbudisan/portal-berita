<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'news_source_url' => 'required|url',
            'news_api_id' => 'nullable|string'
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'news_source_url' => $validated['news_source_url'],
            'news_api_id' => $validated['news_api_id'],
            'is_approved' => false // Set default ke false untuk moderasi
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil dikirim dan sedang menunggu moderasi');
    }

    public function userComments()
    {
        $comments = Comment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.comments.index', [
            'comments' => $comments
        ]);
    }
}
