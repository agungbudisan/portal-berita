<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;
    
    public function store(Request $request, News $news)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = new Comment([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        $news->comments()->save($comment);

        return back()->with('success', 'Komentar telah dikirim dan sedang menunggu persetujuan.');
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $validated['content'],
            'status' => 'pending'
        ]);

        return back()->with('success', 'Komentar telah diperbarui dan sedang menunggu persetujuan.');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Komentar telah dihapus.');
    }
}
