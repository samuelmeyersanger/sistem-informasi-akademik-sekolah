<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\WaliSiswa;
use App\Models\RiwayatStatusSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $validated = $request->validate([
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
            'no_peserta_un' => 'nullable|string|max:50',
            'asal_sekolah' => 'required|string|max:150',
            'anak_ke' => 'required|integer|min:1',
            'tingkat' => 'required|in:7,8,9',
            'diterima_pada_tanggal' => 'required|date',
            'status_siswa' => 'required|in:Aktif,Lulus,Keluar,Mutasi',
            'semester_id' => 'nullable|exists:semester,id',
            
            // Validasi Array 3 Wali
            'wali' => 'required|array|min:1',
            'wali.*.nama_lengkap' => 'nullable|string|max:255',
            'wali.*.nik' => 'required_with:wali.*.nama_lengkap|nullable|string|size:16',
            'wali.*.nomor_hp' => 'nullable|string|max:20',
            'wali.*.hubungan' => 'required_with:wali.*.nama_lengkap|nullable|in:Ayah,Ibu,Wali',
        ]);

        DB::beginTransaction();
        try {
            // Simpan siswa (kolom kelas_id otomatis null karena tidak ada di form)
            $siswa = Siswa::create($request->except('wali'));

            // Loop input data wali
            if ($request->has('wali')) {
                foreach ($request->wali as $rowWali) {
                    if (!empty($rowWali['nama_lengkap'])) {
                        $wali = WaliSiswa::firstOrCreate(
                            ['nik' => $rowWali['nik']],
                            [
                                'nama_lengkap' => $rowWali['nama_lengkap'],
                                'nomor_hp' => $rowWali['nomor_hp'] ?? '-'
                            ]
                        );

                        $siswa->wali()->attach($wali->id, [
                            'hubungan' => $rowWali['hubungan']
                        ]);
                    }
                }
            }

            // Otomatis catat log awal status 'Aktif' ke Riwayat Status Siswa
            RiwayatStatusSiswa::create([
                'siswa_id' => $siswa->id,
                'semester_id' => $siswa->semester_id,
                'status' => $siswa->status_siswa,
                'metadata' => ['keterangan' => 'Pendaftaran pertama siswa baru di sistem']
            ]);

            DB::commit();
            return redirect()->route('kesiswaan.siswa')->with('success', 'Data siswa dan wali berhasil didaftarkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
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
            // Update identitas core (Abaikan status_siswa dan kelas_id di sini karena diurus menu khusus)
            $siswa->update($request->except(['wali', 'status_siswa', 'kelas_id']));

            if ($request->has('wali')) {
                $syncData = [];
                foreach ($request->wali as $rowWali) {
                    if (!empty($rowWali['nama_lengkap'])) {
                        $wali = WaliSiswa::firstOrCreate(
                            ['nik' => $rowWali['nik']],
                            [
                                'nama_lengkap' => $rowWali['nama_lengkap'],
                                'nomor_hp' => $rowWali['nomor_hp'] ?? '-'
                            ]
                        );
                        $syncData[$wali->id] = ['hubungan' => $rowWali['hubungan']];
                    }
                }
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
}