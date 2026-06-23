<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Logo & Kelengkapan Cetak Dokumen') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm space-y-1">
                    <div class="flex items-center gap-2 font-semibold">
                        <span>⚠️</span> Terjadi kesalahan validasi berkas:
                    </div>
                    <ul class="list-disc pl-5 text-xs space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Aset Gambar & Atribut Resmi Instansi</h3>
                    <p class="text-xs text-gray-500">Berkas gambar transparan (PNG) di bawah ini digunakan otomatis untuk kop surat administrasi, cetak lembar Rapor Siswa, piagam, serta kartu pelajar.</p>
                </div>

                <form action="{{ route('master.pengaturan-logo.save') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="border border-gray-100 p-4 rounded-xl bg-gray-50/30 flex flex-col justify-between">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Logo Pemerintah Daerah (PEMDA)</label>
                                <p class="text-[10px] text-gray-400 mb-3">Digunakan pada sisi kiri Kop Surat kedinasan. Rekomendasi format PNG transparan.</p>
                                <input type="file" name="logo_pemda" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div class="mt-4 flex items-center justify-center p-3 bg-white border border-gray-100 rounded-lg min-h-[100px]">
                                @if($logoSetting && $logoSetting->logo_pemda)
                                    <img src="{{ asset('storage/' . $logoSetting->logo_pemda) }}" class="h-20 object-contain" alt="Logo Pemda">
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum diunggah</span>
                                @endif
                            </div>
                        </div>

                        <div class="border border-gray-100 p-4 rounded-xl bg-gray-50/30 flex flex-col justify-between">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Logo Resmi Sekolah / Tut Wuri</label>
                                <p class="text-[10px] text-gray-400 mb-3">Digunakan untuk header website, favicon, sisi kanan Kop Surat, dan Rapor.</p>
                                <input type="file" name="logo_sekolah" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div class="mt-4 flex items-center justify-center p-3 bg-white border border-gray-100 rounded-lg min-h-[100px]">
                                @if($logoSetting && $logoSetting->logo_sekolah)
                                    <img src="{{ asset('storage/' . $logoSetting->logo_sekolah) }}" class="h-20 object-contain" alt="Logo Sekolah">
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum diunggah</span>
                                @endif
                            </div>
                        </div>

                        <div class="md:col-span-2 border border-gray-100 p-4 rounded-xl bg-gray-50/30">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Gambar Banner Kop Surat Jadi</label>
                            <p class="text-[10px] text-gray-400 mb-3">Opsional. Jika diisi, sistem cetak dokumen otomatis memakai banner Kop Surat ini secara penuh (Landscape lebar).</p>
                            <input type="file" name="kop_surat" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <div class="mt-4 flex items-center justify-center p-3 bg-white border border-gray-100 rounded-lg min-h-[120px]">
                                @if($logoSetting && $logoSetting->kop_surat)
                                    <img src="{{ asset('storage/' . $logoSetting->kop_surat) }}" class="w-full max-h-24 object-contain" alt="Kop Surat">
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum ada banner kop surat kustom terunggah</span>
                                @endif
                            </div>
                        </div>

                        <div class="border border-gray-100 p-4 rounded-xl bg-gray-50/30 flex flex-col justify-between">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanda Tangan Kepala Sekolah (Digital)</label>
                                <p class="text-[10px] text-gray-400 mb-3">Gunakan tanda tangan berlatar transparan/putih polos bersih.</p>
                                <input type="file" name="ttd_kepala_sekolah" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div class="mt-4 flex items-center justify-center p-3 bg-white border border-gray-100 rounded-lg min-h-[100px]">
                                @if($logoSetting && $logoSetting->ttd_kepala_sekolah)
                                    <img src="{{ asset('storage/' . $logoSetting->ttd_kepala_sekolah) }}" class="h-16 object-contain" alt="TTD Kepsek">
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum diunggah</span>
                                @endif
                            </div>
                        </div>

                        <div class="border border-gray-100 p-4 rounded-xl bg-gray-50/30 flex flex-col justify-between">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stempel / Cap Resmi Sekolah</label>
                                <p class="text-[10px] text-gray-400 mb-3">Disarankan berformat PNG transparan warna ungu/biru instansi.</p>
                                <input type="file" name="stempel_sekolah" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div class="mt-4 flex items-center justify-center p-3 bg-white border border-gray-100 rounded-lg min-h-[100px]">
                                @if($logoSetting && $logoSetting->stempel_sekolah)
                                    <img src="{{ asset('storage/' . $logoSetting->stempel_sekolah) }}" class="h-16 object-contain" alt="Stempel Sekolah">
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum diunggah</span>
                                @endif
                            </div>
                        </div>

                        <div class="md:col-span-2 border border-gray-100 p-4 rounded-xl bg-gray-50/30">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kombinasi Langsung TTD + Stempel Menyatu</label>
                            <p class="text-[10px] text-gray-400 mb-3">Berguna untuk otomatisasi cetak cepat massal tanpa perlu menumpuk gambar via CSS editor.</p>
                            <input type="file" name="ttd_dan_stempel" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <div class="mt-4 flex items-center justify-center p-3 bg-white border border-gray-100 rounded-lg min-h-[120px]">
                                @if($logoSetting && $logoSetting->ttd_dan_stempel)
                                    <img src="{{ asset('storage/' . $logoSetting->ttd_dan_stempel) }}" class="h-24 object-contain" alt="TTD dan Stempel">
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum diunggah</span>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-md transition-colors cursor-pointer flex items-center gap-1">
                            💾 Simpan & Perbarui Seluruh Logo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>