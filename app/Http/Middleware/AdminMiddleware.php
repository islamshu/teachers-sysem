<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if ($user->getAllPermissions()->count() > 0) {
            return $next($request);
        }

        abort(403);
    }
}
