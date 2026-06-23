<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Role; 
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua role (Dengan Fitur Search & Paginasi)
     */
    public function index(Request $request) // 👈 Tambahkan parameter Request di sini
    {
        // Tangkap kata kunci pencarian dari input name="search"
        $search = $request->input('search');

        // Buat query dasar dengan Eager Loading relasi permissions dan hitung jumlah user
        $query = Role::withCount('users')->with('permissions');

        // Jika user mengetikkan sesuatu di kolom pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                // Cari berdasarkan kolom 'display_name' di tabel roles
                $q->where('display_name', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        // Urutkan dari yang terbaru, beri paginasi 10 data, dan kunci parameter pencariannya
        $roles = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->appends(['search' => $search]);
        
        // Ambil semua izin untuk pilihan centang (checkbox) dikelompokkan/diurutkan berdasarkan modul
        $permissions = Permission::orderBy('modul', 'asc')->get(); 

        return view('master.role.index', compact('roles', 'permissions', 'search'));
    }

    /**
     * Menyimpan role baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => ['required', 'string', 'max:50', 'unique:roles,display_name'],
            'permissions' => ['nullable', 'array'] 
        ]);

        $role = Role::create([
            'name' => Str::slug($request->display_name), 
            'display_name' => $request->display_name,
        ]);

        // Jika ada permission yang dicentang, tempelkan ke role baru ini
        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('master.role.index')->with('success', 'Role baru dan hak aksesnya berhasil ditambahkan.');
    }

    /**
     * Memperbarui nama role dan hak aksesnya
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'display_name' => ['required', 'string', 'max:50', 'unique:roles,display_name,' . $role->id],
            'permissions' => ['nullable', 'array']
        ]);

        $role->update([
            'name' => Str::slug($request->display_name), 
            'display_name' => $request->display_name,
        ]);

        // Sinkronisasi otomatis data permission (Hapus yang lama, simpan pilihan yang baru saja dicentang)
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('master.role.index')->with('success', 'Role dan hak akses berhasil diperbarui.');
    }

    /**
     * Menghapus role dari sistem
     */
    public function destroy(Role $role)
    {
        // Proteksi Keamanan: Jangan izinkan penghapusan jika role masih dipakai oleh user
        if ($role->users()->count() > 0) {
            return redirect()->route('master.role.index')
                ->with('error', 'Gagal menghapus! Role "' . $role->display_name . '" masih tertempel pada beberapa akun pengguna.');
        }

        // Proteksi Tambahan: Mencegah ketidaksengajaan menghapus role inti/krusial sistem
        $protectedRoles = ['admin', 'guru', 'siswa'];
        if (in_array(strtolower($role->name), $protectedRoles) || (isset($role->slug) && in_array($role->slug, $protectedRoles))) {
            return redirect()->route('master.role.index')
                ->with('error', 'Role inti bawaan sistem (' . $role->display_name . ') tidak diperbolehkan untuk dihapus.');
        }

        $role->delete();

        return redirect()->route('master.role.index')
            ->with('success', 'Role berhasil dihapus secara permanen.');
    }
}