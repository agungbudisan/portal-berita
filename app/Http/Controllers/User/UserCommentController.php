<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('news')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.comments', compact('comments'));
    }
}
