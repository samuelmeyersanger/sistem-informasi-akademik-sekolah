<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog; // Sesuaikan dengan model log milikmu
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query log beserta relasi usernya
        $query = ActivityLog::with(['user.roles'])->orderBy('created_at', 'desc');

        // Logika Pencarian sesuai dengan form di Blade
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Paginasi 10 data per halaman
        $logs = $query->paginate(10)->withQueryString();

        return view('master.activity-logs.index', compact('logs'));
    }
}