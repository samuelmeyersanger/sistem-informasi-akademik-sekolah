<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden" 
     @click="sidebarOpen = false" 
     style="display: none;">
</div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-slate-950/95 backdrop-blur-2xl border-r border-white/5 text-slate-300 transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 shrink-0 shadow-2xl shadow-black/50 overflow-hidden">
    
    {{-- Aksen Cahaya Latar Samping Kiri Atas --}}
    <div class="absolute -top-20 -left-20 w-40 h-40 bg-indigo-500/10 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="flex items-center justify-between h-16 px-6 bg-transparent border-b border-white/10 shrink-0 relative z-10">
        @php
            $logoSetting = \DB::table('pengaturan_logo')->first(); 
        @endphp

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="p-1.5 bg-white/5 rounded-lg border border-white/10 shadow-inner group-hover:bg-white/10 transition-colors">
                <img src="{{ !empty($logoSetting?->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                     class="w-6 h-6 object-contain shrink-0" 
                     alt="Logo">
            </div>
            
            <div class="flex flex-col">
                <span class="font-black text-white tracking-[0.15em] text-xs uppercase leading-none">{{ config('app.name', 'SIAS') }}</span>
                <span class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest mt-0.5">Akademik</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-rose-500 text-2xl font-black cursor-pointer transition-colors focus:outline-none">&times;</button>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto relative z-10 custom-scrollbar">
        
        @if(isset($sidebarMenus) && $sidebarMenus->isNotEmpty())
            
            @foreach($sidebarMenus->groupBy('kategori') as $kategori => $daftarMenu)
                @php
                    $isCategoryActive = false;
                    foreach($daftarMenu as $menu) {
                        if (request()->is($menu->url) || request()->is($menu->url . '/*')) {
                            $isCategoryActive = true;
                            break;
                        }
                    }
                    
                    $isOpen = empty($kategori) ? 'true' : ($isCategoryActive ? 'true' : 'false');
                @endphp

                <div x-data="{ open: {{ $isOpen }} }" class="mb-2">
                    
                    @if(!empty($kategori))
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2 text-[9px] font-black text-slate-500 hover:text-indigo-400 uppercase tracking-[0.2em] transition-colors cursor-pointer {{ $loop->first ? '' : 'pt-4 mt-2 border-t border-white/5' }} focus:outline-none group">
                            <span>{{ $kategori }}</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 text-[10px] opacity-50 group-hover:opacity-100" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                    @endif

                    <div x-show="open" 
                         x-transition:enter="transition-all ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-1.5 {{ !empty($kategori) ? 'mt-2' : '' }}">
                         
                        @foreach($daftarMenu as $menu)
                            @php
                                $isActive = request()->is($menu->url) || request()->is($menu->url . '/*');
                            @endphp

                            <a href="{{ url($menu->url) }}" 
                                class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all group overflow-hidden relative
                                    {{ $isActive ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/20 border border-indigo-400/30' : 'text-slate-400 hover:bg-white/5 hover:text-white border border-transparent' }}">
                                
                                {{-- Aksen Garis Aktif --}}
                                @if($isActive)
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-white/30 rounded-r-full"></div>
                                @endif

                                <div class="flex items-center gap-3 z-10 relative">
                                    <div class="w-6 text-center text-sm {{ $isActive ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400 transition-colors' }}">
                                        <i class="fa-solid fa-{{ $menu->icon }}"></i>
                                    </div>
                                    <span class="tracking-wide">{{ $menu->nama_menu }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

            @endforeach

        @else
            <div class="p-4 text-center bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl shadow-inner">
                <span class="text-xl mb-2 block opacity-50">🔒</span>
                <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">Akses belum diatur</p>
            </div>
        @endif

    </nav>

    {{-- BAGIAN USER PROFILE BOTTOM SIDEBAR --}}
    <div class="p-4 bg-transparent border-t border-white/10 flex items-center gap-3 shrink-0 relative z-10 hover:bg-white/5 transition-colors cursor-pointer group">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 border border-indigo-400 flex items-center justify-center font-black text-white uppercase text-xs shrink-0 shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-transform">
            {{ substr(Auth::user()?->name ?? 'US', 0, 2) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[11px] font-black text-white truncate uppercase tracking-wider">{{ Auth::user()?->name ?? 'Administrator' }}</p>
            <span class="text-[9px] font-bold px-2 py-0.5 bg-indigo-500/20 text-indigo-300 rounded-lg border border-indigo-500/30 inline-block mt-1 tracking-widest">
                {{ Auth::user()?->roles()->first()?->display_name ?? 'Guest' }}
            </span>
        </div>
    </div>

</aside>

<style>
    /* Styling Scrollbar Khusus untuk Sidebar Gelap */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
    }
</style>