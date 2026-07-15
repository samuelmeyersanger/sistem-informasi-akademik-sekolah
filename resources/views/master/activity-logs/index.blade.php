<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Log Aktivitas Sistem') }}
            </h2>
        </div>
    </x-slot>

    <div x-data="{ 
        openModal: false, 
        detail: { kegiatan: '', pengguna: '', waktu: '', sebelum: null, sesudah: null } 
    }" class="py-8 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Riwayat Aktivitas</h3>
                        <p class="text-sm text-slate-500 mt-1">Daftar jejak audit dan manipulasi data pada sistem.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('master.activity-logs') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-72">
                                <svg class="absolute left-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas atau pengguna..." 
                                    class="text-sm rounded-xl border-slate-200 pl-10 pr-8 py-2 focus:border-indigo-500 focus:ring-indigo-500/20 shadow-sm w-full transition-all">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.activity-logs') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 transition-colors" title="Bersihkan Pencarian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors shrink-0 shadow-sm">
                                Cari Data
                            </button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 w-16 text-center">No</th>
                                <th class="p-4 w-44">Waktu (WIB)</th>
                                <th class="p-4 w-60">Pengguna</th>
                                <th class="p-4 w-40">Aktivitas</th>
                                <th class="p-4 pr-6 w-48">Koneksi & Perangkat</th>
                                <th class="p-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-sm">
                            @forelse($logs as $index => $log)
                                <tr class="hover:bg-slate-50/80 transition-colors duration-150 group">
                                    <td class="p-4 text-center text-xs font-mono text-slate-400">
                                        {{ $logs->firstItem() + $index }}
                                    </td>

                                    <td class="p-4 whitespace-nowrap text-slate-600">
                                        {{ $log->created_at ? $log->created_at->format('d M Y, H:i:s') : '-' }}
                                    </td>
                                    
                                    <td class="p-4">
                                        @if($log->user)
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ substr($log->user->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-slate-800">{{ $log->user->name }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $log->user->email }}</div>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($log->user->roles as $role)
                                                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide rounded-md bg-slate-100 text-slate-600">
                                                                {{ $role->display_name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 text-slate-400 italic">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                </div>
                                                Sistem / Guest
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="p-4 whitespace-nowrap">
                                        @php
                                            $act = strtolower($log->activity);
                                            $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                            
                                            if (str_contains($act, 'create') || str_contains($act, 'tambah') || str_contains($act, 'store')) {
                                                $badgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-200/60';
                                            } elseif (str_contains($act, 'update') || str_contains($act, 'ubah') || str_contains($act, 'edit')) {
                                                $badgeClass = 'bg-indigo-50 text-indigo-600 border-indigo-200/60';
                                            } elseif (str_contains($act, 'delete') || str_contains($act, 'hapus') || str_contains($act, 'destroy')) {
                                                $badgeClass = 'bg-rose-50 text-rose-600 border-rose-200/60';
                                            } elseif (str_contains($act, 'login')) {
                                                $badgeClass = 'bg-amber-50 text-amber-600 border-amber-200/60';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-1 border text-[10px] font-bold rounded-full uppercase tracking-wider {{ $badgeClass }}">
                                            {{ $log->activity }}
                                        </span>
                                    </td>
                                    
                                    <td class="p-4 pr-6">
                                        <div class="flex items-center gap-1.5 text-xs font-mono font-medium text-slate-600">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            {{ $log->ip_address ?? '0.0.0.0' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-1 truncate max-w-[180px]" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="p-4 text-center">
                                        <button type="button"
                                            data-kegiatan="{{ $log->activity }}"
                                            data-pengguna="{{ $log->user->name ?? 'Sistem / Guest' }}"
                                            data-waktu="{{ $log->created_at ? $log->created_at->format('d F Y - H:i:s') : '-' }}"
                                            data-sebelum="{{ isset($log->properties['sebelum']) ? json_encode($log->properties['sebelum']) : (isset($log->properties) && !isset($log->properties['sesudah']) ? json_encode($log->properties) : '') }}"
                                            data-sesudah="{{ isset($log->properties['sesudah']) ? json_encode($log->properties['sesudah']) : '' }}"
                                            @click="
                                                let btn = $el;
                                                detail.kegiatan = btn.getAttribute('data-kegiatan');
                                                detail.pengguna = btn.getAttribute('data-pengguna');
                                                detail.waktu = btn.getAttribute('data-waktu');
                                                
                                                let seb = btn.getAttribute('data-sebelum');
                                                let ses = btn.getAttribute('data-sesudah');
                                                
                                                detail.sebelum = (seb && seb !== 'null') ? JSON.parse(seb) : null;
                                                detail.sesudah = (ses && ses !== 'null') ? JSON.parse(ses) : null;
                                                
                                                openModal = true;
                                            " 
                                            class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all cursor-pointer opacity-80 group-hover:opacity-100" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            @if(request('search'))
                                                <span class="text-sm">Tidak ditemukan data log untuk "{{ request('search') }}".</span>
                                            @else
                                                <span class="text-sm">Belum ada rekaman aktivitas saat ini.</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openModal" 
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-4xl w-full max-h-[85vh] flex flex-col overflow-hidden" 
                 @click.away="openModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Detail Log Aktivitas
                    </h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1.5 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-6 text-sm bg-slate-50/50">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-white border border-slate-200/60 shadow-sm rounded-xl">
                        <div>
                            <span class="block text-xs text-slate-400 mb-1 uppercase tracking-wider font-semibold">Aktivitas</span>
                            <span class="text-sm font-bold text-indigo-600" x-text="detail.kegiatan"></span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 mb-1 uppercase tracking-wider font-semibold">Pengguna</span>
                            <span class="text-sm font-bold text-slate-800" x-text="detail.pengguna"></span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 mb-1 uppercase tracking-wider font-semibold">Waktu Eksekusi</span>
                            <span class="text-sm font-bold text-slate-800" x-text="detail.waktu"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 mb-3 text-sm font-bold text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Data Sebelum (Lama)
                            </div>
                            <div class="bg-slate-900 p-4 rounded-xl overflow-x-auto min-h-[220px] flex flex-col justify-start shadow-inner">
                                <template x-if="detail.sebelum">
                                    <pre class="text-slate-300 font-mono text-[12px] leading-relaxed whitespace-pre-wrap block w-full text-left bg-transparent p-0 m-0 border-0" 
                                         x-text="JSON.stringify(detail.sebelum, null, 2)"></pre>
                                </template>
                                <template x-if="!detail.sebelum">
                                    <div class="flex items-center justify-center h-full text-slate-500 italic font-mono text-xs">
                                        Tidak ada data rekaman awal.
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 mb-3 text-sm font-bold text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Data Sesudah (Baru)
                            </div>
                            <div class="bg-slate-900 p-4 rounded-xl overflow-x-auto min-h-[220px] flex flex-col justify-start shadow-inner">
                                <template x-if="detail.sesudah">
                                    <pre class="text-emerald-400 font-mono text-[12px] leading-relaxed whitespace-pre-wrap block w-full text-left bg-transparent p-0 m-0 border-0" 
                                         x-text="JSON.stringify(detail.sesudah, null, 2)"></pre>
                                </template>
                                <template x-if="!detail.sesudah && detail.sebelum">
                                    <pre class="text-emerald-400 font-mono text-[12px] leading-relaxed whitespace-pre-wrap block w-full text-left bg-transparent p-0 m-0 border-0" 
                                         x-text="JSON.stringify(detail.sebelum, null, 2)"></pre>
                                </template>
                                <template x-if="!detail.sesudah && !detail.sebelum">
                                    <div class="flex items-center justify-center h-full text-slate-500 italic font-mono text-xs">
                                        Tidak ada modifikasi payload data.
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 bg-white flex justify-end">
                    <button @click="openModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all cursor-pointer">
                        Tutup Panel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>