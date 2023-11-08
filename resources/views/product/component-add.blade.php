<x-app-layout>
    <script type="module">

        function getActiveOnLoad(prefix, inputVal) {
            let numbers = inputVal.split('_');
            numbers.forEach(function(val) {
                if(!isNaN(parseInt(val))) {
                    let id = '#prodschema-' + val;
                    if($(id).hasClass('hidden')) {
                        $(id).removeClass('hidden').addClass('active-list-elem')
                    }
                }
            });

            $('.production-standard').each(function () {
                if($(this).parent().hasClass('active-list-elem')) {
                    $(this).removeClass('hidden');
                }
            });
        }

        function checkActive() {
            //check if any element is active, if not details button's href is set to current url
            if($('.list-element.active-list-elem').length === 0) {
                $('.remove').css('background-color','gray');
                $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
            }
            //else if id is set properly, url is set to be classified as product.details route
            else {
                var id = $('.list-element.active-list-elem').attr('id').split('-');
                if(id.length > 1) {
                    id = id[1];
                    var newUrl = "";
                    //if products div has display block, then create route to products, else to components
                    if($('#left').css('display') === 'block') {
                        newUrl = $(location).attr('href') + '/' + id;
                    } else {
                        newUrl = $(location).attr('href').replace('produkty','komponenty') + '/' + id;
                    }


                    $('.remove').css('background-color','rgb(224 36 36)');
                    $('.details').css('background-color','#1ca2e6').attr('href', newUrl);
                }
                else {
                    $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
                    $('.remove').css('background-color','gray');


                }
            }
        }
        function isActiveProdSchema(elem_id) {
            let ids = $('#prodschema-input').val().split('_');
            return ids.includes(elem_id)
        }

        $(document).ready(function() {
            getActiveOnLoad('prodschema', $('#prodschema-input').val());
            checkActive();

            $('.list-element').on('click', function () {
                let is_active = ($(this).hasClass('active-list-elem') ? true : false);
                $(this).addClass('active-list-elem');

                let id = $(this).attr('id').split('-')[1];
                //prodsschema can be unclicked if list of schemas is visible (schema is not chosen)
                if(!$('#confirm-schema-button').hasClass('hidden')) {
                    let list_id = '.prodschema-list-' + id;
                    if($(list_id).hasClass('hidden')) {
                        if (is_active) {
                            if(!$(list_id).hasClass('just-hidden')) {
                                $(this).removeClass('active-list-elem');
                            } else {
                                $(list_id).removeClass('just-hidden');
                            }
                        }
                    }
                }

                checkActive();
            });

            //on click button is rotated and component list appears
            $('.expand-btn').on('click', function () {
                let id = $(this).attr('id').split('-')[1];
                var list_id = '.prodschema-list-' + id;

                console.log(list_id);
                if($(this).hasClass('rotate-180')) {
                    $(this).removeClass('rotate-180');
                    $(this).addClass('rotate-0');
                } else {
                    $(this).removeClass('rotate-0');
                    $(this).addClass('rotate-180');
                }

                if($(list_id).hasClass('hidden')) {
                    $(list_id).removeClass('hidden');
                } else {
                    $(list_id).addClass('hidden');
                    $(list_id).addClass('just-hidden');
                }
            });

            $('#dropdownSearchButton').on('click', function () {
                $('.production-standard').addClass('hidden');

                if($('.list-element-prodschema').hasClass('hidden')) {
                    $('.list-element-prodschema').removeClass('hidden');
                }
                else {
                    $('.list-element-prodschema').addClass('hidden');

                }
                if($('.prodschema-toggle').hasClass('hidden')) {
                    $('.prodschema-toggle').removeClass('hidden');
                    $('#label-schema').addClass('hidden');

                }
                else {
                    $('.prodschema-toggle').addClass('hidden');
                    $('#label-schema').removeClass('hidden');
                }
            });


            $('#confirm-schema-button').on('click', function (){
                if($('#label-schema').hasClass('hidden')) {
                    $('#label-schema').removeClass('hidden');
                    $('.prodschema-toggle').addClass('hidden');
                    $('.list-element-prodschema:not(.active-list-elem)').addClass('hidden');
                    let id_string = '';
                    let chosen_elements = $('.list-element-prodschema.active-list-elem');
                    let i = 1;
                    chosen_elements.each(function() {
                        let id = $(this).attr('id').split('-')[1];
                        $('#sequenceno_'+id).val(i);
                        id_string += !id ? '_' : id + '_';
                        i++;
                    })
                    id_string = id_string.slice(0, id_string.length - 1)
                    $('#prodschema-input').val(id_string);

                    $('.production-standard').each(function () {
                        if($(this).parent().hasClass('active-list-elem')) {
                            $(this).removeClass('hidden');
                        }
                    });
                }
            });

            $('#independent').on('click', function () {
                $(this).val($(this).val() === 1 ? 0 : 1);
            });
        });

    </script>
    @props(['insert_err'])
    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
        <a class ='block w-1/2 pl-3 pr-4 py-2 border-blue-450 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
            {{ __('Dodaj komponent') }}
        </a>
        <div class="py-5 pr-5 flex justify-center align-middle">
        </div>
    </div>
    @if(isset($insert_error))
        <div class="flex justify-center items-center">
            <x-input-error :messages="$insert_error" class="w-full !text-md lg:text-xl font-medium text-center p-6"/>
        </div>
    @endif
    <section class="gradient-form h-full dark:bg-neutral-700 flex justify-center">
        <div class="container h-full w-full p-10">
            <div class="g-6 flex h-full flex-wrap items-center justify-center text-neutral-800 dark:text-neutral-200">
                <div class="w-full">
                    <form method="POST" action="{{ route('product.store_component') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="block rounded-lg bg-white shadow-lg dark:bg-neutral-800">
                            <div class="g-0 lg:flex lg:flex-wrap">
                                <!-- Left column container-->
                                <div class="px-4 md:px-0 lg:w-6/12">
                                    <div class="md:mx-6 md:p-12">
                                        <div class="mb-6">
                                            <label for="name" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Nazwa <span class="text-red-700">*</span></label>
                                            <input type="text" id="name" name="name" value="{{old('name')}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                        </div>
                                        <div class="mb-6">
                                            <label for="material" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Materiał <span class="text-red-700">*</span></label>
                                            <select id="material" name="material" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                @if(isset($material_list) and count($material_list) > 0)
                                                    @foreach($material_list as $mat)
                                                        <option value="{{$mat->value}}">{{$mat->value_full}}</option>
                                                    @endforeach
                                                @else
                                                    <option value=""></option>
                                                @endif
                                            </select>
                                            <x-input-error :messages="$errors->get('material')" class="mt-2" />
                                        </div>
                                        <div class="mb-6">
                                            <label for="independent" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Produkowany Niezależnie</label>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" id="independent" name="independent" value="{{old('independent')}}" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                <x-input-error :messages="$errors->get('independent')" class="mt-2" />
                                            </label>
                                        </div>
                                        <div class="mb-6">
                                            <label for="dimension" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Wymiary</label>
                                            <div id="dimension" class="flex flex-row justify-start items-center w-full xl:w-[60%]">
                                                <div class="w-[30%] mr-[3%]">
                                                    <label for="height" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Wysokość</label>
                                                    <input type="number" id="height" name="height" value="{{old('height') ? old('height') : 0}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                </div>
                                                <div class="w-[30%] mr-[3%]">
                                                    <label for="length" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Długość</label>
                                                    <input type="number" id="length" name="length" value="{{old('length') ? old('length') : 0}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                </div>
                                                <div class="w-[30%] mr-[3%]">
                                                    <label for="width" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Szerokość</label>
                                                    <input type="number" id="width" name="width" value="{{old('width') ? old('width') : 0}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                </div>
                                            </div>
                                            <x-input-error :messages="$errors->get('height')" />
                                            <x-input-error :messages="$errors->get('length')" />
                                            <x-input-error :messages="$errors->get('width')" />
                                        </div>
                                        <div class="mb-6">
                                            @php
                                                $label = 'Zdjęcie Komponentu';
                                                $info = 'Format: svg, png, jpg, jpeg, bmp';
                                                $input_name = 'comp_photo';
                                            @endphp
                                            <x-file-input :name="$input_name" :label="$label" :info="$info"></x-file-input>
                                        </div>
                                        <div class="mb-6">
                                            <label for="description" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Opis komponentu</label>
                                            <input type="text" id="description" name="description" value="{{old('description')}}" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Right column container with background and description-->
                                <div class="flex items-center flex-col justify-start rounded-b-lg lg:w-6/12 lg:rounded-r-lg lg:rounded-bl-none p-2 lg:p-0 bg-white/30">
                                    <div class="flex items-center flex-col justify-start md:mx-6 md:px-12 w-full">
                                        <button id="dropdownSearchButton" class="mt-5[%] lg:mt-[7%] text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md lg:text-lg px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                type="button"
                                                data-te-ripple-init
                                                data-te-ripple-color="light">
                                            Schematy produkcji
                                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        @if(isset($schema_data) and count($schema_data) > 0)
                                            <div class="w-full mt-[5%] mx-auto">
                                                <p id="label-schema" class=" w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                    Wybrane schematy <span class="text-red-700">*</span>
                                                    <br><span class="text-green-500 text-xs lg:text-sm"><em>Aby dodać komponent przypisz do niego schemat(y) produkcji</em></span>
                                                </p>
                                                <x-input-error :messages="$errors->get('prodschema_input')" class="w-full px-2"/>
                                                @if(isset($prod_schema_errors))
                                                    @foreach($prod_schema_errors as $err)
                                                        <x-input-error :messages="$err" class="w-full px-2"/>
                                                    @endforeach
                                                @endif
                                                <div class="px-4 sm:px-8 bg-white flex justify-start items-center flex-col mt-4">
                                                    @php
                                                        $inputPlaceholder = "Wpisz nazwę schematu...";
                                                        $xListElem = "prodschema";
                                                    @endphp
                                                    <div id="search-schema" class="prodschema-toggle w-full hidden">
                                                        <x-search-input class="w-full" :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>
                                                    </div>
                                                    <div id="prodschema-dropdown" class="w-full">
                                                        @foreach($schema_data as $prod_schema_tasks)
                                                            @if(count($prod_schema_tasks) > 0)
                                                                <x-list-element class="list-element-{{$xListElem}} list-element w-full hidden flex-col text-md lg:text-lg lg:py-4 my-3" id="prodschema-{{$prod_schema_tasks[0]->prod_schema_id}}">
                                                                    <div class="w-[100%] flex justify-between items-center">
                                                                        <div class="w-full flex justify-between items-center">
                                                                            <div class="w-full flex justify-left items-center">
                                                                                <p class="inline-block list-element-name ">{{$prod_schema_tasks[0]->prod_schema}}</p>
                                                                            </div>
                                                                        </div>
                                                                        <div id="expbtn-{{$prod_schema_tasks[0]->prod_schema_id}}" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-6 lg:h-6 md:rounded-md rounded-sm rotate-0 transition-all mr-0">
                                                                            <img src="{{asset('storage/expand-down.png') }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="production-standard mt-4 w-full ml-[3%] hidden">
                                                                        <label for="production-standard-{{$prod_schema_tasks[0]->prod_schema_id}}" class="block mb-2 text-sm lg:text-md font-medium text-gray-900 dark:text-white">
                                                                            Norma Produkcji
                                                                        </label>
                                                                        <div id="production-standard-{{$prod_schema_tasks[0]->prod_schema_id}}" class="flex flex-row justify-start items-center w-full xl:w-full">
                                                                            <div class="w-[15%] mr-[3%]">
                                                                                @php $duration = 'duration_'.$prod_schema_tasks[0]->prod_schema_id @endphp
                                                                                <label for="{{$duration}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Czas [h] <span class="text-red-700">*</span></label>
                                                                                <input type="number" id="{{$duration}}" name="{{$duration}}" value="{{old($duration)}}"
                                                                                       class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                            </div>
                                                                            <div class="w-[15%] mr-[3%]">
                                                                                @php $amount = 'amount_'.$prod_schema_tasks[0]->prod_schema_id @endphp
                                                                                <label for="{{$amount}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Ilość <span class="text-red-700">*</span></label>
                                                                                <input type="number" id="{{$amount}}" name="{{$amount}}" value="{{old($amount)}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                            </div>
                                                                            <div class="w-[30%] mr-[3%]">
                                                                                @php $unit_name = 'unit_'.$prod_schema_tasks[0]->prod_schema_id @endphp
                                                                                <label for="{{$unit_name}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Jednostka <span class="text-red-700">*</span></label>
                                                                                <select id="{{$unit_name}}" name="{{$unit_name}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                                                    @if(isset($units) and count($units) > 0)
                                                                                        @foreach($units as $u)
                                                                                            <option value="{{$u->unit}}">{{$u->unit}}</option>
                                                                                        @endforeach
                                                                                    @else
                                                                                        <option value=""></option>
                                                                                    @endif
                                                                                </select>
                                                                            </div>
                                                                            <div class="w-[20%] mr-[3%]">
                                                                                @php $sequenceno = 'sequenceno_'.$prod_schema_tasks[0]->prod_schema_id @endphp
                                                                                <label for="{{$sequenceno}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Kolejność wyk <span class="text-red-700">*</span></label>
                                                                                <input type="number" id="{{$sequenceno}}" name="{{$sequenceno}}" value="{{old($sequenceno)}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <ul class="prodschema-list-{{$prod_schema_tasks[0]->prod_schema_id}} mt-[3%] ml-[3%] relative m-0 w-full hidden list-none overflow-hidden p-0 transition-[height] duration-200 ease-in-out text-xs md:text-sm lg:text-md">
                                                                        @php $i = 1; @endphp
                                                                        <h2 class="text-gray-800">Lista zadań:</h2>
                                                                        @foreach($prod_schema_tasks as $task)
                                                                            <li class="relative h-fit after:absolute after:left-[2.45rem] after:top-[3.6rem] after:mt-px after:h-[calc(100%-2.45rem)] after:w-px after:bg-[#e0e0e0] after:content-[''] dark:after:bg-neutral-600">
                                                                                <div class="w-full flex cursor-pointer items-center p-6 leading-[1.3rem] no-underline after:bg-[#e0e0e0] after:content-[''] hover:bg-[#f9f9f9] focus:outline-none dark:after:bg-neutral-600 dark:hover:bg-[#3b3b3b]">
                                                                                <span class="mr-5 flex h-[1.938rem] w-[1.938rem] items-center justify-center rounded-full bg-blue-450 text-sm md:text-md lg:text-lg font-medium text-white">
                                                                                    {{$i}}
                                                                                </span>
                                                                                    <span class="text-gray-800 after:absolute after:flex after:text-[0.8rem] after:content-[data-content] dark:text-neutral-300">
                                                                                    {{$task->task_name}}
                                                                                </span>
                                                                                </div>
                                                                                <div class="transition-[height, margin-bottom, padding-top, padding-bottom] left-0 overflow-hidden pb-2 pl-[3.75rem] pr-6 duration-300 ease-in-out text-neutral-500 ">
                                                                                    {{$task->task_desc}}
                                                                                </div>
                                                                            </li>
                                                                            @php $i++; @endphp
                                                                        @endforeach
                                                                    </ul>
                                                                </x-list-element>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <button type="button" id="confirm-schema-button" class="prodschema-toggle hidden text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                        WYBIERZ
                                                    </button>
                                                    <input id="prodschema-input" name="prodschema_input" value="{{old('prodschema_input')}}" type="text" class="hidden"/>
                                                </div>
                                            </div>
                                        @else
                                            <p class="w-full text-center text-red-700 text-lg mt-6">Brak danych.</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center flex-col justify-start mt-[5%] md:mx-6 md:px-12 w-full">
                                        <button id="dropdownInstructionButton" class="text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md lg:text-lg px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                type="button"
                                                data-te-ripple-init
                                                data-te-ripple-color="light">
                                            Instrukcje
                                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        <div class="w-full mt-[5%] mx-auto">
                                            <p id="label-schema" class=" w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                Instrukcje wykonania
                                                <br><span class="text-green-500 text-xs lg:text-sm"><em>Możesz dodać instrukcję w formacie pdf oraz/lub film</em></span>
                                            </p>
                                        </div>
                                        <div class="mb-6 w-full">
                                            @php
                                                $label = 'Instrukcja wykonania Komponentu';
                                                $info = 'Format: pdf, docx';
                                                $input_name = 'instr_pdf';
                                            @endphp
                                            <x-file-input :name="$input_name" :label="$label" :info="$info"></x-file-input>
                                        </div>
                                        <div class="mb-6 w-full">
                                            @php
                                                $label = 'Film instruktażowy';
                                                $info = 'Format: mp4, mov, wmv, mkv';
                                                $input_name = 'instr_video';
                                            @endphp
                                            <x-file-input :name="$input_name" :label="$label" :info="$info"></x-file-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Submit button-->
                        <div class="mb-12 pb-1 pt-1 text-center">
                            <button
                                class="mb-3 inline-block rounded-lg px-6 py-2 md:py-4 text-xs font-medium uppercase w-full text-md md:text-lg xl:text-xl bg-blue-800 hover:bg-blue-950 leading-normal text-white focus:ring-4 focus:outline-none focus:ring-blue-300 shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]"
                                type="submit"
                                data-te-ripple-init
                                data-te-ripple-color="light">
                                Dodaj
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
