<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomentarBlog;
use Illuminate\Http\Request;

class KomentarBlogController extends Controller
{
    /**
     * Menampilkan semua daftar komentar dari pembaca
     */
    public function index()
    {
        // Load data bersama relasi 'blog' untuk optimasi N+1 query
        $komentars = KomentarBlog::with('blog')->orderBy('created_at', 'desc')->get();

        return view('admin.komentar-blog.index', compact('komentars'));
    }

    /**
     * Mengubah status moderasi komentar (Setujui / Batalkan Persetujuan)
     */
    public function toggleApprove($id)
    {
        $komentar = KomentarBlog::findOrFail($id);
        
        // Membalikkan status boolean disetujui
        $komentar->update([
            'disetujui' => !$komentar->disetujui
        ]);

        $statusPesan = $komentar->disetujui ? 'disetujui dan tampil di publik.' : 'dibatalkan persetujuannya (disembunyikan).';

        return redirect()->route('admin.komentar-blog.index')
            ->with('success', "Komentar dari {$komentar->nama} berhasil {$statusPesan}");
    }

    /**
     * Menghapus komentar (Spam / Tidak Pantas)
     */
    public function destroy($id)
    {
        $komentar = KomentarBlog::findOrFail($id);
        $komentar->delete();

        return redirect()->route('admin.komentar-blog.index')
            ->with('success', 'Komentar berhasil dihapus secara permanen.');
    }
}