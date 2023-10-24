<x-app-layout>

    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
        <a class ='block w-1/2 pl-3 pr-4 py-2 border-blue-450 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
            {{ __('Dodaj komponent') }}
        </a>
        <div class="py-5 pr-5 flex justify-center align-middle">
        </div>
    </div>
    <section class="gradient-form h-full dark:bg-neutral-700 flex justify-center">
        <div class="container h-full w-full p-10">
            <div class="g-6 flex h-full flex-wrap items-center justify-center text-neutral-800 dark:text-neutral-200">
                <div class="w-full">
                    <form>
                        <div class="block rounded-lg bg-white shadow-lg dark:bg-neutral-800">
                            <div class="g-0 lg:flex lg:flex-wrap">
                                <!-- Left column container-->
                                <div class="px-4 md:px-0 lg:w-6/12">
                                    <div class="md:mx-6 md:p-12">
                                        <div class="mb-6">
                                            <label for="name" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Nazwa</label>
                                            <input type="text" id="name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                        </div>
                                        <div class="mb-6">
                                            <label for="material" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Materiał</label>
                                            <select id="material" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="drewno">drewno</option>
                                                <option value="MDF">płyta MDF</option>
                                            </select>
                                        </div>
                                        <div class="mb-6">
                                            <label for="independent" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Produkowany Niezależnie</label>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" id="independent" value="" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>
                                        <div class="mb-6">
                                            <label for="dimension" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Wymiary</label>
                                            <div id="dimension" class="flex flex-row justify-start items-center w-full xl:w-[60%]">
                                                <div class="w-[30%] mr-[3%]">
                                                    <label for="height" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Wysokość</label>
                                                    <input type="number" id="height" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                </div>
                                                <div class="w-[30%] mr-[3%]">
                                                    <label for="length" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Długość</label>
                                                    <input type="number" id="length" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                </div>
                                                <div class="w-[30%] mr-[3%]">
                                                    <label for="width" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Szerokość</label>
                                                    <input type="number" id="width" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label for="image" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Zdjęcie komponentu</label>
                                            <div id="image" class="flex items-center justify-center w-full">
                                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                        </svg>
                                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klikni aby dodać</span> lub upuść plik w polu</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Format: SVG, PNG, JPG, JPEG</p>
                                                    </div>
                                                    <input id="dropzone-file" type="file" class="hidden" />
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label for="description" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Opis komponentu</label>
                                            <input type="text" id="description" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right column container with background and description-->
                                <div class="flex items-center flex-col justify-start rounded-b-lg lg:w-6/12 lg:rounded-r-lg lg:rounded-bl-none p-2 lg:p-0 bg-white/30">
                                    <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom"
                                            class="mt-5[%] lg:mt-[7%] text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-lg px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                        Schematy produkcji
                                        <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                        </svg>
                                    </button>
                                    @if(isset($prod_schemas))
                                        <div class="w-full mt-[3%] mx-auto sm:px-2 space-y-6 border-2 border-red-600">
                                            <div class="p-4 sm:p-8 bg-white flex justify-start items-center flex-col">
                                                @php
                                                    $inputPlaceholder = "Wpisz nazwę schematu...";
                                                    $xListElem = "prod_schema";
                                                @endphp
                                                <x-search-input class="w-full" :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>
                                                <div class="w-full">
                                                    @foreach($prod_schemas as $schem)
                                                        <x-list-element class="list-element-{{$xListElem}} list-element w-full flex-col text-xs md:text-sm lg:text-md 2xl:text-lg lg:py-4 my-3" id="prod-schema-{{$schem->id}}">
                                                            <div class="w-[100%] flex justify-between items-center">
                                                                <div class="w-full flex justify-between items-center">
                                                                    <div class="w-full flex justify-left items-center">
                                                                        <p class="inline-block list-element-name ml-[3%]">{{$schem->production_schema}}</p>
                                                                    </div>
                                                                </div>
                                                                <div id="expbtn-{{$schem->id}}" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-6 lg:h-6 md:rounded-md rounded-sm rotate-0 transition-all mr-0">
                                                                    <img src="{{asset('storage/expand-down.png') }}" >
                                                                </div>
                                                            </div>
{{--                                                                <ul class="comp-list-{{$prod->id}} w-[80%] mt-[3%] relative m-0 w-full hidden list-none overflow-hidden p-0 transition-[height] duration-200 ease-in-out text-lg"  data-te-stepper-init data-te-stepper-type="vertical">--}}
{{--                                                                    @php $i = 1; @endphp--}}
{{--                                                                    <h2 class="text-gray-800">Lista komponentów:</h2>--}}
{{--                                                                    @foreach($prod_comp_list[$prod->id] as $comp)--}}
{{--                                                                        <li data-te-stepper-step-ref class="relative h-fit after:absolute after:left-[2.45rem] after:top-[3.6rem] after:mt-px after:h-[calc(100%-2.45rem)] after:w-px after:bg-[#e0e0e0] after:content-[''] dark:after:bg-neutral-600">--}}
{{--                                                                            <div data-te-stepper-head-ref class="w-[80%] flex cursor-pointer items-center p-6 leading-[1.3rem] no-underline after:bg-[#e0e0e0] after:content-[''] hover:bg-[#f9f9f9] focus:outline-none dark:after:bg-neutral-600 dark:hover:bg-[#3b3b3b]">--}}
{{--                                                                        <span data-te-stepper-head-icon-ref class="mr-3 flex h-[1.938rem] w-[1.938rem] items-center justify-center rounded-full bg-[#ebedef] text-lg font-medium text-[#40464f]">--}}
{{--                                                                            {{$i}}--}}
{{--                                                                        </span>--}}
{{--                                                                                <span data-te-stepper-head-text-ref class="text-gray-800 after:absolute after:flex after:text-[0.8rem] after:content-[data-content] dark:text-neutral-300">--}}
{{--                                                                            {{$comp->name}}--}}
{{--                                                                        </span>--}}
{{--                                                                            </div>--}}
{{--                                                                            <div data-te-stepper-content-ref class="transition-[height, margin-bottom, padding-top, padding-bottom] left-0 overflow-hidden pb-6 pl-[3.75rem] pr-6 duration-300 ease-in-out text-[16px] text-neutral-500 ">--}}
{{--                                                                                {{$comp->description}}--}}
{{--                                                                            </div>--}}
{{--                                                                        </li>--}}
{{--                                                                        @php $i++; @endphp--}}
{{--                                                                    @endforeach--}}
{{--                                                                </ul>--}}
                                                        </x-list-element>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
{{--                                    <!-- Dropdown menu -->--}}
{{--                                    <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-[80%] lg:w-[40%] dark:bg-gray-700">--}}
{{--                                        <div class="p-3">--}}
{{--                                            <label for="input-group-search" class="sr-only">Search</label>--}}
{{--                                            <div class="relative">--}}
{{--                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">--}}
{{--                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">--}}
{{--                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>--}}
{{--                                                    </svg>--}}
{{--                                                </div>--}}
{{--                                                <input type="text" id="input-group-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search user">--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">--}}
{{--                                            <li>--}}
{{--                                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">--}}
{{--                                                    <input id="checkbox-item-11" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">--}}
{{--                                                    <label for="checkbox-item-11" class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Bonnie Green</label>--}}
{{--                                                </div>--}}
{{--                                            </li>--}}
{{--                                            <li>--}}
{{--                                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">--}}
{{--                                                    <input checked id="checkbox-item-12" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">--}}
{{--                                                    <label for="checkbox-item-12" class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Jese Leos</label>--}}
{{--                                                </div>--}}
{{--                                            </li>--}}
{{--                                        </ul>--}}
{{--                                        <div class="pb-1 pt-1 text-center">--}}
{{--                                            <button--}}
{{--                                                class="mb-3 inline-block w-[30%] rounded px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:outline-none focus:ring-0 active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]"--}}
{{--                                                type="button"--}}
{{--                                                data-te-ripple-init--}}
{{--                                                data-te-ripple-color="light"--}}
{{--                                                style="background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);">--}}
{{--                                                Wybierz--}}
{{--                                            </button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                        <!--Submit button-->
                        <div class="mb-12 pb-1 pt-1 text-center">
                            <button
                                class="mb-3 inline-block w-full rounded px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:outline-none focus:ring-0 active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]"
                                type="button"
                                data-te-ripple-init
                                data-te-ripple-color="light"
                                style="background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);">
                                Log in
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
