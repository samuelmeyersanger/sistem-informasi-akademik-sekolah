<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenis_ptk = $request->input('jenis_ptk');

        $pegawai = Pegawai::with(['user', 'semester'])
            ->when($search, function ($query, $search) {
                return $query->where('nama_lengkap', 'like', "%{$search}%")
                             ->orWhere('nip', 'like', "%{$search}%")
                             ->orWhere('nuptk', 'like', "%{$search}%");
            })
            ->when($jenis_ptk, function ($query, $jenis_ptk) {
                return $query->where('jenis_ptk', $jenis_ptk);
            })
            ->paginate(10);

        // Mengambil data pendukung untuk dropdown di form/modal
        $user_list = User::all(); 
        $semester_list = Semester::all(); 

        return view('kepegawaian.pegawai.index', compact('pegawai', 'user_list', 'semester_list'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => 'nullable|exists:users,id',
            'semester_id'       => 'nullable|exists:semester,id',
            'nama_lengkap'      => 'required|string|max:255',
            'jenis_kelamin'     => 'required|in:Laki-Laki,Perempuan',
            'nip'               => 'nullable|string|unique:pegawai,nip',
            'nuptk'             => 'nullable|string|unique:pegawai,nuptk',
            'status_pegawai'    => 'required|in:PNS,PPPK,HONORER',
            'pangkat_golongan'  => 'nullable|string|max:255',
            'jenis_ptk'         => 'required|in:Kepala Sekolah,Guru,Tenaga Kependidikan',
            'email'             => 'nullable|email|max:255',
        ]);

        Pegawai::create($validated);

        return redirect()->back()->with('success', 'Data pegawai baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = $request->validate([
            'user_id'           => 'nullable|exists:users,id',
            'semester_id'       => 'nullable|exists:semester,id',
            'nama_lengkap'      => 'required|string|max:255',
            'jenis_kelamin'     => 'required|in:Laki-Laki,Perempuan',
            'nip'               => 'nullable|string|unique:pegawai,nip,' . $id,
            'nuptk'             => 'nullable|string|unique:pegawai,nuptk,' . $id,
            'status_pegawai'    => 'required|in:PNS,PPPK,HONORER',
            'pangkat_golongan'  => 'nullable|string|max:255',
            'jenis_ptk'         => 'required|in:Kepala Sekolah,Guru,Tenaga Kependidikan',
            'status_keaktifan'  => 'required|in:Aktif,Mutasi,Pensiun',
            'email'             => 'nullable|email|max:255',
            
            // Validasi Kondisional Mutasi
            'tanggal_mutasi'    => 'required_if:status_keaktifan,Mutasi|nullable|date',
            'alasan_mutasi'     => 'required_if:status_keaktifan,Mutasi|nullable|string',
            'sekolah_tujuan'    => 'required_if:status_keaktifan,Mutasi|nullable|string|max:255',
            'file_surat_mutasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            
            // Validasi Kondisional Pensiun
            'tanggal_pensiun'    => 'required_if:status_keaktifan,Pensiun|nullable|date',
            'file_surat_pensiun' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle Upload File Surat Mutasi
        if ($request->hasFile('file_surat_mutasi')) {
            if ($pegawai->file_surat_mutasi) {
                Storage::delete($pegawai->file_surat_mutasi);
            }
            $validated['file_surat_mutasi'] = $request->file('file_surat_mutasi')->store('surat_mutasi');
        }

        // Handle Upload File Surat Pensiun
        if ($request->hasFile('file_surat_pensiun')) {
            if ($pegawai->file_surat_pensiun) {
                Storage::delete($pegawai->file_surat_pensiun);
            }
            $validated['file_surat_pensiun'] = $request->file('file_surat_pensiun')->store('surat_pensiun');
        }

        // Bersihkan data mutasi/pensiun jika status diubah kembali ke 'Aktif'
        if ($request->status_keaktifan === 'Aktif') {
            $validated['tanggal_mutasi'] = null;
            $validated['alasan_mutasi'] = null;
            $validated['sekolah_tujuan'] = null;
            $validated['tanggal_pensiun'] = null;
        }

        $pegawai->update($validated);

        return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete(); // Soft Delete sesuai konfigurasi model

        return redirect()->back()->with('success', 'Data pegawai berhasil dihapus (Soft Delete).');
    }
}