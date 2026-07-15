<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">📡</span> {{ __('Log Aktivitas') }}
                </h2>
            </div>
            
            <form action="{{ route('master.activity-logs') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-80 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas, user, IP..." 
                        class="w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-medium text-sm shadow-sm transition-all text-slate-700 placeholder-slate-400">
                    
                    @if(request('search'))
                        <a href="{{ route('master.activity-logs') }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-rose-500 transition-colors cursor-pointer" title="Reset Pencarian">
                            <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-2xl shadow-lg shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                    <span>🔍</span> Cari
                </button>
            </form>
        </div>
    </x-slot>

    <div x-data="{ 
        openModal: false, 
        detail: { kegiatan: '', pengguna: '', waktu: '', sebelum: null, sesudah: null } 
    }" class="py-10 bg-slate-50/50 min-h-screen font-sans">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 w-16 text-center">No</th>
                                <th class="p-5 w-44">Timestamp</th>
                                <th class="p-5 w-60">Aktor / Pengguna</th>
                                <th class="p-5 w-40">Tindakan</th>
                                <th class="p-5 pr-6 w-48">Jejak IP & Klien</th>
                                <th class="p-5 text-center w-24">Inspeksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-sm font-medium">
                            @forelse($logs as $index => $log)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-200 group">
                                    <td class="p-5 text-center text-xs font-mono font-bold text-slate-400">
                                        {{ str_pad($logs->firstItem() + $index, 3, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <td class="p-5 whitespace-nowrap">
                                        <div class="text-slate-800 font-bold">{{ $log->created_at ? $log->created_at->format('d M Y') : '-' }}</div>
                                        <div class="text-slate-400 font-mono text-xs mt-0.5">{{ $log->created_at ? $log->created_at->format('H:i:s') : '-' }} WIB</div>
                                    </td>
                                    
                                    <td class="p-5">
                                        @if($log->user)
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-100 to-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm shrink-0 shadow-inner">
                                                    {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-black text-slate-900 leading-tight mb-0.5">{{ $log->user->name }}</div>
                                                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                                                        @foreach($log->user->roles as $role)
                                                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md {{ $role->name == 'super-admin' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                                                {{ $role->display_name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 text-slate-500 italic font-bold">
                                                <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                                    <span>🤖</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span>Sistem Otomatis</span>
                                                    <span class="text-[10px] uppercase font-black tracking-widest text-slate-400">Guest / Unauth</span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="p-5 whitespace-nowrap">
                                        @php
                                            $act = strtolower($log->activity);
                                            $badgeClass = 'bg-slate-50 text-slate-600 border-slate-200';
                                            
                                            if (str_contains($act, 'create') || str_contains($act, 'tambah') || str_contains($act, 'store') || str_contains($act, 'buat')) {
                                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            } elseif (str_contains($act, 'update') || str_contains($act, 'ubah') || str_contains($act, 'edit')) {
                                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                            } elseif (str_contains($act, 'delete') || str_contains($act, 'hapus') || str_contains($act, 'destroy')) {
                                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                            } elseif (str_contains($act, 'login') || str_contains($act, 'auth')) {
                                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            }
                                        @endphp
                                        <span class="px-3 py-1.5 border border-opacity-60 text-[10px] font-black rounded-lg uppercase tracking-widest shadow-sm {{ $badgeClass }}">
                                            {{ $log->activity }}
                                        </span>
                                    </td>
                                    
                                    <td class="p-5 pr-6">
                                        <div class="flex items-center gap-2 text-xs font-mono font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded-md border border-slate-100 w-fit mb-1">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            {{ $log->ip_address ?? '0.0.0.0' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1 truncate max-w-[180px] font-semibold" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent ?? 'Unknown User Agent' }}
                                        </div>
                                    </td>

                                    <td class="p-5 text-center">
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
                                                
                                                detail.sebelum = (seb && seb !== 'null' && seb !== '') ? JSON.parse(seb) : null;
                                                detail.sesudah = (ses && ses !== 'null' && ses !== '') ? JSON.parse(ses) : null;
                                                
                                                openModal = true;
                                            " 
                                            class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Lihat Payload Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-20 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                👻
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Pencarian Kosong</h4>
                                            @if(request('search'))
                                                <span class="text-sm">Tidak ditemukan rekam jejak untuk kata kunci "<strong class="text-slate-800">{{ request('search') }}</strong>".</span>
                                            @else
                                                <span class="text-sm">Sistem belum merekam aktivitas apapun saat ini.</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Inspeksi JSON --}}
        <div x-show="openModal" 
             class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden relative" 
                 @click.away="openModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                {{-- Header Modal --}}
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 leading-tight">Data Payload Inspeksi</h3>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Audit Trail Debugger</p>
                        </div>
                    </div>
                    <button @click="openModal = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="p-6 overflow-y-auto space-y-6 text-sm bg-white flex-1">
                    
                    {{-- Info Singkat Card --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl flex items-center gap-4">
                            <span class="text-2xl">⚡</span>
                            <div>
                                <span class="block text-[10px] text-indigo-400 mb-0.5 uppercase tracking-widest font-black">Jenis Aktivitas</span>
                                <span class="text-sm font-black text-indigo-900" x-text="detail.kegiatan"></span>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center gap-4">
                            <span class="text-2xl">👤</span>
                            <div>
                                <span class="block text-[10px] text-slate-400 mb-0.5 uppercase tracking-widest font-black">Aktor Pengguna</span>
                                <span class="text-sm font-black text-slate-800" x-text="detail.pengguna"></span>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center gap-4">
                            <span class="text-2xl">🕒</span>
                            <div>
                                <span class="block text-[10px] text-slate-400 mb-0.5 uppercase tracking-widest font-black">Waktu Eksekusi</span>
                                <span class="text-sm font-black text-slate-800" x-text="detail.waktu"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Data Diff Viewer (Terminal Style) --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-slate-900 p-6 rounded-3xl shadow-inner border border-slate-800 relative overflow-hidden">
                        
                        {{-- Hiasan Terminal Mac --}}
                        <div class="absolute top-4 left-6 flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        
                        <div class="absolute top-3 left-0 right-0 text-center pointer-events-none">
                            <span class="text-[10px] font-mono text-slate-600 font-bold tracking-widest">JSON_DIFF_VIEWER.sh</span>
                        </div>

                        <div class="flex flex-col mt-6 relative z-10">
                            <div class="flex items-center gap-2 mb-3 px-3 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-black uppercase tracking-widest rounded-lg w-fit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Snapshot Lama (Before)
                            </div>
                            <div class="bg-black/40 p-4 rounded-2xl overflow-x-auto min-h-[250px] flex flex-col justify-start border border-white/5 font-mono text-[13px] leading-relaxed">
                                <template x-if="detail.sebelum && Object.keys(detail.sebelum).length > 0">
                                    <pre class="text-rose-300 w-full text-left bg-transparent p-0 m-0 border-0" 
                                         x-text="JSON.stringify(detail.sebelum, null, 2)"></pre>
                                </template>
                                <template x-if="!detail.sebelum || Object.keys(detail.sebelum).length === 0">
                                    <div class="flex flex-col items-center justify-center h-full text-slate-600 font-mono text-xs italic gap-2">
                                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4M8 16l-4-4 4-4"></path></svg>
                                        [ Null / Tidak Ada Data Lama ]
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex flex-col mt-6 lg:mt-6 relative z-10">
                            <div class="flex items-center gap-2 mb-3 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-black uppercase tracking-widest rounded-lg w-fit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Snapshot Baru (After)
                            </div>
                            <div class="bg-black/40 p-4 rounded-2xl overflow-x-auto min-h-[250px] flex flex-col justify-start border border-white/5 font-mono text-[13px] leading-relaxed relative">
                                
                                {{-- Jika update (ada sebelum dan sesudah) --}}
                                <template x-if="detail.sesudah && Object.keys(detail.sesudah).length > 0">
                                    <pre class="text-emerald-300 w-full text-left bg-transparent p-0 m-0 border-0" 
                                         x-text="JSON.stringify(detail.sesudah, null, 2)"></pre>
                                </template>
                                
                                {{-- Jika insert/store (tidak ada sesudah, tapi ada properties yang masuk ke 'sebelum' secara default dari spatie activity log) --}}
                                <template x-if="(!detail.sesudah || Object.keys(detail.sesudah).length === 0) && detail.sebelum && Object.keys(detail.sebelum).length > 0">
                                    <pre class="text-emerald-300 w-full text-left bg-transparent p-0 m-0 border-0" 
                                         x-text="JSON.stringify(detail.sebelum, null, 2)"></pre>
                                </template>

                                {{-- Jika benar-benar kosong --}}
                                <template x-if="(!detail.sesudah || Object.keys(detail.sesudah).length === 0) && (!detail.sebelum || Object.keys(detail.sebelum).length === 0)">
                                    <div class="flex flex-col items-center justify-center h-full text-slate-600 font-mono text-xs italic gap-2">
                                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        [ Null / Tidak Ada Payload Tersimpan ]
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button @click="openModal = false" class="px-6 py-3 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-100 text-slate-700 font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                        Tutup Inspektor
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>