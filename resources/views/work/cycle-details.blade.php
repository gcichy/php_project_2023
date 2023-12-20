<x-app-layout>
    <script type="module">
        function getRowData(id) {
            let row = $('#row-'+id).find('.col-value');
            let modalDetailsTable = $('#modal-detais-table');
            let productivity = $('#row-'+id).find('.col-value.productivity');
            let productivityStyle = 'text-red-500';
            if(productivity.length === 1 && parseInt(productivity.text().trim()) > 100 ) {
                productivityStyle = 'text-green-450';
            }
            row.each(function () {
                // Get the class attribute of the current element
                let classNames = $(this).attr('class').split(' ');
                if(classNames.length >= 2) {
                    let colName = classNames[1];
                    let elem = modalDetailsTable.find('.col-value.'+colName);
                    if(elem.length === 1) {
                        modalProductivityStyles(colName, elem, productivityStyle);
                        modalStatusStyles(colName, elem, $(this).text().trim());
                        elem.text($(this).text().trim());
                    }
                }
            });
        }
        function modalProductivityStyles(colName, elem, style) {
            let productivityList = ['productivity','current-amount','expected-amount-per-spent-time']
            if(productivityList.includes(colName)) {
                elem.addClass(style);
            }
        }
        function modalStatusStyles(colName, elem, sourceText) {
            if(colName === 'status') {
                if(sourceText === 'Po terminie') {
                    elem.addClass('bg-red-500');
                } else if(sourceText === 'Zakończony') {
                    elem.addClass('bg-red-500');
                } else if(sourceText === 'Aktywny') {
                    elem.addClass('bg-blue-450');
                } else {
                    elem.addClass('bg-yellow-300');
                }
            }
        }

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

            $("#close-modal-details-button").on('click', function () {
                $("#modal-details-background").addClass("hidden");
            });

            $(".open-modal").on('click', function () {
                $("#modal-details-background").removeClass("hidden");
                let idArr = $(this).attr('id').split('-');
                getRowData(idArr[idArr.length-1]);

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
        </x-information-panel>
        @if(isset($p_cycle) and isset($child_cycles))
            <div class="flex flex-col justify-center items-center w-full mt-4">
                <div id="cycle-{{$p_cycle->cycle_id}}" class="cycle w-[95%] rounded-xl bg-white my-5 shadow-md">
                    <p class="cycle_status hidden">{{$p_cycle->status}}</p>
                    <p class="cycle_styles hidden">{{$p_cycle->status}};{{$p_cycle->style_progress}}</p>
                    <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl">
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
                            <button type="button" id="cycle-details-{{$p_cycle->cycle_id}}" class="cycle-details mr-4 text-gray-800 bg-white uppercase hover:bg-gray-100 focus:outline-none font-medium rounded-md text-xs lg:text-sm px-2 py-1 shadow-md">
                                Statystyki
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
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2 hidden">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->defect_amount}}
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
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. czas wyk. (h)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                    {{$p_cycle->expected_time_to_complete_in_hours}}
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
                            {{($p_cycle->category == 1)? 'Materiały i zadania' : 'Zadania'}}
                        </div>
                    </div>
                    <div class="shadow-md sm:rounded-b-xl mb-4">
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right pb-2 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
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
                                        Oczek. ilość/dzień (szt)
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Oczek. czas wyk. (h)
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
                                    <tr id="row-{{$c_cycle->child_id}}" class="{{$c_cycle->category == 2 ? 'bg-gray-200' : 'bg-white' }} font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-slate-300 ">
                                        <td class="px-1 py-1 rounded-md">
                                            <div class="flex justify-center">
                                                <a id="open-modal-{{$c_cycle->child_id}}" type="button"
                                                   class="open-modal font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                                    <svg fill="#000000" width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                                        <title>podgląd podcyklu</title>
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
{{--                                                    id means row and column--}}
                                                    <div class="col-value status cycle-tag p-2">
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
                                        <td class="col-value category px-6 py-4 whitespace-nowrap rounded-md">
                                            {{$c_cycle->category == 2? 'Materiał' : 'Zadanie'}}
                                        </td>
                                        <td class="p-1 rounded-md">
                                            @if(!is_null($c_cycle->image) and $c_cycle->category == 2)
                                                <div class="flex justify-center">
                                                    <div class="max-w-[100px]">
                                                        <img src="{{asset('storage/components/'.$c_cycle->image)}}" alt="">
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="col-value name px-6 py-4 whitespace-nowrap rounded-md">
                                            {{$c_cycle->name}}
                                        </td>
                                        <td class="col-value productivity px-6 py-4 text-center {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                            {{$c_cycle->productivity.'%'}}
                                        </td>
                                        <td class="col-value time-spent-in-hours px-6 py-4 text-center">
                                            {{$c_cycle->time_spent_in_hours}}
                                        </td>
                                        <td class="col-value current-amount px-6 py-4 text-center {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                            {{$c_cycle->current_amount}}
                                        </td>
                                        <td class="col-value expected-amount-per-spent-time px-6 py-4 text-center {{floatval($p_cycle->productivity) >= 100? 'text-green-450' : 'text-red-500'}}">
                                            {{$c_cycle->expected_amount_per_spent_time}}
                                        </td>
                                        <td class="col-value total-amount px-6 py-4 text-center">
                                            {{$c_cycle->total_amount}}
                                        </td>
                                        <td class="col-value progress px-6 py-4 text-center">
                                            {{$c_cycle->progress}}
                                        </td>
                                        <td class="col-value start-time px-6 py-4 whitespace-nowrap">
                                            {{$c_cycle->start_time}}
                                        </td>
                                        <td class="col-value end-time px-6 py-4 whitespace-nowrap">
                                            {{empty($c_cycle->end_time) ? 'cykl trwa' : $c_cycle->end_time}}
                                        </td>
                                        <td class="col-value expected-amount-per-time-frame px-6 py-4">
                                            {{$c_cycle->expected_amount_per_time_frame}}
                                        </td>
                                        <td class="col-value expected-time-to-complete-in-hours px-6 py-4">
                                            {{$c_cycle->expected_time_to_complete_in_hours}}
                                        </td>
                                        <td class="col-value defect-amount px-6 py-4 text-center">
                                            {{$c_cycle->defect_amount}}
                                        </td>
                                        <td class="col-value defect-percent px-6 py-4 text-center">
                                            {{$c_cycle->defect_percent}}
                                        </td>
                                        <td class="col-value waste-amount px-6 py-4 text-center">
                                            {{is_null($c_cycle->waste_amount) ? '-' : $c_cycle->waste_amount}}
                                        </td>
                                        <td class="col-value waste-unit px-6 py-4 text-center">
                                            {{is_null($c_cycle->waste_unit) ? '-' : $c_cycle->waste_unit}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="w-full mx-2 p-2 bg-gray-50">
                            {{ $child_cycles->links() }}
                        </div>
                    </div>
                    </div>
            </div>

            <div id="modal-details-background" class="z-[100] fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden">
                <!-- Modal Container -->
                <div id="modal-details" class="z-[100] fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[95%] md:w-[90%] xl:w-4/5 bg-white rounded-lg shadow-md">
                    <!-- Modal Header -->
                    <div class="w-full bg-gray-800 rounded-t-lg text-white p-4 flex flex-row justify-between items-center">
                        <h2 class="text-xl lg:text-2xl font-medium">Szczegóły podcyklu</h2>
                        <x-nav-button id="close-modal-details-button" class="">
                            <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.207 6.207a1 1 0 0 0-1.414-1.414L12 10.586 6.207 4.793a1 1 0 0 0-1.414 1.414L10.586 12l-5.793 5.793a1 1 0 1 0 1.414 1.414L12 13.414l5.793 5.793a1 1 0 0 0 1.414-1.414L13.414 12l5.793-5.793z" fill="#ffffff"/></svg>
                        </x-nav-button>
                    </div>
                    <div class="flex justify-center items-start max-h-[500px] overflow-y-scroll mt-6">
                        <div id="modal-detais-table" class="cycle w-[95%] rounded-xl bg-white my-5 shadow-md">
                            <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl">
                                <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                                    <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[45%] xl:w-1/2 rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                        <div class="col-value category p-1">
                                        </div>
                                        <div class="text-xs lg:text-sm flex justify-center items-center">
                                            <div class="col-value status cycle-tag p-1 mx-2 rounded-md whitespace-nowrap"></div>
                                        </div>
                                    </dt>
                                    <dd class="col-value name text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4"></dd>
                                </div>
                                <div class="col-span-2 flex flex-col bg-gray-200/50">
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
                                            <p class="col-value start-time"></p>
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
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
                                            <p class="col-value end-time"></p>
                                        </dd>
                                    </div>
                                </div>
                                {{--                            ROW 3--}}
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Produktywność</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value productivity w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Postęp (%)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value progress w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wyk. ilość (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value current-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 ">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Cel (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value total-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. ilość/dzień (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value expected-amount-per-time-frame w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value defect-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. il/Czas pracy (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value expected-amount-per-spent-time w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Czas pracy (h)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value time-spent-in-hours w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. czas wyk. (h)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value expected-time-to-complete-in-hours w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class=" col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (%)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value defect-percent w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Odpady</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value waste-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Odpady jednostka</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value waste-unit w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    @endif

</x-app-layout>
