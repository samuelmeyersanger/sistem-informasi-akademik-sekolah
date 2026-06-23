<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Detail & Dokumen Lampiran Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{
        tabActive: 'profil',
        statusSelected: 'Aktif'
    }" class="py-12 bg-slate-900/10 min-h-screen font-sans">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="mb-2">
                <a href="{{ route('kesiswaan.siswa') }}" class="text-xs font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                    ⬅️ Kembali ke Tabel Master Siswa
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                        🎓
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 leading-tight">{{ $siswa->nama_lengkap }}</h1>
                        <div class="flex items-center gap-3 text-xs text-gray-400 font-medium mt-1">
                            <span>NIPD: <strong class="text-gray-600 font-mono">{{ $siswa->nipd }}</strong></span>
                            <span>•</span>
                            <span>NISN: <strong class="text-gray-600 font-mono">{{ $siswa->nisn ?? '-' }}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <div class="text-[11px] text-gray-400 font-bold uppercase tracking-wide">Plot Ruang Kelas:</div>
                    <div class="text-xs font-bold text-gray-800 bg-gray-100 border px-3 py-1 rounded-lg mt-0.5 inline-block">
                        🏫 {{ $siswa->kelas->nama_kelas ?? 'Belum Dipetakan' }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6 space-y-6">
                
                <div class="flex border-b border-gray-100 text-xs font-bold text-gray-400 gap-6">
                    <button @click="tabActive = 'profil'" :class="tabActive === 'profil' ? 'text-indigo-600 border-b-2 border-indigo-600 pb-3' : 'pb-3 hover:text-gray-600'" class="transition-all cursor-pointer">
                        👤 Identitas & Silsilah
                    </button>
                    <button @click="tabActive = 'dokumen'" :class="tabActive === 'dokumen' ? 'text-indigo-600 border-b-2 border-indigo-600 pb-3' : 'pb-3 hover:text-gray-600'" class="transition-all cursor-pointer">
                        📁 Arsip Digital Berkas ({{ $siswa->dokumen->count() }})
                    </button>
                    <button @click="tabActive = 'riwayat'" :class="tabActive === 'riwayat' ? 'text-indigo-600 border-b-2 border-indigo-600 pb-3' : 'pb-3 hover:text-gray-600'" class="transition-all cursor-pointer">
                        📜 Log Akademik & Status
                    </button>
                </div>

                <div x-show="tabActive === 'profil'" class="space-y-6" x-transition>
                    <div class="space-y-4 text-xs">
                        <h3 class="font-bold text-sm text-gray-800 border-b border-gray-50 pb-2">Biodata Diri Personal</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-gray-600">
                            <div><span class="text-gray-400 block mb-0.5">NIK Kependudukan:</span><strong class="text-gray-900 font-mono text-sm">{{ $siswa->nik }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Tempat, Tanggal Lahir:</span><strong class="text-gray-900">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d F Y') }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Jenis Kelamin / Agama:</span><strong class="text-gray-900">{{ $siswa->jenis_kelamin }} / {{ $siswa->agama }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">No Handphone Aktif:</span><strong class="text-gray-900">{{ $siswa->nomor_hp }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Sekolah Asal (SD/MI):</span><strong class="text-gray-900">{{ $siswa->asal_sekolah }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Anak Ke / Kode Pos:</span><strong class="text-gray-900">{{ $siswa->anak_ke }} / {{ $siswa->kode_pos }}</strong></div>
                        </div>
                        <div class="p-3.5 bg-gray-50 border border-gray-100 rounded-xl">
                            <span class="text-gray-400 block mb-1 font-semibold">Alamat Domisili Tetap Sesuai KK:</span>
                            <strong class="text-gray-800">{{ $siswa->alamat_lengkap }}, RT.{{ $siswa->rt }}/RW.{{ $siswa->rw }}, Kel/Desa. {{ $siswa->kelurahan_desa }}, Kec. {{ $siswa->kecamatan }}, {{ $siswa->kota }}, {{ $siswa->provinsi }}</strong>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs">
                        <h3 class="font-bold text-sm text-gray-800 border-b border-gray-50 pb-2">Daftar Relasi Orang Tua / Wali</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @forelse($siswa->wali as $parent)
                                <div class="border border-gray-100 bg-gray-50/50 p-4 rounded-xl space-y-2">
                                    <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold rounded text-[9px] uppercase">{{ $parent->pivot->hubungan }}</span>
                                    <div class="text-gray-700 pt-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ $parent->nama_lengkap }}</div>
                                        <div class="font-mono text-[11px] text-gray-400 mt-0.5">NIK: {{ $parent->nik }}</div>
                                        <div class="text-gray-500 mt-1">📞 No HP: {{ $parent->nomor_hp ?? '-' }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="md:col-span-3 py-4 text-center text-gray-400 italic">Ikatan relasi data penanggung jawab wali siswa belum diisi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div x-show="tabActive === 'dokumen'" class="grid grid-cols-1 md:grid-cols-3 gap-6" style="display: none;" x-transition>
                    <div class="bg-gray-50/50 p-4 border border-gray-100 rounded-xl h-fit space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-200 pb-1.5 uppercase">Unggah Berkas Baru</h4>
                        <form action="{{ route('kesiswaan.dokumen.store', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">Jenis Arsip Dokumen</label>
                                <select name="jenis_dokumen" required class="w-full text-xs border-gray-300 rounded-lg shadow-sm">
                                    <option value="Akta Kelahiran">Akta Kelahiran</option>
                                    <option value="Kartu Keluarga">Kartu Keluarga (KK)</option>
                                    <option value="Ijazah SD/MI">Ijazah SD/MI</option>
                                    <option value="KIP/PIP">Kartu Indonesia Pintar</option>
                                    <option value="Rapor Semester">Rapor Semester</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">Tahun Penerbitan Berkas</label>
                                <input type="number" name="tahun_dokumen" value="{{ date('Y') }}" required class="w-full text-xs border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">File Berkas (PDF/JPG, Max 2MB)</label>
                                <input type="file" name="file_dokumen" required class="w-full text-xs border border-gray-200 bg-white p-1 rounded-lg w-full">
                            </div>
                            <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-sm transition-colors cursor-pointer text-center">
                                Upload File
                            </button>
                        </form>
                    </div>

                    <div class="md:col-span-2 space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase">Berkas Digital Terverifikasi</h4>
                        <div class="space-y-2">
                            @forelse($siswa->dokumen as $doc)
                                <div class="flex justify-between items-center p-3 border border-gray-100 rounded-xl bg-white hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">📄</span>
                                        <div>
                                            <strong class="text-gray-900 font-bold block">{{ $doc->jenis_dokumen }}</strong>
                                            <span class="text-[11px] text-gray-400">Tahun terbit: {{ $doc->tahun_dokumen }} • Nama: {{ $doc->nama_dokumen }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-semibold border border-gray-300">Lihat</a>
                                        <form action="{{ route('kesiswaan.dokumen.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus permanen berkas fisik dokumen ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-500 hover:bg-rose-50 rounded font-bold">🗑️</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 border border-dashed rounded-xl italic text-gray-400">Belum ada dokumen digital terunggah di sistem.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div x-show="tabActive === 'riwayat'" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;" x-transition>
                    
                    <div class="space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase">Log Histori Ruang Kelas</h4>
                        <div class="relative border-l-2 border-indigo-100 pl-4 space-y-4 ml-2 pt-2">
                            @forelse($siswa->riwayatKelas as $rk)
                                <div class="relative">
                                    <span class="absolute -left-[22px] top-0 w-3 h-3 bg-indigo-600 rounded-full ring-4 ring-indigo-50"></span>
                                    <div class="font-bold text-gray-900 text-sm">Kelas: {{ $rk->kelas->nama_kelas ?? 'Gantung' }}</div>
                                    <div class="text-[11px] text-gray-400 font-medium">Tingkat {{ $rk->tingkat }} • Semester: {{ $rk->semester->nama_semester ?? '-' }}</div>
                                    <div class="mt-1 px-1.5 py-0.5 bg-gray-100 border border-gray-200 inline-block text-[10px] text-gray-600 rounded font-medium italic">{{ $rk->keterangan }}</div>
                                </div>
                            @empty
                                <div class="text-gray-400 italic">Belum terekam jejak mutasi plotting kelas siswa.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-6 text-xs flex flex-col justify-between">
                        <div class="space-y-3">
                            <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase">Log Status Operasional</h4>
                            <div class="relative border-l-2 border-emerald-100 pl-4 space-y-4 ml-2 pt-2">
                                @forelse($siswa->riwayatStatus as $rs)
                                    <div class="relative">
                                        <span class="absolute -left-[22px] top-0 w-3 h-3 bg-emerald-600 rounded-full ring-4 ring-emerald-50"></span>
                                        <div class="font-bold text-gray-900">
                                            Status: <span class="px-1.5 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded font-extrabold text-[9px] uppercase">{{ $rs->status }}</span>
                                        </div>
                                        <div class="text-[11px] text-gray-400 font-medium">Log dibuat pada: {{ $rs->created_at->format('d M Y H:i') }}</div>
                                        @if(is_array($rs->metadata))
                                            <div class="mt-1 bg-gray-50 border border-gray-200 p-2 rounded-lg text-[11px] text-gray-600 space-y-0.5 font-medium">
                                                <div>💬 Alasan: {{ $rs->metadata['alasan'] ?? '-' }}</div>
                                                @if(!empty($rs->metadata['sekolah_tujuan'])) <div>🏫 Tujuan: {{ $rs->metadata['sekolah_tujuan'] }}</div> @endif
                                                @if(!empty($rs->metadata['no_ijazah'])) <div>📜 No. Ijazah: {{ $rs->metadata['no_ijazah'] }}</div> @endif
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-gray-400 italic">Belum ada perubahan status operasional.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="p-4 bg-rose-50/50 border border-rose-200 rounded-2xl space-y-3 mt-4">
                            <h5 class="font-bold text-rose-900 uppercase tracking-wide flex items-center gap-1">⚡ Sakelar Status Akademik</h5>
                            <form action="{{ route('kesiswaan.siswa.updateStatus', $siswa->id) }}" method="POST" class="space-y-3">
                                @csrf @method('PUT')
                                <input type="hidden" name="semester_id" value="{{ $semester_aktif->id ?? '' }}">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-1">Pilih Status Operasional</label>
                                        <select name="status_siswa" x-model="statusSelected" class="w-full text-xs border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white">
                                            <option value="Aktif">🟢 Aktif</option>
                                            <option value="Mutasi">🟡 Mutasi Keluar</option>
                                            <option value="Keluar">🔴 Dikeluarkan (DO)</option>
                                            <option value="Lulus">🔵 Lulus / Alumni</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-1">Catatan Keterangan</label>
                                        <input type="text" name="alasan" required placeholder="Alasan perubahan status..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm bg-white">
                                    </div>
                                </div>

                                <div x-show="statusSelected === 'Mutasi'" style="display: none;" x-transition>
                                    <label class="block text-gray-700 font-semibold mb-1">Nama Sekolah Tujuan Mutasi Keluar</label>
                                    <input type="text" name="sekolah_tujuan" placeholder="Contoh: SMP Negeri 1 Jakarta" class="w-full text-xs border-gray-300 rounded-lg shadow-sm bg-white">
                                </div>

                                <div x-show="statusSelected === 'Lulus'" style="display: none;" x-transition>
                                    <label class="block text-gray-700 font-semibold mb-1">Nomor Surat Kelulusan / No. Ijazah Resmi</label>
                                    <input type="text" name="no_ijazah" placeholder="Contoh: DN-01/M-SM/0012345" class="w-full text-xs border-gray-300 rounded-lg shadow-sm bg-white">
                                </div>

                                <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs shadow-sm transition-colors cursor-pointer" onclick="return confirm('Apakah Anda yakin ingin memproses perubahan status permanen siswa ini?')">
                                    🔒 Eksekusi Perubahan Status Siswa
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>