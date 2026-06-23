<?php

namespace App\Http\Controllers\Master;

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
     * Menampilkan daftar artikel, form tambah, dan form edit dalam satu halaman
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori_id');

        $query = Blog::with(['kategori', 'user']);

        // Fitur Pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Fitur Filter Kategori
        if (!empty($kategoriId)) {
            $query->where('kategori_blog_id', $kategoriId);
        }

        // Menggunakan paginasi agar performa list data tetap cepat
        $blogs = $query->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->appends(['search' => $search, 'kategori_id' => $kategoriId]);
        
        $categories = KategoriBlog::orderBy('nama', 'asc')->get();

        return view('master.blog.index', compact('blogs', 'categories', 'search', 'kategoriId'));
    }

    /**
     * Menyimpan artikel blog baru via Modal
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => ['required', 'string', 'max:255', 'unique:blog,judul'],
            'kategori_blog_id' => ['required', 'exists:kategori_blog,id'],
            'konten' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
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
            $gambarPath = $request->file('gambar')->store('blogs', 'public');
        }

        Blog::create([
            'user_id' => Auth::id(),
            'kategori_blog_id' => $request->kategori_blog_id,
            'judul' => $request->judul,
            'konten' => $request->konten,
            'gambar' => $gambarPath,
            'is_published' => $request->is_published,
        ]);

        return redirect()->route('master.blog.index')->with('success', 'Artikel baru berhasil diterbitkan.');
    }

    /**
     * Memperbarui artikel blog via Modal
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

        $gambarPath = $blog->gambar;

        if ($request->hasFile('gambar')) {
            if ($blog->gambar && Storage::disk('public')->exists($blog->gambar)) {
                Storage::disk('public')->delete($blog->gambar);
            }
            $gambarPath = $request->file('gambar')->store('blogs', 'public');
        }

        $blog->slug = null;

        $blog->update([
            'kategori_blog_id' => $request->kategori_blog_id,
            'judul' => $request->judul,
            'konten' => $request->konten,
            'gambar' => $gambarPath,
            'is_published' => $request->is_published,
        ]);

        return redirect()->route('master.blog.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Menghapus artikel blog
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->gambar && Storage::disk('public')->exists($blog->gambar)) {
            Storage::disk('public')->delete($blog->gambar);
        }

        $blog->delete();

        return redirect()->route('master.blog.index')->with('success', 'Artikel berhasil dihapus dari sistem.');
    }
}