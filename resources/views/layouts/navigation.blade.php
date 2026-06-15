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
    
    <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-bold text-white tracking-wider text-sm uppercase">
            <x-application-logo class="w-7 h-7 fill-current text-indigo-400" />
            <span>{{ config('app.name', 'SIAS AKADEMIK') }}</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        
        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Utama</p>
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
            <span>📊</span> Dashboard
        </a>

        @if(Auth::user()->role === 'admin')
            
            <div class="pt-4 mt-4 border-t border-slate-800/60">
                <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Sistem Pengguna</p>
            </div>

            <a href="{{ route('admin.user.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.user.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>👥</span> Manajemen User
            </a>

            <a href="{{ route('admin.role.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.role.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>🔑</span> Master Role
            </a>

            <a href="{{ route('admin.permission.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.permission.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>🛡️</span> Izin Fitur (Permission)
            </a>


            <div class="pt-4 mt-4 border-t border-slate-800/60">
                <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Akademik & Kalender</p>
            </div>

            <a href="{{ route('admin.tahun-ajaran.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.tahun-ajaran.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>📅</span> Tahun Ajaran
            </a>

            <a href="{{ route('admin.semester.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.semester.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>📝</span> Semester
            </a>


            <div class="pt-4 mt-4 border-t border-slate-800/60">
                <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Portal Berita</p>
            </div>

            <a href="{{ route('admin.kategori-blog.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.kategori-blog.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>🏷️</span> Kategori Blog
            </a>

            <a href="{{ route('admin.blog.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.blog.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>📰</span> Artikel Berita
            </a>

            <a href="{{ route('admin.komentar-blog.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.komentar-blog.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                <span>💬</span> Moderasi Komentar
            </a>


            <div class="pt-4 mt-4 border-t border-slate-800/60">
                <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Identitas & Profil Web</p>
            </div>

            <div class="space-y-1 bg-slate-950/20 p-1.5 rounded-xl border border-slate-800/40">
                <a href="{{ route('admin.tentang.index') }}" 
                   class="flex items-center gap-3 px-2 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.tentang.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                    <span>🏫</span> Tentang Sekolah
                </a>
                
                <a href="{{ route('admin.profil-sekolah.index') }}" 
                   class="flex items-center gap-3 px-2 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.profil-sekolah.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                    <span>🆔</span> Identitas Sekolah
                </a>

                <a href="{{ route('admin.page.index') }}" 
                   class="flex items-center gap-3 px-2 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.page.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                    <span>📄</span> Halaman Statis (Pages)
                </a>

                <a href="{{ route('admin.kontak.index') }}" 
                   class="flex items-center gap-3 px-2 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.kontak.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                    <span>📩</span> Pesan Masuk (Kontak)
                </a>

                <a href="{{ route('admin.setting-kontak.index') }}" 
                   class="flex items-center gap-3 px-2 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.setting-kontak.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                    <span>⚙️</span> Pengaturan Kontak
                </a>

                <a href="{{ route('admin.footer-link.index') }}" 
                   class="flex items-center gap-3 px-2 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.footer-link.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                    <span>🔗</span> Tautan Footer
                </a>
            </div>

        @endif
    </nav>

    <div class="p-4 bg-slate-950 border-t border-slate-800 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center font-bold text-indigo-400 uppercase text-xs shrink-0">
            {{ substr(Auth::user()->name, 0, 2) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
            <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 bg-slate-800 text-slate-400 rounded border border-slate-700/50 inline-block mt-0.5">
                {{ Auth::user()->role ?? 'User' }}
            </span>
        </div>
    </div>
</aside>