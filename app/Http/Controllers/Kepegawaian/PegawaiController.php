<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support5\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    /**
     * Menampilkan daftar pegawai dengan fitur pencarian.
     */
    public function index(Request $request)
    {
        $query = Pegawai::latest();

        // Fitur Pencarian berdasarkan nama, NIP, atau NUPTK seirama dengan View
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        $pegawai = $query->get();

        return view('kepegawaian.pegawai.index', compact('pegawai'));
    }

    /**
     * Menyimpan data pegawai baru dari Modal Tambah.
     */
/**
     * Menyimpan data pegawai baru dari Modal Tambah.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:Laki-Laki,Perempuan',
            // Gunakan 'nullable' agar jika dikosongkan tidak dianggap duplikat
            'nip'            => 'nullable|string|unique:pegawai,nip',
            'nuptk'          => 'nullable|string|unique:pegawai,nuptk',
            'status_pegawai' => 'required|in:PNS,PPPK,HONORER',
            'jenis_ptk'      => 'required|in:Kepala Sekolah,Guru,Tenaga Kependidikan',
            'email'          => 'nullable|email|max:255',
        ], [
            // Kustomisasi Pesan Error agar tidak muncul "validation.unique"
            'nip.unique'   => 'NIP tersebut sudah terdaftar di sistem.',
            'nuptk.unique' => 'NUPTK tersebut sudah terdaftar di sistem.',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();

        Pegawai::create(array_merge($request->all(), [
            'semester_id'      => $semesterAktif ? $semesterAktif->id : null,
            'status_keaktifan' => 'Aktif'
        ]));

        return redirect()->route('kepegawaian.pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }
    
    /**
     * Menampilkan detail profil pegawai, berkas dokumen, KGB, dan pangkat (Sistem Tab Menu).
     */
    public function show($id)
    {
        // Eager loading relasi agar query database efisien
        $pegawai = Pegawai::with(['dokumen', 'kgb', 'kenaikanPangkat'])->findOrFail($id);
        
        return view('kepegawaian.pegawai.show', compact('pegawai'));
    }

    /**
     * Memperbarui biodata data pokok pegawai dari Tab 1.
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:Laki-Laki,Perempuan',
            // Abaikan ID pegawai ini sendiri saat pengecekan unique agar bisa di-save
            'nip'            => 'nullable|string|unique:pegawai,nip,' . $id,
            'nuptk'          => 'nullable|string|unique:pegawai,nuptk,' . $id,
            'status_pegawai' => 'required|in:PNS,PPPK,HONORER',
            'jenis_ptk'      => 'required|in:Kepala Sekolah,Guru,Tenaga Kependidikan',
            'email'          => 'nullable|email|max:255',
        ], [
            'nip.unique'   => 'NIP tersebut sudah digunakan oleh pegawai lain.',
            'nuptk.unique' => 'NUPTK tersebut sudah digunakan oleh pegawai lain.',
        ]);

        $pegawai->update($request->all());

        return redirect()->route('kepegawaian.pegawai.show', $id)->with('success', 'Biodata pegawai berhasil diperbarui.');
    }

    /**
     * Menghapus data pegawai (Mendukung Soft Deletes sesuai model Anda).
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('kepegawaian.pegawai.index')->with('success', 'Data pegawai berhasil diarsipkan ke dalam sistem.');
    }

    /**
     * Memproses aksi mutasi keluar pegawai.
     */
    public function mutasi(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'tanggal_mutasi'    => 'required|date',
            'alasan_mutasi'     => 'required|string',
            'sekolah_tujuan'    => 'required|string|max:255',
            'file_surat_mutasi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'status_keaktifan' => 'Mutasi',
            'tanggal_mutasi'   => $request->tanggal_mutasi,
            'alasan_mutasi'    => $request->alasan_mutasi,
            'sekolah_tujuan'   => $request->sekolah_tujuan,
        ];

        if ($request->hasFile('file_surat_mutasi')) {
            $data['file_surat_mutasi'] = $request->file('file_surat_mutasi')->store('surat_mutasi', 'public');
        }

        $pegawai->update($data);

        return redirect()->back()->with('success', 'Pegawai telah berhasil dimutasi keluar.');
    }

    /**
     * Memproses aksi penonaktifan pegawai karena pensiun.
     */
    public function pensiun(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'tanggal_pensiun'    => 'required|date',
            'file_surat_pensiun' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'status_keaktifan' => 'Pensiun',
            'tanggal_pensiun'  => $request->tanggal_pensiun,
        ];

        if ($request->hasFile('file_surat_pensiun')) {
            $data['file_surat_pensiun'] = $request->file('file_surat_pensiun')->store('surat_pensiun', 'public');
        }

        $pegawai->update($data);

        return redirect()->back()->with('success', 'Pegawai telah dikonfirmasi pensiun.');
    }
    // ==========================================
    // 1. GENERATE AKUN PEGAWAI INDIVIDU
    // ==========================================
    public function generateAkunIndividu($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if ($pegawai->user_id) {
            return redirect()->back()->with('error', 'Pegawai ini sudah memiliki akun.');
        }

        // --- AMBIL NAMA SEKOLAH DARI DATABASE ---
        $profil = DB::table('profil_sekolah')->first();
        
        // Bersihkan nama sekolah: hilangkan spasi, ubah jadi huruf kecil
        $namaSekolahBersih = $profil ? strtolower(str_replace(' ', '', $profil->nama_sekolah)) : 'sekolah';
        
        // Gabungkan menjadi domain email resmi sekolah
        $domainSekolah = '@' . $namaSekolahBersih . '.sch.id'; 
        // ----------------------------------------

        // Gunakan NIP/NUPTK sebagai prefix email jika ada, jika tidak ada pakai nama depan + acak
        if ($pegawai->nip) {
            $prefixEmail = trim($pegawai->nip);
        } elseif ($pegawai->nuptk) {
            $prefixEmail = trim($pegawai->nuptk);
        } else {
            $prefixEmail = strtolower(explode(' ', trim($pegawai->nama_lengkap))[0]) . rand(10, 99);
        }
        
        $emailPegawai = strtolower($prefixEmail) . $domainSekolah;

        if (User::where('email', $emailPegawai)->exists()) {
            $emailPegawai = strtolower($prefixEmail) . rand(1, 9) . $domainSekolah;
        }

        // Buat User Baru
        $user = User::create([
            'name' => $pegawai->nama_lengkap,
            'email' => $emailPegawai,
            'password' => Hash::make('pegawai123'),
            'is_approved' => true,
        ]);

        // 🟢 PENENTUAN ROLE DINAMIS INTERNAL (Anti Hardcode)
        // Mengubah "Kepala Sekolah" -> "kepala_sekolah", "Guru" -> "guru", "Tenaga Kependidikan" -> "tenaga_kependidikan"
        $roleTargetName = strtolower(str_replace(' ', '-', trim($pegawai->jenis_ptk)));
        $rolePegawai = Role::where('name', $roleTargetName)->first();
        
        // Fallback jika role spesifik tidak ditemukan, cari role umum 'pegawai' atau 'staf'
        if (!$rolePegawai) {
            $rolePegawai = Role::whereIn('name', ['pegawai', 'staf'])->first();
        }

        if ($rolePegawai) {
            $user->roles()->attach($rolePegawai->id); // Masuk ke tabel pivot user_role Anda
        }

        $pegawai->update(['user_id' => $user->id]);

        return redirect()->back()->with('success', "Akun untuk {$pegawai->nama_lengkap} berhasil dibuat! Email: {$emailPegawai}");
    }

    // ==========================================
    // 2. GENERATE AKUN PEGAWAI MASSAL
    // ==========================================
    public function generateAkunMassal()
    {
        // 1. Hilangkan batasan waktu eksekusi skrip php
        set_time_limit(0);

        // Ambil data pegawai aktif yang belum punya akun
        $pegawaiBelumPunyaAkun = Pegawai::whereNull('user_id')
            ->where('status_keaktifan', 'Aktif')
            ->get();

        if ($pegawaiBelumPunyaAkun->isEmpty()) {
            return redirect()->back()->with('info', 'Semua pegawai aktif sudah memiliki akun login.');
        }

        // Ambil profil sekolah untuk domain email
        $profil = DB::table('profil_sekolah')->first();
        $namaSekolahBersih = $profil ? strtolower(str_replace(' ', '', $profil->nama_sekolah)) : 'sekolah';
        $domainSekolah = '@' . $namaSekolahBersih . '.sch.id';

        $counter = 0;
        $passwordHash = Hash::make('pegawai123'); // Cukup hash 1 kali di luar loop agar hemat CPU!

        // 🟢 AMBIL MAP DATA ROLES INTERNAL (Ambil semua role 1 kali di luar loop demi performa cepat)
        $rolesMap = Role::pluck('id', 'name')->toArray();

        // 2. Gunakan Database Transaction agar aman dan terproteksi rollBack
        DB::beginTransaction();

        try {
            foreach ($pegawaiBelumPunyaAkun as $pegawai) {
                
                if ($pegawai->nip) {
                    $prefixEmail = trim($pegawai->nip);
                } elseif ($pegawai->nuptk) {
                    $prefixEmail = trim($pegawai->nuptk);
                } else {
                    $prefixEmail = strtolower(explode(' ', trim($pegawai->nama_lengkap))[0]) . rand(10, 99);
                }
                
                $emailPegawai = strtolower($prefixEmail) . $domainSekolah;

                // Cek keunikan email
                if (User::where('email', $emailPegawai)->exists()) {
                    $emailPegawai = strtolower($prefixEmail) . rand(1, 9) . $domainSekolah;
                }

                // Buat User
                $user = User::create([
                    'name' => $pegawai->nama_lengkap,
                    'email' => $emailPegawai,
                    'password' => $passwordHash, 
                    'is_approved' => true,
                ]);

                // 🟢 PROSES ATTACH ROLE DINAMIS MASSAL
                $roleTargetName = strtolower(str_replace(' ', '-', trim($pegawai->jenis_ptk)));

                if (array_key_exists($roleTargetName, $rolesMap)) {
                    $user->roles()->attach($rolesMap[$roleTargetName]);
                } else {
                    // Fallback ke role pegawai/staf umum
                    if (array_key_exists('pegawai', $rolesMap)) {
                        $user->roles()->attach($rolesMap['pegawai']);
                    } elseif (array_key_exists('staf', $rolesMap)) {
                        $user->roles()->attach($rolesMap['staf']);
                    }
                }

                // Update Pegawai
                $pegawai->update(['user_id' => $user->id]);
                $counter++;
            }

            // Jika semua berhasil tanpa interupsi, commit serentak ke database
            DB::commit();

            return redirect()->back()->with('success', "Berhasil men-generate {$counter} akun pegawai secara dinamis!");

        } catch (\Exception $e) {
            // Batalkan semua data yang sempat di-insert jika di tengah jalan crash
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate massal pegawai: ' . $e->getMessage());
        }
    }
}