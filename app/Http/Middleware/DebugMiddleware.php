<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        file_put_contents(storage_path('logs/middleware_debug.log'),
            json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role
                ] : null,
                'path' => $request->path(),
                'method' => $request->method()
            ], JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL,
            FILE_APPEND);

        return $next($request);
    }
}
