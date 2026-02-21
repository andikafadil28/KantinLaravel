<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKantinMenuManageAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((int) $request->session()->get('level_kantin', 0) === 2) {
            return redirect()
                ->route('app.menu.index')
                ->withErrors(['menu' => 'Akses ditolak. Kasir hanya bisa melihat data menu.']);
        }

        return $next($request);
    }
}
