<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanSarpras;
use App\Models\Inventaris;
use App\Models\Pegawai; // Pastikan Model Pegawai sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanSarprasController extends Controller
{
    /**
     * Menampilkan daftar transaksi peminjaman sarpras
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = PeminjamanSarpras::with(['inventaris', 'peminjam', 'pencatat'])->latest();

        // Filter Pencarian (Berdasarkan nama barang atau nama pegawai peminjam)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('inventaris', function($inv) use ($search) {
                    $inv->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                })->orWhereHas('peminjam', function($peg) use ($search) {
                    $peg->where('nama_lengkap', 'like', "%{$search}%"); // Sesuaikan field nama di tabel pegawai Anda
                });
            });
        }

        // Filter Berdasarkan Status
        if ($status) {
            $query->where('status', $status);
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        // Ambil data pendukung untuk Form Modal Tambah/Edit
        $daftarInventaris = Inventaris::where('kondisi', 'Baik')->where('jumlah', '>', 0)->get();
        $daftarPegawai = Pegawai::orderBy('nama_lengkap', 'asc')->get(); // Sesuaikan field nama di tabel pegawai Anda

        return view('sarpras.peminjaman.index', compact('peminjaman', 'daftarInventaris', 'daftarPegawai'));
    }

    /**
     * Menyimpan data peminjaman baru (Otomatis mengurangi jumlah stok inventaris)
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'peminjam_id' => 'required|exists:pegawai,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $inventaris = Inventaris::findOrFail($request->inventaris_id);

        // Validasi ketersediaan stok fisik barang
        if ($inventaris->jumlah < 1) {
            return redirect()->back()->with('error', 'Gagal memproses! Stok barang ini sedang kosong.');
        }

        // Jalankan operasi pengurangan stok barang
        $inventaris->decrement('jumlah', 1);

        // Dapatkan ID Pegawai pencatat dari User yang sedang login
        // Catatan: Pastikan model User Anda memiliki relasi atau field yang menghubungkannya ke tabel pegawai, 
        // Contoh di bawah ini mengasumsikan Auth::user()->pegawai_id. Sesuaikan jika berbeda.
        $pencatatId = Auth::user()->pegawai_id ?? null;

        PeminjamanSarpras::create([
            'inventaris_id' => $request->inventaris_id,
            'peminjam_id' => $request->peminjam_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status' => 'Dipinjam',
            'keperluan' => $request->keperluan,
            'catatan' => $request->catatan,
            'pegawai_id_pencatat' => $pencatatId,
        ]);

        return redirect()->route('sarpras.peminjaman.index')->with('success', 'Transaksi peminjaman berhasil dicatat!');
    }

    /**
     * Memperbarui data peminjaman (Hanya bisa diedit jika statusnya masih 'Dipinjam')
     */
    public function update(Request $request, $id)
    {
        $peminjaman = PeminjamanSarpras::findOrFail($id);

        if ($peminjaman->status !== 'Dipinjam') {
            return redirect()->back()->with('error', 'Data peminjaman yang sudah kembali tidak dapat diubah lagi.');
        }

        $request->validate([
            'peminjam_id' => 'required|exists:pegawai,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        // Jika barang diubah saat edit, kita harus mengembalikan stok barang lama dan memotong stok barang baru
        if ($request->has('inventaris_id') && $request->inventaris_id != $peminjaman->inventaris_id) {
            $request->validate(['inventaris_id' => 'exists:inventaris,id']);
            
            // Kembalikan stok barang lama
            if ($peminjaman->inventaris_id) {
                Inventaris::where('id', $peminjaman->inventaris_id)->increment('jumlah', 1);
            }

            // Potong stok barang baru
            $inventarisBaru = Inventaris::findOrFail($request->inventaris_id);
            if ($inventarisBaru->jumlah < 1) {
                return redirect()->back()->with('error', 'Gagal mengubah barang! Stok barang pilihan baru sedang kosong.');
            }
            $inventarisBaru->decrement('jumlah', 1);
            
            $peminjaman->inventaris_id = $request->inventaris_id;
        }

        $peminjaman->update([
            'peminjam_id' => $request->peminjam_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'keperluan' => $request->keperluan,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('sarpras.peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    /**
     * Proses Pengembalian Barang (Mengembalikan stok inventaris + penentuan status keterlambatan)
     */
    public function kembalikan(Request $request, $id)
    {
        $peminjaman = PeminjamanSarpras::findOrFail($id);

        if ($peminjaman->status !== 'Dipinjam') {
            return redirect()->back()->with('error', 'Barang pada transaksi ini sudah dikembalikan sebelumnya.');
        }

        $request->validate([
            'tanggal_kembali_realisasi' => 'required|date|after_or_equal:' . $peminjaman->tanggal_pinjam,
            'catatan' => 'nullable|string'
        ]);

        $rencana = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $realisasi = Carbon::parse($request->tanggal_kembali_realisasi);

        // Menentukan status akhir berdasarkan ketepatan waktu mengembalikan
        $statusAkhir = $realisasi->greaterThan($rencana) ? 'Terlambat' : 'Dikembalikan';

        // Kembalikan kuantitas fisik stok ke tabel inventaris
        if ($peminjaman->inventaris_id) {
            Inventaris::where('id', $peminjaman->inventaris_id)->increment('jumlah', 1);
        }

        $peminjaman->update([
            'tanggal_kembali_realisasi' => $request->tanggal_kembali_realisasi,
            'status' => $statusAkhir,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('sarpras.peminjaman.index')->with('success', 'Proses pengembalian barang berhasil dicatat! Status: ' . $statusAkhir);
    }

    /**
     * Menghapus transaksi (Soft Delete + Mengembalikan stok barang jika statusnya masih 'Dipinjam')
     */
    public function destroy($id)
    {
        $peminjaman = PeminjamanSarpras::findOrFail($id);

        // Jika data dihapus saat statusnya masih dipinjam, kembalikan stoknya ke gudang sarpras
        if ($peminjaman->status === 'Dipinjam' && $peminjaman->inventaris_id) {
            Inventaris::where('id', $peminjaman->inventaris_id)->increment('jumlah', 1);
        }

        $peminjaman->delete();

        return redirect()->route('sarpras.peminjaman.index')->with('success', 'Data transaksi peminjaman berhasil dihapus.');
    }
}