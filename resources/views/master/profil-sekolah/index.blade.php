<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Identitas Resmi & Lokasi Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm space-y-1">
                    <span class="font-bold flex items-center gap-1">⚠️ Terjadi Kesalahan Validasi:</span>
                    <ul class="list-disc list-inside text-xs pl-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Data Pokok Pendidikan & Kewilayahan</h3>
                    <p class="text-xs text-gray-500">Sesuaikan data identitas resmi lembaga dan titik koordinat geografis untuk pemetaan berkas rapor serta portal publik.</p>
                </div>

                <form action="{{ route('master.profil-sekolah.save') }}" method="POST" class="p-6 space-y-8">
                    @csrf
                    
                    <div>
                        <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider border-b border-indigo-50 pb-2 mb-4">I. Profil & Nomor Registrasi</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Resmi Sekolah *</label>
                                <input type="text" name="nama_sekolah" required value="{{ old('nama_sekolah', $profil->nama_sekolah ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jenjang Sekolah *</label>
                                <input type="text" name="jenjang" required value="{{ old('jenjang', $profil->jenjang ?? '') }}" placeholder="Contoh: SMK" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">NPSN *</label>
                                <input type="text" name="npsn" required value="{{ old('npsn', $profil->npsn ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">NSS</label>
                                <input type="text" name="nss" value="{{ old('nss', $profil->nss ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Fase Kurikulum</label>
                                <input type="text" name="fase" value="{{ old('fase', $profil->fase ?? '') }}" placeholder="Fase F" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider border-b border-indigo-50 pb-2 mb-4">II. Lokasi Kewilayahan & Koordinat Peta</h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Provinsi *</label>
                                        <select id="provinsi" name="provinsi" data-current="{{ old('provinsi', $profil->provinsi ?? '') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kabupaten / Kota *</label>
                                        <select id="kota" name="kota" data-current="{{ old('kota', $profil->kota ?? '') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                            <option value="">-- Pilih Kota --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kecamatan *</label>
                                        <select id="kecamatan" name="kecamatan" data-current="{{ old('kecamatan', $profil->kecamatan ?? '') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Desa / Kelurahan *</label>
                                        <select id="kelurahan" name="kelurahan" data-current="{{ old('kelurahan', $profil->kelurahan ?? '') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                            <option value="">-- Pilih Kelurahan --</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Jalan / Kampung / RT-RW *</label>
                                    <textarea id="alamat" name="alamat" rows="2" required placeholder="Nama jalan, nomor, RT/RW" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('alamat', $profil->alamat ?? '') }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Pos *</label>
                                        <input type="text" id="kode_pos" name="kode_pos" required value="{{ old('kode_pos', $profil->kode_pos ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Latitude *</label>
                                        <input type="text" id="latitude" name="latitude" required value="{{ old('latitude', $profil->latitude ?? '-6.2088') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Longitude *</label>
                                        <input type="text" id="longitude" name="longitude" required value="{{ old('longitude', $profil->longitude ?? '106.8456') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                                    </div>
                                </div>
                                
                                <button type="button" onclick="cariAlamatKePeta()" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-[11px] font-bold rounded-lg shadow-sm cursor-pointer transition-colors">
                                    🔍 Ambil Koordinat Otomatis Dari Teks Alamat Di Atas
                                </button>
                            </div>

                            <div class="flex flex-col">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Titik Koordinat Lokasi Lembaga (Klik/Geser Pin pada Peta)</label>
                                <div id="map" class="w-full h-72 rounded-xl border border-gray-200 shadow-inner z-10"></div>
                                <p class="text-[10px] text-gray-400 mt-1">💡 Anda dapat menggeser pin merah di atas atau klik di mana saja pada peta untuk memperbarui koordinat secara instan.</p>
                            </div>

                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider border-b border-indigo-50 pb-2 mb-4">III. Media Komunikasi Elektronik</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Email Resmi Sekolah *</label>
                                <input type="email" name="email" required value="{{ old('email', $profil->email ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Website Resmi URL</label>
                                <input type="url" name="website" value="{{ old('website', $profil->website ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                            💾 Simpan & Sinkronisasi Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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