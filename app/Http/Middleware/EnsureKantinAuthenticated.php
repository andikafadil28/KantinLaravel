<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKantinAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('kantin_user_id')) {
            return redirect('/app/login');
        }

        return $next($request);
    }
}
