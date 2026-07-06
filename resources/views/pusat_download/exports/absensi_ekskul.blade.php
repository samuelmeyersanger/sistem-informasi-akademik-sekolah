<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Ekskul - {{ $ekskul->nama_ekskul }}</title>
    <style>
        /* Pengaturan Kertas Folio (F4) = 8.5 x 13 inci */
        @page { size: 8.5in 13in; margin: 20mm; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #000; }
        
        /* Kop Header */
        .kop-surat { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .teks-tengah { text-align: center; flex: 1; }
        .logo-sekolah { width: 80px; height: 80px; object-fit: contain; }
        .logo-ekskul { width: 70px; height: 70px; object-fit: contain; }
        
        /* Tabel Absensi */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background-color: #f3f4f6; text-align: center; }
        
        /* Pengaturan Print Otomatis: Menyembunyikan tombol saat dicetak */
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <!-- Tombol Bantuan (Otomatis hilang saat halaman di-print) -->
    <div class="btn-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 18px; background: #dc2626; color: white; border: none; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 14px;">
            📄 Cetak / Simpan PDF
        </button>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <!-- Logo Sekolah (Kiri) - Pastikan gambar logo_sekolah.png ada di folder public/images -->
        <img src="{{ asset('images/logo_sekolah.png') }}" class="logo-sekolah" alt="Logo Sekolah">
        
        <div class="teks-tengah">
            <h2 style="margin: 0; font-size: 18px;">DAFTAR HADIR EKSTRAKURIKULER</h2>
            <h1 style="margin: 5px 0; font-size: 22px;">{{ strtoupper($ekskul->nama_ekskul) }}</h1>
            <p style="margin: 0; font-size: 12px;">Semester: Ganjil / Genap &nbsp;&nbsp;|&nbsp;&nbsp; Tahun Ajaran: 2026/2027</p>
        </div>
        
        <!-- Logo Ekskul (Kanan) -->
        @if($ekskul->logo_ekskul)
            <img src="{{ asset('storage/' . $ekskul->logo_ekskul) }}" class="logo-ekskul" alt="Logo Ekskul">
        @else
            <!-- Jika tidak ada logo ekskul, buat kotak kosong penyimbang -->
            <div style="width: 70px; height: 70px;"></div> 
        @endif
    </div>

    <!-- Informasi Pembina -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: bold; font-size: 13px;">
        <div>Pembina : {{ $ekskul->pembina->nama_lengkap ?? '___________________________' }}</div>
        <div>Bulan : ___________________________</div>
    </div>

    <!-- Tabel Nama Otomatis -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">NO</th>
                <th rowspan="2" style="width: 250px;">NAMA LENGKAP SISWA</th>
                <th rowspan="2" style="width: 80px;">KELAS</th>
                <!-- Disediakan 5 kolom pertemuan untuk absen -->
                <th colspan="5">TANGGAL PERTEMUAN</th>
            </tr>
            <tr>
                <th style="width: 45px; height: 25px;"></th>
                <th style="width: 45px;"></th>
                <th style="width: 45px;"></th>
                <th style="width: 45px;"></th>
                <th style="width: 45px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($ekskul->anggota as $index => $anggota)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-weight: bold;">{{ $anggota->siswa->nama_lengkap ?? '-' }}</td>
                <td style="text-align: center;">{{ $anggota->siswa->kelas->nama_kelas ?? '-' }}</td>
                <!-- Kolom Kosong untuk di-ceklis manual pakai pulpen -->
                <td></td><td></td><td></td><td></td><td></td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #666; font-style: italic;">
                    Belum ada anggota siswa yang terdaftar di Ekstrakurikuler ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>