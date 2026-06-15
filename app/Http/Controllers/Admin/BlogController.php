<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\KategoriBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Menampilkan daftar semua artikel/blog
     */
    public function index()
    {
        // Ambil data blog beserta relasi kategori dan user (penulis) untuk optimasi database
        $blogs = Blog::with(['kategori', 'user'])->orderBy('created_at', 'desc')->get();
        
        // Ambil data kategori untuk pilihan dropdown di modal form
        $categories = KategoriBlog::orderBy('nama', 'asc')->get();

        return view('admin.blog.index', compact('blogs', 'categories'));
    }

    /**
     * Menyimpan artikel blog baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => ['required', 'string', 'max:255', 'unique:blog,judul'],
            'kategori_blog_id' => ['required', 'exists:kategori_blog,id'],
            'konten' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Batas 2MB
            'is_published' => ['required', 'boolean'],
        ], [
            'judul.required' => 'Judul artikel wajib diisi.',
            'judul.unique' => 'Judul artikel ini sudah pernah digunakan.',
            'kategori_blog_id.required' => 'Silakan pilih kategori terlebih dahulu.',
            'konten.required' => 'Isi konten artikel tidak boleh kosong.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 2MB.',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            // Simpan gambar ke dalam folder public/blogs
            $gambarPath = $request->file('gambar')->store('blogs', 'public');
        }

        Blog::create([
            'user_id' => Auth::id(), // Otomatis mengisi ID admin/user yang sedang login
            'kategori_blog_id' => $request->kategori_blog_id,
            'judul' => $request->judul,
            'konten' => $request->konten,
            'gambar' => $gambarPath,
            'is_published' => $request->is_published,
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Artikel baru berhasil diterbitkan.');
    }

    /**
     * Memperbarui artikel blog
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'judul' => ['required', 'string', 'max:255', 'unique:blog,judul,' . $id],
            'kategori_blog_id' => ['required', 'exists:kategori_blog,id'],
            'konten' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_published' => ['required', 'boolean'],
        ]);

        // Tetapkan path gambar lama sebagai bawaan
        $gambarPath = $blog->gambar;

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari server jika ada untuk menghemat ruang penyimpanan
            if ($blog->gambar && Storage::disk('public')->exists($blog->gambar)) {
                Storage::disk('public')->delete($blog->gambar);
            }
            // Simpan gambar baru
            $gambarPath = $request->file('gambar')->store('blogs', 'public');
        }

        // Paksa reset slug agar fungsi booted() memicu pembuatan slug baru jika judul berubah
        $blog->slug = null;

        $blog->update([
            'kategori_blog_id' => $request->kategori_blog_id,
            'judul' => $request->judul,
            'konten' => $request->konten,
            'gambar' => $gambarPath,
            'is_published' => $request->is_published,
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Menghapus artikel blog
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        // Hapus file fisik gambar unggulan dari folder storage sebelum menghapus row data
        if ($blog->gambar && Storage::disk('public')->exists($blog->gambar)) {
            Storage::disk('public')->delete($blog->gambar);
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Artikel berhasil dihapus dari sistem.');
    }
}