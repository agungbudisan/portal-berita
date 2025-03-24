<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Gunakan $request->user() sebagai gantinya auth()->user()
        $user = $request->user();

        // Periksa apakah user sudah login dan memiliki role admin
        if (!$user || $user->role !== 'admin') {
            // Jika menggunakan Inertia, beri respons Inertia redirect
            if ($request->header('X-Inertia')) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            // Fallback ke redirect biasa
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        // Lanjutkan ke request berikutnya jika user adalah admin
        return $next($request);
    }
}
