<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Master Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openDelete: false,
        currentStep: 1,
        deleteActionUrl: '',
        deleteTargetName: '',

        initDelete(actionUrl, siswaName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = siswaName;
            this.openDelete = true;
        },

        resetWizard() {
            this.openCreate = false;
            this.currentStep = 1;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen font-sans">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1 flex items-center gap-1">⚠️ Gagal menyimpan/memproses data:</p>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Daftar Induk Siswa</h3>
                            <p class="text-xs text-gray-500">Gunakan bilah pencarian dan filter di bawah untuk memilah status operasional akademik siswa.</p>
                        </div>
                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Tambah Siswa Baru
                        </button>
                    </div>

                    <form action="{{ route('kesiswaan.siswa') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NISN, NIPD, NIK..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full">
                        
                        <select name="tingkat" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-gray-600">
                            <option value="">-- Semua Tingkat --</option>
                            <option value="7" {{ request('tingkat') == '7' ? 'selected' : '' }}>Tingkat 7</option>
                            <option value="8" {{ request('tingkat') == '8' ? 'selected' : '' }}>Tingkat 8</option>
                            <option value="9" {{ request('tingkat') == '9' ? 'selected' : '' }}>Tingkat 9</option>
                        </select>

                        <select name="status" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-gray-600">
                            <option value="">-- Semua Status --</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>🟢 Aktif</option>
                            <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>🔵 Lulus</option>
                            <option value="Mutasi" {{ request('status') == 'Mutasi' ? 'selected' : '' }}>🟡 Mutasi Masuk/Keluar</option>
                            <option value="Keluar" {{ request('status') == 'Keluar' ? 'selected' : '' }}>🔴 Keluar / DO</option>
                        </select>

                        <div class="flex gap-2">
                            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer">Filter</button>
                            <a href="{{ route('kesiswaan.siswa') }}" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg flex items-center justify-center transition-colors">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Lengkap / NIPD</th>
                                <th class="p-4">NISN / NIK</th>
                                <th class="p-4">Tingkat & Ruang Kelas</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 pr-6 text-center w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($siswa as $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="font-bold text-gray-900 text-sm">{{ $item->nama_lengkap }}</div>
                                        <div class="text-gray-400 font-mono mt-0.5">{{ $item->nipd }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div>{{ $item->nisn ?? '-' }}</div>
                                        <div class="text-gray-400 font-mono mt-0.5">{{ $item->nik }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 bg-slate-100 border rounded-md font-medium text-gray-600 text-[10px]">Tgt {{ $item->tingkat }}</span>
                                        <span class="ml-1 text-gray-700 font-bold">{{ $item->kelas->nama_kelas ?? '⚠️ Belum Dipetakan' }}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_siswa == 'Aktif')
                                            <span class="px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold uppercase rounded">🟢 Aktif</span>
                                        @elseif($item->status_siswa == 'Lulus')
                                            <span class="px-2 py-0.5 bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-bold uppercase rounded">🔵 Lulus</span>
                                        @elseif($item->status_siswa == 'Mutasi')
                                            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase rounded">🟡 Mutasi</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-bold uppercase rounded">🔴 Keluar</span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('kesiswaan.siswa.show', $item->id) }}" class="p-1 text-indigo-600 hover:underline font-semibold flex items-center gap-0.5">
                                                👁️ Profil
                                            </a>
                                            <button type="button" @click="initDelete('{{ route('kesiswaan.siswa.destroy', $item->id) }}', '{{ addslashes($item->nama_lengkap) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Tidak ada data rekam catatan siswa terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswa->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $siswa->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl border border-gray-100 flex flex-col max-h-[90vh]" @click.away="resetWizard()">
                
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Form Pendaftaran Akun Siswa</h3>
                        <p class="text-[11px] text-gray-400">Harap isi form data diri, domisili, dan silsilah keluarga di bawah.</p>
                    </div>
                    <button type="button" @click="resetWizard()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <div class="px-6 py-2.5 bg-indigo-50/40 border-b border-gray-100 grid grid-cols-3 text-center text-xs font-bold text-gray-400">
                    <div class="pb-1 border-b-2" :class="currentStep === 1 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">1. Data Personal</div>
                    <div class="pb-1 border-b-2" :class="currentStep === 2 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">2. Domisili & Academic</div>
                    <div class="pb-1 border-b-2" :class="currentStep === 3 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">3. Data Orang Tua</div>
                </div>

                <form action="{{ route('kesiswaan.siswa.store') }}" method="POST" class="flex-1 overflow-y-auto p-6 text-xs space-y-4 text-gray-700">
                    @csrf

                    <div x-show="currentStep === 1" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Sesuai Dokumen Resmi *</label>
                                <input type="text" name="nama_lengkap" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NIK (Nomor Induk Kependudukan) *</label>
                                <input type="text" name="nik" required maxlength="16" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NIPD *</label>
                                <input type="text" name="nipd" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NISN (Opsional)</label>
                                <input type="text" name="nisn" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Agama *</label>
                                <select name="agama" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katholik">Katholik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Budha">Budha</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WhatsApp *</label>
                                <input type="text" name="nomor_hp" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Asal Sekolah Dasar (SD/MI) *</label>
                                <input type="text" name="asal_sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 2" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Provinsi *</label>
                                <select id="siswa_provinsi" name="provinsi" data-current="{{ old('provinsi') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kabupaten / Kota *</label>
                                <select id="siswa_kota" name="kota" data-current="{{ old('kota') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kecamatan *</label>
                                <select id="siswa_kecamatan" name="kecamatan" data-current="{{ old('kecamatan') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa *</label>
                                <select id="siswa_kelurahan" name="kelurahan_desa" data-current="{{ old('kelurahan_desa') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block font-semibold text-gray-600 mb-1">Alamat Jalan / Blok / Kampung *</label>
                                <input type="text" name="alamat_lengkap" required placeholder="Nama jalan, RT/RW, nomor rumah..." class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div class="grid grid-cols-3 gap-1">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RT *</label>
                                    <input type="text" name="rt" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RW *</label>
                                    <input type="text" name="rw" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pos *</label>
                                    <input type="text" name="kode_pos" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-2">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tingkat Kelas Masuk *</label>
                                <select name="tingkat" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="7">Tingkat 7</option>
                                    <option value="8">Tingkat 8</option>
                                    <option value="9">Tingkat 9</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Daftar Semester Masuk *</label>
                                <select name="semester_id" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    @foreach($semester_list as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->nama_semester }} ({{ $sem->tahunAjaran->nama_tahun_ajaran ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tanggal Diterima Masuk *</label>
                                <input type="date" name="diterima_pada_tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 3" class="space-y-4">
                        @foreach(['Ayah', 'Ibu', 'Wali'] as $index => $hub)
                        <div class="p-3 bg-gray-50/80 border border-gray-100 rounded-xl space-y-3">
                            <div class="font-bold text-gray-800 border-b border-gray-200/60 pb-1">
                                Hubungan Keluarga: {{ $hub }}
                                <input type="hidden" name="wali[{{ $index }}][hubungan]" value="{{ $hub }}">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-gray-600 mb-1">Nama Lengkap {{ $hub }}</label>
                                    <input type="text" name="wali[{{ $index }}][nama_lengkap]" class="w-full border-gray-300 text-xs bg-white rounded-lg shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-gray-600 mb-1">NIK {{ $hub }}</label>
                                    <input type="text" name="wali[{{ $index }}][nik]" maxlength="16" class="w-full border-gray-300 text-xs bg-white rounded-lg shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-gray-600 mb-1">No. Handphone / Telp</label>
                                    <input type="text" name="wali[{{ $index }}][nomor_hp]" class="w-full border-gray-300 text-xs bg-white rounded-lg shadow-sm">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-between bg-white">
                        <button type="button" x-show="currentStep > 1" @click="currentStep--" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                            ⬅️ Kembali
                        </button>
                        <div x-show="currentStep === 1"></div>
                        
                        <button type="button" x-show="currentStep < 3" @click="currentStep++" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                            Lanjut ➡️
                        </button>
                        
                        <button type="submit" x-show="currentStep === 3" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer">
                            💾 Daftarkan Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Data Master Siswa?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus data siswa bernama <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Semua dokumen terunggah & ikatan wali murid akan terputus.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        const siswaProvSelect = document.getElementById('siswa_provinsi');
        const siswaKotaSelect = document.getElementById('siswa_kota');
        const siswaKecSelect  = document.getElementById('siswa_kecamatan');
        const siswaKelSelect  = document.getElementById('siswa_kelurahan');

        const sCurrentProv = siswaProvSelect.getAttribute('data-current');
        const sCurrentKota = siswaKotaSelect.getAttribute('data-current');
        const sCurrentKec  = siswaKecSelect.getAttribute('data-current');
        const sCurrentKel  = siswaKelSelect.getAttribute('data-current');

        async function sFetchJson(url) {
            try {
                let response = await fetch(url);
                return await response.json();
            } catch (e) {
                console.error("Gagal menarik data API wilayah kesiswaan: ", e);
                return [];
            }
        }

        // 1. Memuat Data Provinsi Utama (Diarahkan ke prefix URL /kesiswaan/)
        async function sLoadProvinsi() {
            let data = await sFetchJson(`/kesiswaan/api/provinsi`);
            siswaProvSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            data.forEach(p => {
                let selected = (p.name === sCurrentProv) ? 'selected' : '';
                siswaProvSelect.innerHTML += `<option value="${p.name}" data-id="${p.code}" ${selected}>${p.name}</option>`;
            });

            if (sCurrentProv) {
                siswaProvSelect.dispatchEvent(new Event('change'));
            }
        }

        // 2. Event: Provinsi Berubah -> Ambil Kota
        siswaProvSelect.addEventListener('change', async function() {
            siswaKotaSelect.innerHTML = '<option value="">-- Memuat Kota... --</option>';
            siswaKecSelect.innerHTML  = '<option value="">-- Pilih Kecamatan --</option>';
            siswaKelSelect.innerHTML  = '<option value="">-- Pilih Kelurahan/Desa --</option>';
            
            let opt = this.options[this.selectedIndex];
            let provId = opt ? opt.getAttribute('data-id') : null;
            if(!provId) { siswaKotaSelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>'; return; }

            let data = await sFetchJson(`/kesiswaan/api/kota/${provId}`);
            siswaKotaSelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
            data.forEach(k => {
                let selected = (k.name === sCurrentKota) ? 'selected' : '';
                siswaKotaSelect.innerHTML += `<option value="${k.name}" data-id="${k.code}" ${selected}>${k.name}</option>`;
            });

            if (sCurrentKota) {
                siswaKotaSelect.dispatchEvent(new Event('change'));
            }
        });

        // 3. Event: Kota Berubah -> Ambil Kecamatan
        siswaKotaSelect.addEventListener('change', async function() {
            siswaKecSelect.innerHTML = '<option value="">-- Memuat Kecamatan... --</option>';
            siswaKelSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';

            let opt = this.options[this.selectedIndex];
            let kotaId = opt ? opt.getAttribute('data-id') : null;
            if(!kotaId) { siswaKecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>'; return; }

            let data = await sFetchJson(`/kesiswaan/api/kecamatan/${kotaId}`);
            siswaKecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            data.forEach(kc => {
                let selected = (kc.name === sCurrentKec) ? 'selected' : '';
                siswaKecSelect.innerHTML += `<option value="${kc.name}" data-id="${kc.code}" ${selected}>${kc.name}</option>`;
            });

            if (sCurrentKec) {
                siswaKecSelect.dispatchEvent(new Event('change'));
            }
        });

        // 4. Event: Kecamatan Berubah -> Ambil Desa/Kelurahan
        siswaKecSelect.addEventListener('change', async function() {
            siswaKelSelect.innerHTML = '<option value="">-- Memuat Kelurahan... --</option>';

            let opt = this.options[this.selectedIndex];
            let kecId = opt ? opt.getAttribute('data-id') : null;
            if(!kecId) { siswaKelSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>'; return; }

            let data = await sFetchJson(`/kesiswaan/api/kelurahan/${kecId}`);
            siswaKelSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
            data.forEach(kl => {
                let selected = (kl.name === sCurrentKel) ? 'selected' : '';
                siswaKelSelect.innerHTML += `<option value="${kl.name}" ${selected}>${kl.name}</option>`;
            });
        });

        // Jalankan inisialisasi saat halaman selesai dimuat
        document.addEventListener("DOMContentLoaded", function() {
            sLoadProvinsi();
        });
    </script>
</x-app-layout>