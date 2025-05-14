<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login to access admin area');
            }

            if (Auth::user()->role !== 'admin') {
                Log::channel('stderr')->warning('Unauthorized admin access attempt', [
                    'user_id' => Auth::id(),
                    'ip' => $request->ip()
                ]);
                return redirect()->route('home')->with('error', 'You do not have permission to access this area');
            }

            return $next($request);
        } catch (\Throwable $e) {
            Log::channel('stderr')->error('Admin middleware error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication error. Please login again.');
        }
    }
}
