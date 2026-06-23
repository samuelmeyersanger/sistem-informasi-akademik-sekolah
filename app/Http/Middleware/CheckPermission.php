<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Jika tidak login, tendang ke login
        if (!$user) {
            return redirect()->route('login');
        }

        // 🟢 2. BYPASS SUPERADMIN: Jika role-nya admin, loloskan semua rute tanpa syarat!
        // (Ini akan menyelesaikan masalah 403 selamanya untuk akun admin Anda)
        if ($user->role === 'admin' || $user->roles()->where('name', 'admin')->exists()) {
            return $next($request);
        }

        // 3. Logika pengecekan permission untuk role selain admin (misal: guru/siswa)
        $routeName = $request->route()->getName();
        
        // Asumsi model User Anda memiliki method hasPermission()
        if (method_exists($user, 'hasPermission') && !$user->hasPermission($routeName)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}