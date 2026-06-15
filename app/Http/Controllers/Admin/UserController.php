<?php

namespace App\Http\Controllers\Admin;

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
        // Ambil user beserta data roles custom-nya
        $query = User::with('roles');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('is_approved', 'asc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        // Ambil semua daftar role untuk ditampilkan sebagai pilhan checkbox di modal
        $allRoles = Role::all(); 

        return view('admin.user.index', compact('users', 'allRoles'));
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

        return redirect()->route('admin.user.index')->with('success', 'User baru berhasil dibuat.');
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

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Putus semua relasi role terlebih dahulu sebelum menghapus data user
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }
}