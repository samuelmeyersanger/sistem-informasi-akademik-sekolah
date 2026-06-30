<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 shadow-sm sticky top-0 z-30">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-md text-gray-500 hover:bg-gray-100 lg:hidden cursor-pointer">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="font-bold text-sm sm:text-base text-gray-800 leading-tight">
            {{ $header ?? 'Sistem Informasi Sekolah' }}
        </div>
    </div>

    <div class="flex items-center gap-3">
        
        <a href="{{ route('chat.index') }}" 
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer {{ request()->routeIs('chat.*') ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
           title="Ruang Diskusi & Chat">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785 10.53 10.53 0 0 0 3.104-.94c.54-.191 1.135-.112 1.657.211.967.6 2.108.923 3.303.923Z"></path>
            </svg>
            <span class="hidden sm:inline">Chat Sekolah</span>
        </a>

        <div class="h-4 w-[1px] bg-gray-200"></div>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors cursor-pointer rounded-lg hover:bg-gray-50">
                    <span>Menu Akun</span>
                    <svg class="fill-current ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>