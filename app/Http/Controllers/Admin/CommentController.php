<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Inertia\Inertia;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'savedArticle']);

        if ($request->has('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $comments = $query->latest()->paginate(20);

        return Inertia::render('Admin/Comments/Index', [
            'comments' => $comments,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Komentar berhasil disetujui');
    }

    public function reject(Comment $comment)
    {
        $comment->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Komentar berhasil ditolak');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }
}
