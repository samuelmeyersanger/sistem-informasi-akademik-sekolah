<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriBlogController extends Controller
{
    /**
     * Menampilkan daftar semua kategori blog
     */
    public function index()
    {
        // Mengambil semua kategori dan menghitung jumlah artikel di dalamnya
        $kategoriBlogs = KategoriBlog::withCount('blogs')->orderBy('nama', 'asc')->get();

        return view('admin.kategori-blog.index', compact('kategoriBlogs'));
    }

    /**
     * Menyimpan kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:kategori_blog,nama'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori ini sudah terdaftar.',
        ]);

        // Model akan otomatis membuat slug dari nama lewat event booted()
        KategoriBlog::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama), // Opsional, sebagai fallback pengaman tambahan
        ]);

        return redirect()->route('admin.kategori-blog.index')->with('success', 'Kategori blog baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui kategori blog
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriBlog::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:kategori_blog,nama,' . $id],
        ], [
            'nama.required' => 'Nama kategori tidak boleh kosong.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
        ]);

        // Paksa reset slug agar function booted() updating memicu regenerasi slug baru yang sinkron
        $kategori->slug = null; 
        
        $kategori->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.kategori-blog.index')->with('success', 'Nama kategori blog berhasil diperbarui.');
    }

    /**
     * Menghapus kategori blog
     */
    public function destroy($id)
    {
        $kategori = KategoriBlog::findOrFail($id);

        // Proteksi Keamanan: Jika ada artikel/blog yang memakai kategori ini, gagalkan penghapusan
        if ($kategori->blogs()->count() > 0) {
            return redirect()->route('admin.kategori-blog.index')
                ->with('error', 'Gagal menghapus! Kategori ini masih memiliki artikel aktif di dalamnya. Pindahkan atau hapus artikel terlebih dahulu.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori-blog.index')->with('success', 'Kategori blog berhasil dihapus secara permanen.');
    }
}