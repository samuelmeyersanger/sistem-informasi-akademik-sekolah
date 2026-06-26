<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\WaliSiswa;
use App\Models\RiwayatStatusSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\SiswaWaliImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TemplateImportSiswaExport;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;

class SiswaController extends Controller
{
    /**
     * 1. Menampilkan Daftar Semua Siswa (Master)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter_tingkat = $request->input('tingkat');
        $filter_status = $request->input('status');

        // Eager load kelas, semester, dan wali untuk mencegah N+1 Query Problem
        $query = Siswa::with(['kelas', 'semester', 'wali'])->orderBy('nama_lengkap', 'asc');

        // Pencarian multi-kolom
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nipd', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($filter_tingkat) {
            $query->where('tingkat', $filter_tingkat);
        }

        if ($filter_status) {
            $query->where('status_siswa', $filter_status);
        }

        $siswa = $query->paginate(15)->withQueryString();
        
        // Ambil data semester yang sedang aktif untuk pilihan form tambah
        $semester_list = Semester::with('tahunAjaran')
            ->where('is_aktif', true)
            ->get(); 

        return view('kesiswaan.siswa.index', compact('siswa', 'semester_list'));
    }

    /**
     * 2. Menyimpan Data Siswa Baru & Banyak Wali Sekaligus
     */
    public function store(Request $request)
    {
        // Validasi data input sebelum diproses oleh database
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nipd' => 'required|string|unique:siswa,nipd',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nisn' => 'nullable|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'nik' => 'required|string|size:16|unique:siswa,nik',
            'agama' => 'required|in:Islam,Kristen,Katholik,Hindu,Budha',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kelurahan_desa' => 'required|string|max:100',
            'alamat_lengkap' => 'required|string',
            'rt' => 'required|string|max:5',
            'rw' => 'required|string|max:5',
            'kode_pos' => 'required|string|max:10',
            'nomor_hp' => 'required|string|max:20',
            'asal_sekolah' => 'required|string|max:150',
            'no_peserta_un' => 'required|string|max:150',
            'anak_ke' => 'required|integer|min:1', // <-- Validasi ditambahkan agar tidak null
            'tingkat' => 'required|in:7,8,9',
            'semester_id' => 'required|exists:semester,id',
            'diterima_pada_tanggal' => 'required|date',

            'wali' => 'nullable|array',
            'wali.*.nama_lengkap' => 'nullable|string|max:255',
            'wali.*.nik' => 'required_with:wali.*.nama_lengkap|nullable|string|size:16',
            'wali.*.nomor_hp' => 'nullable|string|max:20',
            'wali.*.hubungan' => 'required_with:wali.*.nama_lengkap|nullable|in:Ayah,Ibu,Wali',
        ]);

        // Jalankan database transaction demi keamanan data (jika salah satu error, semua dibatalkan)
        DB::beginTransaction();

        try {
            // 1. Simpan Data Utama Siswa terlebih dahulu
            $siswa = Siswa::create([
                'nama_lengkap'              => $request->nama_lengkap,
                'nik'                       => $request->nik,
                'nipd'                      => $request->nipd,
                'nisn'                      => $request->nisn,
                'jenis_kelamin'             => $request->jenis_kelamin,
                'tempat_lahir'              => $request->tempat_lahir,
                'tanggal_lahir'             => $request->tanggal_lahir,
                'agama'                     => $request->agama,
                'nomor_hp'                  => $request->nomor_hp,
                'asal_sekolah'              => $request->asal_sekolah,
                'provinsi'                  => $request->provinsi,
                'kota'                      => $request->kota,
                'kecamatan'                 => $request->kecamatan,
                'kelurahan_desa'            => $request->kelurahan_desa,
                'alamat_lengkap'            => $request->alamat_lengkap,
                'rt'                        => $request->rt,
                'rw'                        => $request->rw,
                'kode_pos'                  => $request->kode_pos,
                'tingkat'                   => $request->tingkat,
                'semester_id'               => $request->semester_id,
                'diterima_pada_tanggal'     => $request->diterima_pada_tanggal,
                'anak_ke'                   => $request->anak_ke, // <-- Kolom anak_ke sekarang ikut disimpan
                'no_peserta_un'             => $request->no_peserta_un,
                'status_siswa'              => 'Aktif',
            ]);

            // 2. Looping data Ayah, Ibu, dan Wali yang dikirim dari form
            if ($request->has('wali') && is_array($request->wali)) {
                foreach ($request->wali as $inputWali) {
                    
                    // Jika baris input nama_lengkap kosong (terutama pada inputan Wali), lewati baris ini
                    if (empty($inputWali['nama_lengkap'])) {
                        continue;
                    }

                    // Simpan label hubungan ('Ayah' / 'Ibu' / 'Wali') ke variabel terpisah
                    $hubungan = $inputWali['hubungan'] ?? 'Wali';

                    // Eksekusi penyimpanan data Wali ke tabel `wali_siswa` secara utuh sesuai skema migration
                    $wali = WaliSiswa::create([
                        'nama_lengkap'        => $inputWali['nama_lengkap'],
                        'nik'                 => $inputWali['nik'] ?? null,
                        'jenis_kelamin'       => $inputWali['jenis_kelamin'] ?? ($hubungan == 'Ibu' ? 'Perempuan' : 'Laki-laki'),
                        'tempat_lahir'        => $inputWali['tempat_lahir'] ?? null,
                        'tanggal_lahir'       => $inputWali['tanggal_lahir'] ?? null,
                        'agama'               => $inputWali['agama'] ?? null,
                        'pendidikan_terakhir' => $inputWali['pendidikan_terakhir'] ?? null,
                        'pekerjaan'           => $inputWali['pekerjaan'] ?? null,
                        'penghasilan_bulanan' => $inputWali['penghasilan_bulanan'] ?? 0,
                        'alamat_lengkap'      => $inputWali['alamat_lengkap'] ?? $siswa->alamat_lengkap,
                        'rt'                  => $inputWali['rt'] ?? null,
                        'rw'                  => $inputWali['rw'] ?? null,
                        'kelurahan_desa'      => $inputWali['kelurahan_desa'] ?? null,
                        'kecamatan'           => $inputWali['kecamatan'] ?? null,
                        'kota'                => $inputWali['kota'] ?? null,
                        'provinsi'            => $inputWali['provinsi'] ?? null,
                        'kode_pos'            => $inputWali['kode_pos'] ?? null,
                        'nomor_hp'            => $inputWali['nomor_hp'] ?? null,
                        'email'               => $inputWali['email'] ?? null,
                        'nomor_hp_darurat'    => $inputWali['nomor_hp_darurat'] ?? null,
                        'catatan'             => $inputWali['catatan'] ?? null,
                    ]);

                    // 3. Rekatkan data Wali ke Siswa lewat tabel Pivot `siswa_wali` beserta status hubungannya
                    $siswa->wali()->attach($wali->id, [
                        'hubungan'   => $hubungan,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('kesiswaan.siswa')->with('success', 'Data Master Siswa beserta Orang Tua berhasil tersimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal mendaftarkan data: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * 3. Melihat Detail Profil Siswa (Melihat Riwayat Kelas, Status, & Dokumen)
     */
    public function show($id)
    {
        // Memuat seluruh relasi riwayat dan dokumen pendukung siswa terkait
        $siswa = Siswa::with([
            'kelas', 
            'semester.tahunAjaran', 
            'wali', 
            'dokumen', 
            'riwayatKelas.kelas', 
            'riwayatKelas.semester.tahunAjaran', 
            'riwayatStatus.semester.tahunAjaran'
        ])->findOrFail($id);

        // Digunakan jika admin ingin mengubah status operasional siswa dari halaman profile
        $semester_aktif = Semester::where('is_aktif', true)->first();

        return view('kesiswaan.siswa.show', compact('siswa', 'semester_aktif'));
    }

    /**
     * 4. Memperbarui Profil Data Diri & Data Wali Siswa
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nipd' => 'required|string|unique:siswa,nipd,' . $siswa->id,
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nisn' => 'nullable|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'nik' => 'required|string|size:16|unique:siswa,nik,' . $siswa->id,
            'agama' => 'required|in:Islam,Kristen,Katholik,Hindu,Budha',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kelurahan_desa' => 'required|string|max:100',
            'alamat_lengkap' => 'required|string',
            'rt' => 'required|string|max:5',
            'rw' => 'required|string|max:5',
            'kode_pos' => 'required|string|max:10',
            'nomor_hp' => 'required|string|max:20',
            'no_peserta_un' => 'nullable|string|max:50',
            'asal_sekolah' => 'required|string|max:150',
            'anak_ke' => 'required|integer|min:1',
            'tingkat' => 'required|in:7,8,9',
            'diterima_pada_tanggal' => 'required|date',

            'wali' => 'nullable|array',
            'wali.*.nama_lengkap' => 'nullable|string|max:255',
            'wali.*.nik' => 'required_with:wali.*.nama_lengkap|nullable|string|size:16',
            'wali.*.nomor_hp' => 'nullable|string|max:20',
            'wali.*.hubungan' => 'required_with:wali.*.nama_lengkap|nullable|in:Ayah,Ibu,Wali',
        ]);

        DB::beginTransaction();
        try {
            // Update identitas core siswa (Abaikan status_siswa dan kelas_id di sini karena diurus menu khusus)
            $siswa->update($request->except(['wali', 'status_siswa', 'kelas_id']));

            if ($request->has('wali')) {
                $syncData = [];
                foreach ($request->wali as $rowWali) {
                    if (!empty($rowWali['nama_lengkap'])) {
                        
                        // Gunakan NIK atau kriteria unik untuk update/create data wali
                        $wali = WaliSiswa::updateOrCreate(
                            ['nik' => $rowWali['nik']],
                            [
                                'nama_lengkap'        => $rowWali['nama_lengkap'],
                                'jenis_kelamin'       => $rowWali['jenis_kelamin'] ?? ($rowWali['hubungan'] == 'Ibu' ? 'Perempuan' : 'Laki-laki'),
                                'tempat_lahir'        => $rowWali['tempat_lahir'] ?? null,
                                'tanggal_lahir'       => $rowWali['tanggal_lahir'] ?? null,
                                'agama'               => $rowWali['agama'] ?? null,
                                'pendidikan_terakhir' => $rowWali['pendidikan_terakhir'] ?? null,
                                'pekerjaan'           => $rowWali['pekerjaan'] ?? null,
                                'penghasilan_bulanan' => $rowWali['penghasilan_bulanan'] ?? 0,
                                'alamat_lengkap'      => $rowWali['alamat_lengkap'] ?? $siswa->alamat_lengkap,
                                'rt'                  => $rowWali['rt'] ?? null,
                                'rw'                  => $rowWali['rw'] ?? null,
                                'kelurahan_desa'      => $rowWali['kelurahan_desa'] ?? null,
                                'kecamatan'           => $rowWali['kecamatan'] ?? null,
                                'kota'                => $rowWali['kota'] ?? null,
                                'provinsi'            => $rowWali['provinsi'] ?? null,
                                'kode_pos'            => $rowWali['kode_pos'] ?? null,
                                'nomor_hp'            => $rowWali['nomor_hp'] ?? null,
                                'email'               => $rowWali['email'] ?? null,
                                'nomor_hp_darurat'    => $rowWali['nomor_hp_darurat'] ?? null,
                                'catatan'             => $rowWali['catatan'] ?? null,
                            ]
                        );
                        
                        // Siapkan data sync ke tabel pivot siswa_wali
                        $syncData[$wali->id] = [
                            'hubungan'   => $rowWali['hubungan'],
                            'updated_at' => now()
                        ];
                    }
                }
                
                // Sinkronisasi data di tabel pivot siswa_wali (memutuskan yang dihapus, memperbarui yang ada)
                $siswa->wali()->sync($syncData);
            }

            DB::commit();
            return redirect()->route('kesiswaan.siswa.show', $siswa->id)->with('success', 'Profil personal siswa berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal mengubah data: ' . $e->getMessage()]);
        }
    }

    /**
     * 5. Mengubah Status Siswa Secara Individual (Mutasi/Keluar/Lulus) + Isi Metadata
     */
    public function updateStatus(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'status_siswa' => 'required|in:Aktif,Lulus,Keluar,Mutasi',
            'semester_id' => 'required|exists:semester,id',
            'alasan' => 'nullable|string',
            'sekolah_tujuan' => 'nullable|string', // Khusus jika status Mutasi
            'no_ijazah' => 'nullable|string',      // Khusus jika status Lulus
        ]);

        DB::beginTransaction();
        try {
            // 1. Update status di tabel utama siswa
            $siswa->update(['status_siswa' => $request->status_siswa]);

            // 2. Satukan data pendukung dinamis ke dalam array metadata
            $metadata = [
                'alasan' => $request->alasan ?? 'Tidak ada keterangan tambahan.',
                'sekolah_tujuan' => $request->sekolah_tujuan,
                'no_ijazah' => $request->no_ijazah,
                'tanggal_eksekusi' => now()->format('Y-m-d H:i:s')
            ];

            // 3. Masukkan ke log Riwayat Status Siswa
            RiwayatStatusSiswa::create([
                'siswa_id' => $siswa->id,
                'semester_id' => $request->semester_id,
                'status' => $request->status_siswa,
                'metadata' => $metadata
            ]);

            DB::commit();
            return redirect()->route('kesiswaan.siswa.show', $siswa->id)->with('success', 'Status operasional akademik siswa berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
    }

    /**
     * 6. Menghapus Data Siswa (Soft Delete)
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Putus hubungan pivot wali_siswa sebelum di-delete
        $siswa->wali()->detach();
        $siswa->delete();

        return redirect()->route('kesiswaan.siswa')->with('success', 'Data rekam jejak siswa berhasil dipindahkan ke soft-deletes.');
    }

    /**
     * 7. Eksekusi Mass-Import Siswa & Wali Sekaligus
     */
    public function importSiswaWali(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:4096' // Batas maks 4MB
        ]);

        try {
            Excel::import(new SiswaWaliImport, $request->file('file_excel'));
            
            return redirect()->route('kesiswaan.siswa')
                ->with('success', 'Massal data Siswa dan data Wali berhasil diimport bersamaan!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengimport data berkas: ' . $e->getMessage()]);
        }
    }
    /**
     * Mengunduh berkas template format Excel untuk import
     */
    public function downloadTemplate()
    {
        return Excel::download(new TemplateImportSiswaExport, 'template_import_siswa_dan_wali.xlsx');
    }

    // 1. GENERATE INDIVIDU
    public function generateAkunIndividu($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->user_id) {
            return redirect()->back()->with('error', 'Siswa ini sudah memiliki akun.');
        }

        // --- AMBIL NAMA SEKOLAH DARI DATABASE ---
        $profil = DB::table('profil_sekolah')->first(); // Mengambil baris pertama tabel profil
        
        // Bersihkan nama sekolah: hilangkan spasi, ubah jadi huruf kecil
        $namaSekolahBersih = $profil ? strtolower(str_replace(' ', '', $profil->nama_sekolah)) : 'sekolah';
        
        // Gabungkan menjadi domain email resmi sekolah
        $domainSekolah = '@' . $namaSekolahBersih . '.sch.id'; 
        // ----------------------------------------

        $prefixEmail = $siswa->nipd ? trim($siswa->nipd) : strtolower(explode(' ', trim($siswa->nama_lengkap))[0]) . rand(10, 99);
        $emailSiswa = strtolower($prefixEmail) . $domainSekolah;

        if (User::where('email', $emailSiswa)->exists()) {
            $emailSiswa = strtolower($prefixEmail) . rand(1, 9) . $domainSekolah;
        }

        // 🟢 PERBAIKAN MULTIROLE: Hapus 'role' dari array, ganti dengan attachRole/assignRole
        $user = User::create([
            'name' => $siswa->nama_lengkap,
            'email' => $emailSiswa,
            'password' => Hash::make('siswa123'),
            'is_approved' => true,
        ]);

        // 🟢 CARA CUSTOM MULTIROLE:
        // Cari data role 'siswa' di tabel roles Anda
        $roleSiswa = Role::where('name', 'siswa')->first();
        if ($roleSiswa) {
            $user->roles()->attach($roleSiswa->id); // Masuk ke tabel user_role
        }

        $siswa->update(['user_id' => $user->id]);

        return redirect()->back()->with('success', "Akun untuk {$siswa->nama_lengkap} berhasil dibuat! Email: {$emailSiswa}");
    }

    // 2. GENERATE MASSAL
    public function generateAkunMassal()
    {
        // 1. Hilangkan batasan waktu 30 detik khusus untuk fungsi ini
        set_time_limit(0);

        // Ambil data siswa yang belum punya akun
        $siswaBelumPunyaAkun = Siswa::whereNull('user_id')->get();

        if ($siswaBelumPunyaAkun->isEmpty()) {
            return redirect()->back()->with('info', 'Semua siswa sudah memiliki akun login.');
        }

        // Ambil profil sekolah untuk domain email
        $profil = DB::table('profil_sekolah')->first();
        $namaSekolahBersih = $profil ? strtolower(str_replace(' ', '', $profil->nama_sekolah)) : 'sekolah';
        $domainSekolah = '@' . $namaSekolahBersih . '.sch.id';

        $counter = 0;
        $passwordHash = Hash::make('siswa123'); // Cukup hash 1 kali di luar loop agar hemat CPU!

        // 🟢 AMBIL ROLE SISWA (Cukup cari 1 kali di luar foreach agar performa cepat)
        $roleSiswa = Role::where('name', 'siswa')->first();

        if (!$roleSiswa) {
            return redirect()->back()->with('error', 'Role "siswa" tidak ditemukan di database. Pastikan tabel roles sudah terisi.');
        }

        // 2. Gunakan Database Transaction agar proses insert massal jauh lebih cepat
        DB::beginTransaction();

        try {
            foreach ($siswaBelumPunyaAkun as $siswa) {
                $prefixEmail = $siswa->nipd ? trim($siswa->nipd) : strtolower(explode(' ', trim($siswa->nama_lengkap))[0]) . rand(10, 99);
                $emailSiswa = strtolower($prefixEmail) . $domainSekolah;

                // Cek keunikan email
                if (User::where('email', $emailSiswa)->exists()) {
                    $emailSiswa = strtolower($prefixEmail) . rand(1, 9) . $domainSekolah;
                }

                // Buat User
                $user = User::create([
                    'name' => $siswa->nama_lengkap,
                    'email' => $emailSiswa,
                    'password' => $passwordHash, 
                    'is_approved' => true, // Langsung aktif
                ]);

                // 🟢 PERBAIKAN CUSTOM MULTIROLE MASSAL (Mengisi tabel user_role Anda)
                $user->roles()->attach($roleSiswa->id);

                // Update Siswa
                $siswa->update(['user_id' => $user->id]);
                $counter++;
            }

            // Jika semua lancar, simpan ke database sekaligus
            DB::commit();

            return redirect()->back()->with('success', "Berhasil men-generate {$counter} akun siswa! Format Email: nisn{$domainSekolah}");

        } catch (\Exception $e) {
            // Jika ada error di tengah jalan, batalkan semua agar data tidak korup
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate massal: ' . $e->getMessage());
        }
    }
}