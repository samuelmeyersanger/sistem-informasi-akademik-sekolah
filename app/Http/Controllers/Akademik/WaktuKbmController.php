<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\WaktuKbm;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class WaktuKbmController extends Controller
{
    /**
     * Menampilkan daftar konfigurasi slot waktu Kegiatan Belajar Mengajar (KBM).
     * Data dikelompokkan atau diurutkan berdasarkan hari dan urutan jam ke.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $hariFilter = $request->get('hari');

        $waktuKbm = WaktuKbm::latest() // atau query lainnya
        ->orderByRaw("
            CASE hari 
                WHEN 'Senin' THEN 1
                WHEN 'Selasa' THEN 2
                WHEN 'Rabu' THEN 3
                WHEN 'Kamis' THEN 4
                WHEN 'Jumat' THEN 5
                WHEN 'Sabtu' THEN 6
                ELSE 7 
            END
        ")
        ->orderBy('jam_ke', 'asc')
        ->paginate(20); // sesuaikan jika Anda pakai paginate atau get()

        return view('akademik.waktu_kbm.index', compact('waktuKbm'));
    }

    /**
     * Menyimpan data slot waktu KBM baru ke dalam database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'hari'          => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])],
            'jam_ke'        => 'required|string|min:0',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'kegiatan'      => ['required', Rule::in(['Upacara', 'G7', 'Korikuler', 'MBG', 'KBM', 'Istirahat'])],
        ]);

        WaktuKbm::create([
            'hari'          => $request->hari,
            'jam_ke'        => $request->jam_ke,
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'kegiatan'      => $request->kegiatan,
        ]);

        return redirect()->route('akademik.waktu-kbm.index')
            ->with('success', "Slot waktu KBM hari {$request->hari}, Jam Ke-{$request->jam_ke} ({$request->kegiatan}) berhasil ditambahkan.");
    }

    /**
     * Memperbarui data konfigurasi slot waktu KBM di database.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $waktu = WaktuKbm::findOrFail($id);

        $request->validate([
            'hari'          => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])],
            'jam_ke'        => 'required|string|min:0',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'kegiatan'      => ['required', Rule::in(['Upacara', 'G7', 'Korikuler', 'MBG', 'KBM', 'Istirahat'])],
        ]);

        $waktu->update([
            'hari'          => $request->hari,
            'jam_ke'        => $request->jam_ke,
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'kegiatan'      => $request->kegiatan,
        ]);

        return redirect()->route('akademik.waktu-kbm.index')
            ->with('success', "Perubahan konfigurasi waktu KBM berhasil disimpan.");
    }

    /**
     * Menghapus slot waktu KBM menggunakan sistem Soft Delete.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $waktu = WaktuKbm::findOrFail($id);
        $hari = $waktu->hari;
        $jamKe = $waktu->jam_ke;
        
        $waktu->delete();

        return redirect()->route('akademik.waktu-kbm.index')
            ->with('success', "Slot waktu KBM hari {$hari} Jam Ke-{$jamKe} berhasil dihapus.");
    }
}