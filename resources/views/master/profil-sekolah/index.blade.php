<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🏢</span> {{ __('Profil & Lokasi Institusi') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Data master identitas resmi sekolah dan pemetaan koordinat geografis kewilayahan.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div>
                        <div class="mb-2 text-base font-black">Gagal menyimpan data profil!</div>
                        <ul class="list-disc list-inside text-xs font-medium text-rose-700 space-y-1 bg-rose-100/50 p-3 rounded-xl border border-rose-200/50">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <span class="text-indigo-500">📋</span> Data Pokok Pendidikan (Dapodik)
                    </h3>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed max-w-4xl">
                        Pastikan seluruh informasi di bawah ini diisi dengan valid sesuai surat keputusan resmi. Data ini akan diproyeksikan langsung pada <strong class="text-slate-700">cetak Rapor, Surat Mutasi, serta Kartu Pelajar</strong> siswa.
                    </p>
                </div>

                <form action="{{ route('master.profil-sekolah.save') }}" method="POST" class="p-6 md:p-8 space-y-10 relative z-10">
                    @csrf
                    
                    {{-- SEKSI I: PROFIL IDENTITAS --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-black text-sm">I</span>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Informasi Identitas & Registrasi</h4>
                            <div class="flex-grow h-px bg-slate-200 ml-2"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            <div class="md:col-span-6">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nama Resmi Sekolah <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_sekolah" required value="{{ old('nama_sekolah', $profil->nama_sekolah ?? '') }}" 
                                       placeholder="Cth: SMA Negeri 1 Nusantara" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Jenjang <span class="text-rose-500">*</span></label>
                                <input type="text" name="jenjang" required value="{{ old('jenjang', $profil->jenjang ?? '') }}" 
                                       placeholder="Cth: SMA/SMK" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">NPSN <span class="text-rose-500">*</span></label>
                                <input type="text" name="npsn" required value="{{ old('npsn', $profil->npsn ?? '') }}" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 font-mono text-indigo-700 bg-indigo-50/30">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Fase Kurikulum</label>
                                <input type="text" name="fase" value="{{ old('fase', $profil->fase ?? '') }}" 
                                       placeholder="Cth: Fase E & F" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-400">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nomor Statistik Sekolah (NSS)</label>
                                <input type="text" name="nss" value="{{ old('nss', $profil->nss ?? '') }}" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 font-mono text-slate-600">
                            </div>
                        </div>
                    </div>

                    {{-- SEKSI II: GEOGRAFIS LOKASI --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-black text-sm">II</span>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Alamat Fisik & Titik Peta (GPS)</h4>
                            <div class="flex-grow h-px bg-slate-200 ml-2"></div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            
                            <div class="space-y-5 bg-slate-50 p-6 rounded-[1.5rem] border border-slate-100">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Provinsi <span class="text-rose-500">*</span></label>
                                        <select id="provinsi" name="provinsi" data-current="{{ old('provinsi', $profil->provinsi ?? '') }}" required 
                                                class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 bg-white">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kabupaten / Kota <span class="text-rose-500">*</span></label>
                                        <select id="kota" name="kota" data-current="{{ old('kota', $profil->kota ?? '') }}" required 
                                                class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 bg-white">
                                            <option value="">-- Menunggu Provinsi --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kecamatan <span class="text-rose-500">*</span></label>
                                        <select id="kecamatan" name="kecamatan" data-current="{{ old('kecamatan', $profil->kecamatan ?? '') }}" required 
                                                class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 bg-white">
                                            <option value="">-- Menunggu Kota --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Desa / Kelurahan <span class="text-rose-500">*</span></label>
                                        <select id="kelurahan" name="kelurahan" data-current="{{ old('kelurahan', $profil->kelurahan ?? '') }}" required 
                                                class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 bg-white">
                                            <option value="">-- Menunggu Kecamatan --</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Jalan / Komplek / Detail <span class="text-rose-500">*</span></label>
                                    <textarea id="alamat" name="alamat" rows="2" required 
                                              placeholder="Jl. Pendidikan No. 123, Blok A, RT 01/RW 02..." 
                                              class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 bg-white placeholder-slate-400">{{ old('alamat', $profil->alamat ?? '') }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-5">
                                    <div class="sm:col-span-4">
                                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Kode Pos <span class="text-rose-500">*</span></label>
                                        <input type="text" id="kode_pos" name="kode_pos" required value="{{ old('kode_pos', $profil->kode_pos ?? '') }}" 
                                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-2.5 px-3 bg-white">
                                    </div>
                                    <div class="sm:col-span-4">
                                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Latitude (Y) <span class="text-rose-500">*</span></label>
                                        <input type="text" id="latitude" name="latitude" required value="{{ old('latitude', $profil->latitude ?? '-6.2088') }}" 
                                               class="w-full rounded-xl border-slate-200 text-xs font-mono shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-2.5 px-3 bg-amber-50/50 text-slate-600">
                                    </div>
                                    <div class="sm:col-span-4">
                                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Longitude (X) <span class="text-rose-500">*</span></label>
                                        <input type="text" id="longitude" name="longitude" required value="{{ old('longitude', $profil->longitude ?? '106.8456') }}" 
                                               class="w-full rounded-xl border-slate-200 text-xs font-mono shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-2.5 px-3 bg-amber-50/50 text-slate-600">
                                    </div>
                                </div>
                                
                                <button type="button" onclick="cariAlamatKePeta()" 
                                        class="w-full py-3.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-black rounded-xl shadow-md cursor-pointer transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <span>📡</span> Deteksi Koordinat dari Teks Alamat Di Atas
                                </button>
                            </div>

                            <div class="flex flex-col h-full bg-slate-50 p-6 rounded-[1.5rem] border border-slate-100">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span>📍</span> Visualisasi Peta (Leaflet JS)
                                </label>
                                <div class="bg-white p-2 rounded-[1.5rem] border border-slate-200 shadow-sm flex-grow min-h-[350px] relative">
                                    <div id="map" class="w-full h-full rounded-xl z-10 absolute inset-2" style="width: calc(100% - 16px); height: calc(100% - 16px);"></div>
                                </div>
                                <div class="mt-4 flex gap-3 text-[11px] font-medium text-slate-500 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100/50">
                                    <span class="text-indigo-500 text-lg">💡</span> 
                                    <p>Tarik & geser Pin Merah (Marker) pada peta di atas secara manual jika deteksi otomatis kurang akurat. Titik kordinat akan tersimpan otomatis.</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- SEKSI III: MEDIA KOMUNIKASI --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-black text-sm">III</span>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Media Komunikasi & Kontak Publik</h4>
                            <div class="flex-grow h-px bg-slate-200 ml-2"></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 bg-slate-50 p-6 rounded-[1.5rem] border border-slate-100">
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Alamat Email Resmi <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400">📧</span>
                                    </div>
                                    <input type="email" name="email" required value="{{ old('email', $profil->email ?? '') }}" 
                                           placeholder="sekolah@domain.sch.id"
                                           class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 pl-10 pr-4 bg-white">
                                </div>
                            </div>
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Tautan Portal Website</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400">🌐</span>
                                    </div>
                                    <input type="url" name="website" value="{{ old('website', $profil->website ?? '') }}" 
                                           placeholder="https://www.sekolah.sch.id"
                                           class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 pl-10 pr-4 bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                            <span>💾</span> Rekam Permanen Data Institusi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT LEAFLET DAN LARAVOLT API SAMA SEKALI TIDAK DIUBAH -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        // =========================================================================
        // 1. MANAGEMENT SISTEM PETA INTERAKTIF LEAFLET
        // =========================================================================
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        let initialLat = parseFloat(latInput.value) || -6.2088;
        let initialLng = parseFloat(lngInput.value) || 106.8456;

        // Inisialisasi Kontainer Peta
        const map = L.map('map').setView([initialLat, initialLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker/Pin Utama yang dapat digeser
        let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

        function updateInputs(lat, lng) {
            latInput.value = parseFloat(lat).toFixed(6);
            lngInput.value = parseFloat(lng).toFixed(6);
        }

        // Listener saat pin digeser manual
        marker.on('dragend', function (e) {
            let position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });

        // Listener saat peta diklik pada titik tertentu
        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });

        // Sinkronisasi balik jika user mengubah teks input koordinat secara manual
        [latInput, lngInput].forEach(input => {
            input.addEventListener('change', function() {
                let lat = parseFloat(latInput.value) || -6.2088;
                let lng = parseFloat(lngInput.value) || 106.8456;
                let newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                map.panTo(newLatLng);
            });
        });

        // Geocoding: Estimasi pencarian alamat berdasarkan teks ke titik spasial
        const geocoder = L.Control.Geocoder.nominatim();
        function cariAlamatKePeta() {
            const alamat = document.getElementById('alamat').value;
            const kel = document.getElementById('kelurahan').value || '';
            const kec = document.getElementById('kecamatan').value || '';
            const kota = document.getElementById('kota').value || '';
            const prov = document.getElementById('provinsi').value || '';

            if(!alamat) {
                alert("Harap isi bidang teks Alamat Jalan terlebih dahulu!");
                return;
            }

            // Gabungkan teks pembentuk alamat global
            let arrayQuery = [alamat];
            if(kel) arrayQuery.push(kel);
            if(kec) arrayQuery.push(kec);
            if(kota) arrayQuery.push(kota);
            if(prov) arrayQuery.push(prov);
            arrayQuery.push("Indonesia");

            const queryLengkap = arrayQuery.join(', ');

            geocoder.geocode(queryLengkap, function(results) {
                if (results && results.length > 0) {
                    let topResult = results[0];
                    let latlng = topResult.center;
                    
                    marker.setLatLng(latlng);
                    map.setView(latlng, 16);
                    updateInputs(latlng.lat, latlng.lng);
                } else {
                    alert("Koordinat spesifik tidak ditemukan. Silakan tandai langsung pada peta.");
                }
            });
        }

        // =========================================================================
        // 2. CHAINED SELECTION LARAVOLT API (SINKRONISASI CODES & OLD DATA)
        // =========================================================================
        const provSelect = document.getElementById('provinsi');
        const kotaSelect = document.getElementById('kota');
        const kecSelect  = document.getElementById('kecamatan');
        const kelSelect  = document.getElementById('kelurahan');

        const currentProv = provSelect.getAttribute('data-current');
        const currentKota = kotaSelect.getAttribute('data-current');
        const currentKec  = kecSelect.getAttribute('data-current');
        const currentKel  = kelSelect.getAttribute('data-current');

        async function fetchJson(url) {
            try {
                let response = await fetch(url);
                return await response.json();
            } catch (e) {
                console.error("Gagal menarik data API wilayah: ", e);
                return [];
            }
        }

        // Memuat Utama Data Provinsi - Diarahkan ke rute prefix /master/
        async function loadProvinsi() {
            let data = await fetchJson(`/master/api/provinsi`);
            provSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            data.forEach(p => {
                let selected = (p.name === currentProv) ? 'selected' : '';
                provSelect.innerHTML += `<option value="${p.name}" data-id="${p.code}" ${selected}>${p.name}</option>`;
            });

            if (currentProv) {
                provSelect.dispatchEvent(new Event('change'));
            }
        }

        // Event: Provinsi Berubah -> Mengambil Kabupaten
        provSelect.addEventListener('change', async function() {
            kotaSelect.innerHTML = '<option value="">-- Memuat... --</option>';
            kecSelect.innerHTML  = '<option value="">-- Pilih Kecamatan --</option>';
            kelSelect.innerHTML  = '<option value="">-- Pilih Kelurahan --</option>';
            
            let opt = this.options[this.selectedIndex];
            let provId = opt ? opt.getAttribute('data-id') : null;
            if(!provId) { kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>'; return; }

            let data = await fetchJson(`/master/api/kota/${provId}`);
            kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            data.forEach(k => {
                let selected = (k.name === currentKota) ? 'selected' : '';
                kotaSelect.innerHTML += `<option value="${k.name}" data-id="${k.code}" ${selected}>${k.name}</option>`;
            });

            if (currentKota) {
                kotaSelect.dispatchEvent(new Event('change'));
            }
        });

        // Event: Kota Berubah -> Mengambil Kecamatan
        kotaSelect.addEventListener('change', async function() {
            kecSelect.innerHTML = '<option value="">-- Memuat... --</option>';
            kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

            let opt = this.options[this.selectedIndex];
            let kotaId = opt ? opt.getAttribute('data-id') : null;
            if(!kotaId) { kecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>'; return; }

            let data = await fetchJson(`/master/api/kecamatan/${kotaId}`);
            kecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            data.forEach(kc => {
                let selected = (kc.name === currentKec) ? 'selected' : '';
                kecSelect.innerHTML += `<option value="${kc.name}" data-id="${kc.code}" ${selected}>${kc.name}</option>`;
            });

            if (currentKec) {
                kecSelect.dispatchEvent(new Event('change'));
            }
        });

        // Event: Kecamatan Berubah -> Mengambil Desa/Kelurahan
        kecSelect.addEventListener('change', async function() {
            kelSelect.innerHTML = '<option value="">-- Memuat... --</option>';

            let opt = this.options[this.selectedIndex];
            let kecId = opt ? opt.getAttribute('data-id') : null;
            if(!kecId) { kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>'; return; }

            let data = await fetchJson(`/master/api/kelurahan/${kecId}`);
            kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            data.forEach(kl => {
                let selected = (kl.name === currentKel) ? 'selected' : '';
                kelSelect.innerHTML += `<option value="${kl.name}" ${selected}>${kl.name}</option>`;
            });
        });

        // Inisialisasi Lifecycle saat DOM Siap
        document.addEventListener("DOMContentLoaded", function() {
            loadProvinsi();
            
            // Mengatasi glitch render peta Leaflet dalam kontainer dinamis Tailwind Grid
            setTimeout(() => { 
                map.invalidateSize(); 
            }, 300);
        });
    </script>
</x-app-layout>