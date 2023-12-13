@component('mail::message')
@if(isset($cycle))
    @if(isset($user))
        <div class="flex justify-center items-center">
            <p class="w-full !text-md lg:text-xl font-medium text-center p-6 space-y-1">
                Witaj {{$user->name}}. Użytkownik {{$cycle->created_by}} właśnie stworzył nowy cykl produkcji i przypisał Cię jako wykonawcę.
            </p>
        </div>
    @endif
    <div class="flex flex-col justify-center items-center w-full mt-4">
        <div id="cycle-remove-{{$cycle->cycle_id}}" class="w-[95%] rounded-xl bg-gray-200/50 my-5">
            <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl shadow-md">
                <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                    <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[45%] xl:w-1/2 rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                        <div class="p-1">
                            {{($cycle->category == 1)? 'Produkt' : (($cycle->category == 2)? 'Materiał' : 'Zadanie')}}
                        </div>
                        <div class="text-xs lg:text-sm flex justify-center items-center">
                            <div class="p-1 mx-2 rounded-md bg-yellow-300">
                                Nierozpoczęty
                            </div>
                        </div>
                    </dt>
                    <dd class=" text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4">{{$cycle->name}}</dd>
                </div>
                <div class="col-span-2 flex flex-col bg-gray-200/50 border-r">
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
                            {{$cycle->expected_start_time}}
                        </dd>
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
                            {{$cycle->expected_end_time}}
                        </dd>
                    </div>
                </div>
                {{--                            ROW 1--}}
                <div class="additional-info xl:col-span-4 col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Przypisani pracownicy</dt>
                    <div class="w-full h-full flex justify-center items-center">
                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                            {{$cycle->assigned_employee_no}}
                        </dd>
                    </div>
                </div>
                <div class="additional-info xl:col-span-4 col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Ilość (szt)</dt>
                    <div class="w-full h-full flex justify-center items-center">
                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                            {{$cycle->total_amount}}
                        </dd>
                    </div>
                </div>
                <div class="additional-info xl:col-span-8 col-span-4 flex flex-col bg-gray-200/50 border-r-2">
                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Dodatkowe Uwagi</dt>
                    <div class="w-full h-full flex justify-center items-center">
                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                            {{$cycle->additional_comment}}
                        </dd>
                    </div>
                </div>
            </dl>
        </div>
    </div>
@endif


@endcomponent
