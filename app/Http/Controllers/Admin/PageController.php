<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get();
        return view('admin.page.index', compact('pages'));
    }

    // Fungsi mengambil data mentah untuk dilempar ke form Edit di Popup AJAX
    public function edit(Page $page)
    {
        return response()->json($page);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug'],
            'content' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        Page::create([
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : null,
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'is_published' => $request->has('is_published'),
            'sort_order' => $request->sort_order,
        ]);

        return response()->json(['success' => 'Halaman baru berhasil ditambahkan!']);
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', "unique:pages,slug,{$page->id}"],
            'content' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->title),
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'is_published' => $request->has('is_published'),
            'sort_order' => $request->sort_order,
        ]);

        return response()->json(['success' => 'Halaman berhasil diperbarui!']);
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return response()->json(['success' => 'Halaman berhasil dihapus!']);
    }
}