<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Download') }}
        </h2>
    </x-slot>
    <div class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Download Dokumen Sekolah</h3>
                    <p class="text-xs text-gray-500">Pilih jenis dokumen yang ingin Anda unduh dalam format Excel atau PDF.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Form Download Absensi -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-indigo-50 border-b border-gray-200 p-4">
                            <h4 class="font-bold text-indigo-900 text-sm flex items-center gap-2">
                                📝 Daftar Hadir (Absensi)
                            </h4>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('pusat_download.absensi') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kelas *</label>
                                    <select name="kelas_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($daftarKelas as $kelas)
                                            <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <button type="submit" name="format" value="excel" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📊 Excel
                                    </button>
                                    <button type="submit" name="format" value="pdf" class="flex-1 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📄 PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Tempat untuk fitur Jadwal di masa depan -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm opacity-60">
                        <div class="bg-gray-50 border-b border-gray-200 p-4">
                            <h4 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                                📅 Jadwal Pelajaran
                            </h4>
                        </div>
                        <div class="p-4 flex items-center justify-center h-32 text-xs font-medium text-gray-400">
                            (Fitur Segera Hadir)
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>