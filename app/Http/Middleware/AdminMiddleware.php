<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Anda tidak memiliki hak akses untuk masuk ke halaman tersebut.');
    }
}
