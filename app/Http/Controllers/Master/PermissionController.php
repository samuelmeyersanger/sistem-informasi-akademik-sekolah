<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Permission; 
use App\Models\Role; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Menampilkan daftar semua permission beserta pilihan Role (Dengan Fitur Search & Paginasi)
     */
    public function index(Request $request)
    {
        // Gunakan query() agar Laravel fokus mengambil parameter dari URL (GET Request)
        $search = $request->query('search');
        // Menggunakan fitur 'when' bawaan Laravel, lebih bersih dan aman dari error
        $permissions = Permission::with('roles')
            ->when($search, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('modul', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
                });
            })
            ->orderBy('modul', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // Ini fitur otomatis Laravel agar parameter pencarian ikut ke halaman paginasi 2, 3, dst
        $roles = Role::orderBy('display_name', 'asc')->get();
        return view('master.permission.index', compact('permissions', 'roles', 'search'));
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
            'roles' => ['nullable', 'array'] 
        ], [
            'modul.required' => 'Nama modul wajib diisi.',
            'name.required' => 'Kode izin (name) wajib diisi.',
            'name.unique' => 'Kode izin ini sudah terdaftar.',
        ]);

        // SOLUSI: Ubah huruf kecil, hapus spasi, tapi IZINKAN tanda titik (.) dan strip (-)
        $cleanName = strtolower(str_replace(' ', '', $request->name));

        $permission = Permission::create([
            'modul' => $request->modul,
            'name' => $cleanName, // <-- Gunakan string yang sudah dibersihkan secara aman
            'description' => $request->description,
        ]);

        if ($request->has('roles')) {
            $permission->roles()->attach($request->roles);
        }

        return redirect()->route('master.permission.index')->with('success', 'Izin fitur baru dan hak akses role berhasil ditambahkan.');
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

        // SOLUSI: Ubah huruf kecil, hapus spasi, tapi IZINKAN tanda titik (.) dan strip (-)
        $cleanName = strtolower(str_replace(' ', '', $request->name));

        $permission->update([
            'modul' => $request->modul,
            'name' => $cleanName, // <-- Gunakan string yang sudah dibersihkan secara aman
            'description' => $request->description,
        ]);

        $permission->roles()->sync($request->roles ?? []);

        return redirect()->route('master.permission.index')->with('success', 'Izin fitur dan hak akses role berhasil diperbarui.');
    }

    /**
     * Menghapus permission dari sistem secara aman
     */
    public function destroy(Permission $permission)
    {
        $protected = ['akses-admin', 'kelola-role', 'kelola-user'];
        if (in_array($permission->name, $protected)) {
            return redirect()->route('master.permission.index')
                ->with('error', 'Izin inti bawaan sistem tidak boleh dihapus.');
        }

        $permission->delete();

        return redirect()->route('master.permission.index')
            ->with('success', 'Izin fitur berhasil dihapus secara permanen.');
    }
}