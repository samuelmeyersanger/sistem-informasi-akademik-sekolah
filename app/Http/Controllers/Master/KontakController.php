<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    /**
     * Menampilkan daftar semua pesan masuk dengan fitur pencarian dan paginasi
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Kontak::query();

        // Fitur Pencarian berdasarkan nama, email, subjek, atau potongan isi pesan
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('subject', 'like', '%' . $search . '%')
                  ->orWhere('pesan', 'like', '%' . $search . '%');
            });
        }

        // Paginasi 10 data per halaman agar manajemen load data optimal
        $kontaks = $query->orderBy('created_at', 'desc')
                         ->paginate(10)
                         ->appends(['search' => $search]);

        return view('master.kontak.index', compact('kontaks', 'search'));
    }

    /**
     * Mengambil detail satu pesan spesifik via AJAX JSON
     */
    public function show(Kontak $kontak)
    {
        return response()->json([
            'nama' => $kontak->nama,
            'email' => $kontak->email,
            'subject' => $kontak->subject,
            'pesan' => nl2br(e($kontak->pesan)), // Mengubah baris baru menjadi <br> agar rapi di HTML modal
            'tanggal' => $kontak->created_at->translatedFormat('d F Y - H:i') . ' WIB'
        ]);
    }

    /**
     * Menghapus pesan masuk dari database via AJAX
     */
    public function destroy(Kontak $kontak)
    {
        $kontak->delete();
        return response()->json(['success' => 'Pesan masuk berhasil dihapus permanen.']);
    }
}