
@if(isset($user) and $user instanceof \App\Models\User)
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
            <div class="flex flex-wrap justify-between items-center">
                <div class="flex justify-start items-center">
                    <button data-drawer-target="drawer-navigation" data-drawer-toggle="drawer-navigation" aria-controls="drawer-navigation" class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <svg aria-hidden="true" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span class="sr-only">Toggle sidebar</span>
                    </button>
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo class="block h-20 w-auto fill-current text-gray-800"/>
                        </a>
                    </div>
                </div>
                <div class="flex items-center lg:order-2 mr-2 lg:mr-8">
                    <button type="button" data-drawer-toggle="drawer-navigation" aria-controls="drawer-navigation" class="p-2 mr-1 text-gray-500 rounded-lg md:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                        <span class="sr-only">Toggle search</span>
                        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/>
                        </svg>
                    </button>
                    <button type="button" class="flex mx-3 text-sm rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                        <span class="sr-only">Open user menu</span>
                        <svg fill="#000000"  id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40px" height="40px" viewBox="0 0 45.532 45.532" xml:space="preserve">
						<g>
                            <path d="M22.766,0.001C10.194,0.001,0,10.193,0,22.766s10.193,22.765,22.766,22.765c12.574,0,22.766-10.192,22.766-22.765
                                S35.34,0.001,22.766,0.001z M22.766,6.808c4.16,0,7.531,3.372,7.531,7.53c0,4.159-3.371,7.53-7.531,7.53
                                c-4.158,0-7.529-3.371-7.529-7.53C15.237,10.18,18.608,6.808,22.766,6.808z M22.761,39.579c-4.149,0-7.949-1.511-10.88-4.012
                                c-0.714-0.609-1.126-1.502-1.126-2.439c0-4.217,3.413-7.592,7.631-7.592h8.762c4.219,0,7.619,3.375,7.619,7.592
                                c0,0.938-0.41,1.829-1.125,2.438C30.712,38.068,26.911,39.579,22.761,39.579z"/>
                        </g>
					</svg>
                    </button>
                    <!-- Dropdown menu -->
                    <div class="hidden z-[100] my-4 w-56 text-sm lg:text-lg list-none bg-white divide-y divide-gray-100 dark:bg-gray-700 dark:divide-gray-600 " id="dropdown">
                        <div class="py-3 px-4">
                            <span class="block font-semibold text-gray-900 dark:text-white">{{$user->firstName}} {{$user->lastName}}</span>
                            <span class="block text-gray-900 truncate dark:text-white">{{$user->role}}</span>
                            <span class="block text-gray-900 truncate dark:text-white">nazwa: {{$user->employeeNo}}</span>
                        </div>
                        <ul class="py-1 text-gray-700 dark:text-gray-300" aria-labelledby="dropdown">
                            <li>
                                <a href="{{route('profile.index', $user->employeeNo)}}" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">Profil</a>
                            </li>
                        </ul>
                        <ul class="py-2 text-gray-700 dark:text-gray-300" aria-labelledby="dropdown">
                            <li>
                                <div class="flex justify-center mx-auto">
                                    <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
                                        @csrf

                                        <x-primary-button class="" :href="route('logout')"
                                                          onclick="event.preventDefault();
                                                                    this.closest('form').submit();">
                                            {{ __('Wyloguj') }}
                                        </x-primary-button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidenav" id="drawer-navigation">
            <div class="overflow-y-auto mt-5 py-5 px-3 h-full bg-white dark:bg-gray-800">
                <form action="#" method="GET" class="md:hidden mb-2">
                    <label for="sidebar-search" class="sr-only">Search</label>
                    <div class="relative">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" id="sidebar-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Search"/>
                    </div>
                </form>
                <ul class="space-y-3 border-b border-gray-200 pb-2">
                    <li>
                        <x-responsive-nav-link :href="route('profile.index', Auth::user()->employeeNo )" :active="request()->routeIs('profile.index')" class="">
                            {{ __('Profil') }}
                        </x-responsive-nav-link>
{{--                        <a href="#" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">--}}
{{--                            <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">--}}
{{--                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>--}}
{{--                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>--}}
{{--                            </svg>--}}
{{--                            <span class="ml-3">Overview</span>--}}
{{--                        </a>--}}
                    </li>
                    <li>
                        @if(request()->user()->role != 'pracownik')
                            <x-responsive-nav-link :href="route('employee.index')" :active="request()->routeIs('employee.index')" >
                                {{ __('Pracownicy') }}
                            </x-responsive-nav-link>
                        @endif
                    </li>
                    <li>
                        @php
                            $name = 'Zarządzaj';
                            $dropdown_id = 'dropdown-management'
                        @endphp
                        <x-responsive-nav-button :name="$name" :dropdown_id="$dropdown_id">
                            <ul id="{{$dropdown_id}}" class="hidden py-2 space-y-2">
                                <li>
                                    <x-responsive-nav-link :href="route('product.index')" :active="request()->routeIs('product.index')" class="w-[95%] ml-[5%]">
                                        {{ __('Produkty') }}
                                    </x-responsive-nav-link>
                                </li>
                                <li>
                                    <x-responsive-nav-link :href="route('component.index')" :active="request()->routeIs('component.index')" class="w-[95%] ml-[5%]">
                                        {{ __('Materiały') }}
                                    </x-responsive-nav-link>
                                </li>
                                <li>
                                    <x-responsive-nav-link :href="route('schema.index')" :active="request()->routeIs('schema.index')" class="w-[95%] ml-[5%]">
                                        {{ __('Zadania') }}
                                    </x-responsive-nav-link>
                                </li>
                            </ul>
                        </x-responsive-nav-button>
                    </li>
                    <li>
                        <x-responsive-nav-link :href="route('production.index-wrapper')" :active="request()->routeIs('production.index')">
                            {{ __('Produkcja') }}
                        </x-responsive-nav-link>
                    </li>
                    <li>
                        @php
                            $name = 'Praca';
                            $dropdown_id = 'dropdown-work'
                        @endphp
                        <x-responsive-nav-button :name="$name" :dropdown_id="$dropdown_id">
                            <ul id="{{$dropdown_id}}" class="hidden py-2 space-y-2">
                                <li>
                                    <x-responsive-nav-link :href="route('work-cycle.index')" :active="request()->routeIs('work-cycle.index')" class="w-[95%] ml-[5%]">
                                        {{ __('Praca w cyklu') }}
                                    </x-responsive-nav-link>
                                </li>
                                <li>
                                    <x-responsive-nav-link :href="route('work.index')" :active="request()->routeIs('work.index')" class="w-[95%] ml-[5%]">
                                        {{ __('Przeglądaj') }}
                                    </x-responsive-nav-link>
                                </li>
                                <li>
                                    <x-responsive-nav-link :href="route('work.add-wrapper')" :active="request()->routeIs('work.add')" class="w-[95%] ml-[5%]">
                                        {{ __('Raportuj') }}
                                    </x-responsive-nav-link>
                                </li>
                            </ul>
                        </x-responsive-nav-button>
                    </li>
                    <li>
                        <x-responsive-nav-link :href="route('stastistics.index')" :active="request()->routeIs('stastistics.index')">
                            {{ __('Statystyki') }}
                        </x-responsive-nav-link>
                    </li>
                </ul>
{{--                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">--}}
{{--                    <li>--}}
{{--                        <a href="#" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">--}}
{{--                            <svg aria-hidden="true" class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">--}}
{{--                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>--}}
{{--                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>--}}
{{--                            </svg>--}}
{{--                            <span class="ml-3">Docs</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <a href="#" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">--}}
{{--                            <svg aria-hidden="true" class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">--}}
{{--                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>--}}
{{--                            </svg>--}}
{{--                            <span class="ml-3">Components</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
            </div>
            {{--        move to the top --}}
            <div class="absolute bottom-0 left-0 justify-center p-4 space-x-4 w-full flex bg-white dark:bg-gray-800 z-20">
                <a href="#" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
                    <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"/>
                    </svg>
                </a>
            </div>
        </aside>
        <main class="md:ml-64 h-auto pt-20">
                {{$slot}}
        </main>
    </div>
@endif

