<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">💬</span> {{ __('Moderasi Komentar Blog') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Saring tanggapan masuk dan kelola interaksi pembaca untuk menjaga kualitas publikasi.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
        openDelete: false, 
        deleteAction: '',
        deleteTargetSender: ''
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Antrean Tanggapan</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar komentar yang menunggu atau sudah dimoderasi.</p>
                    </div>
                    
                    <form action="{{ route('master.komentar-blog.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch gap-3 w-full lg:w-auto">
                        
                        {{-- Dropdown Status --}}
                        <div class="relative w-full sm:w-48">
                            <select name="status" class="w-full text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl shadow-sm py-2.5 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors appearance-none pr-10 cursor-pointer">
                                <option value="">Tampilkan Semua</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>🟢 Disetujui Tampil</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>🟡 Tertunda (Pending)</option>
                            </select>
                        </div>

                        {{-- Input Search --}}
                        <div class="relative flex items-center w-full sm:w-64 group">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari isi pesan atau nama..." 
                                class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                            @if(request('search') || request('status'))
                                <a href="{{ route('master.komentar-blog.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Reset Pencarian">
                                    <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center">
                            <span class="hidden sm:inline">🔍</span> Saring
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-8 w-64">Identitas Pengirim</th>
                                <th class="p-5">Isi Pesan & Tautan Artikel</th>
                                <th class="p-5 w-36 text-center">Status Tayang</th>
                                <th class="p-5 pr-8 text-center w-40">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($komentars as $komentar)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-200 {{ !$komentar->disetujui ? 'bg-amber-50/30' : '' }}">
                                    <td class="p-5 pl-8 align-top">
                                        <div class="flex gap-4">
                                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-600 flex items-center justify-center font-black text-sm shrink-0 border border-slate-200 shadow-inner">
                                                {{ strtoupper(substr($komentar->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-slate-900 text-base leading-tight">{{ $komentar->nama }}</div>
                                                <div class="text-indigo-500 font-bold text-xs mt-0.5 truncate max-w-[150px] sm:max-w-xs" title="{{ $komentar->email }}">
                                                    {{ $komentar->email }}
                                                </div>
                                                <div class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-widest">
                                                    {{ $komentar->created_at->translatedFormat('d M Y, H:i') }} WIB
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="p-5 align-top">
                                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-sm text-slate-700 leading-relaxed relative">
                                            {{-- Triangle for speech bubble effect --}}
                                            <div class="absolute w-3 h-3 bg-white border-l border-t border-slate-200 -left-1.5 top-4 transform -rotate-45 hidden md:block"></div>
                                            {{ $komentar->isi_komentar }}
                                        </div>
                                        <div class="mt-3 ml-2 flex items-center gap-2 text-xs text-slate-500 font-bold">
                                            <span class="p-1 bg-slate-100 rounded-lg text-lg">📑</span>
                                            <span>Ditulis pada artikel: 
                                                <span class="text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md italic">
                                                    {{ $komentar->blog->judul ?? '— Artikel Telah Dihapus —' }}
                                                </span>
                                            </span>
                                        </div>
                                    </td>

                                    <td class="p-5 text-center align-top whitespace-nowrap pt-7">
                                        @if($komentar->disetujui)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                                DIIZINKAN
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                                TERTUNDA
                                            </div>
                                        @endif
                                    </td>

                                    <td class="p-5 pr-8 text-center align-top pt-6">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('master.komentar-blog.toggle-approve', $komentar->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                @if($komentar->disetujui)
                                                    <button type="submit" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 hover:bg-slate-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Cabut Izin Tayang (Sembunyikan)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                                    </button>
                                                @else
                                                    <button type="submit" class="inline-flex items-center justify-center w-10 h-10 bg-emerald-50 border border-emerald-200 text-emerald-600 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-100 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Setujui Tampil Publik">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                @endif
                                            </form>

                                            <button type="button" 
                                                    @click="
                                                        deleteAction = '{{ route('master.komentar-blog.destroy', $komentar->id) }}';
                                                        deleteTargetSender = '{{ addslashes($komentar->nama) }}';
                                                        openDelete = true;
                                                    "
                                                    class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Komentar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                📭
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Kotak Komentar Bersih</h4>
                                            <span class="text-sm">Tidak ada komentar yang menunggu dimoderasi atau sesuai dengan kriteria filter.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($komentars, 'hasPages') && $komentars->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $komentars->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL DELETE --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🗑️
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hapus Komentar?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Komentar kiriman dari <strong class="text-slate-800" x-text="deleteTargetSender"></strong> akan dihapus selamanya.
                    </p>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex justify-center gap-3 w-full pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>