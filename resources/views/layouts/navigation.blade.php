<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 h-24 flex align-middle justify-between mb-6">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl w-3/4 ml-1 px-4 sm:px-6 lg:px-8 border">
        <div class="flex justify-between h-20 mx-auto border-2">
{{--            <div class="flex justify-between h-20 mx-auto border-2">--}}
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-20 w-auto fill-current text-gray-800" />
                    </a>
                </div>
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link class="text-2xl" :href="route('profile.index', Auth::user()->employeeNo)" :active="request()->routeIs('profile.index')">
                        {{ __('Profil') }}
                    </x-nav-link>
                </div>
                @if(request()->user()->role != 'pracownik')
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link class="text-2xl" :href="route('employee.index')" :active="request()->routeIs('employee.index')">
                            {{ __('Pracownicy') }}
                        </x-nav-link>
                    </div>
                @endif
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link class="text-2xl" :href="route('product.index')" :active="request()->routeIs('product.index')">
                        {{ __('Produkty') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link class="text-2xl" :href="route('production.index')" :active="request()->routeIs('production.index')">
                        {{ __('Produkcja') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link class="text-2xl" :href="route('schedule.index')" :active="request()->routeIs('schedule.index')">
                        {{ __('Harmonogram') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link class="text-2xl" :href="route('stastistics.index')" :active="request()->routeIs('stastistics.index')">
                        {{ __('Statystyki') }}
                    </x-nav-link>
                </div>



{{--            </div>--}}

{{--            <!-- Settings Dropdown -->--}}
{{--            <div class="hidden sm:flex sm:items-center sm:ml-6">--}}
{{--                <x-dropdown align="right" width="48">--}}
{{--                    <x-slot name="trigger">--}}
{{--                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">--}}
{{--                            <div>{{ Auth::user()->name }}</div>--}}

{{--                            <div class="ml-1">--}}
{{--                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">--}}
{{--                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />--}}
{{--                                </svg>--}}
{{--                            </div>--}}
{{--                        </button>--}}
{{--                    </x-slot>--}}

{{--                    <x-slot name="content">--}}
{{--                        <x-dropdown-link :href="route('profile.edit')">--}}
{{--                            {{ __('Profile') }}--}}
{{--                        </x-dropdown-link>--}}

{{--                        <!-- Authentication -->--}}
{{--                        <form method="POST" action="{{ route('logout') }}">--}}
{{--                            @csrf--}}

{{--                            <x-dropdown-link :href="route('logout')"--}}
{{--                                    onclick="event.preventDefault();--}}
{{--                                                this.closest('form').submit();">--}}
{{--                                {{ __('Log Out') }}--}}
{{--                            </x-dropdown-link>--}}
{{--                        </form>--}}
{{--                    </x-slot>--}}
{{--                </x-dropdown>--}}
{{--            </div>--}}

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
        <div class="flex justify-between h-20 mx-auto">
            <form method="POST" action="{{ route('logout') }}" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                @csrf

                <x-nav-link class="text-2xl" :href="route('logout')"
                            onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Wyloguj') }}
                </x-nav-link>
            </form>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 border-2 bg-black">
            <div class="px-4">
                <div class="font-medium text-white">{{ Auth::user()->firstName }} {{ Auth::user()->lastName }}</div>
                <div class="font-medium text-sm text-gray-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">
                    {{ __('Start') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.index', Auth::user()->employeeNo )" :active="request()->routeIs('profile.index')" class="text-white">
                    {{ __('Profil') }}
                </x-responsive-nav-link>
                @if(request()->user()->role != 'pracownik')
                    <x-responsive-nav-link :href="route('employee.index')" :active="request()->routeIs('employee.index')" class="text-white" >
                        {{ __('Pracownicy') }}
                    </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('production.index')" :active="request()->routeIs('production.index')" class="text-white" >
                    {{ __('Produkcja') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('schedule.index')" :active="request()->routeIs('schedule.index')" class="text-white" >
                    {{ __('Harmonogram') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('stastistics.index')" :active="request()->routeIs('stastistics.index')" class="text-white" >
                    {{ __('Statystyki') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" class="text-white"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Wyloguj') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
