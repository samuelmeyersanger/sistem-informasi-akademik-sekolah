<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Models\SuratMasuk;
use App\Models\DisposisiSurat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    /**
     * Tampilkan semua arsip surat masuk
     */
    public function index()
    {
        // Ambil data surat masuk terbaru beserta relasi user penginputnya
        $suratMasuk = SuratMasuk::with('penerima')->latest()->get();
        return view('surat.surat_masuk.index', compact('suratMasuk'));
    }

    /**
     * Halaman form tambah surat masuk (Tampilan untuk TU)
     */
    public function create()
    {
        return view('surat.surat_masuk.create');
    }

    /**
     * Proses simpan data surat masuk + Upload Berkas Scan PDF
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat'   => 'required|string|max:255',
            'asal_instansi' => 'required|string|max:255',
            'perihal'       => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tanggal_terima'=> 'required|date',
            'sifat'         => 'required|in:Biasa,Penting,Rahasia',
            'file_surat'    => 'required|mimes:pdf|max:5120', // Maksimal file PDF 5 MB
        ]);

        // Proses upload file berkas ke folder storage/app/public/surat_masuk
        $filePath = null;
        if ($request->hasFile('file_surat')) {
            $filePath = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        SuratMasuk::create([
            'nomor_surat'   => $request->nomor_surat,
            'asal_instansi' => $request->asal_instansi,
            'perihal'       => $request->perihal,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_terima'=> $request->tanggal_terima,
            'sifat'         => $request->sifat,
            'file_surat'    => $filePath,
            'penerima_id'   => auth()->id(), // TU yang sedang login
        ]);

        return redirect()->route('surat.masuk.index')->with('success', 'Surat masuk berhasil diarsipkan!');
    }

    /**
     * Detail Lembar Surat Masuk & Ruang Disposisi bagi Kepala Sekolah
     */
    public function show($id)
    {
        // Muat surat masuk beserta riwayat disposisi yang sudah pernah dibuat sebelumnya
        $surat = SuratMasuk::with(['disposisi.pengirim', 'disposisi.penerimaTugas'])->findOrFail($id);
        
        // Ambil semua daftar user (Guru/Waka) untuk dijadikan opsi tujuan lembar disposisi
        $daftarGuru = User::where('id', '!=', auth()->id())->get();

        return view('surat.surat_masuk.show', compact('surat', 'daftarGuru'));
    }

    /**
     * Proses Pengiriman Instruksi Disposisi oleh Kepala Sekolah
     */
    public function storeDisposisi(Request $request, $id)
    {
        $request->validate([
            'kepada_user_id'    => 'required|exists:users,id',
            'catatan_instruksi' => 'required|string',
            'sifat_disposisi'   => 'required|string',
        ]);

        DisposisiSurat::create([
            'surat_masuk_id'    => $id,
            'dari_user_id'      => auth()->id(), // Kepala Sekolah
            'kepada_user_id'    => $request->kepada_user_id, // Guru / Waka tujuan
            'catatan_instruksi' => $request->catatan_instruksi,
            'sifat_disposisi'   => $request->sifat_disposisi,
            'status'            => 'Belum Dibaca',
        ]);

        return redirect()->back()->with('success', 'Lembar instruksi disposisi berhasil dikirimkan!');
    }

    /**
     * Hapus arsip surat masuk beserta file PDF-nya
     */
    public function destroy($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        // Hapus file fisik PDF dari storage agar memori server tidak bengkak
        if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
            Storage::disk('public')->delete($surat->file_surat);
        }

        $surat->delete();

        return redirect()->route('surat.masuk.index')->with('success', 'Arsip surat masuk berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);

        $request->validate([
            'nomor_surat'   => 'required|string|max:255',
            'asal_instansi' => 'required|string|max:255',
            'perihal'       => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tanggal_terima'=> 'required|date',
            'sifat'         => 'required|in:Biasa,Penting,Rahasia',
            'file_surat'    => 'nullable|mimes:pdf|max:5120', // Nullable karena sifatnya opsional saat edit
        ]);

        $data = $request->except('file_surat');

        // Jika user mengunggah berkas PDF baru untuk menggantikan berkas lama
        if ($request->hasFile('file_surat')) {
            // Hapus file lama terlebih dahulu
            if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
                Storage::disk('public')->delete($surat->file_surat);
            }
            // Simpan file baru
            $data['file_surat'] = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        $surat->update($data);

        return redirect()->back()->with('success', 'Data arsip surat masuk berhasil diperbarui!');
    }
}