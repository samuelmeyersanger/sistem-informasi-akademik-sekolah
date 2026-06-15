<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\KategoriBlog;
use App\Models\KomentarBlog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Menampilkan daftar artikel blog/berita sekolah (Halaman Index).
     */
    public function index(Request $request)
    {
        // 1. Ambil semua kategori untuk filter di sidebar/menu blog
        $categories = KategoriBlog::withCount('blogs')->get();

        // 2. Mulai query blog yang berstatus published (menggunakan scopePublished dari Model Blog)
        $query = Blog::published()->with(['kategori', 'user']);

        // 3. Fitur Pencarian Berdasarkan Judul atau Konten
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('konten', 'like', '%' . $search . '%');
            });
        }

        // 4. Fitur Filter Berdasarkan Kategori (menggunakan Slug Kategori)
        if ($request->has('kategori') && !empty($request->kategori)) {
            $kategoriSlug = $request->kategori;
            $query->whereHas('kategori', function ($q) use ($kategoriSlug) {
                $q->where('slug', $kategoriSlug);
            });
        }

        // 5. Eksekusi dengan Pagination (tampilkan 9 artikel per halaman)
        $posts = $query->paginate(9)->withQueryString();

        // 6. Ambil 4 artikel terpopuler/terbaru untuk widget "Artikel Terkini"
        $recentPosts = Blog::published()->take(4)->get();

        return view('publik.blog.index', compact('posts', 'categories', 'recentPosts'));
    }

    /**
     * Menampilkan detail satu artikel blog berdasarkan slug (Halaman Detail).
     */
    public function show($slug)
    {
        // 1. Cari artikel berdasarkan slug yang aktif tayang, jika tidak ada tampilkan 404
        $post = Blog::published()
            ->with(['kategori', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // 2. Ambil komentar-komentar yang sudah disetujui admin (menggunakan scopeApproved dari Model KomentarBlog)
        $comments = $post->komentar()->approved()->get();

        // 3. Ambil artikel lainnya di kategori yang sama sebagai rekomendasi pembaca
        $relatedPosts = Blog::published()
            ->where('kategori_blog_id', $post->kategori_blog_id)
            ->where('id', '!=', $post->id) // Jangan tampilkan artikel yang sedang dibaca
            ->take(3)
            ->get();

        return view('publik.blog.show', compact('post', 'comments', 'relatedPosts'));
    }

    /**
     * Menyimpan komentar baru dari pengunjung website (Submit Komentar).
     */
    public function storeKomentar(Request $request, $id)
    {
        // 1. Validasi input komentar pengunjung
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'komentar' => 'required|string|max:1000',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'komentar.required' => 'Isi komentar tidak boleh kosong.',
        ]);

        // 2. Pastikan artikel blog-nya memang ada
        $post = Blog::findOrFail($id);

        // 3. Simpan data komentar ke database
        // Status 'disetujui' otomatis FALSE sesuai default migration (menunggu moderasi admin)
        KomentarBlog::create([
            'blog_id' => $post->id,
            'nama' => strip_tags($request->nama),
            'email' => strip_tags($request->email),
            'komentar' => strip_tags($request->komentar),
        ]);

        // 4. Kembalikan ke halaman sebelumnya dengan pesan sukses alert
        return back()->with('success_komentar', 'Komentar Anda berhasil dikirim! Komentar akan tampil setelah diperiksa dan disetujui oleh Admin Sekolah.');
    }
}