<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendataan Siswa Ekstrakurikuler</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-indigo-600 py-6 px-8 text-center">
            <h2 class="text-2xl font-bold text-white">Formulir Pendataan</h2>
            <p class="text-indigo-100 mt-1">Ekstrakurikuler Sekolah</p>
        </div>

        <div class="p-8">
            <!-- Menampilkan Pesan Sukses / Error -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('ekskul.pendaftaran.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- 1. Pilih Ekskul -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">1. Pilih Ekstrakurikuler</label>
                    <select name="ekstrakurikuler_id" id="ekskul_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border" onchange="cekDropdown()">
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        @foreach($ekskulList as $eks)
                            <option value="{{ $eks->id }}">{{ $eks->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Pilih Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">2. Pilih Kelas</label>
                    <select name="kelas_id" id="kelas_id" required disabled class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-100" onchange="fetchSiswa()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <p id="kelas_hint" class="text-xs text-red-500 mt-1">Pilih ekstrakurikuler terlebih dahulu</p>
                </div>

                <!-- 3. Pilih Nama Siswa (Otomatis muncul dari AJAX) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">3. Nama Kamu</label>
                    <select name="siswa_id" id="siswa_id" required disabled class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border bg-gray-100">
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                </div>

                <!-- 4. Nomor HP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp Aktif</label>
                    <input type="text" name="nomor_hp" required placeholder="Contoh: 081234567890" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border">
                </div>

                <!-- 5. Motivasi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivasi Bergabung</label>
                    <textarea name="motivasi" rows="3" required placeholder="Kenapa kamu ingin ikut ekskul ini?" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border"></textarea>
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Daftar Sekarang
                </button>
            </form>
        </div>
    </div>

    <script>
        // Membuka gembok kelas setelah ekskul dipilih
        function cekDropdown() {
            const ekskul = document.getElementById('ekskul_id').value;
            const kelas = document.getElementById('kelas_id');
            const hint = document.getElementById('kelas_hint');
            const siswa = document.getElementById('siswa_id');

            if(ekskul) {
                kelas.disabled = false;
                kelas.classList.remove('bg-gray-100');
                hint.classList.add('hidden');
                // Panggil ulang fetch jika kelas sudah terlanjur dipilih sebelumnya
                if(kelas.value) fetchSiswa(); 
            } else {
                kelas.disabled = true;
                kelas.classList.add('bg-gray-100');
                kelas.value = '';
                hint.classList.remove('hidden');
                
                siswa.disabled = true;
                siswa.innerHTML = '<option value="">-- Pilih Kelas Terlebih Dahulu --</option>';
                siswa.classList.add('bg-gray-100');
            }
        }

        // Mengambil data nama anak lewat API AJAX
        function fetchSiswa() {
            const ekskul_id = document.getElementById('ekskul_id').value;
            const kelas_id = document.getElementById('kelas_id').value;
            const siswaSelect = document.getElementById('siswa_id');

            if (!ekskul_id || !kelas_id) {
                siswaSelect.innerHTML = '<option value="">-- Pilih Kelas Terlebih Dahulu --</option>';
                siswaSelect.disabled = true;
                return;
            }

            // Tampilkan tulisan loading
            siswaSelect.innerHTML = '<option value="">Sedang memuat data siswa...</option>';
            siswaSelect.disabled = true;

            // Memanggil API yang barusan kita buat
            fetch(`/api/siswa-ekskul/${ekskul_id}/${kelas_id}`)
                .then(response => response.json())
                .then(data => {
                    siswaSelect.disabled = false;
                    siswaSelect.classList.remove('bg-gray-100');
                    
                    if (data.length === 0) {
                        siswaSelect.innerHTML = '<option value="">Semua siswa di kelas ini sudah mendaftar ekskul ini</option>';
                        siswaSelect.disabled = true;
                        siswaSelect.classList.add('bg-gray-100');
                        return;
                    }

                    siswaSelect.innerHTML = '<option value="">-- Pilih Nama Kamu --</option>';
                    data.forEach(siswa => {
                        siswaSelect.innerHTML += `<option value="${siswa.id}">${siswa.nama_lengkap}</option>`;
                    });
                })
                .catch(error => {
                    siswaSelect.innerHTML = '<option value="">Terjadi kesalahan jaringan.</option>';
                });
        }
    </script>
</body>
</html>