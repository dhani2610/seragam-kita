<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak! Anda bukan Admin.'], 403);
        }

        return redirect('/')->with('error', 'Akses ditolak! Anda bukan Admin.');
    }
}
