<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\KomentarBlog;
use Illuminate\Http\Request;

class KomentarBlogController extends Controller
{
    /**
     * Menampilkan semua daftar komentar dengan fitur filter, search, dan paginasi
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); // approved, pending, atau kosong (semua)

        $query = KomentarBlog::with('blog');

        // Fitur Pencarian berdasarkan nama, email, atau isi komentar
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('isi_komentar', 'like', '%' . $search . '%');
            });
        }

        // Fitur Filter Status Moderasi
        if ($status === 'approved') {
            $query->where('disetujui', true);
        } elseif ($status === 'pending') {
            $query->where('disetujui', false);
        }

        // Paginasi 15 data per halaman agar manajemen load data optimal
        $komentars = $query->orderBy('created_at', 'desc')
                           ->paginate(15)
                           ->appends(['search' => $search, 'status' => $status]);

        return view('master.komentar-blog.index', compact('komentars', 'search', 'status'));
    }

    /**
     * Mengubah status moderasi komentar (Setujui / Batalkan Persetujuan)
     */
    public function toggleApprove($id)
    {
        $komentar = KomentarBlog::findOrFail($id);
        
        $komentar->update([
            'disetujui' => !$komentar->disetujui
        ]);

        $statusPesan = $komentar->disetujui ? 'disetujui dan tampil di publik.' : 'dibatalkan persetujuannya (disembunyikan).';

        return redirect()->route('master.komentar-blog.index')
            ->with('success', "Komentar dari {$komentar->nama} berhasil {$statusPesan}");
    }

    /**
     * Menghapus komentar (Spam / Tidak Pantas)
     */
    public function destroy($id)
    {
        $komentar = KomentarBlog::findOrFail($id);
        $komentar->delete();

        return redirect()->route('master.komentar-blog.index')
            ->with('success', 'Komentar berhasil dihapus secara permanen.');
    }
}