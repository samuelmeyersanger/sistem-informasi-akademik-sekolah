<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan semua daftar menu di satu halaman (Single-Page CRUD)
     */
    public function index()
    {
        // Ambil semua data menu, kelompokkan berdasarkan kategori, lalu urutkan sesuai urutannya
        $menus = Menu::orderBy('kategori', 'asc')->orderBy('urutan', 'asc')->get();
        
        // Ambil daftar permission untuk opsi dropdown di dalam modal pop-up tambah/edit
        $permissions = Permission::orderBy('name', 'asc')->get();

        return view('admin.menu.index', compact('menus', 'permissions'));
    }

    /**
     * Menyimpan data menu baru dari modal popup tambah
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => ['required', 'string', 'max:255'],
            'nama_menu' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
            'permission_slug' => ['nullable', 'string', 'max:255'],
        ], [
            'kategori.required' => 'Kategori menu wajib diisi atau dipilih.',
            'nama_menu.required' => 'Nama menu tidak boleh kosong.',
            'url.required' => 'Jalur URL menu wajib ditentukan.',
            'urutan.required' => 'Nomor urutan menu wajib diisi.',
            'urutan.integer' => 'Nomor urutan harus berupa angka.',
        ]);

        Menu::create([
            'kategori' => $request->kategori,
            'nama_menu' => $request->nama_menu,
            'url' => $request->url,
            'icon' => $request->icon ?? 'collection', // default icon jika kosong
            'urutan' => $request->urutan,
            'permission_slug' => $request->permission_slug,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu baru berhasil ditambahkan secara live.');
    }

    /**
     * Memperbarui data menu dari modal popup edit (Menggunakan ID)
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'kategori' => ['required', 'string', 'max:255'],
            'nama_menu' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
            'permission_slug' => ['nullable', 'string', 'max:255'],
        ], [
            'kategori.required' => 'Kategori menu tidak boleh kosong.',
            'nama_menu.required' => 'Nama menu tidak boleh kosong.',
            'url.required' => 'Jalur URL menu tidak boleh kosong.',
            'urutan.required' => 'Nomor urutan wajib ditentukan.',
        ]);

        $menu->update([
            'kategori' => $request->kategori,
            'nama_menu' => $request->nama_menu,
            'url' => $request->url,
            'icon' => $request->icon ?? 'collection',
            'urutan' => $request->urutan,
            'permission_slug' => $request->permission_slug,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Data konfigurasi menu berhasil diperbarui.');
    }

    /**
     * Menghapus menu dari database
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Proteksi Logika: Cegah penghapusan menu kritis agar admin tidak terkunci secara tidak sengaja
        if ($menu->url === 'admin/user' || $menu->url === 'admin/menu') {
            return redirect()->route('admin.menu.index')
                ->with('error', 'Gagal menghapus! Menu ini merupakan menu inti sistem yang tidak boleh dihapus.');
        }

        $menu->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus dari sistem sidebar.');
    }
}