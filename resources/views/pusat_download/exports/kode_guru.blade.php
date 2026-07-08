<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Kode Guru</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3 { margin: 2px 0; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 8px; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        .guru-name { font-weight: bold; font-size: 12px; margin-bottom: 3px; display: block; }
        .guru-nip { color: #444; font-size: 10px; }
        
        ul { margin: 0; padding-left: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>DAFTAR KODE GURU & BEBAN MENGAJAR</h2>
        <h3>{{ $nama_sekolah }}</h3>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kode</th>
                <th width="35%">Nama Lengkap & NIP</th>
                <th width="25%">Mata Pelajaran Diampu</th>
                <th width="25%">Daftar Kelas</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($daftar_kode as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center" style="font-weight: bold; font-size: 14px;">{{ $item->kode_guru ?? '-' }}</td>
                    <td class="text-left">
                        <span class="guru-name">{{ $item->pegawai ? $item->pegawai->nama_lengkap : 'Data Guru Kosong' }}</span>
                        <span class="guru-nip">NIP: {{ $item->pegawai ? ($item->pegawai->nip ?? '-') : '-' }}</span>
                    </td>
                    <td class="text-left">
                        @if($item->mataPelajarans && $item->mataPelajarans->count() > 0)
                            <ul>
                                @foreach($item->mataPelajarans as $mapel)
                                    <li>{{ $mapel->nama_pelajaran }}</li>
                                @endforeach
                            </ul>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-left">
                        {{-- Logika otomatis mengambil data kelas dari Jadwal Pelajaran --}}
                        @php
                            $kelas_list = [];
                            // Mengecek apakah model KodeGuru punya relasi ke Jadwal
                            if(isset($item->jadwals) || isset($item->jadwalPelajarans)) {
                                $jadwals = isset($item->jadwals) ? $item->jadwals : $item->jadwalPelajarans;
                                foreach($jadwals as $jadwal) {
                                    if(isset($jadwal->kelas)) {
                                        $kelas_list[$jadwal->kelas->id] = $jadwal->kelas->nama_kelas;
                                    }
                                }
                            }
                        @endphp

                        @if(count($kelas_list) > 0)
                            {{ implode(', ', $kelas_list) }}
                        @else
                            <span style="color: #888; font-style: italic;">(Belum ada kelas)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px;">Belum ada data kode guru yang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>