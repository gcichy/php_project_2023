<x-app-layout>
    <script type="module">

        function addCycleStyles() {
            let cycles = $('.cycle');
            cycles.each(function() {
                let styles = $(this).find('.cycle_styles').text();
                styles = styles.split(';');
                if(styles.length === 2) {
                    let color = '';
                    if(styles[0] === 'green') {
                        color = 'ring-green-450 ring-4 ring-offset-4';
                    } else if(styles[0] === 'red') {
                        color = 'ring-red-500 ring-4 ring-offset-4';
                    } else if(styles[0] === 'blue') {
                        color = 'ring-blue-450 ring-4 ring-offset-4';
                    }
                    $(this).addClass(color);
                    let progress = $(this).find('.progress');
                    if(progress.length === 1) {
                        let width = parseInt(styles[1]);
                        if(width === '100') {
                            $(progress).addClass('rounded-lg');
                        } else {
                            $(progress).addClass('rounded-l-lg');
                        }
                        if(width < 5) {
                            width = 'w-0';
                        } else if(width < 20) {
                            width = 'w-1/6';
                        } else if(width < 30) {
                            width = 'w-1/4';
                        } else if(width < 40) {
                            width = 'w-1/3';
                        } else if(width < 60) {
                            width = 'w-1/2';
                        } else if(width < 70) {
                            width = 'w-2/3';
                        } else if(width < 85) {
                            width = 'w-3/4';
                        } else if(width < 100) {
                            width = 'w-5/6';
                        } else {
                            width = 'w-full';
                        }
                        $(progress).addClass(width);
                    }
                }


            });
        }

        function checkActive() {
            let similar = $('.similar');
            let remove = $('.remove');
            let details = $('.details');
            let edit = $('.edit');
            //check if any element is active, if not details button's href is set to current url
            if($('.list-element.active-list-elem').length === 0) {
                remove.removeClass('bg-red-600').addClass('bg-gray-400')
                details.removeClass('bg-blue-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                similar.removeClass('bg-gray-800').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                edit.removeClass('bg-orange-500').addClass('bg-gray-400').attr('href', $(location).attr('href'));
            }
            //else if id is set properly, url is set to be classified as product.details route
            else {
                let id = $('.list-element.active-list-elem').attr('id').split('-');
                if(id.length > 1) {
                    id = id[1];
                    let newUrl = '';
                    let similarUrl = '';
                    let editUrl = '';
                    //if products div has display block, then create route to products, else to components

                    newUrl = $(location).attr('href') + '/' + id;
                    similarUrl = $(location).attr('href').replace('produkty','dodaj-produkt') + '/' + id;
                    editUrl = $(location).attr('href').replace('produkty','edytuj-produkt') + '/' + id;

                    remove.removeClass('bg-gray-400').addClass('bg-red-600').prop('disabled', false)
                    details.removeClass('bg-gray-400').addClass('bg-blue-450').attr('href', newUrl);
                    similar.removeClass('bg-gray-400').addClass('bg-gray-800').attr('href', similarUrl);
                    edit.removeClass('bg-gray-400').addClass('bg-orange-500').attr('href', editUrl);
                }
                else {
                    remove.removeClass('bg-red-600').addClass('bg-gray-400')
                    details.removeClass('bg-blue-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                    similar.removeClass('bg-gray-800').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                    edit.removeClass('bg-orange-500').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                }
            }
        }

        $(document).ready(function() {
            addCycleStyles();

            $('.cycle-details').on('click',function (){
                let id = $(this).attr('id');
                id = id.split('-');
                id = id[id.length - 1];

                let cycle = $('#cycle-'+id);
                if(cycle.length === 1) {
                    let additionalInfo = cycle.find('.additional-info');
                    if(additionalInfo.length > 0 && additionalInfo.hasClass('hidden')) {
                        additionalInfo.removeClass('hidden');
                    } else {
                        additionalInfo.addClass('hidden');
                    }
                }

            });

        });
    </script>
    @if(isset($user) and $user instanceof \App\Models\User)
        @if(session('status'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-green-600 space-y-1">
                    {{session('status')}}
                </p>
            </div>
        @endif
        @if(session('status_err'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{session('status_err')}}
                </p>
            </div>
        @endif
        @php
            $name = "Produkcja";
        @endphp
        <x-information-panel :viewName="$name">
            {{--    routing for details set in java script above   --}}
            <x-nav-button  class="on-select details bg-blue-450 hover:bg-blue-800">
                {{ __('Szczegóły') }}
            </x-nav-button>
            @if(in_array($user->role,array('admin','manager')))
                <x-nav-button :href="route('product.add')" class="ml-1 lg:ml-3">
                    {{ __('Dodaj') }}
                </x-nav-button>
                <x-nav-button class="on-select similar hover:bg-gray-700 ml-1 lg:ml-3">
                    {{ __('Dodaj Podobny') }}
                </x-nav-button>
                <x-nav-button class="on-select edit bg-orange-500 hover:bg-orange-800 ml-1 lg:ml-3">
                    {{ __('Edytuj') }}
                </x-nav-button>
{{--                @php--}}
{{--                    $name = 'produkt';--}}
{{--                    $route = 'product.destroy';--}}
{{--                    $button_id = 'remove-prod-modal';--}}
{{--                    $id = '2';--}}
{{--                    $remove_elem_class = 'element-remove';--}}
{{--                    $remove_elem_id = 'product-remove-';--}}
{{--                @endphp--}}
{{--                <x-remove-modal :name="$name" :button_id="$button_id" :route="$route" :id="$id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id">--}}
{{--                    @foreach($products as $prod)--}}
{{--                        <div class="{{$remove_elem_class}} hidden" id="{{$remove_elem_id}}{{$prod->id}}">--}}
{{--                            <x-list-element class="flex-col">--}}
{{--                                <div class="w-full flex justify-between items-center">--}}
{{--                                    <div class="w-full flex justify-left items-center">--}}
{{--                                        <div class="border-2 inline-block w-[50px] h-[50px] md:w-[70px] md:h-[70px] lg:w-[100px] lg:h-[100px]">--}}
{{--                                            @if(!empty($prod->image))--}}
{{--                                                @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp--}}
{{--                                                <img src="{{asset('storage/'.$path.$prod->image)}}">--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                        <p class="inline-block list-element-name ml-[3%]  xl:text-lg text-md">{{$prod->name}} - {{$prod->material}}</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </x-list-element>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </x-remove-modal>--}}
            @endif
        </x-information-panel>
        @if(isset($parent_cycles) and isset($child_cycles))
            <div class="flex flex-col justify-center items-center w-full mt-4">
                @foreach($parent_cycles as $p_cycle)
                    <div id="cycle-{{$p_cycle->cycle_id}}" class="cycle w-[80%] rounded-xl bg-white my-5">
                        <p class="cycle_styles hidden">{{$p_cycle->cycle_color}};{{$p_cycle->style_progress}}</p>
                        <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl">
                            <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                                <dt class="order-first text-sm lg:text-lg font-semibold leading-6 bg-gray-800 text-white w-[40%] xl:w-1/2 rounded-tl-xl pl-5 py-2">{{$p_cycle->category}}</dt>
                                <dd class=" text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4">{{$p_cycle->name}}</dd>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50 border-r">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Postęp</dt>
                                <div class="flex justify-center items-center w-full h-full p-2">
                                    <div class="rounded-lg w-1/2 border h-[32px]  relative bg-white">
                                        <div class="absolute h-1/2 w-full top-[16%] lg:top-[8%] flex justify-center text-sm lg:text-lg font-semibold">
                                            {{$p_cycle->current_amount}}/{{$p_cycle->total_amount}}
                                        </div>
                                        <div class="progress h-full  tracking-tight bg-green-450"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Zakładany termin</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                            <g fill-rule="evenodd">
                                                <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                            </g>
                                        </svg>
                                        {{$p_cycle->expected_end_time}}
                                    </dd>
                                </div>
                            </div>
{{--                            <div class="flex flex-col bg-gray-200/50 p-2">--}}
{{--                                <dt class="text-sm font-semibold leading-6 text-gray-600">Happy Customers</dt>--}}
{{--                                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">98%</dd>--}}
{{--                            </div>--}}
{{--                            <div class="flex flex-col bg-gray-200/50 p-2">--}}
{{--                                <dt class="text-sm font-semibold leading-6 text-gray-600">Total Revenue</dt>--}}
{{--                                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">$5.6B</dd>--}}
{{--                            </div>--}}
{{--                            <div class="flex flex-col bg-gray-200/50 p-2">--}}
{{--                                <dt class="text-sm font-semibold leading-6 text-gray-600">Happy Customers</dt>--}}
{{--                                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900">98%</dd>--}}
{{--                            </div>--}}
                            <div class="col-span-4 xl:col-span-8 w-full bg-gray-300 py-2 flex flex-row justify-end">
                                <button type="button" id="cycle-details-{{$p_cycle->cycle_id}}" class="cycle-details mr-2 text-gray-800 bg-white hover:bg-gray-100 focus:outline-none font-medium rounded-sm text-xs lg:text-sm px-2 py-0.5">
                                    Szczegóły
                                </button>
                            </div>
{{--                            ROW 2--}}
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Zakładany start</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                            <g fill-rule="evenodd">
                                                <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                            </g>
                                        </svg>
                                        {{$p_cycle->expected_start_time}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Zakładany termin</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                            <g fill-rule="evenodd">
                                                <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                            </g>
                                        </svg>
                                        {{$p_cycle->expected_end_time}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Początek cyklu</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                            <g fill-rule="evenodd">
                                                <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                            </g>
                                        </svg>
                                        {{$p_cycle->start_time}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Koniec cyklu</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                            <g fill-rule="evenodd">
                                                <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                            </g>
                                        </svg>
                                        {{empty($p_cycle->end_time) ? 'cykl trwa' : $p_cycle->end_time}}
                                    </dd>
                                </div>
                            </div>
{{--                            ROW 3--}}
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Cel (szt)</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->total_amount}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wyk. ilość (szt)</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->current_amount}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Postęp (%)</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->progress}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">defekty (?)</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->defect_amount}}
                                    </dd>
                                </div>
                            </div>
{{--                            ROW 4--}}
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Czas pracy (h)</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->time_spent_in_hours}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. ilość (szt)</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->expected_amount_per_spent_time}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                @php
                                    $expected_amount_time_frame = 'Ilość na jednostkę czasu(szt)';
                                    if($p_cycle->expected_amount_time_frame == 'day') {
                                        $expected_amount_time_frame = 'Oczek. ilość/dzień(szt)';
                                    } else if($p_cycle->expected_amount_time_frame == 'hour') {
                                        $expected_amount_time_frame = 'Oczek. ilość/godzina(szt)';
                                    }
                                @endphp
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">{{$expected_amount_time_frame}}</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->expected_amount_per_time_frame}}
                                    </dd>
                                </div>
                            </div>
                            <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Jakaś miara</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        X
                                    </dd>
                                </div>
                            </div>
{{--                            tu jeszcze jakiś ROW 5 z miarami itp--}}
                            <div class="additional-info col-span-4 xl:col-span-8 w-full bg-gray-300 py-2 flex flex-row justify-start hidden">
                                <p class="pl-5 text-gray-800 focus:outline-none font-medium rounded-sm text-xs lg:text-sm">
                                    Informacje o produkcie
                                </p>
                            </div>
{{--                            ROW 6 - product photo--}}
                            @if(!is_null($p_cycle->image))
                                @if($p_cycle->category == 'Produkt')
                                    @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp
                                @elseif($p_cycle->category == 'Materiał')
                                    @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                                @endif
                                    <div class="additional-info col-span-4 flex flex-col justify-center bg-gray-200/50 border-r-2 hidden">
                                        <div class="max-w-[70px] lg:max-w-[100px]">
                                            <img src="{{asset('storage/'.$path.$p_cycle->image)}}">
                                        </div>
                                    </div>
                            @endif
                            @if(!is_null($p_cycle->description))
                                <div class="additional-info col-span-4 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Opis</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->description}}
                                        </dd>
                                    </div>
                                </div>
                            @endif
{{--                            ROW 7 - product information--}}
                            @if(!is_null($p_cycle->height) or !is_null($p_cycle->length) or !is_null($p_cycle->width))
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                    @php
                                        $name = '';
                                        $dim = '';
                                        if(!is_null($p_cycle->height)) {
                                            $name .= 'wys ';
                                            $dim .= $p_cycle->height.' ';
                                        }
                                        if(!is_null($p_cycle->length)) {
                                            if(!empty($name)) {
                                                $name .= 'x  ';
                                                $dim .= 'x  ';
                                            }
                                            $name .= 'dług ';
                                            $dim .= $p_cycle->length.' ';
                                        }
                                        if(!is_null($p_cycle->width)) {
                                            if(!empty($name)) {
                                                $name .= 'x  ';
                                                $dim .= 'x  ';
                                            }
                                            $name .= 'szer';
                                            $dim .= $p_cycle->width.' ';
                                        }
                                        $name .= empty($name) ? 'Wymiary' : ' [cm]';
                                    @endphp
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">{{$name}}</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$dim}}
                                        </dd>
                                    </div>
                                </div>
                            @endif
                            @if(!is_null($p_cycle->material))
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Surowiec</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->material}}
                                        </dd>
                                    </div>
                                </div>
                            @endif
                            @if(!is_null($p_cycle->price))
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Cena (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->price}}
                                        </dd>
                                    </div>
                                </div>
                            @endif
                            @if(!is_null($p_cycle->color))
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Kolor</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->color}}
                                        </dd>
                                    </div>
                                </div>
                            @endif
                        </dl>

                    </div>
                @endforeach
            </div>
        @endif
    @endif
</x-app-layout>
