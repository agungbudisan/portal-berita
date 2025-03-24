<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReadingHistory;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function index(Request $request)
    {
        $reading_history = UserReadingHistory::with('savedArticle', 'savedArticle.category')
                                           ->where('user_id', $request->user()->id)
                                           ->latest('read_at')
                                           ->take(5)
                                           ->get();

        $comments = Comment::with('savedArticle')
                         ->where('user_id', $request->user()->id)
                         ->latest()
                         ->take(5)
                         ->get();

        return Inertia::render('User/Profile', [
            'reading_history' => $reading_history,
            'comments' => $comments
        ]);
    }

    public function edit()
    {
        return Inertia::render('User/EditProfile');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function readingHistory(Request $request)
    {
        $history = UserReadingHistory::with('savedArticle', 'savedArticle.category')
                                    ->where('user_id', $request->user()->id)
                                    ->latest('read_at')
                                    ->paginate(15);

        return Inertia::render('User/ReadingHistory', [
            'history' => $history
        ]);
    }

    public function comments(Request $request)
    {
        $comments = Comment::with('savedArticle')
                         ->where('user_id', $request->user()->id)
                         ->latest()
                         ->paginate(15);

        return Inertia::render('User/Comments', [
            'comments' => $comments
        ]);
    }
}
