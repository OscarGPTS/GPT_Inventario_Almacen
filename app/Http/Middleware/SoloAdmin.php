<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SoloAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return $next($request);
    }
}
