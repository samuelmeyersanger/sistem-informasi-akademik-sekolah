<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission; 
use App\Models\Role; // 1. Import Model Role
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Menampilkan daftar semua permission beserta pilihan Role
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('modul', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // 2. Load hubungan 'roles' agar kita tahu siapa saja yang punya izin ini
        $permissions = $query->with('roles')->orderBy('modul', 'asc')->orderBy('created_at', 'desc')->get();
        
        // 3. Ambil semua data role untuk ditampilkan sebagai checkbox
        $roles = Role::orderBy('display_name', 'asc')->get();

        return view('admin.permission.index', compact('permissions', 'roles'));
    }

    /**
     * Menyimpan permission baru dan menempelkan ke Role terpilih
     */
    public function store(Request $request)
    {
        $request->validate([
            'modul' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:50', 'unique:permissions,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'roles' => ['nullable', 'array'] // Validasi input roles berbentuk array
        ], [
            'modul.required' => 'Nama modul wajib diisi.',
            'name.required' => 'Kode izin (name) wajib diisi.',
            'name.unique' => 'Kode izin ini sudah terdaftar.',
        ]);

        $permission = Permission::create([
            'modul' => $request->modul,
            'name' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        // 4. Jika ada role yang dicentang saat pembuatan, langsung tempelkan
        if ($request->has('roles')) {
            $permission->roles()->attach($request->roles);
        }

        return redirect()->route('admin.permission.index')->with('success', 'Izin fitur baru dan hak akses role berhasil ditambahkan.');
    }

    /**
     * Memperbarui data permission dan sinkronisasi Role
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'modul' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:50', 'unique:permissions,name,' . $permission->id],
            'description' => ['nullable', 'string', 'max:255'],
            'roles' => ['nullable', 'array']
        ], [
            'modul.required' => 'Nama modul tidak boleh kosong.',
            'name.required' => 'Kode izin tidak boleh kosong.',
            'name.unique' => 'Kode izin tersebut sudah digunakan.',
        ]);

        $permission->update([
            'modul' => $request->modul,
            'name' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        // 5. Sinkronisasi otomatis (sync) tabel pivot hubungan permission dengan role
        $permission->roles()->sync($request->roles ?? []);

        return redirect()->route('admin.permission.index')->with('success', 'Izin fitur dan hak akses role berhasil diperbarui.');
    }

    /**
     * Menghapus permission dari sistem
     */
    public function destroy(Permission $permission)
    {
        $protected = ['akses-admin', 'kelola-role', 'kelola-user'];
        if (in_array($permission->name, $protected)) {
            return redirect()->route('admin.permission.index')
                ->with('error', 'Izin inti bawaan sistem tidak boleh dihapus.');
        }

        $permission->delete();

        return redirect()->route('admin.permission.index')
            ->with('success', 'Izin fitur berhasil dihapus secara permanen.');
    }
}