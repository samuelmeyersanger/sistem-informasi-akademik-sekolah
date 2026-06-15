<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use Illuminate\Http\Request;

class FooterLinkController extends Controller
{
    /**
     * Menampilkan semua daftar tautan footer
     */
    public function index()
    {
        // Diurutkan berdasarkan kelompok group dulu, lalu nomor urutan terkecil
        $links = FooterLink::orderBy('group', 'asc')->orderBy('urutan', 'asc')->get();
        return view('admin.footer-link.index', compact('links'));
    }

    /**
     * Menyimpan tautan footer baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'group' => ['required', 'string', 'max:100'],
            'judul' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:0'],
        ], [
            'group.required' => 'Kelompok tautan wajib dipilih/diisi.',
            'judul.required' => 'Label judul tautan tidak boleh kosong.',
            'url.required' => 'Alamat URL tautan wajib diisi.',
        ]);

        FooterLink::create([
            'group' => $request->group,
            'judul' => $request->judul,
            'url' => $request->url,
            'urutan' => $request->urutan,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.footer-link.index')->with('success', 'Tautan footer baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data tautan footer
     */
    public function update(Request $request, FooterLink $footerLink)
    {
        $request->validate([
            'group' => ['required', 'string', 'max:100'],
            'judul' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:0'],
        ]);

        $footerLink->update([
            'group' => $request->group,
            'judul' => $request->judul,
            'url' => $request->url,
            'urutan' => $request->urutan,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.footer-link.index')->with('success', 'Data tautan footer berhasil diperbarui.');
    }

    /**
     * Menghapus tautan footer
     */
    public function destroy(FooterLink $footerLink)
    {
        $footerLink->delete();
        return redirect()->route('admin.footer-link.index')->with('success', 'Tautan footer berhasil dihapus permanen.');
    }
}