<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\PetugasPiket;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;

class PetugasPiketController extends Controller
{
    public function index()
    {
        // Ambil semua jadwal piket beserta relasi penanggung jawabnya
        $jadwalPiket = PetugasPiket::with('penanggungJawab')->orderBy('id', 'asc')->get();
        
        // Ambil data pegawai untuk pilihan dropdown di modal/form
        $daftarPegawai = Pegawai::orderBy('nama_lengkap', 'asc')->get();
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $semesterAktif = Semester::where('is_aktif', true)->first();

        return view('piket.petugas.index', compact('jadwalPiket', 'daftarPegawai', 'tahunAktif', 'semesterAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'penanggung_jawab_id' => 'nullable|exists:pegawai,id',
            'anggota_piket' => 'required|array',
            'anggota_piket.*' => 'exists:pegawai,id'
        ]);

        $tahunAktif = TahunAjaran::where('status', 'Aktif')->first();
        $semesterAktif = Semester::where('status', 'Aktif')->first();

        PetugasPiket::create([
            'hari' => $request->hari,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'anggota_piket' => $request->anggota_piket, // Otomatis tercasting jadi JSON karena model
            'tahun_ajaran_id' => $tahunAktif?->id,
            'semester_id' => $semesterAktif?->id,
        ]);

        return redirect()->route('piket.petugas.index')->with('success', 'Jadwal petugas piket berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'penanggung_jawab_id' => 'nullable|exists:pegawai,id',
            'anggota_piket' => 'required|array',
            'anggota_piket.*' => 'exists:pegawai,id'
        ]);

        $petugas = PetugasPiket::findOrFail($id);
        $petugas->update([
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'anggota_piket' => $request->anggota_piket
        ]);

        return redirect()->route('piket.petugas.index')->with('success', 'Jadwal petugas piket berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $petugas = PetugasPiket::findOrFail($id);
        $petugas->delete();

        return redirect()->route('piket.petugas.index')->with('success', 'Jadwal petugas piket berhasil dihapus.');
    }
}