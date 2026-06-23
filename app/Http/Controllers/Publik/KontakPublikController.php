<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakPublikController extends Controller
{
    /**
     * Menampilkan halaman formulir kontak (opsional jika halaman kontak terpisah)
     */
    public function index()
    {
        return view('publik.kontak');
    }

    /**
     * Menyimpan pesan masuk dari pengunjung website
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Pengunjung
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.max' => 'Alamat email maksimal 150 karakter.',
            'subject.required' => 'Subjek atau perihal pesan wajib diisi.',
            'subject.max' => 'Subjek maksimal 255 karakter.',
            'pesan.required' => 'Isi pesan tidak boleh kosong.',
            'pesan.min' => 'Isi pesan minimal terdiri dari 10 karakter.',
            'pesan.max' => 'Isi pesan terlalu panjang (maksimal 2000 karakter).',
        ]);

        // 2. Simpan ke Database
        Kontak::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'subject' => $request->subject,
            'pesan' => $request->pesan,
        ]);

        // 3. Cek apakah request dikirim via AJAX (Fetch/Axios) atau Form HTML Biasa
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih, pesan Anda telah berhasil dikirim! Kami akan segera menghubungi Anda kembali.'
            ], 201);
        }

        // Fallback untuk submit form biasa / non-AJAX
        return redirect()->back()->with('success', 'Pesan Anda telah berhasil dikirim! Terima kasih.');
    }
}