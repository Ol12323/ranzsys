<nav x-data="{ isOpen: false }" class="h-16 py-4 px-8 mx-auto lg:flex lg:justify-between lg:items-center bg-gray-900 shadow-sm ring-1 ring-white/40">
    <div class="flex items-center justify-between">
       @include('vendor.filament-panels.components.logo')
        <!-- Mobile menu button -->
        <div class="flex lg:hidden">
            <button x-cloak @click="isOpen = !isOpen" type="button" class="text-gray-500 dark:text-gray-200 hover:text-gray-600 dark:hover:text-gray-400 focus:outline-none focus:text-gray-600 dark:focus:text-gray-400" aria-label="toggle menu">
                <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
                </svg>
        
                <svg x-show="isOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu open: "block", Menu closed: "hidden" -->
    <div x-cloak :class="[isOpen ? 'translate-x-0 opacity-100 ' : 'opacity-0 -translate-x-full']" class="absolute inset-x-0 z-20 w-full px-6 py-4 transition-all duration-300 ease-in-out bg-gray-900 shadow-md lg:bg-transparent lg:dark:bg-transparent lg:shadow-none dark:bg-gray-900 lg:mt-0 lg:p-0 lg:top-0 lg:relative lg:w-auto lg:opacity-100 lg:translate-x-0 lg:flex lg:items-center">
        <div class="flex flex-col space-y-4 lg:mt-0 lg:flex-row lg:-px-8 lg:space-y-0">
            <x-filament::link class="bg-white/5 px-4 py-2 rounded text-white" :href="route('home')">
                Home
            </x-filament::link>

            @guest
            <x-filament::link class="px-4 py-2 rounded text-white" href="/customer">
                Login
            </x-filament::link>

            <x-filament::link class="px-4 py-2 rounded text-white" href="/customer/register">
                Register
            </x-filament::link>
            @endguest
        </div>
    </div>
</nav>