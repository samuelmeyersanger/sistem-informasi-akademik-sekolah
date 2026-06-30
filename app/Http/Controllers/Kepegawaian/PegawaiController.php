<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

// 💡 TAMBAHKAN BERKAS EXPORT & IMPORT DI SINI
use App\Exports\PegawaiTemplateExport;
use App\Imports\PegawaiImport;
use Maatwebsite\Excel\Facades\Excel;

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
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:Laki-Laki,Perempuan',
            'nip'            => 'nullable|string|unique:pegawai,nip',
            'nuptk'          => 'nullable|string|unique:pegawai,nuptk',
            'status_pegawai' => 'required|in:PNS,PPPK,HONORER',
            'jenis_ptk'      => 'required|in:Kepala Sekolah,Guru,Tenaga Kependidikan',
            'email'          => 'nullable|email|max:255',
        ], [
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
     * Menghapus data pegawai (Mendukung Soft Deletes).
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('kepegawaian.pegawai.index')->with('success', 'Data pegawai berhasil diarsipkan ke dalam sistem.');
    }

    // =========================================================================
    // 🆕 SEKSI UTAMA IMPORT & DOWNLOAD TEMPLATE EXCEL PEGAWAI
    // =========================================================================
    
    /**
     * Mendownload template Excel multi-sheet (Template + Referensi Dropdown).
     */
    public function downloadTemplate()
    {
        return Excel::download(new PegawaiTemplateExport, 'template_import_pegawai.xlsx');
    }

    /**
     * Memproses file Excel yang di-upload oleh pengguna.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:5120',
        ], [
            'file_excel.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file_excel.mimes'    => 'Format dokumen harus berakhiran .xlsx atau .xls',
        ]);

        try {
            Excel::import(new PegawaiImport, $request->file('file_excel'));
            return redirect()->route('kepegawaian.pegawai.index')->with('success', 'Massal data pegawai berhasil dimasukkan ke sistem!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses berkas Excel. Periksa susunan kolom Anda. Error: ' . $e->getMessage());
        }
    }

    // =========================================================================

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

    /**
     * GENERATE AKUN PEGAWAI INDIVIDU
     */
    public function generateAkunIndividu($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if ($pegawai->user_id) {
            return redirect()->back()->with('error', 'Pegawai ini sudah memiliki akun.');
        }

        $profil = DB::table('profil_sekolah')->first();
        $namaSekolahBersih = $profil ? strtolower(str_replace(' ', '', $profil->nama_sekolah)) : 'sekolah';
        $domainSekolah = '@' . $namaSekolahBersih . '.sch.id'; 

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

        $user = User::create([
            'name' => $pegawai->nama_lengkap,
            'email' => $emailPegawai,
            'password' => Hash::make('@Sekolah4#'),
            'is_approved' => true,
        ]);

        $roleTargetName = strtolower(str_replace(' ', '-', trim($pegawai->jenis_ptk)));
        $rolePegawai = Role::where('name', $roleTargetName)->first();
        
        if (!$rolePegawai) {
            $rolePegawai = Role::whereIn('name', ['pegawai', 'staf'])->first();
        }

        if ($rolePegawai) {
            $user->roles()->attach($rolePegawai->id);
        }

        $pegawai->update(['user_id' => $user->id]);

        return redirect()->back()->with('success', "Akun untuk {$pegawai->nama_lengkap} berhasil dibuat! Email: {$emailPegawai}");
    }

    /**
     * GENERATE AKUN PEGAWAI MASSAL
     */
    public function generateAkunMassal()
    {
        set_time_limit(0);

        $pegawaiBelumPunyaAkun = Pegawai::whereNull('user_id')
            ->where('status_keaktifan', 'Aktif')
            ->get();

        if ($pegawaiBelumPunyaAkun->isEmpty()) {
            return redirect()->back()->with('info', 'Semua pegawai aktif sudah memiliki akun login.');
        }

        $profil = DB::table('profil_sekolah')->first();
        $namaSekolahBersih = $profil ? strtolower(str_replace(' ', '', $profil->nama_sekolah)) : 'sekolah';
        $domainSekolah = '@' . $namaSekolahBersih . '.sch.id';

        $counter = 0;
        $passwordHash = Hash::make('@Sekolah4#');
        $rolesMap = Role::pluck('id', 'name')->toArray();

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

                if (User::where('email', $emailPegawai)->exists()) {
                    $emailPegawai = strtolower($prefixEmail) . rand(1, 9) . $domainSekolah;
                }

                $user = User::create([
                    'name' => $pegawai->nama_lengkap,
                    'email' => $emailPegawai,
                    'password' => $passwordHash, 
                    'is_approved' => true,
                ]);

                $roleTargetName = strtolower(str_replace(' ', '-', trim($pegawai->jenis_ptk)));

                if (array_key_exists($roleTargetName, $rolesMap)) {
                    $user->roles()->attach($rolesMap[$roleTargetName]);
                } else {
                    if (array_key_exists('pegawai', $rolesMap)) {
                        $user->roles()->attach($rolesMap['pegawai']);
                    } elseif (array_key_exists('staf', $rolesMap)) {
                        $user->roles()->attach($rolesMap['staf']);
                    }
                }

                $pegawai->update(['user_id' => $user->id]);
                $counter++;
            }

            DB::commit();
            return redirect()->back()->with('success', "Berhasil men-generate {$counter} akun pegawai secara dinamis!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate massal pegawai: ' . $e->getMessage());
        }
    }
}