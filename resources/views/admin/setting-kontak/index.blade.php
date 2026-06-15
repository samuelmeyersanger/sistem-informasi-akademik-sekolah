<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Kontak Informasi Sekolah') }}
    </x-slot>

    <div class="max-w-4xl space-y-6">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                <p class="font-bold mb-1">Gagal menyimpan pengaturan. Silakan cek kembali:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-bold text-gray-900">Konfigurasi Pusat Informasi & Sosial Media</h3>
                <p class="text-xs text-gray-500">Data di bawah ini akan tampil otomatis di bagian Footer website utama, halaman kontak, dan tombol chat bantuan.</p>
            </div>

            <form action="{{ route('admin.setting-kontak.save') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-1">
                        📞 Saluran Komunikasi Utama
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Telepon Kantor / Sekolah</label>
                            <input type="text" name="settings[telepon]" value="{{ old('settings.telepon', $settings['telepon'] ?? '') }}" placeholder="Contoh: (021) 789-1234" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">WhatsApp Center (Gunakan Kode Negara)</label>
                            <input type="text" name="settings[whatsapp]" value="{{ old('settings.whatsapp', $settings['whatsapp'] ?? '') }}" placeholder="Contoh: 628123456789" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <p class="text-[10px] text-gray-400 mt-1">Awali langsung dengan angka 62 (tanpa tanda + atau angka 0 di depan).</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Email Official Sekolah</label>
                            <input type="email" name="settings[email]" value="{{ old('settings.email', $settings['email'] ?? '') }}" placeholder="Contoh: info@smkn1kotasekolah.sch.id" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Surat Fisik / Kantor Tata Usaha</label>
                            <textarea name="settings[alamat]" rows="3" placeholder="Tuliskan alamat lengkap instansi beserta kode pos..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('settings.alamat', $settings['alamat'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 my-2">

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-1">
                        🌐 Jejaring Sosial Media Resmi
                    </h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Link Akun / Halaman Facebook</label>
                            <input type="url" name="settings[facebook]" value="{{ old('settings.facebook', $settings['facebook'] ?? '') }}" placeholder="Contoh: https://facebook.com/nama.sekolah resmi" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Link Akun Instagram Resmi</label>
                            <input type="url" name="settings[instagram]" value="{{ old('settings.instagram', $settings['instagram'] ?? '') }}" placeholder="Contoh: https://instagram.com/sekolahofficial" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Link Saluran / Channels YouTube</label>
                            <input type="url" name="settings[youtube]" value="{{ old('settings.youtube', $settings['youtube'] ?? '') }}" placeholder="Contoh: https://youtube.com/c/OfficialSekolahKita" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-md transition-colors flex items-center gap-1 cursor-pointer">
                        💾 Simpan Semua Pengaturan Kontak
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>