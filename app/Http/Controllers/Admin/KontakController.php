<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    /**
     * Menampilkan daftar semua pesan masuk
     */
    public function index()
    {
        // Urutkan berdasarkan pesan terbaru yang masuk
        $kontaks = Kontak::orderBy('created_at', 'desc')->get();
        return view('admin.kontak.index', compact('kontaks'));
    }

    /**
     * Mengambil detail satu pesan spesifik via AJAX JSON
     */
    public function show(Kontak $kontak)
    {
        // Mengembalikan data pesan berformat JSON untuk dibaca oleh popup modal
        return response()->json([
            'nama' => $kontak->nama,
            'email' => $kontak->email,
            'subject' => $kontak->subject,
            'pesan' => nl2br(e($kontak->pesan)), // Mengubah baris baru (\n) menjadi <br> agar rapi di HTML
            'tanggal' => $kontak->created_at->translatedFormat('d F Y - H:i') . ' WIB'
        ]);
    }

    /**
     * Menghapus pesan masuk dari database
     */
    public function destroy(Kontak $kontak)
    {
        $kontak->delete();
        return response()->json(['success' => 'Pesan masuk berhasil dihapus permanen.']);
    }
}