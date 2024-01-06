<x-app-layout>
    <script type="module">
        function addCycleStyles() {
            let cycles = $('.cycle');
            cycles.each(function() {
                let styles = $(this).find('.cycle_styles').text();
                styles = styles.split(';');
                console.log(styles);
                if(styles.length === 2) {
                    let cycleClasses = '';
                    let cycleTagBg = '';
                    let cycleTagText = '';
                    let status = parseInt(styles[0]);
                    if(status === 0) {
                        cycleClasses = 'ring-green-450 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-green-450';
                        cycleTagText = 'Zakończony';
                    } else if(status === 3) {
                        cycleClasses = 'ring-red-500 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-red-500';
                        cycleTagText = 'Po terminie';
                    } else if(status === 1) {
                        cycleClasses = 'ring-blue-450 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-blue-450';
                        cycleTagText = 'Aktywny';
                    } else if(status === 2) {
                        cycleClasses = 'ring-yellow-300 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-yellow-300';
                        cycleTagText = 'Nierozpoczęty';
                    }
                    $(this).addClass(cycleClasses);
                    let cycleTag = $(this).find('.cycle-tag');
                    console.log(cycleTag);
                    if(cycleTag.length === 1) {
                        cycleTag.addClass(cycleTagBg).text(cycleTagText);
                    }
                    let progress = $(this).find('.progress');
                    if(progress.length === 1) {
                        let width = parseInt(styles[1]);
                        if(width >= 100) {
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

            $('.next-modal').on('click', function (){
                $('.next-modal').removeClass('selected bg-blue-800 ring-2');
                $(this).addClass('bg-blue-800 ring-2');
                $(this).delay(300).queue(function() {
                    // $("#close-modal-button-1").trigger( "click" );
                    // $("#modal-background-2, #modal-2").removeClass("hidden");
                    // $(this).removeClass('bg-blue-800 ring-2');
                    // $('#new-cycle-cat').text($(this).text());
                    let category = 3;
                    if($(this).attr('id') === 'category-1') {
                        category = 1;
                    } else if($(this).attr('id') === 'category-2') {
                        category = 2;
                    }
                    $('#new-category').val(category);
                    $('#add-cycle-sumbit').trigger('click');
                    $(this).dequeue();
                });
            });

            $('#filter-btn').on('click',function() {
                let filterGrid = $('#filters');
                if(filterGrid.hasClass('hidden')) {
                    filterGrid.removeClass('hidden');
                } else {
                    filterGrid.addClass('hidden');
                }
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
        @if(session('status_err'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{session('status_err')}}
                </p>
            </div>
        @endif
        @php
            $name = "Praca w cyklu";
        @endphp
        <x-information-panel :viewName="$name">
            {{--    routing for details set in java script above   --}}
            <x-nav-button  id="filter-btn" class="on-select details bg-yellow-300 hover:bg-yellow-600">
                {{ __('Filtry') }}
            </x-nav-button>
        </x-information-panel>
        <form method="GET" action="{{ route('work-cycle.index') }}" enctype="multipart/form-data">
            @if(isset($parent_cycles) and isset($users) and isset($filt_items))
                <div class="w-full mt-4 flex justify-center">
                    <div id="filters" class="flex flex-row justify-start w-[90%] border-2 rounded-lg hidden">
                        <dl class="grid grid-cols-3 bg-white text-left rounded-l-lg w-4/5">
                            <div class="col-span-1 flex flex-col justify-center">
                                <a class ='block px-2 text-xs md:text-sm bg-gray-800 rounded-tl-lg font-medium text-center text-white'>
                                    Status
                                </a>
                                <div class="p-1 h-full">
                                    @php $unique_id = 'status' @endphp
                                    <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Status')">
                                        <x-slot name="options">
                                            <option value="0" {{(array_key_exists($unique_id,$filt_items) and in_array('0', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Zakończony
                                            </option>
                                            <option value="1" {{(array_key_exists($unique_id,$filt_items) and in_array('1', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Aktywny
                                            </option>
                                            <option value="2" {{(array_key_exists($unique_id,$filt_items) and in_array('2', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Nierozpoczęty
                                            </option>
                                            <option value="3" {{(array_key_exists($unique_id,$filt_items) and in_array('3', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Po terminie
                                            </option>
                                        </x-slot>
                                    </x-select-multiple>
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-center">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Kategoria
                                </a>
                                <div class="p-1 h-full">
                                    @php $unique_id = 'category' @endphp
                                    <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Kategoria')">
                                        <x-slot name="options">
                                            <option value="1" {{(array_key_exists($unique_id,$filt_items) and in_array('1', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Produkty
                                            </option>
                                            <option value="2" {{(array_key_exists($unique_id,$filt_items) and in_array('2', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Materiały
                                            </option>
                                            <option value="3" {{(array_key_exists($unique_id,$filt_items) and in_array('3', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Zadania
                                            </option>
                                        </x-slot>
                                    </x-select-multiple>
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-start  border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Nazwa
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    @php $unique_id = 'name' @endphp
                                    <input type="search" id="{{$unique_id}}" value="{{array_key_exists($unique_id,$filt_items)? $filt_items[$unique_id] : ''}}"
                                           class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           name="{{$unique_id}}" placeholder="Nazwa">
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-center h-full">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Pracownicy
                                </a>
                                <div class="p-1">
                                    @php $unique_id = 'employees' @endphp
                                    <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')">
                                        <x-slot name="options">
                                            @foreach($users as $u)
                                                <option value="{{$u->id}}" {{(array_key_exists($unique_id,$filt_items) and in_array($u->id, $filt_items[$unique_id]))? 'selected' : ''}}>
                                                    {{$u->employeeNo}}
                                                </option>
                                            @endforeach
                                        </x-slot>
                                    </x-select-multiple>
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-start">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Termin od
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div id="exp-start-time" class="relative w-full"
                                         data-te-datepicker-init
                                         data-te-format="yyyy-mm-dd"
                                         data-te-input-wrapper-init>
                                        <input name="exp_start" value="{{isset($filt_start_time)? $filt_start_time : null}}"
                                               class="exp-start-time p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Start"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-center border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Termin do
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div id="exp-end-time" class="exp-end-time relative w-full"
                                         data-te-datepicker-init
                                         data-te-format="yyyy-mm-dd"
                                         data-te-input-wrapper-init>
                                        <input name="exp_end" value="{{isset($filt_end_time)? $filt_end_time : null}}"
                                               class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Termin"/>
                                    </div>
                                </div>
                            </div>
                            @if(isset($order))
                                <div class="col-span-3 flex flex-col justify-start  border-r-2">
                                    <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                        Sortuj według
                                    </a>
                                    <div class="p-1 flex justify-center items-center h-full">
                                        @php $unique_id = 'order' @endphp
                                        <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Sortuj według')">
                                            <x-slot name="options">
                                                @if(isset($order_items))
                                                    @foreach($order_items as $item)
                                                        @php
                                                            $item_exploded = explode(';', $item);
                                                            $value = array_key_exists($item_exploded[0], $order)? $order[$item_exploded[0]] : '';
                                                        @endphp
                                                        @if(!empty($value))
                                                            <option value="{{$item}}" selected>
                                                                {{$value}} {{$item_exploded[1] == 'asc'? '(rosnąco)' : '(malejąco)'}}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @foreach($order as $key => $val)
                                                    @if(isset($order_items) and is_array($order_items))
                                                        @if(!in_array($key.';asc',$order_items))
                                                            <option value="{{$key}};asc">
                                                                {{$val}} (rosnąco)
                                                            </option>
                                                        @endif
                                                        @if(!in_array($key.';desc',$order_items))
                                                            <option value="{{$key}};desc">
                                                                {{$val}} (malejąco)
                                                            </option>
                                                        @endif
                                                    @else
                                                        <option value="{{$key}};asc">
                                                            {{$val}} (rosnąco)
                                                        </option>
                                                        <option value="{{$key}};desc">
                                                            {{$val}} (malejąco)
                                                        </option>
                                                    @endif
                                                    {{--                                                        {{(isset($order_items) and in_array($key.';desc', $order_items))? 'selected' : ''}}--}}
                                                @endforeach
                                            </x-slot>
                                        </x-select-multiple>
                                    </div>
                                </div>
                            @endif
                        </dl>
                        <div class="flex flex-col justify-center items-center bg-white xl:border-r-2 rounded-r-lg w-1/5">
                            <button type="submit" class ='w-[60%] xl:w-[40%] text-sm md:text-lg bg-gray-800 hover:bg-gray-600 font-medium text-center text-white rounded-lg'>
                                Filtruj
                            </button>
                        </div>
                    </div>
                </div>
                @if(session('edit_err'))
                    <div class="flex justify-center items-center flex-col">
                        @foreach(session('edit_err') as $err)
                            <p class="w-full text-xs text-center text-red-700 space-y-1">
                                {{$err}}
                            </p>
                        @endforeach
                    </div>
                @endif
                @if(session('delete_err'))
                    <div class="flex justify-center items-center flex-col">
                        @foreach(session('edit_err') as $err)
                            <p class="w-full text-xs text-center text-red-700 space-y-1">
                                {{$err}}
                            </p>
                        @endforeach
                    </div>
                @endif
                <div class="flex flex-col justify-center items-center w-full mt-4">
                    @foreach($parent_cycles as $p_cycle)
                        <div id="cycle-{{$p_cycle->cycle_id}}" class="cycle w-[95%] rounded-xl bg-gray-100 my-5 shadow-md">
                            <p class="cycle_status hidden">{{$p_cycle->status}}</p>
                            <p class="cycle_styles hidden">{{$p_cycle->status}};{{$p_cycle->style_progress}}</p>
                            <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-t-xl">
                                <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                                    <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[45%] xl:w-1/2 rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                        <div class="p-1">
                                            {{($p_cycle->category == 1)? 'Produkt' : (($p_cycle->category == 2)? 'Materiał' : 'Zadanie')}}
                                        </div>
                                        <div class="text-xs lg:text-sm flex justify-center items-center">
                                            <div class="cycle-tag p-1 mx-2 rounded-md whitespace-nowrap"></div>
                                        </div>
                                    </dt>
                                    <dd class=" text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4">{{$p_cycle->name}}</dd>
                                </div>
                                <div class="col-span-2 flex flex-col bg-gray-200/50 border-r">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Postęp</dt>
                                    <div class="flex justify-center items-center w-full h-full p-2">
                                        <div class="rounded-lg w-1/2 border h-[32px]  relative bg-white shadow-md">
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
                                <div class="col-span-4 xl:col-span-8 w-full bg-gray-300 py-2 flex flex-row justify-end">
                                    @if($p_cycle->category != 3)
                                        <a href="produkcja/{{$p_cycle->cycle_id}}"  id="sub-cycle-details-{{$p_cycle->cycle_id}}" class="sub-cycle-details flex justify-center items-center mr-2 text-gray-800 bg-white hover:bg-gray-100 uppercase focus:outline-none font-medium rounded-md text-xs lg:text-sm px-2 py-1 shadow-md">
                                            Więcej
                                        </a>
                                    @endif
                                    <button type="button" id="cycle-details-{{$p_cycle->cycle_id}}" class="cycle-details mr-4 text-gray-800 bg-white uppercase hover:bg-gray-100 focus:outline-none font-medium rounded-md text-xs lg:text-sm px-2 py-1 shadow-md">
                                        Statystyki
                                    </button>
                                </div>
    {{--                            ROW 1--}}
                                <div class="additional-info col-span-4 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Uwagi</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->additional_comment}}
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-4 flex flex-col bg-gray-200/50 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Przypisani pracownicy</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->assigned_employee_no}}
                                        </dd>
                                    </div>
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
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 hidden">
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
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2 hidden">
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
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 hidden">
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
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Cel (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->total_amount}}
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
                                        <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->expected_amount_per_time_frame}}
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->defect_amount}}
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. ilość/Czas pracy (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                            {{$p_cycle->expected_amount_per_spent_time}}
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Czas pracy (h)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->time_spent_in_hours}}
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. czas wyk. (h)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->expected_time_to_complete_in_hours}}
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 hidden">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (%)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            {{$p_cycle->defect_percent}}
                                        </dd>
                                    </div>
                                </div>
    {{--                            tu jeszcze jakiś ROW 5 z miarami itp--}}
                                <div class="additional-info col-span-4 xl:col-span-8 w-full bg-gray-300 py-2 flex flex-row justify-start hidden">
                                    <p class="pl-5 text-gray-800 focus:outline-none font-medium rounded-sm text-xs lg:text-sm">
                                        Informacje dodatkowe
                                    </p>
                                </div>
    {{--                            ROW 6 - product photo--}}
                                @if(!is_null($p_cycle->image))
                                    @php $path = ''; @endphp
                                    @if($p_cycle->category == 1)
                                        @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp
                                    @elseif($p_cycle->category == 2)
                                        @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                                    @endif
                                        <div class="additional-info col-span-4 xl:col-span-2 flex justify-center bg-gray-200/50 border-r-2 hidden p-2">
                                            <div class="max-w-[150px]">
                                                <img src="{{asset('storage/'.$path.$p_cycle->image)}}">
                                            </div>
                                        </div>
                                @endif
                                @if(!is_null($p_cycle->description))
                                    <div class="additional-info col-span-4 {{is_null($p_cycle->image)? 'xl:col-span-8' : 'xl:col-span-6'}} flex flex-col bg-gray-200/50 hidden">
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
                                    <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 hidden">
                                        <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Kolor</dt>
                                        <div class="w-full h-full flex justify-center items-center">
                                            <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                                {{$p_cycle->color}}
                                            </dd>
                                        </div>
                                    </div>
                                @endif
                            </dl>
                            @if(isset($work_array))
                                @php
                                    $cycle_work = array_key_exists($p_cycle->cycle_id,$work_array)? $work_array[$p_cycle->cycle_id] : [];
                                    $storage_path_components = isset($storage_path_components)? $storage_path_components : null;
                                    $storage_path_products = isset($storage_path_products)? $storage_path_products : null;
                                @endphp
                                @if(count($cycle_work) > 0)
                                    <div class="w-full flex justify-center items-center">
                                        <div class="w-full">
                                            <div class="w-full text-lg lg:text-xl font-semibold bg-gray-800 text-white pl-5 py-2 flex flex-row justify-between">
                                                <div class="p-2">
                                                    Praca w cyklu
                                                </div>
                                            </div>
                                            <div class="shadow-md rounded-b-xl mb-4">
                                                <div class="relative overflow-x-auto">
                                                    <table class="block max-h-[400px] overflow-y-scroll w-full text-sm text-left rtl:text-right pb-2 bg-gray-100 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
                                                        <x-work-table :work_array="$cycle_work"
                                                                      :storage_path_components="$storage_path_components"
                                                                      :storage_path_products="$storage_path_products">
                                                        </x-work-table>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                    <div class="w-[95%] mb-4">
                            {{ $parent_cycles->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </form>
    @endif
</x-app-layout>
