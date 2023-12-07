<x-app-layout>
    <script type="module">
        function addCycleStyles() {
            let cycles = $('.cycle');
            cycles.each(function() {
                let styles = $(this).find('.cycle_styles').text();
                styles = styles.split(';');
                if(styles.length === 2) {
                    let cycleClasses = '';
                    let cycleTagBg = '';
                    let cycleTagText = '';
                    let status = parseInt(styles[0]);

                    if(status === 0) {
                        //cycleClasses = 'ring-green-450 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-green-450';
                        cycleTagText = 'Zakończony';
                    } else if(status === 3) {
                        //cycleClasses = 'ring-red-500 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-red-500';
                        cycleTagText = 'Po terminie';
                    } else if(status === 1) {
                        //cycleClasses = 'ring-blue-450 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-blue-450';
                        cycleTagText = 'Aktywny';
                    } else if(status === 2) {
                        //cycleClasses = 'ring-yellow-300 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-yellow-300';
                        cycleTagText = 'Nierozpoczęty';
                    }
                    //$(this).addClass(cycleClasses);
                    let cycleTag = $(this).find('.cycle-tag');
                    if(cycleTag.length === 1) {
                        cycleTag.addClass(cycleTagBg).text(cycleTagText);
                    }
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

        $(document).ready(function() {
            addCycleStyles();

            $('.open-modal').on('click', function () {
                console.log($(this).attr('id'));
            });

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
        @if(isset($status_err))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{$status_err}}
                </p>
            </div>
        @endif
        @php
            $name = "Szczegóły cyklu";
        @endphp
        <x-information-panel :viewName="$name">
            {{--    routing for details set in java script above   --}}
            @if(in_array($user->role,array('admin','manager')))
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
        @if(isset($p_cycle) and isset($child_cycles))
            <div class="flex flex-col justify-center items-center w-full mt-4">
                <div id="cycle-{{$p_cycle->cycle_id}}" class="cycle w-[95%] rounded-xl bg-white my-5">
                    <p class="cycle_status hidden">{{$p_cycle->status}}</p>
                    <p class="cycle_styles hidden">{{$p_cycle->status}};{{$p_cycle->style_progress}}</p>
                    <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl">
                        <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                            <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[45%] xl:w-1/2 rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                <div class="p-1">
                                    {{($p_cycle->category == 0)? 'Produkt' : (($p_cycle->category == 1)? 'Materiał' : 'Zadanie')}}
                                </div>
                                <div class="text-xs lg:text-sm flex justify-center items-center">
                                    <div class="cycle-tag p-1 mx-2 rounded-md"></div>
                                </div>
                            </dt>
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
                        {{--                            ROW 1--}}
                        <div class="additional-info col-span-4 flex flex-col bg-gray-200/50 border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Uwagi</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->additional_comment}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-4 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Przypisani pracownicy</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->assigned_employee_no}}
                                </dd>
                            </div>
                        </div>
                        <div class="col-span-4 xl:col-span-8 w-full bg-gray-300 py-2 flex flex-row justify-end">
                            <button type="button" id="cycle-details-{{$p_cycle->cycle_id}}" class="cycle-details mr-4 text-gray-800 bg-white hover:bg-gray-100 focus:outline-none font-medium rounded-sm text-xs lg:text-sm px-2 py-0.5">
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
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Produktywność</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1  {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                    {{$p_cycle->productivity.'%'}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Postęp (%)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 {{floatval($p_cycle->time_passed) < floatval($p_cycle->progress)? 'text-green-450' : 'text-red-500'}}">
                                    {{$p_cycle->progress.'%'}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Czasu upłynęło (%)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1  {{floatval($p_cycle->time_passed) < floatval($p_cycle->progress)? 'text-green-450' : 'text-red-500'}}">
                                    {{($p_cycle->finished and floatval($p_cycle->time_passed) > 100)? '100%' : $p_cycle->time_passed.'%'}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Pozostało czasu (h)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 {{$p_cycle->status == 3 ? 'text-red-500' : ''}}">
                                    {{$p_cycle->time_left}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wyk. ilość (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                    {{$p_cycle->current_amount}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Cel (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->total_amount}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">defekty (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->defect_amount}}
                                </dd>
                            </div>
                        </div>
{{--                            ROW 4--}}
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
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->expected_amount_per_time_frame}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. ilość/Czas pracy (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                    {{$p_cycle->expected_amount_per_spent_time}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Czas pracy (h)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->time_spent_in_hours}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (%)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->defect_percent}}
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-4 xl:col-span-8 w-full bg-gray-300 py-2 flex flex-row justify-start hidden">
                            <p class="pl-5 text-gray-800 focus:outline-none font-medium rounded-sm text-xs lg:text-sm">
                                Informacje dodatkowe
                            </p>
                        </div>
{{--                            ROW 6 - product photo--}}
                        @if(!is_null($p_cycle->image))
                            @php $path = ''; @endphp
                            @if($p_cycle->category == 0)
                                @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp
                            @elseif($p_cycle->category == 1)
                                @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                            @endif
                                <div class="additional-info col-span-4 xl:col-span-2 flex justify-center bg-gray-200/50 border-r-2 hidden p-2">
                                    <div class="max-w-[150px]">
                                        <img src="{{asset('storage/'.$path.$p_cycle->image)}}">
                                    </div>
                                </div>
                        @endif
                        @if(!is_null($p_cycle->description))
                            <div class="additional-info col-span-4 {{is_null($p_cycle->image)? 'xl:col-span-8' : 'xl:col-span-6'}} flex flex-col bg-gray-200/50 border-r-2 hidden">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Opis</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-xs font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
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
            </div>
            <div class="w-full flex justify-center items-center mb-4">
                <div class="w-[95%]">
                    <div class="w-full text-lg lg:text-xl font-semibold bg-gray-800 text-white rounded-t-xl pl-5 py-2 flex flex-row justify-between">
                        <div class="p-3">
                            {{($p_cycle->category == 0)? 'Materiały i zadania' : 'Zadania'}}
                        </div>
                    </div>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-b-xl">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border border-slate-300">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Podgląd
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Kategoria
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Zdjęcie
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nazwa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Produktywność
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Czas pracy (h)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Wyk. ilość (szt)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Oczek. ilość/Czas pracy (szt)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Cel (szt)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Postęp (%)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Początek cyklu
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Koniec cyklu
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Oczek. ilość/ dzień (szt)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Defekty (szt)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Defekty (%)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Odpady
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Odpady jednostka
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $current_id = $child_cycles[0]; @endphp
                            @foreach($child_cycles as $c_cycle)
                                @if($c_cycle != $current_id)
{{--                                    coś tu na odróżnienie--}}
                                @endif
                                <tr class="{{$c_cycle->category == 1 ? 'bg-gray-200' : 'bg-white' }} font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-slate-300 ">
                                    <td class="px-1 py-1 rounded-md">
                                        <div class="flex justify-center">
                                            <a id="open-modal-{{$c_cycle->cycle_id}}" type="button"
                                               data-modal-target="editUserModal" data-modal-show="editUserModal"
                                               class="open-modal font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                                <svg fill="#000000" width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                                    <title>popout</title>
                                                    <path d="M15.694 13.541l2.666 2.665 5.016-5.017 2.59 2.59 0.004-7.734-7.785-0.046 2.526 2.525-5.017 5.017zM25.926 16.945l-1.92-1.947 0.035 9.007-16.015 0.009 0.016-15.973 8.958-0.040-2-2h-7c-1.104 0-2 0.896-2 2v16c0 1.104 0.896 2 2 2h16c1.104 0 2-0.896 2-2l-0.074-7.056z"></path>
                                                </svg>
                                            </a>
                                        </div>

                                    </td>
                                    <td class="whitespace-nowrap rounded-md">
                                        @php
                                            switch ($c_cycle->status) {
                                                case 0:
                                                    $status = 'Zakończony';
                                                    $bg = 'bg-green-450';
                                                    break;
                                                case 1:
                                                    $status = 'Aktywny';
                                                    $bg = 'bg-blue-450';
                                                    break;
                                                case 2:
                                                    $status = 'Nierozpoczęty';
                                                    $bg = 'bg-yellow-300';
                                                    break;
                                                case 3:
                                                    $status = 'Po terminie';
                                                    $bg = 'bg-red-500';
                                                    break;
                                            }
                                        @endphp
                                        <div class="w-full h-full flex justify-center">
                                            <div class="{{$bg}} text-xs lg:text-sm text-white flex justify-center items-center font-semibold rounded-md mx-2">
                                                <div class=" cycle-tag p-2">
                                                    {{$status}}
                                                </div>
                                            </div>
                                        </div>

                                    </td>
{{--                                    <th scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">--}}
{{--                                        <div class="ps-3">--}}
{{--                                            <div class="text-base font-semibold">Neil Sims</div>--}}
{{--                                            <div class="font-normal text-gray-500">neil.sims@flowbite.com</div>--}}
{{--                                        </div>--}}
{{--                                    </th>--}}
                                    <td class="px-6 py-4 whitespace-nowrap rounded-md">
                                        {{$c_cycle->category == 1? 'Materiał' : 'Zadanie'}}
                                    </td>
                                    <td class="p-1 rounded-md">
                                        @if(!is_null($c_cycle->image) and $c_cycle->category == 1)
                                            <div class="flex justify-center">
                                                <div class="max-w-[100px]">
                                                    <img src="{{asset('storage/components/'.$c_cycle->image)}}" alt="">
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap rounded-md">
                                        {{$c_cycle->name}}
                                    </td>
                                    <td class="px-6 py-4 text-center {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                        {{$c_cycle->productivity.'%'}}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{$p_cycle->time_spent_in_hours}}
                                    </td>
                                    <td class="px-6 py-4 text-center {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                        {{$c_cycle->current_amount}}
                                    </td>
                                    <td class="px-6 py-4 text-center {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                        {{$c_cycle->expected_amount_per_spent_time}}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{$c_cycle->total_amount}}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{$c_cycle->progress}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{$c_cycle->start_time}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{empty($c_cycle->end_time) ? 'cykl trwa' : $c_cycle->end_time}}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{$c_cycle->expected_amount_per_time_frame}}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{$c_cycle->defect_amount}}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{$c_cycle->defect_percent}}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{is_null($c_cycle->waste_amount) ? '-' : $c_cycle->waste_amount}}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{is_null($c_cycle->waste_unit) ? '-' : $c_cycle->waste_unit}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!-- Edit user modal -->
                        <div id="editUserModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-[150] items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative w-full max-w-2xl max-h-full">
                                <!-- Modal content -->
                                <form class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Edit user
                                        </h3>
                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="editUserModal">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        <div class="grid grid-cols-6 gap-6">
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="first-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First Name</label>
                                                <input type="text" name="first-name" id="first-name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Bonnie" required="">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="last-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
                                                <input type="text" name="last-name" id="last-name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Green" required="">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                                <input type="email" name="email" id="email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="example@company.com" required="">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="phone-number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
                                                <input type="number" name="phone-number" id="phone-number" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="e.g. +(12)3456 789" required="">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="department" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
                                                <input type="text" name="department" id="department" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Development" required="">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="company" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company</label>
                                                <input type="number" name="company" id="company" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="123456" required="">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="current-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Current Password</label>
                                                <input type="password" name="current-password" id="current-password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="••••••••" required="">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="new-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New Password</label>
                                                <input type="password" name="new-password" id="new-password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="••••••••" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-3 rtl:space-x-reverse border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save all</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

</x-app-layout>
