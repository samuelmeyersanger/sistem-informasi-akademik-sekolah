<?php

namespace App\Http\Controllers\Ekskul;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\AnggotaEkstrakurikuler;
use App\Models\PrestasiEkstrakurikuler;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EkstrakurikulerController extends Controller
{
    /**
     * 1. HALAMAN UTAMA: Daftar Semua Ekskul
     */
    public function index()
    {
        // 👇 PENGAMAN: Hanya munculkan ekskul binaannya
        $ekskul = Ekstrakurikuler::aksesPembina(auth()->user())->with('pembina')->withCount('anggota')->get();
        
        $pembina = Pegawai::all(); 

        return view('ekskul.index', compact('ekskul', 'pembina'));
    }

    /**
     * 2. FORM TAMBAH EKSKUL (Biarkan, biasanya hanya admin yang bisa akses ini lewat menu/blade)
     */
    public function create()
    {
        $pembina = Pegawai::all(); 
        return view('ekskul.create', compact('pembina'));
    }

    /**
     * 3. PROSES SIMPAN EKSKUL BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'pembina_id' => 'nullable|exists:pegawai,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'hari_latihan' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'deskripsi' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('ekskul/logo', 'public');
        }

        Ekstrakurikuler::create($data);

        return redirect()->route('ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan!');
    }

    /**
     * 4. HUB SENTRAL (SHOW): Dashboard Detail Ekskul (Anggota, Absen, Prestasi)
     */
    public function show($id)
    {
        // 👇 PENGAMAN: Cegah intip ekskul lain
        $ekskul = Ekstrakurikuler::aksesPembina(auth()->user())
                    ->with(['pembina', 'anggota.siswa', 'anggota.kelas', 'prestasi'])
                    ->findOrFail($id);
        
        $siswaBelumMendaftar = Siswa::whereDoesntHave('ekskulYangDiikuti', function($query) use ($id) {
            $query->where('ekstrakurikuler_id', $id);
        })->get();

        $kelas = Kelas::all();

        return view('ekskul.show', compact('ekskul', 'siswaBelumMendaftar', 'kelas'));
    }

    /**
     * 5. FORM EDIT EKSKUL
     */
    public function edit($id)
    {
        // 👇 PENGAMAN
        $ekskul = Ekstrakurikuler::aksesPembina(auth()->user())->findOrFail($id);
        $pembina = Pegawai::all();
        return view('ekskul.edit', compact('ekskul', 'pembina'));
    }

    /**
     * 6. PROSES UPDATE EKSKUL
     */
    public function update(Request $request, $id)
    {
        // 👇 PENGAMAN
        $ekskul = Ekstrakurikuler::aksesPembina(auth()->user())->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'pembina_id' => 'nullable|exists:pegawai,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'hari_latihan' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'deskripsi' => 'nullable|string',
            'is_aktif' => 'required|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('logo')) {
            if ($ekskul->logo) {
                Storage::disk('public')->delete($ekskul->logo);
            }
            $data['logo'] = $request->file('logo')->store('ekskul/logo', 'public');
        }

        $ekskul->update($data);

        return redirect()->route('ekstrakurikuler.index')->with('success', 'Data Ekstrakurikuler berhasil diperbarui!');
    }

    /**
     * 7. HAPUS EKSKUL (SOFT DELETES)
     */
    public function destroy($id)
    {
        // 👇 PENGAMAN
        $ekskul = Ekstrakurikuler::aksesPembina(auth()->user())->findOrFail($id);
        $ekskul->delete();

        return redirect()->route('ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil dinonaktifkan (Soft Delete).');
    }

    /**
     * 🟢 SUB-FITUR: PENGELOLAAN ANGGOTA (DARI HALAMAN SHOW)
     */
    public function storeAnggota(Request $request, $ekskulId)
    {
        // 👇 PENGAMAN: Pastikan dia pemilik ekskul ini
        Ekstrakurikuler::aksesPembina(auth()->user())->findOrFail($ekskulId);

        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'kelas_id' => 'required|exists:kelas,id',
            'nomor_hp' => 'nullable|string|max:15',
            'motivasi' => 'nullable|string',
            'tanggal_bergabung' => 'required|date',
        ]);

        $terdaftar = AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $ekskulId)
                                            ->where('siswa_id', $request->siswa_id)
                                            ->exists();
        if ($terdaftar) {
            return redirect()->back()->with('error', 'Siswa ini sudah terdaftar di ekstrakurikuler ini!');
        }

        AnggotaEkstrakurikuler::create([
            'ekstrakurikuler_id' => $ekskulId,
            'siswa_id' => $request->siswa_id,
            'kelas_id' => $request->kelas_id,
            'nomor_hp' => $request->nomor_hp,
            'motivasi' => $request->motivasi,
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'status' => 'Aktif',
        ]);

        return redirect()->route('ekstrakurikuler.show', $ekskulId)->with('success', 'Anggota baru berhasil ditambahkan!');
    }

    public function destroyAnggota($ekskulId, $anggotaId)
    {
        // 👇 PENGAMAN: Pastikan dia pemilik ekskul ini
        Ekstrakurikuler::aksesPembina(auth()->user())->findOrFail($ekskulId);

        $anggota = AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $ekskulId)->findOrFail($anggotaId);
        $anggota->delete();

        return redirect()->route('ekstrakurikuler.show', $ekskulId)->with('success', 'Anggota berhasil dikeluarkan dari ekskul.');
    }

    /**
     * 🟢 SUB-FITUR: PENGELOLAAN PRESTASI (DARI HALAMAN SHOW)
     */
    public function storePrestasi(Request $request, $ekskulId)
    {
        // 👇 PENGAMAN: Pastikan dia pemilik ekskul ini
        Ekstrakurikuler::aksesPembina(auth()->user())->findOrFail($ekskulId);

        $request->validate([
            'nama_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'juara' => 'nullable|string',
            'tanggal_prestasi' => 'required|date',
            'penyelenggara' => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
            'file_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->all();
        $data['ekstrakurikuler_id'] = $ekskulId;

        if ($request->hasFile('file_sertifikat')) {
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('ekskul/sertifikat', 'public');
        }

        if ($request->hasFile('file_dokumentasi')) {
            $data['file_dokumentasi'] = $request->file('file_dokumentasi')->store('ekskul/dokumentasi', 'public');
        }

        PrestasiEkstrakurikuler::create($data);

        return redirect()->route('ekstrakurikuler.show', $ekskulId)->with('success', 'Data prestasi berhasil direkam!');
    }

    public function destroyPrestasi($ekskulId, $prestasiId)
    {
        // 👇 PENGAMAN: Pastikan dia pemilik ekskul ini
        Ekstrakurikuler::aksesPembina(auth()->user())->findOrFail($ekskulId);

        $prestasi = PrestasiEkstrakurikuler::where('ekstrakurikuler_id', $ekskulId)->findOrFail($prestasiId);
        
        if($prestasi->file_sertifikat) Storage::disk('public')->delete($prestasi->file_sertifikat);
        if($prestasi->file_dokumentasi) Storage::disk('public')->delete($prestasi->file_dokumentasi);

        $prestasi->delete();

        return redirect()->route('ekstrakurikuler.show', $ekskulId)->with('success', 'Data prestasi berhasil dihapus.');
    }
}