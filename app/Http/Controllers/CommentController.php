<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedArticle;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, SavedArticle $article)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'saved_article_id' => $article->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'is_approved' => false, // Default menunggu persetujuan
        ]);

        return redirect()->back()->with('success', 'Komentar Anda telah dikirim dan sedang menunggu persetujuan.');
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update([
            'content' => $request->content,
            'is_approved' => false // Kembali ke status menunggu persetujuan
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil diperbarui.');
    }

    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->user_id !== $request->user()->id) {
            abort(403);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
