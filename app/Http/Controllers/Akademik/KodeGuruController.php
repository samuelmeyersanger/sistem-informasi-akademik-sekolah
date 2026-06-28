<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\KodeGuru;
use App\Models\Pegawai;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KodeGuruController extends Controller
{
    /**
     * Menampilkan daftar kode guru dengan eager loading tabel pivot
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');

        // Memuat relasi mataPelajarans beserta data di tabel pivotnya
        $query = KodeGuru::with(['pegawai', 'mataPelajarans'])->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($p) use ($search) {
                      $p->where('nama_lengkap', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mataPelajarans', function($m) use ($search) {
                      $m->where('nama_mapel', 'like', "%{$search}%");
                  });
            });
        }

        $kodeGuru = $query->paginate(15)->withQueryString();
        $daftarPegawai = Pegawai::orderBy('nama_lengkap', 'asc')->get();
        $daftarMapel = MataPelajaran::orderBy('nomor_urut', 'asc')->get();

        return view('akademik.kode_guru.index', compact('kodeGuru', 'daftarPegawai', 'daftarMapel'));
    }

    /**
     * Menyimpan data kode guru baru beserta multiple mata pelajaran
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kode'                => 'required|string|max:50|unique:kode_guru,kode',
            'pegawai_id'          => 'required|exists:pegawai,id',
            'mata_pelajaran_ids'  => 'required|array', // Wajib bertipe array karena multiselect
            'mata_pelajaran_ids.*'=> 'exists:mata_pelajaran,id',
        ]);

        // 1. Buat data induk Kode Guru terlebih dahulu
        $kodeGuru = KodeGuru::create([
            'kode'       => $request->kode,
            'pegawai_id' => $request->pegawai_id,
        ]);

        // 2. Siapkan data porsi jam mengajar dari masing-masing mapel untuk disuntik ke tabel pivot
        $pivotData = [];
        foreach ($request->mata_pelajaran_ids as $mapelId) {
            $mapel = MataPelajaran::find($mapelId);
            if ($mapel) {
                $pivotData[$mapelId] = [
                    'jam_mengajar_porsi' => $mapel->jumlah_jam // Mengambil otomatis dari master mapel
                ];
            }
        }

        // 3. Gabungkan (Attach) ke tabel pivot
        $kodeGuru->mataPelajarans()->attach($pivotData);

        return redirect()->route('akademik.kode-guru.index')
            ->with('success', "Kode Guru '{$request->kode}' berhasil ditambahkan dengan " . count($pivotData) . " mata pelajaran.");
    }

    /**
     * Memperbarui data kode guru dan menyinkronkan ulang data multiselect
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $kodeGuru = KodeGuru::findOrFail($id);

        $request->validate([
            'kode'                => 'required|string|max:50|unique:kode_guru,kode,' . $kodeGuru->id,
            'pegawai_id'          => 'required|exists:pegawai,id',
            'mata_pelajaran_ids'  => 'required|array',
            'mata_pelajaran_ids.*'=> 'exists:mata_pelajaran,id',
        ]);

        $kodeGuru->update([
            'kode'       => $request->kode,
            'pegawai_id' => $request->pegawai_id,
        ]);

        // Susun ulang data porsi jam untuk metode sinkronisasi (sync)
        $pivotData = [];
        foreach ($request->mata_pelajaran_ids as $mapelId) {
            $mapel = MataPelajaran::find($mapelId);
            if ($mapel) {
                $pivotData[$mapelId] = [
                    'jam_mengajar_porsi' => $mapel->jumlah_jam
                ];
            }
        }

        // sync() otomatis menghapus mapel lama yang tidak dipilih dan memasukkan mapel baru
        $kodeGuru->mataPelajarans()->sync($pivotData);

        return redirect()->route('akademik.kode-guru.index')
            ->with('success', "Perubahan data Kode Guru '{$kodeGuru->kode}' berhasil disimpan.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $kodeGuru = KodeGuru::findOrFail($id);
        $kode = $kodeGuru->kode;
        
        // Relasi pivot otomatis terhapus karena kita menggunakan onDelete('cascade') di migration
        $kodeGuru->delete();

        return redirect()->route('akademik.kode-guru.index')
            ->with('success', "Kode Guru '{$kode}' berhasil dinonaktifkan.");
    }
}