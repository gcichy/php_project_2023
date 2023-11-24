<x-app-layout>
    @php
        if(!isset($selected_schem_tasks)) $selected_schem_tasks = null;
        if(!isset($task_input)) $task_input = null;
        if(!isset($selected_schem)) $selected_schem = null;
        if(!isset($selected_schem_instr)) $selected_schem_instr = null;
    @endphp
    <script type="module">

        function getActiveOnLoad(prefix, inputVal) {
            let numbers = inputVal.split('_');
            numbers.forEach(function(val) {
                if(!isNaN(parseInt(val))) {
                    let id = '#task-' + val;
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
            //getActiveOnLoad('task', $('#task-input').val());
            checkActive();

            $('.list-element').on('click', function () {
                let is_active = ($(this).hasClass('active-list-elem') ? true : false);
                $(this).addClass('active-list-elem');

                let id = $(this).attr('id').split('-')[1];

                //prodsschema can be unclicked if list of schemas is visible (schema is not chosen)
                if(!$('#confirm-schema-button').hasClass('hidden')) {
                    let list_id = '.task-list-' + id;
                    console.log($(list_id));
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
                console.log('halo')
                let prodStd = $('#production-standard')
                if(prodStd.hasClass('hidden')) {
                    prodStd.removeClass('hidden');
                } else {
                    prodStd.addClass('hidden');
                    $('#duration').val(null);
                    $('#amount').val(null);
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

                console.log(newTask.find('.new-name'));
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

            $('#independent').on('click', function () {
                $(this).val($(this).prop("checked") ? 1 : 0);
            });
        });

    </script>
    @php
        $name = (isset($update) and $update) ? 'Edytuj schemat' : 'Dodaj schemat';
    @endphp
    <x-information-panel :viewName="$name"></x-information-panel>
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
                        <form method="POST" action="{{ route('schema.update') }}" enctype="multipart/form-data">
                    @else
                        <form method="POST" action="{{ route('schema.store') }}" enctype="multipart/form-data">
                    @endif
                        @csrf
                        <div class="block rounded-lg bg-white shadow-lg dark:bg-neutral-800">
                            <div class="g-0 lg:flex lg:flex-wrap">
                                <!-- Left column container-->
                                <div class="flex items-center flex-col justify-start rounded-b-lg w-full xl:w-6/12 xl:rounded-r-lg xl:rounded-bl-none p-2 xl:p-0 bg-white/30">
                                    <div class="md:mx-6 md:p-12 px-2 py-6 w-full">
                                        <input type="text" id="schema-id" name="schema_id" value="{{old('schema_id') ? old('schema_id') : (empty($selected_schem) ? '' : $selected_schem->id )}}" class="hidden">
                                        <div class="mb-6">
                                            <label for="name" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Nazwa <span class="text-red-700">*</span></label>
                                            <input type="text" id="name" name="name" value="{{old('name') ? old('name') : (empty($selected_schem) ? '' : $selected_schem->name )}}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                        </div>
                                        <div class="">
                                            <label for="description" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Opis schematu</label>
                                            {{--                                            <input type="textarea" id="description" name="description" value="{{old('description') ? old('description') : (empty($selected_schem) ? '' : $selected_schem->description )}}" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">--}}
                                            <textarea id="description" name="description" rows="4" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs lg:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
{{old('description') ? old('description') : (empty($selected_schem) ? '' : $selected_schem->description )}}
                                                </textarea>
                                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
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
                                                <span class="text-green-500 text-xs lg:text-sm"><em>Jeśli schemat nie jest wykorzystywany do wytwarzania komponentów, można dodać mu własną normę produkcji.</em></span>
                                            </p>
                                        </div>
                                        <div id="production-standard" class="production-standard mt-4 w-full ml-[3%] hidden">
                                            <div id="production-standard" class="flex flex-row justify-start items-center w-full xl:w-full">
                                                <div class="w-[20%] mr-[3%]">
                                                    <label for="duration" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Czas[h]</label>
                                                    @if(!empty($selected_schem))
                                                        <input type="number" id="duration" name="duration" value="{{old('duration') ? old('duration') : (empty($selected_schem->duration_hours) ? '' : $selected_schem->duration_hours )}}"
                                                               class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                    @else
                                                        <input type="number" id="duration" name="duration" value="{{old('duration')}}"
                                                               class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                    @endif
                                                </div>
                                                <div class="w-[20%] mr-[3%]">
                                                    <label for="amount" class="block mb-2 text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Ilość</label>
                                                    @if(!empty($selected_schem))
                                                        <input type="number" id="amount" name="amount" value="{{old('amount') ? old('amount') : (empty($selected_schem->amount) ? '' : $selected_schem->amount)}}"
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
                                                                @if(!empty($selected_schem) and $u->unit == $selected_schem->unit )
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
                                    </div>
                                </div>

                                <!-- Right column container with background and description-->
                                <div class="flex items-center flex-col justify-start rounded-b-lg xl:w-6/12 xl:rounded-r-lg xl:rounded-bl-none p-2 xl:p-0 bg-white/30">
                                    <div class="flex items-center flex-col justify-start md:mx-6 md:px-12 w-full">
                                        <button id="dropdown-search-button" class="mt-5[%] lg:mt-[7%] text-white bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md lg:text-lg px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                type="button"
                                                data-te-ripple-init
                                                data-te-ripple-color="light">
                                            Zadania
                                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        @if(isset($tasks) and count($tasks) > 0)
                                            <div class="w-full mt-[5%] mx-auto">
                                                <p id="label-schema" class=" w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                    Wybrane zadania <span class="text-red-700">*</span>
                                                    <br><span class="text-green-500 text-xs lg:text-sm"><em>Aby dodać schemat przypisz do niego minimum 1 zadanie</em></span>
                                                </p>
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
                                                            <x-list-element class="list-element-{{$xListElem}} list-element w-full hidden flex-col text-md lg:text-lg lg:py-4 my-3" id="task-{{$task->task_id}}">
                                                                <div class="w-[100%] flex justify-between items-center">
                                                                    <div class="w-full flex justify-between items-center">
                                                                        <div class="w-full flex justify-left items-center">
                                                                            <p class="inline-block list-element-name text-md xl:text-lg">{{$task->task_name}}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div id="expbtn-{{$task->task_id}}" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-6 lg:h-6 md:rounded-md rounded-sm rotate-0 transition-all mr-0">
                                                                        <img src="{{asset('storage/expand-down.png') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="task-list-{{$task->task_id}} w-full hidden my-4 text-xs lg:text-sm text-left ml-2 text-gray-600">
                                                                    <p>{{$task->task_desc}}</p>
                                                                </div>
                                                                <div class="sequence-no mt-4 w-full ml-[3%] hidden">
                                                                    <div id="sequence-no-{{$task->task_id}}" class="flex flex-row justify-start items-center w-full xl:w-full">
                                                                        <div class="w-[40%] mr-[3%]">
                                                                            @php $sequenceno = 'sequenceno_'.$task->task_id @endphp
                                                                            <label for="{{$sequenceno}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Kolejność wykonania<span class="text-red-700">*</span></label>
                                                                            @if(!empty($selected_schem_tasks) and count($selected_schem_tasks) > 0 and $task->task_id == $selected_schem_tasks[$j]->production_schema_id)
                                                                                <input type="number" id="{{$sequenceno}}" name="{{$sequenceno}}" value="{{old($sequenceno) ? old($sequenceno) : (empty($selected_schem_tasks[$j]) ? '' : $selected_schem_tasks[$j]->sequence_no)}}"
                                                                                       class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                                @php if($j + 1 < count($selected_schem_tasks)) $j++ @endphp
                                                                            @else
                                                                                <input type="number" id="{{$sequenceno}}" name="{{$sequenceno}}" value="{{old($sequenceno)}}"
                                                                                       class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                            @endif
                                                                        </div>
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
                                            Nowe zadanie
                                            <svg class="w-4 h-4 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path d="M4 12H20M12 4V20" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                        <div class="w-full mt-[5%] mx-auto">
                                            <div class="bg-white flex justify-start items-center flex-col">
                                                <div id="new-dropdown" class="w-full">
                                                    <input type="number" id="new-counter" name="new_counter" value="0" class="hidden">
                                                    <x-list-element class="new-list-elem w-full hidden flex-col text-md lg:text-lg lg:py-4 my-3" id="new-task-ghost">
                                                        <div class="w-full">
                                                            <input type="text" id="schema-id" name="schema_id" value="{{old('schema_id') ? old('schema_id') : (empty($selected_schem) ? '' : $selected_schem->id )}}" class="hidden">
                                                            <div class="">
                                                                <label for="name" class="block mb-1 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Nazwa zadania<span class="text-red-700">*</span></label>
                                                                <input type="text" id="name" name="name" value="{{old('name') ? old('name') : (empty($selected_schem) ? '' : $selected_schem->name )}}"
                                                                       class="new-name shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-xs lg:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                                                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                            </div>
                                                            <div class="">
                                                                <label for="" class="block mt-2 mb-1 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Opis zadania</label>
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
    </section>
</x-app-layout>
