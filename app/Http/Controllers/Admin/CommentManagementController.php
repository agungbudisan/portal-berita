<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = Comment::with(['user', 'news']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $comments = $query->latest()->paginate(10);

        return view('admin.comments.index', compact('comments', 'status'));
    }

    public function approve(Comment $comment)
    {
        $comment->approve();

        return back()->with('success', 'Komentar telah disetujui.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Komentar telah dihapus.');
    }
}
