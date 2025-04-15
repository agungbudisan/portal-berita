<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'pendingComments' => Comment::where('is_approved', false)->count(),
            'articles' => Article::count(),
        ];

        $recentComments = Comment::with('user')
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();

        $recentUsers = User::orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentComments' => $recentComments,
            'recentUsers' => $recentUsers,
        ]);
    }

    public function manageComments()
    {
        $comments = Comment::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.comments.index', [
            'comments' => $comments
        ]);
    }

    public function approveComment(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Komentar berhasil disetujui');
    }

    public function deleteComment(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }

    public function manageUsers()
    {
        $users = User::paginate(15);

        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    public function toggleAdmin(User $user)
    {
        // Prevent changing own status
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah status admin diri sendiri');
        }

        $user->update([
            'is_admin' => !$user->is_admin
        ]);

        $status = $user->is_admin ? 'menjadi admin' : 'tidak lagi menjadi admin';

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} {$status}");
    }
}
