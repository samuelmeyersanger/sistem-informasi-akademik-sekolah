<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role; // Pastikan model Role custom Anda diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Permission;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua role
     */
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->orderBy('created_at', 'desc')->get();
        $permissions = Permission::orderBy('modul', 'asc')->get(); // Ambil semua izin untuk pilihan centang (checkbox)

        return view('admin.role.index', compact('roles', 'permissions'));
    }

    /**
     * Menyimpan role baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => ['required', 'string', 'max:50', 'unique:roles,display_name'],
            'permissions' => ['nullable', 'array'] // Validasi array checkbox izin
        ]);

        $role = Role::create([
            'name' => \Illuminate\Support\Str::slug($request->display_name), 
            'display_name' => $request->display_name,
        ]);

        // Jika ada permission yang dicentang, tempelkan ke role baru ini
        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('admin.role.index')->with('success', 'Role baru dan hak aksesnya berhasil ditambahkan.');
    }

    /**
     * Memperbarui nama role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'display_name' => ['required', 'string', 'max:50', 'unique:roles,display_name,' . $role->id],
            'permissions' => ['nullable', 'array']
        ]);

        $role->update([
            'name' => \Illuminate\Support\Str::slug($request->display_name), 
            'display_name' => $request->display_name,
        ]);

        // Sinkronisasi otomatis data permission (Hapus yang lama, simpan pilihan yang baru saja dicentang)
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.role.index')->with('success', 'Role dan hak akses berhasil diperbarui.');
    }

    /**
     * Menghapus role dari sistem
     */
    public function destroy(Role $role)
    {
        // Proteksi Keamanan: Jangan izinkan penghapusan jika role masih dipakai oleh user
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.role.index')
                ->with('error', 'Gagal menghapus! Role "' . $role->name . '" masih tertempel pada beberapa akun pengguna.');
        }

        // Proteksi Tambahan: Mencegah ketidaksengajaan menghapus role inti/krusial sistem
        $protectedRoles = ['admin', 'guru', 'siswa'];
        if (in_array(strtolower($role->name), $protectedRoles) || in_array($role->slug, $protectedRoles)) {
            return redirect()->route('admin.role.index')
                ->with('error', 'Role inti bawaan sistem (' . $role->name . ') tidak diperbolehkan untuk dihapus.');
        }

        $role->delete();

        return redirect()->route('admin.role.index')
            ->with('success', 'Role berhasil dihapus secara permanen.');
    }
}