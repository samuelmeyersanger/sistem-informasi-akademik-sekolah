<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // 1. Cek apakah user sudah login
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah user memiliki permission yang diminta
        if (! auth()->user()->hasPermission($permission)) {
            // Jika tidak punya akses, lempar error 403 (Forbidden)
            abort(403, 'Maaf, Anda tidak memiliki hak akses untuk membuka halaman/modul ini.');
        }

        return $next($request);
    }
}