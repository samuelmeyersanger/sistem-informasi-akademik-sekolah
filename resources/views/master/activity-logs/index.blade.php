<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Aktivitas Sistem') }}
        </h2>
    </x-slot>

    <div x-data="{ 
        openModal: false, 
        detail: { kegiatan: '', pengguna: '', waktu: '', sebelum: null, sesudah: null } 
    }" class="py-12 bg-slate-900/10 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Riwayat Aktivitas</h3>
                        <p class="text-xs text-gray-500">Daftar jejak audit manipulasi data pada sistem akademik.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('master.activity-logs') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, aktivitas, atau IP..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-64 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.activity-logs') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear Search">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-bold text-xs uppercase tracking-wider">
                                <th class="p-4 w-16 text-center">No</th>
                                <th class="p-4 w-44">Waktu (WIB)</th>
                                <th class="p-4 w-52">Nama Pengguna</th>
                                <th class="p-4 w-40">Nama Kegiatan</th>
                                <th class="p-4 pr-6 w-44">Koneksi & Device</th>
                                <th class="p-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($logs as $index => $log)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4 text-center text-xs font-mono text-gray-500">
                                        {{ $logs->firstItem() + $index }}
                                    </td>

                                    <td class="p-4 whitespace-nowrap text-gray-500 font-medium">
                                        {{ $log->created_at ? $log->created_at->format('d M Y, H:i:s') : '-' }}
                                    </td>
                                    
                                    <td class="p-4">
                                        @if($log->user)
                                            <div class="font-bold text-gray-900">{{ $log->user->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-mono">{{ $log->user->email }}</div>
                                            
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($log->user->roles as $role)
                                                    <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-indigo-50 border border-indigo-200 text-indigo-600 inline-block">
                                                        {{ $role->display_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Sistem Otomatis / Guest</span>
                                        @endif
                                    </td>
                                    
                                    <td class="p-4 whitespace-nowrap">
                                        @php
                                            $act = strtolower($log->activity);
                                            $badgeClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                            
                                            if (str_contains($act, 'create') || str_contains($act, 'tambah') || str_contains($act, 'store')) {
                                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            } elseif (str_contains($act, 'update') || str_contains($act, 'ubah') || str_contains($act, 'edit')) {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                            } elseif (str_contains($act, 'delete') || str_contains($act, 'hapus') || str_contains($act, 'destroy')) {
                                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                            } elseif (str_contains($act, 'login')) {
                                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 border text-[10px] font-bold rounded-md uppercase tracking-wider {{ $badgeClass }}">
                                            {{ $log->activity }}
                                        </span>
                                    </td>
                                    
                                    <td class="p-4 pr-6 text-gray-500">
                                        <div class="font-mono text-[11px] font-bold text-gray-700">
                                            🌐 {{ $log->ip_address ?? '0.0.0.0' }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5 truncate max-w-[150px]" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="p-4 text-center">
                                        <button 
                                            type="button"
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
                                            class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded shadow-sm transition-all cursor-pointer">
                                            👁️ Show
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Tidak ditemukan data log yang sesuai dengan kata kunci "{{ request('search') }}".
                                        @else
                                            Belum ada rekaman aktivitas saat ini.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openModal" 
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="display: none;">
            
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-4xl w-full max-h-[85vh] flex flex-col overflow-hidden" @click.away="openModal = false">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900">Detail Log Aktivitas</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4 text-xs">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-indigo-50/50 border border-indigo-100/60 rounded-xl">
                        <div>
                            <span class="block text-gray-400 font-medium">Nama Kegiatan</span>
                            <span class="text-sm font-bold text-indigo-700" x-text="detail.kegiatan"></span>
                        </div>
                        <div>
                            <span class="block text-gray-400 font-medium">Nama Pengguna</span>
                            <span class="text-sm font-bold text-gray-800" x-text="detail.pengguna"></span>
                        </div>
                        <div>
                            <span class="block text-gray-400 font-medium">Waktu Eksekusi</span>
                            <span class="text-sm font-bold text-gray-800" x-text="detail.waktu"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
    
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-rose-600 mb-1 flex items-center gap-1">🔴 Data Sebelum (Lama)</span>
                            <div class="bg-slate-900 p-4 rounded-xl overflow-x-auto min-h-[180px] flex flex-col justify-start border border-slate-800">
                                <template x-if="detail.sebelum">
                                    <pre class="text-slate-100 font-mono text-[11px] leading-relaxed whitespace-pre-wrap block w-full text-left bg-transparent p-0 m-0 border-0" 
                                         style="color: #f1f5f9 !important;" 
                                         x-text="JSON.stringify(detail.sebelum, null, 2)"></pre>
                                </template>
                                <template x-if="!detail.sebelum">
                                    <span class="text-slate-500 italic font-mono text-[11px]">Tidak ada data perubahan awal (Aksi Create / Log Manual)</span>
                                </template>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-emerald-600 mb-1 flex items-center gap-1">🟢 Data Sesudah (Baru)</span>
                            <div class="bg-slate-900 p-4 rounded-xl overflow-x-auto min-h-[180px] flex flex-col justify-start border border-slate-800">
                                <template x-if="detail.sesudah">
                                    <pre class="text-emerald-400 font-mono text-[11px] leading-relaxed whitespace-pre-wrap block w-full text-left bg-transparent p-0 m-0 border-0" 
                                         style="color: #34d399 !important;" 
                                         x-text="JSON.stringify(detail.sesudah, null, 2)"></pre>
                                </template>
                                <template x-if="!detail.sesudah && detail.sebelum">
                                    <pre class="text-emerald-400 font-mono text-[11px] leading-relaxed whitespace-pre-wrap block w-full text-left bg-transparent p-0 m-0 border-0" 
                                         style="color: #34d399 !important;" 
                                         x-text="JSON.stringify(detail.sebelum, null, 2)"></pre>
                                </template>
                                <template x-if="!detail.sesudah && !detail.sebelum">
                                    <span class="text-slate-500 italic font-mono text-[11px]">Tidak ada modifikasi payload data objek.</span>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button @click="openModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-all cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function tumpukDetailLog(kegiatan, pengguna, waktu, sebelum, sesudah) {
            return {
                kegiatan: kegiatan,
                pengguna: pengguna,
                waktu: waktu,
                sebelum: sebelum,
                sesudah: sesudah
            };
        }
    </script>
</x-app-layout>