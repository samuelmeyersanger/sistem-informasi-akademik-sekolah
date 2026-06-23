<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Moderasi Komentar Blog') }}
        </h2>
    </x-slot>

    <div x-data="{ 
        openDelete: false, 
        deleteAction: '',
        deleteTargetSender: ''
    }" class="py-12 bg-slate-900/10 min-h-screen relative">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="p-4 mb-2 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-200">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Komentar Pembaca</h3>
                        <p class="text-xs text-gray-500">Moderasi tanggapan masuk untuk menghindari spam dan menjaga kualitas diskusi di blog.</p>
                    </div>
                    
                    <form action="{{ route('master.komentar-blog.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch gap-2 w-full lg:w-auto">
                        <select name="status" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>🟢 Disetujui</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>🟡 Tertunda (Pending)</option>
                        </select>

                        <div class="relative flex items-center">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau isi..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                            @if(request('search') || request('status'))
                                <a href="{{ route('master.komentar-blog.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Reset">&times;</a>
                            @endif
                        </div>

                        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg text-center cursor-pointer transition-colors">
                            Saring
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/70 text-xs font-bold text-gray-600 uppercase border-b border-gray-200">
                                <th class="p-4 w-52">Pengirim</th>
                                <th class="p-4">Komentar & Artikel</th>
                                <th class="p-4 w-32 text-center">Status</th>
                                <th class="p-4 text-center w-48">Aksi Moderasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($komentars as $komentar)
                                <tr class="hover:bg-slate-50/80 transition-colors {{ !$komentar->disetujui ? 'bg-amber-50/20' : '' }}">
                                    <td class="p-4 align-top">
                                        <div class="font-bold text-gray-900 text-sm">{{ $komentar->nama }}</div>
                                        <div class="text-gray-400 text-[11px] mt-0.5 font-mono">{{ $komentar->email }}</div>
                                        <div class="text-[10px] text-gray-400 mt-2">📅 {{ $komentar->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                    </td>

                                    <td class="p-4">
                                        <div class="text-gray-800 bg-gray-50/50 p-3 rounded-lg border border-gray-100 text-xs leading-relaxed break-words whitespace-pre-line">
                                            {{ $komentar->isi_komentar }}
                                        </div>
                                        <div class="mt-2 text-[11px] text-gray-400 flex items-center gap-1 font-medium">
                                            <span>🔗 Pada Artikel:</span>
                                            <span class="text-indigo-600 font-semibold italic">
                                                {{ $komentar->blog->judul ?? 'Artikel Telah Dihapus' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="p-4 text-center whitespace-nowrap">
                                        @if($komentar->disetujui)
                                            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Approved
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                                                Pending
                                            </span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <form action="{{ route('master.komentar-blog.toggle-approve', $komentar->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                @if($komentar->disetujui)
                                                    <button type="submit" class="px-2.5 py-1.5 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded shadow-sm text-[11px] transition-all cursor-pointer" title="Sembunyikan komentar dari publik">
                                                        🔒 Sembunyikan
                                                    </button>
                                                </if>
                                                @else
                                                    <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded shadow-sm text-[11px] transition-all cursor-pointer" title="Setujui komentar agar tampil">
                                                        ✅ Setujui
                                                    </button>
                                                @endif
                                            </form>

                                            <button type="button" 
                                                    @click="
                                                        deleteAction = '{{ route('master.komentar-blog.destroy', $komentar->id) }}';
                                                        deleteTargetSender = '{{ addslashes($komentar->nama) }}';
                                                        openDelete = true;
                                                    "
                                                    class="px-2.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded shadow-sm text-[11px] transition-all cursor-pointer" 
                                                    title="Hapus komentar">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Belum ada data komentar pembaca yang masuk dalam kriteria ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($komentars, 'hasPages') && $komentars->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $komentars->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Komentar Permanen?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus komentar dari <span class="font-bold text-gray-800" x-text="deleteTargetSender"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <form :action="deleteAction" method="POST" class="flex justify-center gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>