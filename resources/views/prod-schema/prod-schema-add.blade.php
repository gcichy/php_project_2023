<x-app-layout>
    @php
        if(!isset($selected_schem_tasks)) $selected_schem_tasks = null;
        if(!isset($task_input)) $task_input = null;
        if(!isset($selected_schem)) $selected_schem = null;
        if(!isset($selected_schem_instr)) $selected_schem_instr = null;
        if(!isset($selected_schem_prod_std)) $selected_schem_prod_std = null;
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

            $('.sequence-no').each(function () {
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
            let ids = $('#task-input').val().split('_');
            return ids.includes(elem_id)
        }


        $(document).ready(function() {
            getActiveOnLoad('task', $('#task-input').val());
            checkActive();

            $('.list-element').on('click', function () {
                let is_active = ($(this).hasClass('active-list-elem') ? true : false);
                $(this).addClass('active-list-elem');

                let id = $(this).attr('id').split('-')[1];

                //prodsschema can be unclicked if list of schemas is visible (schema is not chosen)
                if(!$('#confirm-schema-button').hasClass('hidden')) {
                    let list_id = '.task-list-' + id;
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

            $('#dropdown-prodstd-button').on('click',function (){
                let prodStd = $('#production-standard')
                if(prodStd.hasClass('hidden')) {
                    prodStd.removeClass('hidden');
                } else {
                    prodStd.addClass('hidden');
                    $('#duration').val(null);
                    $('#amount').val(null);
                }
                let removeProdStdBtn = $('#remove-prodstd-button');
                if(removeProdStdBtn.hasClass('hidden')) {
                    removeProdStdBtn.removeClass('hidden');
                } else {
                    removeProdStdBtn.addClass('hidden')
                }
            });

            //on click button is rotated and component list appears
            $('.expand-btn').on('click', function () {
                let id = $(this).attr('id').split('-')[1];
                var list_id = '.task-list-' + id;

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

            $('#dropdown-search-button').on('click', function () {
                $('.sequence-no').addClass('hidden');

                if($('.list-element-task').hasClass('hidden')) {
                    $('.list-element-task').removeClass('hidden');
                }
                else {
                    $('.list-element-task').addClass('hidden');

                }
                if($('.task-toggle').hasClass('hidden')) {
                    $('.task-toggle').removeClass('hidden');
                    $('#label-schema').addClass('hidden');

                }
                else {
                    $('.task-toggle').addClass('hidden');
                    $('#label-schema').removeClass('hidden');
                }
            });

            $('#new-task-button').on('click', function () {
                let counter = $('#new-counter');
                counter.val(parseInt(counter.val())+1)

                let newTask = $('#new-task-ghost').clone();
                let id = 'new-task-'+counter.val()
                newTask.attr('id',id).addClass('active-list-elem list-element list-element-task');

                newTask.find('.new-sequence-no')
                    .attr('id','new-sequence-no-'+counter.val())
                    .attr('name','new_sequence_no_'+counter.val());

                newTask.find('.new-desc')
                    .attr('id','new-desc-'+counter.val())
                    .attr('name','new_desc_'+counter.val());

                newTask.find('.new-name')
                    .attr('id','new-name-'+counter.val())
                    .attr('name','new_name_'+counter.val());

                newTask.find('.new-amount-required')
                    .attr('id','new-amount-required-'+counter.val())
                    .attr('name','new_amount_required_'+counter.val());

                newTask.removeClass('hidden');
                $('#new-dropdown').append(newTask);
                if($('#remove-new-button').hasClass('hidden')) {
                    $('#remove-new-button').removeClass('hidden');
                }
                else if(parseInt(counter.val()) === 0){
                    $('#remove-new-button').addClass('hidden');
                }
            });

            $('#remove-new-button').on('click', function () {
                let counter = $('#new-counter');
                let highestId = '#new-task-' + counter.val();
                if($(highestId).length) {
                    $(highestId).remove();
                    if(!$(highestId).length) {
                        counter.val(parseInt(counter.val())-1)
                    }
                }
            });

            $('#remove-prodstd-button').on('click', function(){
                $('#amount').val(null);
                $('#duration').val(null)
            });

            $('#confirm-schema-button').on('click', function (){
                if($('#label-schema').hasClass('hidden')) {
                    $('#label-schema').removeClass('hidden');
                    $('.task-toggle').addClass('hidden');
                    $('.list-element-task:not(.active-list-elem)').addClass('hidden');
                    let id_string = '';
                    let chosen_elements = $('.list-element-task.active-list-elem:not(.new-list-elem)');
                    let i = 1;
                    chosen_elements.each(function() {
                        let id = $(this).attr('id').split('-')[1];
                        $('#sequenceno_'+id).val(i);
                        id_string += !id ? '_' : id + '_';
                        i++;
                    })
                    id_string = id_string.slice(0, id_string.length - 1)
                    $('#task-input').val(id_string);

                    $('.sequence-no').each(function () {
                        if($(this).parent().hasClass('active-list-elem')) {
                            $(this).removeClass('hidden');
                        }
                    });
                }
            });



        });

    </script>
    @php
        $name = (isset($update) and $update) ? 'Edytuj zadanie' : 'Dodaj zadanie';
    @endphp
    <x-information-panel :viewName="$name"></x-information-panel>
    @if(session('status'))
        <div class="flex justify-center items-center">
            <x-input-error :messages="session('status')" class="w-full !text-md lg:text-xl font-medium text-center p-6"/>
        </div>
    @endif
    <div class="gradient-form h-full dark:bg-neutral-700 flex justify-center w-full">
        <div class="h-full w-full p-10">
            <div class="g-6 flex h-full flex-wrap items-center justify-center text-neutral-800 dark:text-neutral-200">
                <div class="w-full">
                        <form method="POST" action="{{(isset($update) and $update)? route('schema.update') : route('schema.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="block rounded-lg bg-white shadow-lg dark:bg-neutral-800">
                            <div class="g-0 lg:flex lg:flex-wrap">
                                <!-- Left column container-->
                                <div class="flex items-center flex-col justify-start rounded-b-lg w-full xl:w-6/12 xl:rounded-r-lg xl:rounded-bl-none p-2 xl:p-0 bg-white/30">
                                    <div class="md:mx-6 md:px-12 md:pt-12 px-2 py-6 w-full">
                                        <input type="text" id="schema-id" name="schema_id" class="hidden"
                                               value="{{old('schema_id') ? old('schema_id') : (empty($selected_schem) ? '' : $selected_schem->id )}}">
                                        <div class="mb-6">
                                            <label for="production-schema" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Nazwa <span class="text-red-700">*</span></label>
                                            <input type="text" id="production-schema" name="production_schema"
                                                   value="{{old('production_schema') ? old('production_schema') : (empty($selected_schem) ? '' : $selected_schem->production_schema )}}"
                                                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                            <x-input-error :messages="$errors->get('production_schema')" class="mt-2" />
                                        </div>
                                        <div class="mb-6">
                                            <label for="description" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Opis zadania</label>
                                            {{--                                            <input type="textarea" id="description" name="description" value="{{old('description') ? old('description') : (empty($selected_schem) ? '' : $selected_schem->description )}}" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">--}}
                                            <textarea id="description" name="description" rows="4" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
{{old('description') ? old('description') : (empty($selected_schem) ? '' : $selected_schem->description )}}
                                                </textarea>
                                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                        </div>
                                        <div class="mb-6">
                                            <label for="countable" class="block text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Niemierzalne</label>
                                            <p class="w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                <span class="text-green-500 text-xs lg:text-sm"><em>Zaznacz, jeśli nie da się zmierzyć efektów zadania, np sprzątanie hali produkcyjnej itp. Jeśli pole jest zaznaczone, norma produkcji nie zostanie dodana.</em></span>
                                            </p>
                                            <input
                                                class="countable ml-2 mr-2 mt-[0.3rem] h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-neutral-600 dark:after:bg-neutral-400 dark:checked:bg-primary dark:checked:after:bg-primary dark:focus:before:shadow-[3px_-1px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca]"
                                                type="checkbox" role="switch" id="countable" name="countable"
                                                {{ old('countable') == 'on' ? 'checked' : ((!empty($selected_schem) and $selected_schem->non_countable) ? 'checked' : '') }}/>
                                        </div>
                                        <div class="mb-6">
                                            <label for="countable" class="block text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Odpady - jednostka</label>
                                            <p class="w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                <span class="text-green-500 text-xs lg:text-sm"><em>Jeśli w trkacie wykonywania zadania może powstać odpad, wybierz jednostkę odpadu.</em></span>
                                            </p>
                                            <select id="waste-unit" name="waste_unit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="" selected></option>
                                                @if(isset($units) and count($units) > 0)
                                                    @foreach($units as $u)
                                                        @if(old('waste_unit') == $u->id)
                                                            <option value="{{$u->id}}" selected>{{$u->unit}}</option>
                                                        @elseif(!empty($selected_schem) and $selected_schem->waste_unit_id == $u->id)
                                                            <option value="{{$u->id}}" selected>{{$u->unit}}</option>
                                                        @else
                                                            <option value="{{$u->id}}">{{$u->unit}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
{{--                                        <div class="mb-6">--}}
{{--                                            <label for="material" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">Surowiec</label>--}}
{{--                                            <select id="material" name="material" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">--}}
{{--                                                @if(isset($material_list) and count($material_list) > 0)--}}
{{--                                                    <option value=""></option>--}}
{{--                                                    @foreach($material_list as $mat)--}}
{{--                                                        @if($selected_prod instanceof \App\Models\Product and $mat->value == $selected_prod->material)--}}
{{--                                                            <option value="{{$mat->value}}" selected>{{$mat->value_full}}</option>--}}
{{--                                                        @else--}}
{{--                                                            <option value="{{$mat->value}}">{{$mat->value_full}}</option>--}}
{{--                                                        @endif--}}
{{--                                                    @endforeach--}}
{{--                                                @else--}}
{{--                                                    <option value=""></option>--}}
{{--                                                @endif--}}
{{--                                            </select>--}}
{{--                                            <x-input-error :messages="$errors->get('material')" class="mt-2" />--}}
{{--                                        </div>--}}
{{--                                        <div class="">--}}
{{--                                            <label for="" class="block text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Niemierzalny</label>--}}
{{--                                            <p class="w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">--}}
{{--                                                <span class="text-green-500 text-xs lg:text-sm"><em>Określa, czy wykonując zadanie, należy wprowadzić ilość sztuk. Najczęściej jest to konieczne dla ostatniego zadania w schemacie.</em></span>--}}
{{--                                            </p>--}}
{{--                                            <input--}}
{{--                                                class="new-amount-required amount-required-input mr-2 mt-[0.3rem] h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-neutral-600 dark:after:bg-neutral-400 dark:checked:bg-primary dark:checked:after:bg-primary dark:focus:before:shadow-[3px_-1px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca]"--}}
{{--                                                type="checkbox" role="switch" id="" name=""/>--}}
{{--                                        </div>--}}
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
                                        <div class="mb-6 w-full p-2">
                                            @php
                                                $label = 'Instrukcja wykonania schematu';
                                                $info = 'Format: pdf, docx';
                                                $input_name = 'instr_pdf';
                                                $file_to_copy = ($selected_schem_instr instanceof \App\Models\Instruction and !empty($selected_schem_instr->instruction_pdf)) ? $selected_schem_instr->instruction_pdf : '';
                                            @endphp
                                            <x-file-input :name="$input_name" :label="$label" :info="$info" :file="$file_to_copy"></x-file-input>
                                        </div>
                                        <div class="mb-6 w-full p-2">
                                            @php
                                                $label = 'Film instruktażowy';
                                                $info = 'Format: mp4, mov, wmv, mkv';
                                                $input_name = 'instr_video';
                                                $file_to_copy = ($selected_schem_instr instanceof \App\Models\Instruction and !empty($selected_schem_instr->video)) ? $selected_schem_instr->video : '';
                                            @endphp
                                            <x-file-input :name="$input_name" :label="$label" :info="$info" :file="$file_to_copy"></x-file-input>
                                        </div>
                                    </div>
                                    <div class="flex items-center flex-col justify-start mt-[2%] mb-[5%] md:mx-6 md:px-12 w-full">
                                        <button id="dropdown-prodstd-button" class="text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md lg:text-lg px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                type="button"
                                                data-te-ripple-init
                                                data-te-ripple-color="light">
                                            Norma produkcji
                                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        <div class="w-full mx-auto">
                                            <p id="" class=" w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                <span class="text-green-500 text-xs lg:text-sm"><em>Jeśli zadanie nie jest wykorzystywane do wytwarzania materiałów, można dodać mu własną normę produkcji.</em></span>
                                            </p>
                                        </div>
                                        <div id="production-standard" class="production-standard mt-4 w-full ml-[3%] {{(old('duration') or !empty($selected_schem_prod_std))? '' : 'hidden'}} ">
                                            <div id="production-standard" class="flex flex-row justify-start items-center w-full xl:w-full">
                                                <div class="w-[20%] mr-[3%]">
                                                    <label for="duration" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Czas[h]</label>
                                                    @if(!empty($selected_schem_prod_std))
                                                        <input type="number" id="duration" name="duration"
                                                               value="{{old('duration') ? old('duration') : (empty($selected_schem_prod_std->duration_hours) ? '' : $selected_schem_prod_std->duration_hours )}}"
                                                               class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                    @else
                                                        <input type="number" id="duration" name="duration" value="{{old('duration')}}"
                                                               class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                    @endif
                                                </div>
                                                <div class="w-[20%] mr-[3%]">
                                                    <label for="amount" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Ilość</label>
                                                    @if(!empty($selected_schem_prod_std))
                                                        <input type="number" id="amount" name="amount"
                                                               value="{{old('amount') ? old('amount') : (empty($selected_schem_prod_std->amount) ? '' : $selected_schem_prod_std->amount)}}"
                                                               class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                    @else
                                                        <input type="number" id="amount" name="amount" value="{{old('amount')}}"
                                                               class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                    @endif
                                                </div>
                                                <div class="w-[35%] mr-[3%]">
                                                    <label for="unit" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Jednostka</label>
                                                    <select id="unit" name="unit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        @if(isset($units) and count($units) > 0)
                                                            @foreach($units as $u)
                                                                @if(!empty($selected_schem_prod_std) and $u->unit == $selected_schem_prod_std->unit )
                                                                    <option value="{{$u->unit}}" selected>{{$u->unit}}</option>
                                                                @else
                                                                    <option value="{{$u->unit}}">{{$u->unit}}</option>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <option value=""></option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full">
                                            <x-input-error :messages="$errors->get('amount')" />
                                            <x-input-error :messages="$errors->get('duration')" />
                                            <x-input-error :messages="$errors->get('unit')" />
                                        </div>
                                        <button type="button" id="remove-prodstd-button" class="my-5 text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 hidden">
                                            USUŃ
                                        </button>
                                    </div>
                                </div>

                                <!-- Right column container with background and description-->
                                <div class="flex items-center flex-col justify-start rounded-b-lg w-full xl:w-6/12 xl:rounded-r-lg xl:rounded-bl-none p-2 xl:p-0 bg-white/30">
                                    <div class="flex items-center flex-col justify-start md:mx-6 md:px-12 w-full">
                                        <button id="dropdown-search-button" class="mt-5[%] lg:mt-[7%] text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md lg:text-lg px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                type="button"
                                                data-te-ripple-init
                                                data-te-ripple-color="light">
                                            Podzadania
                                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        @if(isset($tasks) and count($tasks) > 0)
                                            <div class="w-full mt-[5%] mx-auto">
                                                <p id="label-schema" class=" w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                    Wybrane podzadania <span class="text-red-700">*</span>
                                                    <br><span class="text-green-500 text-xs lg:text-sm"><em>Aby dodać zadanie przypisz do niego minimum 1 podzadanie</em></span>
                                                </p>
                                                @if(session('task_errors'))
                                                    @foreach(session('task_errors') as $err)
                                                        <x-input-error :messages="$err" class="w-full px-2"/>
                                                    @endforeach
                                                @endif
                                                <x-input-error :messages="$errors->get('task_input')" class="w-full px-2"/>
                                                @if(isset($task_errors))
                                                    @foreach($task_errors as $err)
                                                        <x-input-error :messages="$err" class="w-full px-2"/>
                                                    @endforeach
                                                @endif
                                                <div class="bg-white flex justify-start items-center flex-col mt-4">
                                                    @php
                                                        $inputPlaceholder = "Wpisz nazwę zadania...";
                                                        $xListElem = "task";
                                                    @endphp
                                                    <div id="search-schema" class="task-toggle w-full hidden">
                                                        <x-search-input class="w-full" :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>
                                                    </div>
                                                    <div id="task-dropdown" class="w-full">
                                                        @php $j = 0; @endphp
                                                        @foreach($tasks as $task)
                                                            <x-list-element class="list-element-{{$xListElem}} list-element w-full hidden text-md flex-col lg:py-0 py-0"
                                                                            id="task-{{$task->task_id}}">
                                                                <div class="w-full flex flex-row justify-center">
                                                                    <div class="w-[85%] flex flex-col justify-between items-center">
                                                                        <div class="w-full flex justify-left items-center">
                                                                            <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                                                {{$task->task_name}}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="w-[15%] flex justify-end items-center">
                                                                        <div id="expbtn-{{$task->task_id}}" class="expand-btn inline-block  p-0.5 bg-gray-800 rounded-md rotate-0 transition-all mr-1">
                                                                            <svg width="30px" height="30px" viewBox="0 0 1024 1024" class="w-5 h-5 lg:w-6 lg:h-6"  xmlns="http://www.w3.org/2000/svg">
                                                                                <title>szczegóły podzadania</title>
                                                                                <path d="M903.232 256l56.768 50.432L512 768 64 306.432 120.768 256 512 659.072z" fill="#ffffff" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="task-list-{{$task->task_id}} w-full hidden my-4 text-xs lg:text-sm text-left ml-2 text-gray-600">
                                                                    <p>{{$task->task_desc}}</p>
                                                                </div>
                                                                <div class="sequence-no my-4 w-full ml-[3%] hidden">
                                                                    <div id="sequence-no-{{$task->task_id}}" class="flex flex-row justify-start items-center w-full xl:w-full">
                                                                        <div class="w-[40%] mr-[3%]">
                                                                            @php $sequenceno = 'sequenceno_'.$task->task_id @endphp
                                                                            <label for="{{$sequenceno}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Kolejność wykonania<span class="text-red-700">*</span></label>
                                                                            @if(!empty($selected_schem_tasks) and count($selected_schem_tasks) > 0 and $task->task_name == $selected_schem_tasks[$j]->task_name)
                                                                                <input type="number" id="{{$sequenceno}}" name="{{$sequenceno}}" value="{{old($sequenceno) ? old($sequenceno) : (empty($selected_schem_tasks[$j]) ? '' : $selected_schem_tasks[$j]->task_sequence_no)}}"
                                                                                       class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                            @else
                                                                                <input type="number" id="{{$sequenceno}}" name="{{$sequenceno}}" value="{{old($sequenceno)}}"
                                                                                       class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-4">
                                                                        <label for="amount-required-{{$task->task_id}}" class="block text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Ilość wymagana</label>
                                                                        <p class="label-amount-required w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                                            <span class="text-green-500 text-xs lg:text-sm"><em>Określa, czy wykonując zadanie, należy wprowadzić ilość sztuk. Najczęściej jest to konieczne dla ostatniego zadania w schemacie.</em></span>
                                                                        </p>
                                                                        <input
                                                                            class="new-amount-required amount-required-input mr-2 mt-[0.3rem] h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-neutral-600 dark:after:bg-neutral-400 dark:checked:bg-primary dark:checked:after:bg-primary dark:focus:before:shadow-[3px_-1px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca]"
                                                                            type="checkbox" role="switch" id="amount-required-{{$task->task_id}}" name="amount_required_{{$task->task_id}}"
                                                                            @if(!empty($selected_schem_tasks) and count($selected_schem_tasks) > 0 and $task->task_name == $selected_schem_tasks[$j]->task_name)
                                                                                {{($selected_schem_tasks[$j]->amount_required == 1) ? 'checked' : ''}}
                                                                                @php if($j + 1 < count($selected_schem_tasks)) $j++ @endphp
                                                                            @endif/>
                                                                    </div>
                                                                </div>
                                                            </x-list-element>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" id="confirm-schema-button" class="task-toggle hidden mb-3 text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                        WYBIERZ
                                                    </button>
                                                    <input id="task-input" name="task_input" value="{{old('task_input') ? old('task_input') : (empty($task_input) ? '' : $task_input )}}" type="text" class="hidden"/>
                                                </div>
                                            </div>
                                        @else
                                            <p class="w-full text-center text-red-700 text-lg mt-6">Brak danych.</p>
                                        @endif
                                        <button id="new-task-button" class="mt-5[%] lg:mt-[7%] text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md lg:text-lg px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                type="button"
                                                data-te-ripple-init
                                                data-te-ripple-color="light">
                                            Nowe podzadanie
                                            <svg class="w-4 h-4 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path d="M4 12H20M12 4V20" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                        <div class="w-full mt-[5%] mx-auto">
                                            <div class="bg-white flex justify-start items-center flex-col">
                                                <div id="new-dropdown" class="w-full">
                                                    <input type="number" id="new-counter" name="new_counter" value="0" class="hidden">
                                                    <x-list-element class="new-list-elem w-full hidden flex-col text-md lg:text-lg lg:py-4 my-6" id="new-task-ghost">
                                                        <div class="w-full">
                                                            <input type="text" id="schema-id" name="schema_id" value="{{old('schema_id') ? old('schema_id') : (empty($selected_schem) ? '' : $selected_schem->id )}}" class="hidden">
                                                            <div class="">
                                                                <label for="task-name" class="block mb-1 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Nazwa<span class="text-red-700">*</span></label>
                                                                <input type="text" id="" name="" value="{{old('name') ? old('name') : (empty($selected_schem) ? '' : $selected_schem->name )}}"
                                                                       class="new-name shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-xs lg:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                            </div>
                                                            <div class="">
                                                                <label for="" class="block mt-2 mb-1 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Opis podzadania</label>
                                                                {{--                                            <input type="textarea" id="description" name="description" value="{{old('description') ? old('description') : (empty($selected_schem) ? '' : $selected_schem->description )}}" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">--}}
                                                                <textarea id="" name="" rows="2" class="new-desc block w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                                    {{old('description') ? old('description') : (empty($selected_schem) ? '' : $selected_schem->description )}}
                                                                </textarea>
                                                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                                            </div>
                                                            <div class="w-[40%] mr-[3%]">
                                                                <label for="" class="block mt-2 mb-1 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Kolejność wykonania<span class="text-red-700">*</span></label>
                                                                <input type="number" id="" name="" value=""
                                                                       class="new-sequence-no shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                            </div>
                                                            <div class="mt-4">
                                                                <label for="" class="block text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Ilość wymagana</label>
                                                                <p class="w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                                    <span class="text-green-500 text-xs lg:text-sm"><em>Określa, czy wykonując podzadanie, należy wprowadzić ilość sztuk. Najczęściej jest to konieczne dla ostatniego podzadania w zadaniu.</em></span>
                                                                </p>
                                                                <input
                                                                    class="new-amount-required amount-required-input mr-2 mt-[0.3rem] h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-neutral-600 dark:after:bg-neutral-400 dark:checked:bg-primary dark:checked:after:bg-primary dark:focus:before:shadow-[3px_-1px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca]"
                                                                    type="checkbox" role="switch" id="" name=""/>
{{--                                                                <label class="amount-required relative inline-flex items-center cursor-pointer">--}}
{{--                                                                    <input type="checkbox" id="" name="" value="" class="new-amount-required amount-required-input sr-only peer">--}}
{{--                                                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>--}}
{{--                                                                </label>--}}
                                                            </div>
                                                        </div>
                                                    </x-list-element>
                                                </div>
                                                <button type="button" id="remove-new-button" class="hidden mb-3 text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                    USUŃ
                                                </button>
                                            </div>
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
    </div>
</x-app-layout>
