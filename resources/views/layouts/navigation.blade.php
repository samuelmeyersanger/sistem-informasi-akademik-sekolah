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
        @php
            $logoSetting = \DB::table('pengaturan_logo')->first(); 
        @endphp

        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-bold text-white tracking-wider text-sm uppercase">
            <img src="{{ !empty($logoSetting?->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                 class="w-7 h-7 object-contain rounded-md shrink-0" 
                 alt="Logo">
            
            <span>{{ config('app.name', 'SIAS AKADEMIK') }}</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
        
        @if(isset($sidebarMenus) && $sidebarMenus->isNotEmpty())
            
            @foreach($sidebarMenus->groupBy('kategori') as $kategori => $daftarMenu)
                @php
                    // Cek apakah ada salah satu menu di dalam kategori ini yang sedang aktif
                    $isCategoryActive = false;
                    foreach($daftarMenu as $menu) {
                        if (request()->is($menu->url) || request()->is($menu->url . '/*')) {
                            $isCategoryActive = true;
                            break;
                        }
                    }
                    
                    // Jika kategori kosong (menu tanpa kategori), biarkan selalu terbuka. 
                    // Jika tidak, buka jika sedang aktif, tutup jika tidak.
                    $isOpen = empty($kategori) ? 'true' : ($isCategoryActive ? 'true' : 'false');
                @endphp

                <!-- Bungkus kategori dengan Alpine.js Data -->
                <div x-data="{ open: {{ $isOpen }} }" class="mb-1">
                    
                    @if(!empty($kategori))
                        <!-- Tombol Kategori yang bisa diklik -->
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-bold text-slate-500 hover:text-slate-300 uppercase tracking-widest transition-colors cursor-pointer {{ $loop->first ? '' : 'pt-3 mt-1 border-t border-slate-800/40' }}">
                            <span>{{ $kategori }}</span>
                            <!-- Ikon panah yang akan berputar saat terbuka -->
                            <i class="fa-solid fa-chevron-down transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                    @endif

                    <!-- Area isi menu yang akan disembunyikan/dimunculkan -->
                    <div x-show="open" 
                         x-transition:enter="transition-all ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-1 {{ !empty($kategori) ? 'mt-1' : '' }}">
                         
                        @foreach($daftarMenu as $menu)
                            @php
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
                </div>

            @endforeach

        @else
            <div class="p-3 text-center bg-slate-950/40 border border-slate-800 rounded-xl">
                <p class="text-[10px] italic text-slate-500">Akses menu belum dikonfigurasi.</p>
            </div>
        @endif

    </nav>

    {{-- BAGIAN USER PROFILE BOTTOM SIDEBAR --}}
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