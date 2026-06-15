<x-app-layout>
    <x-slot name="header">
        {{ __('Moderasi Komentar Blog') }}
    </x-slot>

    <div class="space-y-6">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-bold text-gray-900">Daftar Tanggapan Pembaca</h3>
                <p class="text-xs text-gray-500">Periksa isi komentar sebelum memberikan persetujuan tayang agar portal informasi sekolah bebas dari spam.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6 w-1/4">Pengirim</th>
                            <th class="p-4 w-1/3">Isi Komentar</th>
                            <th class="p-4">Artikel Terkait</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 pr-6 text-center w-40">Aksi Moderasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($komentars as $kom)
                            <tr class="hover:bg-gray-50/80 transition-colors {{ !$kom->disetujui ? 'bg-amber-50/30' : '' }}">
                                <td class="p-4 pl-6">
                                    <p class="font-bold text-gray-900 text-sm">👤 {{ $kom->nama }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $kom->email }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">📅 {{ $kom->created_at->format('d M Y H:i') }} WIB</p>
                                </td>
                                
                                <td class="p-4">
                                    <div class="text-gray-600 italic bg-gray-50 p-2.5 rounded-lg border border-gray-100 max-w-md whitespace-pre-line text-xs">
                                        "{!! e($kom->komentar) !!}"
                                    </div>
                                </td>
                                
                                <td class="p-4">
                                    <p class="font-semibold text-gray-700 max-w-xs truncate" title="{{ $kom->blog->judul ?? 'Artikel Dihapus' }}">
                                        📰 {{ $kom->blog->judul ?? '⚠️ Artikel telah dihapus' }}
                                    </p>
                                </td>
                                
                                <td class="p-4 text-center">
                                    @if($kom->disetujui)
                                        <span class="px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold uppercase rounded-md shadow-sm">
                                            🟢 Tayang
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase rounded-md shadow-sm animate-pulse">
                                            ⏳ Pending
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                                        <form action="{{ route('admin.komentar-blog.toggle', $kom->id) }}" method="POST" class="inline w-full sm:w-auto">
                                            @csrf
                                            @method('PATCH')
                                            @if($kom->disetujui)
                                                <button type="submit" class="w-full px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold rounded border border-gray-300 transition-colors cursor-pointer">
                                                    🔒 Sembunyikan
                                                </button>
                                            @else
                                                <button type="submit" class="w-full px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded border border-indigo-700 shadow-sm transition-colors cursor-pointer">
                                                    ✅ Setujui
                                                </button>
                                            @endif
                                        </form>

                                        <form action="{{ route('admin.komentar-blog.destroy', $kom->id) }}" method="POST" onsubmit="return confirm('Hapus komentar dari {{ $kom->nama }}? Tindakan ini tidak dapat dibatalkan.')" class="inline w-full sm:w-auto">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold rounded border border-rose-200 transition-colors cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada komentar dari pembaca blog.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>