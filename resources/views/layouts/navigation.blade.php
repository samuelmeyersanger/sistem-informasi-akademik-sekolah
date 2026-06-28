<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-gray-900/40 backdrop-blur-sm lg:hidden" 
     @click="sidebarOpen = false" 
     style="display: none;">
</div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-slate-900 border-r border-slate-800 text-slate-300 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0">
    
    <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-bold text-white tracking-wider text-sm uppercase">
            <x-application-logo class="w-7 h-7 fill-current text-indigo-400" />
            <span>{{ config('app.name', 'SIAS AKADEMIK') }}</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-4 overflow-y-auto">
        
        {{-- Pastikan variabel $sidebarMenus ada --}}
        @if(isset($sidebarMenus) && $sidebarMenus->isNotEmpty())
            
            {{-- Mengelompokkan menu berdasarkan kolom 'kategori' secara otomatis --}}
            @foreach($sidebarMenus->groupBy('kategori') as $kategori => $daftarMenu)
                
                <div class="space-y-1">
                    @if(!empty($kategori))
                        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 {{ $loop->first ? '' : 'pt-2 mt-2 border-t border-slate-800/40' }}">
                            {{ $kategori }}
                        </p>
                    @endif

                    @foreach($daftarMenu as $menu)
                        @php
                            // Cek apakah halaman saat ini sesuai dengan rute database (Mendukung pola wildcard *)
                            $isActive = request()->is($menu->url) || request()->is($menu->url . '/*');
                        @endphp

                        <a href="{{ url($menu->url) }}" 
                            class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors group
                                {{ $isActive ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                            
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-{{ $menu->icon }} w-5 text-center text-sm"></i>

                                    <span>{{ $menu->nama_menu }}</span>
                                </div>
                        </a>
                    @endforeach
                </div>

            @endforeach

        @else
            <div class="p-3 text-center bg-slate-950/40 border border-slate-800 rounded-xl">
                <p class="text-[10px] italic text-slate-500">Akses menu belum dikonfigurasi.</p>
            </div>
        @endif

    </nav>

    {{-- 🟢 BAGIAN YANG DIPERBAIKI (Diberi Jaring Pengaman ?-> dan ??) --}}
    <div class="p-4 bg-slate-950 border-t border-slate-800 flex items-center gap-3 shrink-0">
        <div class="w-8 h-8 rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center font-bold text-indigo-400 uppercase text-xs shrink-0">
            {{ substr(Auth::user()?->name ?? 'US', 0, 2) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-white truncate">{{ Auth::user()?->name ?? 'User Sesi Lama' }}</p>
            <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 bg-slate-800 text-slate-400 rounded border border-slate-700/50 inline-block mt-0.5">
                {{ Auth::user()?->roles()->first()?->display_name ?? 'Guest' }}
            </span>
        </div>
    </div>

</aside>