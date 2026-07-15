<header class="bg-white/80 backdrop-blur-xl border-b border-white/50 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 shadow-sm sticky top-0 z-40 transition-all">
    
    <div class="flex items-center gap-4">
        {{-- Tombol Hamburger Menu (Mobile) --}}
        <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/50 lg:hidden cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Judul Halaman Dinamis --}}
        <div class="font-black text-sm sm:text-base text-slate-800 leading-tight tracking-tight flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 hidden sm:block"></span>
            {{ $header ?? 'Sistem Informasi Sekolah' }}
        </div>
    </div>

    <div class="flex items-center gap-4">
        
        {{-- Tombol Chat Sekolah --}}
        <a href="{{ route('chat.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-1.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md transform hover:-translate-y-0.5 {{ request()->routeIs('chat.*') ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-indigo-500/30 border-none' : 'bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-200' }}"
           title="Ruang Diskusi & Chat">
            <svg class="w-4 h-4 {{ request()->routeIs('chat.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785 10.53 10.53 0 0 0 3.104-.94c.54-.191 1.135-.112 1.657.211.967.6 2.108.923 3.303.923Z"></path>
            </svg>
            <span class="hidden sm:inline">Ruang Obrolan</span>
        </a>

        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

        {{-- Dropdown Akun Profil --}}
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center gap-2 px-3 py-1.5 text-[11px] font-black uppercase tracking-widest text-slate-600 hover:text-indigo-600 focus:outline-none transition-all cursor-pointer rounded-xl hover:bg-indigo-50 border border-transparent hover:border-indigo-100">
                    
                    {{-- Avatar Inisial (Opsional) --}}
                    <div class="w-6 h-6 rounded-md bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shadow-sm">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>

                    <span class="hidden sm:inline">{{ Auth::user()->name ?? 'Menu Akun' }}</span>
                    
                    <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-2 border-b border-slate-100 bg-slate-50/50">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Login Sebagai</p>
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->email ?? '-' }}</p>
                </div>

                <x-dropdown-link :href="route('profile.edit')" class="text-xs font-medium hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-slate-400"></i> {{ __('Profil & Pengaturan') }}
                    </span>
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-xs font-medium text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors border-t border-slate-100">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-from-bracket text-rose-400"></i> {{ __('Keluar (Log Out)') }}
                        </span>
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
        
    </div>
</header>