<x-app-layout>
    @php
        $class_prefix = 'component';
        if(!isset($selected_prod_comps)) $selected_prod_comps = null;
        if(!isset($component_input)) $component_input = null;
        if(!isset($selected_prod)) $selected_prod = null;
        if(!isset($selected_prod_instr)) $selected_prod_instr = null;
    @endphp
    <script type="module">

        function getActiveOnLoad(prefix, inputVal) {
            let numbers = inputVal.split('_');
            numbers.forEach(function(val) {
                if(!isNaN(parseInt(val))) {
                    let id = '#' + prefix + '-' + val;
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
            let ids = $('#{{$class_prefix}}-input').val().split('_');
            return ids.includes(elem_id)
        }

        function checkIndependent() {
            if($('#independent').val()) {
                $('#independent').prop('checked', true);
            } else {
                $('#independent').prop('checked', false);
            }
        }

        $(document).ready(function() {
            checkIndependent();
            getActiveOnLoad('{{$class_prefix}}', $('#{{$class_prefix}}-input').val());
            checkActive();

            $('.list-element').on('click', function () {
                let is_active = ($(this).hasClass('active-list-elem') ? true : false);
                $(this).addClass('active-list-elem');

                let id = $(this).attr('id').split('-')[1];
                //component can be unclicked if list of components is visible (component is not chosen)
                if(!$('#confirm-{{$class_prefix}}-button').hasClass('hidden')) {
                    let list_id = '.{{$class_prefix}}-list-' + id;
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
                let itemsToDisplay = '.{{$class_prefix}}-list-' + id + ', .comp-list-' + id;

                if($(this).hasClass('rotate-180')) {
                    $(this).removeClass('rotate-180');
                    $(this).addClass('rotate-0');
                } else {
                    $(this).removeClass('rotate-0');
                    $(this).addClass('rotate-180');
                }

                if($(itemsToDisplay).hasClass('hidden')) {
                    $(itemsToDisplay).removeClass('hidden');
                } else {
                    $(itemsToDisplay).addClass('hidden');
                    $(itemsToDisplay).addClass('just-hidden');
                }

            });

            $('#dropdownSearchButton').on('click', function () {
                $('.production-standard').addClass('hidden');

                if($('.list-element-component').hasClass('hidden')) {
                    $('.list-element-component').removeClass('hidden');
                }
                else {
                    $('.list-element-component').addClass('hidden');

                }
                if($('.{{$class_prefix}}-toggle').hasClass('hidden')) {
                    $('.{{$class_prefix}}-toggle').removeClass('hidden');
                    $('#label-{{$class_prefix}}').addClass('hidden');

                }
                else {
                    $('.{{$class_prefix}}-toggle').addClass('hidden');
                    $('#label-{{$class_prefix}}').removeClass('hidden');
                }
            });


            $('#confirm-{{$class_prefix}}-button').on('click', function (){
                if($('#label-{{$class_prefix}}').hasClass('hidden')) {
                    $('#label-{{$class_prefix}}').removeClass('hidden');
                    $('.{{$class_prefix}}-toggle').addClass('hidden');
                    $('.list-element-component:not(.active-list-elem)').addClass('hidden');
                    let id_string = '';
                    let chosen_elements = $('.list-element-component.active-list-elem');
                    let i = 1;
                    chosen_elements.each(function() {
                        let id = $(this).attr('id').split('-')[1];
                        $('#sequenceno_'+id).val(i);
                        id_string += !id ? '_' : id + '_';
                        i++;
                    })
                    id_string = id_string.slice(0, id_string.length - 1)
                    $('#{{$class_prefix}}-input').val(id_string);

                    $('.production-standard').each(function () {
                        if($(this).parent().hasClass('active-list-elem')) {
                            $(this).removeClass('hidden');
                        }
                    });
                }
            });

            $('#independent').on('click', function () {
                $(this).val($(this).prop("checked") ? 1 : 0);
            });
        });

    </script>
    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
        <a class ='block w-1/2 pl-3 pr-4 py-2 border-blue-450 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
            {{ (isset($update) and $update) ? __('Edytuj produkt') : __('Dodaj produkt') }}
        </a>
        <div class="py-5 pr-5 flex justify-center align-middle">
        </div>
    </div>
    @if(isset($status))
        <div class="flex justify-center items-center">
            <x-input-error :messages="$status" class="w-full !text-md lg:text-xl font-medium text-center p-6"/>
        </div>
    @endif
    <section class="gradient-form h-full dark:bg-neutral-700 flex justify-center">
        <div class="container h-full w-full p-10">
            <div class="g-6 flex h-full flex-wrap items-center justify-center text-neutral-800 dark:text-neutral-200">
                <div class="w-full">

                    @if(isset($update) and $update)
                        <form method="POST" action="{{ route('product.update') }}" enctype="multipart/form-data">
                            @else
                                <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                                    @endif
                                    @csrf
                                    <div class="block rounded-lg bg-white shadow-lg dark:bg-neutral-800">
                                        <div class="g-0 lg:flex lg:flex-wrap">
                                            <!-- Left column container-->
                                            <div class="px-4 md:px-0 lg:w-6/12">
                                                <div class="md:mx-6 md:p-12">
                                                    <input type="text" id="product-id" name="product_id" value="{{old('product_id') ? old('product_id') : (empty($selected_prod) ? '' : $selected_prod->id )}}" class="hidden">
                                                    <div class="mb-6">
                                                        <label for="name" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Nazwa <span class="text-red-700">*</span></label>
                                                        <input type="text" id="name" name="name" value="{{old('name') ? old('name') : (empty($selected_prod) ? '' : $selected_prod->name )}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                    </div>
                                                    <div class="mb-6">
                                                        <label for="gtin" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">GTIN</label>
                                                        <input type="text" id="gtin" name="gtin" value="{{old('gtin') ? old('gtin') : (empty($selected_prod) ? '' : $selected_prod->gtin )}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                        <x-input-error :messages="$errors->get('gtin')" class="mt-2" />
                                                    </div>
                                                    <div class="mb-6">
                                                        @php
                                                            $label = 'Kod kreskowy';
                                                            $info = 'Format: svg, png, jpg, jpeg, bmp, pdf';
                                                            $input_name = 'prod_barcode';
                                                            $file_to_copy = ($selected_prod instanceof \App\Models\Product and !empty($selected_prod->barcode_image)) ? $selected_prod->barcode_image : '';
                                                        @endphp
                                                        <x-file-input :name="$input_name" :label="$label" :info="$info" :file="$file_to_copy"></x-file-input>
                                                    </div>
                                                    <div class="mb-6">
                                                        <label for="material" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Materiał</label>
                                                        <select id="material" name="material" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                            @if(isset($material_list) and count($material_list) > 0)
                                                                <option value=""></option>
                                                                @foreach($material_list as $mat)
                                                                    @if($selected_prod instanceof \App\Models\Product and $mat->value == $selected_prod->material)
                                                                        <option value="{{$mat->value}}" selected>{{$mat->value_full}}</option>
                                                                    @else
                                                                        <option value="{{$mat->value}}">{{$mat->value_full}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <option value=""></option>
                                                            @endif
                                                        </select>
                                                        <x-input-error :messages="$errors->get('material')" class="mt-2" />
                                                    </div>
                                                    <div class="mb-6">
                                                        <label for="color" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Kolor</label>
                                                        <input type="text" id="color" name="color" value="{{old('color') ? old('color') : (empty($selected_prod) ? '' : $selected_prod->color )}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                                    </div>
                                                    <div class="mb-6">
                                                        <label for="price" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Cena</label>
                                                        <input type="number" id="price" name="price" value="{{old('price') ? old('price') : (empty($selected_prod) || empty($selected_prod->price) ? 0 : $selected_prod->price)}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                                    </div>
                                                    <div class="mb-6">
                                                        @php
                                                            $label = 'Zdjęcie Komponentu';
                                                            $info = 'Format: svg, png, jpg, jpeg, bmp';
                                                            $input_name = 'prod_image';
                                                            $file_to_copy = ($selected_prod instanceof \App\Models\Product and !empty($selected_prod->image)) ? $selected_prod->image : '';
                                                        @endphp
                                                        <x-file-input :name="$input_name" :label="$label" :info="$info" :file="$file_to_copy"></x-file-input>
                                                    </div>
                                                    <div class="mb-6">
                                                        <label for="description" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Opis produktu</label>
                                                        {{--                                            <input type="textarea" id="description" name="description" value="{{old('description') ? old('description') : (empty($selected_prod) ? '' : $selected_prod->description )}}" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">--}}
                                                        <textarea id="description" name="description" rows="4" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
{{old('description') ? old('description') : (empty($selected_prod) ? '' : $selected_prod->description )}}
                                                </textarea>
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
                                                        Komponenty
                                                        <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                                        </svg>
                                                    </button>
                                                    @if(isset($comp_data) and count($comp_data) > 0)
                                                        <div class="w-full mt-[5%] mx-auto">
                                                            <p id="label-{{$class_prefix}}" class=" w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                                Wybrane komponenty
                                                                <br><span class="text-green-500 text-xs lg:text-sm"><em>Jeśli produkt powinien mieć normę produkcji, dodaj komponent(y) z którego(ych) jest wykonany</em></span>
                                                            </p>
                                                            <x-input-error :messages="$errors->get($class_prefix.'_input')" class="w-full px-2"/>
                                                            @if(isset($prod_schema_errors))
                                                                @foreach($prod_schema_errors as $err)
                                                                    <x-input-error :messages="$err" class="w-full px-2"/>
                                                                @endforeach
                                                            @endif
                                                            <div class="px-4 sm:px-8 bg-white flex justify-start items-center flex-col mt-4">
                                                                @php
                                                                    $inputPlaceholder = "Wpisz nazwę komponentu...";
                                                                    $xListElem = "component";
                                                                @endphp
                                                                <div id="search-{{$class_prefix}}" class="{{$class_prefix}}-toggle w-full hidden">
                                                                    <x-search-input class="w-full" :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>
                                                                </div>
                                                                <div id="{{$class_prefix}}-dropdown" class="w-full">
                                                                    @php $j = 0; @endphp
                                                                    @foreach($comp_data as $comp_prod_schemas)
                                                                        @if(count($comp_prod_schemas) > 0)
                                                                            <x-list-element class="list-element-{{$xListElem}} list-element w-full hidden flex-col text-md lg:text-lg lg:py-4 my-3" id="{{$class_prefix}}-{{$comp_prod_schemas[0]->comp_id}}">
                                                                                <div class="w-[100%] flex justify-between items-center">
                                                                                    <div class="w-full flex justify-between items-center">
                                                                                        <div class="w-full flex justify-left items-center">
                                                                                            <p class="inline-block list-element-name ">{{$comp_prod_schemas[0]->name}}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div id="expbtn-{{$comp_prod_schemas[0]->comp_id}}" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-6 lg:h-6 md:rounded-md rounded-sm rotate-0 transition-all mr-0">
                                                                                        <img src="{{asset('storage/expand-down.png') }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="comp-list-{{$comp_prod_schemas[0]->comp_id}} hidden mt-6 w-full">
                                                                                    <div class="relative overflow-x-auto shadow-md">
                                                                                        <table class="w-full text-sm md:text-md text-left text-gray-500 dark:text-gray-400">
                                                                                            <thead class="text-sm md:text-ms text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                                                            <tr>
                                                                                                <th scope="col" class="px-6 py-3">
                                                                                                    Opis
                                                                                                </th>
                                                                                                <th scope="col" class="px-6 py-3"></th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                                                    Materiał
                                                                                                </th>
                                                                                                <td class="px-6 py-4">
                                                                                                    {{is_null($comp_prod_schemas[0]->material) ? '' : $comp_prod_schemas[0]->material}}
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                                                    @php
                                                                                                        $name = '';
                                                                                                        $dim = '';
                                                                                                        if(!is_null($comp_prod_schemas[0]->height)) {
                                                                                                            $name .= 'wys ';
                                                                                                            $dim .= $comp_prod_schemas[0]->height.' ';
                                                                                                        }
                                                                                                        if(!is_null($comp_prod_schemas[0]->length)) {
                                                                                                            if(!empty($name)) {
                                                                                                                $name .= 'x  ';
                                                                                                                $dim .= 'x  ';
                                                                                                            }
                                                                                                            $name .= 'dług ';
                                                                                                            $dim .= $comp_prod_schemas[0]->length.' ';
                                                                                                        }
                                                                                                        if(!is_null($comp_prod_schemas[0]->width)) {
                                                                                                            if(!empty($name)) {
                                                                                                                $name .= 'x  ';
                                                                                                                $dim .= 'x  ';
                                                                                                            }
                                                                                                            $name .= 'szer';
                                                                                                            $dim .= $comp_prod_schemas[0]->width.' ';
                                                                                                        }
                                                                                                        $name .= ' [cm]';
                                                                                                    @endphp
                                                                                                    {{$name}}
                                                                                                </th>
                                                                                                <td class="px-6 py-4">
                                                                                                    {{$dim}}
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                                                    Produkowane niezależnie
                                                                                                </th>
                                                                                                <td class="px-6 py-4">
                                                                                                    @if($comp_prod_schemas[0]->independent == 1)
                                                                                                        tak
                                                                                                    @else
                                                                                                        nie
                                                                                                    @endif
                                                                                                </td>
                                                                                            </tr>
                                                                                            @if(!empty($comp_prod_schemas[0]->description))
                                                                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                                                        Szczegóły
                                                                                                    </th>
                                                                                                    <td class="px-6 py-4">
                                                                                                        {{$comp_prod_schemas[0]->description}}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endif
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                                <ul class="{{$class_prefix}}-list-{{$comp_prod_schemas[0]->comp_id}} mt-[3%] ml-[3%] relative m-0 w-full hidden list-none overflow-hidden p-0 transition-[height] duration-200 ease-in-out text-xs md:text-sm lg:text-md">
                                                                                    <h2 class="text-gray-800">Lista schematów produkcji:</h2>
                                                                                    @foreach($comp_prod_schemas as $prod_schema)
                                                                                        <li class="relative h-fit after:absolute after:left-[2.45rem] after:top-[3.6rem] after:mt-px after:h-[calc(100%-2.45rem)] after:w-px after:bg-[#e0e0e0] after:content-[''] dark:after:bg-neutral-600">
                                                                                            <div class="w-full flex cursor-pointer items-center p-6 leading-[1.3rem] no-underline after:bg-[#e0e0e0] after:content-[''] hover:bg-[#f9f9f9] focus:outline-none dark:after:bg-neutral-600 dark:hover:bg-[#3b3b3b]">
                                                                                                <span class="mr-5 flex h-[1.938rem] w-[1.938rem] items-center justify-center rounded-full bg-blue-450 text-sm md:text-md lg:text-lg font-medium text-white">
                                                                                                    {{$prod_schema->prod_schema_sequence_no}}
                                                                                                </span>
                                                                                                <span class="text-gray-800 after:absolute after:flex after:text-[0.8rem] after:content-[data-content] dark:text-neutral-300">
                                                                                                    {{$prod_schema->prod_schema}}
                                                                                                </span>
                                                                                            </div>
                                                                                            <div class="transition-[height, margin-bottom, padding-top, padding-bottom] left-0 overflow-hidden pb-2 pl-[3.75rem] pr-6 duration-300 ease-in-out text-neutral-500 ">
                                                                                                {{$prod_schema->prod_schema_desc}}
                                                                                            </div>
                                                                                            <div class="transition-[height, margin-bottom, padding-top, padding-bottom] left-0 overflow-hidden mt-6 pb-2 pl-[3.75rem] pr-6 duration-300 ease-in-out text-neutral-500 ">
                                                                                                <table class="w-full text-sm xl:text-md xl:h-[90%] text-left text-gray-700 dark:text-gray-400">
                                                                                                    <thead class="text-gray-950 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-medium">
                                                                                                    <tr>
                                                                                                        <td class="px-6 font-bold">
                                                                                                            Norma Produkcji
                                                                                                        </td>
                                                                                                        <td></td>
                                                                                                        <td></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td class="px-6">
                                                                                                            Czas [h]
                                                                                                        </td>
                                                                                                        <td class="px-6">
                                                                                                            Ilość
                                                                                                        </td>
                                                                                                        <td class="px-6">
                                                                                                            Jednostka
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                    <tr class="text-sm xl:text-md bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                                                        <td class="px-6">
                                                                                                            {{$prod_schema->prod_std_duration}}
                                                                                                        </td>
                                                                                                        <td class="px-6">
                                                                                                            {{$prod_schema->prod_std_amount}}
                                                                                                        </td>
                                                                                                        <td class="px-6">
                                                                                                            {{$prod_schema->prod_std_unit}}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </x-list-element>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                                <button type="button" id="confirm-{{$class_prefix}}-button" class="{{$class_prefix}}-toggle hidden text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                                    WYBIERZ
                                                                </button>
                                                                <input id="{{$class_prefix}}-input" name="{{$class_prefix}}_input" value="{{old($class_prefix.'_input') ? old($class_prefix.'_input') : (empty($component_input) ? '' : $component_input )}}" type="text" class="hidden"/>
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
                                                        <p id="label-instruction" class=" w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                            Instrukcje wykonania
                                                            <br><span class="text-green-500 text-xs lg:text-sm"><em>Możesz dodać instrukcję w formacie pdf oraz/lub film</em></span>
                                                        </p>
                                                    </div>
                                                    <div class="mb-6 w-full">
                                                        @php
                                                            $label = 'Instrukcja wykonania produktu';
                                                            $info = 'Format: pdf';
                                                            $input_name = 'instr_pdf';
                                                            $file_to_copy = ($selected_prod_instr instanceof \App\Models\Instruction and !empty($selected_prod_instr->instruction_pdf)) ? $selected_prod_instr->instruction_pdf : '';
                                                        @endphp
                                                        <x-file-input :name="$input_name" :label="$label" :info="$info" :file="$file_to_copy"></x-file-input>
                                                    </div>
                                                    <div class="mb-6 w-full">
                                                        @php
                                                            $label = 'Film instruktażowy';
                                                            $info = 'Format: mp4, mov, wmv, mkv';
                                                            $input_name = 'instr_video';
                                                            $file_to_copy = ($selected_prod_instr instanceof \App\Models\Instruction and !empty($selected_prod_instr->video)) ? $selected_prod_instr->video : '';
                                                        @endphp
                                                        <x-file-input :name="$input_name" :label="$label" :info="$info" :file="$file_to_copy"></x-file-input>
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
                                            {{ (isset($update) and $update) ? __('Edytuj') : __('Dodaj') }}
                                        </button>
                                    </div>
                                </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
