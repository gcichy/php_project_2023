@props(['work_array','storage_path_components','storage_path_products'])
@if(isset($work_array))
    <script type="module">
        function getRowData(id) {
            let row = $('#row-'+id).find('.col-value');
            let modalDetailsTable = $('#modal-work-table');
            let productivityStyle = getProductivityStyle(id);
            let defectStyle = getDefectStyle(id);
            console.log(productivityStyle);
            console.log(defectStyle);
            row.each(function () {
                // Get the class attribute of the current element
                let classNames = $(this).attr('class').split(' ');
                if(classNames.length >= 2) {
                    let colName = classNames[1];
                    let elem = modalDetailsTable.find('.col-value.'+colName);
                    if(elem.length === 1) {
                        if(colName === 'component-image' || colName === 'product-image') {
                            elem.attr('src',$(this).attr('src'));
                        }
                        else {
                            modalDefectStyles(colName, elem, defectStyle);
                            modalProductivityStyles(colName, elem, productivityStyle);
                            elem.text($(this).text().trim());
                        }

                    }
                }
            });
        }
        function getProductivityStyle(id) {
            let productivity = $('#row-'+id).find('.col-value.productivity');
            let productivityStyle = '';
            // console.log(productivity.length);
            // console.log(productivity.text().trim());
            if(productivity.length === 1 && parseInt(productivity.text().trim()) > 100 ) {
                productivityStyle = 'text-green-450';
            }
            else if(productivity.length === 1 && parseInt(productivity.text().trim()) < 80) {
                productivityStyle = 'text-red-500';
            }
            return productivityStyle;
        }

        function modalProductivityStyles(colName, elem, style) {
            let productivityList = ['productivity','amount','exp-amount-per-time-spent']
            if(productivityList.includes(colName)) {
                elem.addClass(style);
            }
        }
        function getDefectStyle(id) {
            let defectPercent = $('#row-'+id).find('.col-value.defect-percent');
            let defectStyle = '';
            //console.log(parseInt(defectPercent.text().trim()));
            if(defectPercent.length === 1 && parseInt(defectPercent.text().trim()) > 10) {
                defectStyle = 'text-red-500';
            }
            return defectStyle;
        }
        function modalDefectStyles(colName, elem, style) {
            let defectList = ['defect-amount','defect-percent']
            if(defectList.includes(colName)) {
                elem.addClass(style);
            }
        }

        $(document).ready(function() {


            $("#close-modal-work-button").on('click', function () {
                $("#modal-work-background").addClass("hidden");
                $('#work-modal-prod-img').attr('src',null);
                $('#work-modal-comp-img').attr('src',null);
                $('dd.col-value').removeClass('text-green-450 text-red-500');
            });

            $(".open-modal").on('click', function () {
                $("#modal-work-background").removeClass("hidden");
                let idArr = $(this).attr('id').split('-');
                getRowData(idArr[idArr.length-1]);
            });
        });
    </script>

    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
    <tr>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white rounded-tl-xl">
            Podgląd
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Kategoria cyklu
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Wykonawcy
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Zadanie
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Podzadanie
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Kolejność podzadania
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Czas pracy (h)
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Ilość (szt)
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Oczek. Ilość/Czas pracy
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Produktywność (%)
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Początek pracy
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Koniec pracy
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Nazwa materiału
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Zdjęcie materiału
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Nazwa produktu
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Zdjęcie produktu
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Ilość/godzina (szt)
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Oczek. ilość/godzina (szt)
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Defekty (szt)
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Defekty (%)
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Defekty - Przyczyna
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Odpady
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white">
            Odpady jednostka
        </th>
        <th scope="col" class="sticky top-0 px-6 py-3 bg-white rounded-tr-xl">
            Odpady - przyczyna
        </th>
    </tr>
    </thead>
    <tbody class="bg-gray-50 divide-y">
    @foreach($work_array as $work)
        <tr id="row-{{$work->work_id}}" class="{{$work->productivity < 80? 'bg-red-100' : ($work->productivity >= 100? 'bg-green-100' : 'bg-white')}} font-medium text-gray-800 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-slate-300 ">
            <td class="px-1 py-1 rounded-md">
                <div class="flex justify-center">
                    <a id="open-modal-{{$work->work_id}}" type="button"
                       class="open-modal font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        <svg fill="#000000" width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                            <title>podgląd podcyklu</title>
                            <path d="M15.694 13.541l2.666 2.665 5.016-5.017 2.59 2.59 0.004-7.734-7.785-0.046 2.526 2.525-5.017 5.017zM25.926 16.945l-1.92-1.947 0.035 9.007-16.015 0.009 0.016-15.973 8.958-0.040-2-2h-7c-1.104 0-2 0.896-2 2v16c0 1.104 0.896 2 2 2h16c1.104 0 2-0.896 2-2l-0.074-7.056z"></path>
                        </svg>
                    </a>
                </div>
            </td>
            <td class="whitespace-nowrap rounded-md text-center">
                <div class="w-full h-full flex justify-center">
                    <div class="bg-blue-450 text-xs lg:text-sm text-white flex justify-center items-center font-semibold rounded-md mx-2">
                        <div class="col-value cycle-category p-2">
                            {{$work->cycle_category == 2? 'Materiał' : ($work->cycle_category == 3? 'Zadanie' : 'Produkt')}}
                        </div>
                    </div>
                </div>
            </td>
            <td class="col-value exec-employee-no px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->exec_employee_no}}
            </td>
            <td class="col-value production-schema px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->production_schema}}
            </td>
            <td class="col-value task-name px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->task_name}}
            </td>
            <td class="col-value task-sequence-no px-6 py-4 whitespace-nowrap rounded-md text-center">
                {{$work->task_sequence_no}}
            </td>
            <td class="col-value time-spent-in-hours px-6 py-4 whitespace-nowrap rounded-md text-center">
                {{$work->time_spent_in_hours}}
            </td>
            <td class="col-value amount px-6 py-4 whitespace-nowrap rounded-md text-center {{$work->productivity < 100? 'text-red-500' : 'text-green-450'}}">
                {{$work->amount}}
            </td>
            <td class="col-value exp-amount-per-time-spent px-6 py-4 whitespace-nowrap rounded-md text-center {{$work->productivity < 100? 'text-red-500' : 'text-green-450'}}">
                {{$work->exp_amount_per_time_spent}}
            </td>
            <td class="col-value productivity px-6 py-4 whitespace-nowrap rounded-md text-center {{$work->productivity < 100? 'text-red-500' : 'text-green-450'}}">
                {{$work->productivity.'%'}}
            </td>
            <td class="col-value start-time px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->start_time}}
            </td>
            <td class="col-value end-time px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->end_time}}
            </td>
            <td class="col-value component-name px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->component_name}}
            </td>
            <td class="p-1 rounded-md">
                @if(!is_null($work->component_image))
                    @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                    <div class="flex justify-center">
                        <div class="max-w-[100px]">
                            <img class="col-value component-image" src="{{asset('storage/'.$path.$work->component_image)}}" alt="">
                        </div>
                    </div>
                @endif
            </td>
            <td class="col-value product-name px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->product_name}}
            </td>
            <td class="p-1 rounded-md">
                @if(!is_null($work->product_image))
                    @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp
                    <div class="flex justify-center">
                        <div class="max-w-[100px]">
                            <img class="col-value product-image" src="{{asset('storage/'.$path.$work->product_image)}}" alt="">
                        </div>
                    </div>
                @endif
            </td>
            <td class="col-value amount-per-hour px-6 py-4 whitespace-nowrap rounded-md text-center">
                {{$work->amount_per_hour}}
            </td>
            <td class="col-value exp-amount-per-hour px-6 py-4 whitespace-nowrap rounded-md text-center">
                {{$work->exp_amount_per_hour}}
            </td>
            <td class="col-value defect-amount px-6 py-4 whitespace-nowrap rounded-md text-center {{$work->defect_percent > 10? 'text-red-500' : ''}}">
                {{$work->defect_amount}}
            </td>
            <td class="col-value defect-percent px-6 py-4 whitespace-nowrap rounded-md text-center {{$work->defect_percent > 10? 'text-red-500' : ''}}">
                {{$work->defect_percent.'%'}}
            </td>
            <td class="col-value defect-rc-description px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->defect_rc_description}}
            </td>
            <td class="col-value waste-amount px-6 py-4 whitespace-nowrap rounded-md text-center">
                {{$work->waste_amount}}
            </td>
            <td class="col-value waste-unit px-6 py-4 whitespace-nowrap rounded-md text-center">
                {{$work->waste_unit}}
            </td>
            <td class="col-value waste-rc-description px-6 py-4 whitespace-nowrap rounded-md">
                {{$work->waste_rc_description}}
            </td>
        </tr>
    @endforeach
    </tbody>

    <div id="modal-work-background" class="z-[100] fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden">
        <!-- Modal Container -->
        <div id="modal-work" class="z-[100] fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[95%] md:w-[90%] xl:w-4/5 bg-white rounded-lg shadow-md">
            <!-- Modal Header -->
            <div class="w-full bg-gray-800 rounded-t-lg text-white p-4 flex flex-row justify-between items-center">
                <h2 class="text-xl lg:text-2xl font-medium">Praca</h2>
                <x-nav-button id="close-modal-work-button" class="">
                    <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.207 6.207a1 1 0 0 0-1.414-1.414L12 10.586 6.207 4.793a1 1 0 0 0-1.414 1.414L10.586 12l-5.793 5.793a1 1 0 1 0 1.414 1.414L12 13.414l5.793 5.793a1 1 0 0 0 1.414-1.414L13.414 12l5.793-5.793z" fill="#ffffff"/></svg>
                </x-nav-button>
            </div>
            <div class="flex justify-center items-start max-h-[500px] overflow-y-scroll mt-6">
                <div id="modal-work-table" class="cycle w-[95%] rounded-xl bg-white my-5 shadow-md">
                    <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl">
                        <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                            <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[45%] xl:w-1/2 rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                <div class="p-1">
                                    Podzadanie
                                </div>
                            </dt>
                            <dd class="col-value task-name text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4"></dd>
                        </div>
                        <div class="col-span-2 flex flex-col bg-gray-200/50">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Start pracy</dt>
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
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Koniec pracy</dt>
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
                        {{--                            ROW 2--}}
                        <div class="additional-info col-span-4 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Zadanie</dt>
                            <div class="w-full h-full flex justify-center items-center p-2">
                                <dd class="col-value production-schema w-full font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-4 md:col-span-2 xl:col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Materiał</dt>
                            <div class="w-full h-full flex justify-center items-center p-2">
                                <dd class="col-value component-name w-full font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-4 md:col-span-2 xl:col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Produkt</dt>
                            <div class="w-full h-full flex justify-center items-center p-2">
                                <dd class="col-value product-name w-full font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        {{--                            ROW 3--}}
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Rodzaj cyklu</dt>
                            <div class="flex h-full justify-center items-center p-2">
                                <div class="bg-blue-450 text-xs lg:text-sm text-white flex justify-center items-center font-semibold rounded-md mx-2">
                                    <div class="col-value cycle-category cycle-tag p-2">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wykonawcy</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value exec-employee-no w-full font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
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
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wyk. ilość (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. ilość/Czas pracy</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value exp-amount-per-time-spent w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Produktywność</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value productivity w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Ilość/godzina (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value amount-per-hour w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 ">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. ilość/godzina (szt)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value exp-amount-per-hour w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
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
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (%)</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value defect-percent w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 xl:col-span-4 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty - przyczyna </dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value defect-rc-description w-full font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Odpady</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value waste-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Odpady jednostka</dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value waste-unit w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 xl:col-span-4 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Odpady - przyczyna </dt>
                            <div class="w-full h-full flex justify-center items-center">
                                <dd class="col-value waste-rc-description w-full font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                </dd>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 xl:col-span-4 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Materiał - zdjęcie</dt>
                            <div class="flex justify-center p-2">
                                <div class="max-w-[200px]">
                                    <img id="work-modal-prod-img" class="col-value component-image" src="" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="additional-info col-span-2 xl:col-span-4 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                            <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Produkt - zdjęcie</dt>
                            <div class="flex justify-center p-2">
                                <div class="max-w-[200px]">
                                    <img id="work-modal-comp-img" class="col-value product-image" src="" alt="">
                                </div>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endif
