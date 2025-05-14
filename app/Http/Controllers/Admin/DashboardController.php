<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Comment;
use App\Models\ApiSource;
use App\Repositories\NewsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function index()
    {
        try {
            // Bungkus seluruh operasi database dalam try-catch
            $newsCount = News::count();
            $userCount = User::where('role', 'user')->count();
            $commentCount = Comment::count();
            $todayNewsCount = $this->newsRepository->getTodayNewsCount();

            // Batasi query untuk mengurangi beban
            $apiSources = ApiSource::select(['id', 'name', 'url'])->get();

            // Gunakan eager loading yang lebih spesifik
            $recentComments = Comment::with([
                'user:id,name,email',
                'news:id,title,slug'
            ])
                ->latest()
                ->take(3)
                ->get();

            $recentUsers = User::select(['id', 'name', 'email', 'created_at'])
                ->latest()
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
        } catch (\Throwable $e) {
            // Log error
            Log::channel('stderr')->error('Dashboard Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Return error view
            return view('admin.dashboard-error', [
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred loading the dashboard'
            ]);
        }
    }
}
