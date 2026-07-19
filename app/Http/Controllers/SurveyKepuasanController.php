<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkmLayanan;
use App\Models\SkmUnsur;
use App\Models\SkmResponden;
use App\Models\SkmJawaban;
use Illuminate\Support\Facades\DB;

class SurveyKepuasanController extends Controller
{
    // 1. Menampilkan Halaman Kuesioner
    public function index()
    {
        // Hanya memunculkan layanan & pertanyaan yang statusnya Aktif
        $layanans = SkmLayanan::where('status', true)->get();
        $unsurs = SkmUnsur::where('status', true)->orderBy('kode_unsur', 'asc')->get();
        
        return view('survey.index', compact('layanans', 'unsurs'));
    }

    // 2. Memproses Data Bintang dari Masyarakat
    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:skm_layanan,id',
            'jawaban'    => 'required|array', // Harus berupa array dari nilai bintang
        ]);

        DB::beginTransaction();
        try {
            // A. Simpan Profil Demografi Responden
            $responden = SkmResponden::create([
                'layanan_id'          => $request->layanan_id,
                'nama_lengkap'        => $request->nama_lengkap, // Boleh null (anonim)
                'nomor_hp'            => $request->nomor_hp,
                'jenis_kelamin'       => $request->jenis_kelamin,
                'umur'                => $request->umur,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'pekerjaan'           => $request->pekerjaan,
                'saran_masukan'       => $request->saran_masukan
            ]);

            // B. Simpan Penilaian (Looping setiap pertanyaan)
            // Asumsi HTML input-nya menggunakan name="jawaban[ID_UNSUR]" value="1-4"
            foreach ($request->jawaban as $unsur_id => $nilai) {
                SkmJawaban::create([
                    'responden_id' => $responden->id,
                    'unsur_id'     => $unsur_id,
                    'nilai'        => $nilai
                ]);
            }

            DB::commit();
            return redirect()->route('survey.success')->with('success', 'Terima kasih atas penilaian Anda!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mengirim kuesioner.')->withInput();
        }
    }

    // 3. Menampilkan Halaman Sukses
    public function success()
    {
        return view('survey.success');
    }
}