<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Pastikan impor model Role custom kamu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input search dan role dari request
        $search = $request->input('search');
        $roleFilter = $request->input('role');

        // Ambil user beserta data roles custom-nya
        $query = User::with('roles');

        // 2. Filter berdasarkan Pencarian (Nama / Email)
        if ($search && $search != '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // 3. 🟢 PERBAIKAN MULTIROLE: Filter berdasarkan Role Dropdown
        if ($roleFilter && $roleFilter != '') {
            $query->whereHas('roles', function($q) use ($roleFilter) {
                $q->where('name', $roleFilter); // Mencari ke tabel perantara relasi
            });
        }

        // Urutkan berdasarkan approval dan waktu pendaftaran
        $users = $query->orderBy('is_approved', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString(); // 🟢 WAJIB: Agar saat pindah halaman pagination, filter pencarian & role tidak hilang

        // Ambil semua daftar role untuk ditampilkan sebagai pilihan checkbox di modal
        $allRoles = Role::all(); 

        return view('master.user.index', compact('users', 'allRoles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'], // Array ID dari role-role yang dicentang
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_approved' => true, 
        ]);

        // Tempelkan banyak ID role custom ke tabel user_role
        $user->roles()->attach($request->roles);

        return redirect()->route('master.user.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'], 
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->is_approved = $request->has('is_approved') ? true : false;
        $user->save();

        // Singkronisasikan data tabel jembatan user_role secara otomatis
        $user->roles()->sync($request->roles);

        return redirect()->route('master.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Putus semua relasi role terlebih dahulu sebelum menghapus data user
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('master.user.index')->with('success', 'User berhasil dihapus.');
    }
}