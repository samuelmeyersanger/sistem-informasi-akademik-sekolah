<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckApproval
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login tapi belum di-approve oleh admin
        if (Auth::check() && !Auth::user()->is_approved) {
            Auth::logout(); // Paksa logout

            // Lempar kembali ke halaman login dengan pesan peringatan khusus
            return redirect()->route('login')->with('status', 'Akun Anda berhasil didaftarkan. Silakan hubungi Administrator Sekolah untuk aktivasi akun dan penentuan Hak Akses (Role) Anda.');
        }

        return $next($request);
    }
}