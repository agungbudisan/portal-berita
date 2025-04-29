<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Comment;
use App\Models\ApiSource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $newsCount = News::count();
        $userCount = User::where('role', 'user')->count();
        $commentCount = Comment::count();
        $todayNewsCount = News::whereDate('created_at', today())->count();

        $apiSources = ApiSource::all();

        $recentComments = Comment::with(['user', 'news'])
            ->latest()
            ->take(3)
            ->get();

        $recentUsers = User::latest()
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'newsCount',
            'userCount',
            'commentCount',
            'todayNewsCount',
            'apiSources',
            'recentComments',
            'recentUsers'
        ));
    }
}
