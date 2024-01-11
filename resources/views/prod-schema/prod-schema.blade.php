<x-app-layout>
    <script type="module">

        function checkActive() {
            let similar = $('.similar');
            let remove = $('.remove');
            let details = $('.details');
            let edit = $('.edit');
            //check if any element is active, if not details button's href is set to current url
            if($('.list-element.active-list-elem').length === 0) {
                remove.removeClass('bg-red-600').addClass('bg-gray-400')
                details.removeClass('bg-blue-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                similar.removeClass('bg-green-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
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

                    newUrl = $(location).attr('href') + '/' + id;
                    similarUrl = $(location).attr('href').replace('schematy','dodaj-schemat') + '/' + id;
                    editUrl = $(location).attr('href').replace('schematy','edytuj-schemat') + '/' + id;

                    remove.removeClass('bg-gray-400').addClass('bg-red-600').prop('disabled', false)
                    details.removeClass('bg-gray-400').addClass('bg-blue-450').attr('href', newUrl);
                    similar.removeClass('bg-gray-400').addClass('bg-green-450').attr('href', similarUrl);
                    edit.removeClass('bg-gray-400').addClass('bg-orange-500').attr('href', editUrl);
                }
                else {
                    remove.removeClass('bg-red-600').addClass('bg-gray-400')
                    details.removeClass('bg-blue-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                    similar.removeClass('bg-green-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                    edit.removeClass('bg-orange-500').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                }
            }
        }

        $(document).ready(function() {
            checkActive();

            $('.list-element').on('click', function () {
                console.log('lista');
                var is_active = ($(this).hasClass('active-list-elem') ? true : false);
                $('.list-element').removeClass('active-list-elem');
                $(this).addClass('active-list-elem');

                var id = $(this).attr('id').split('-')[1];
                var list_id = '.prodschema-list-' + id;

                if($(list_id).hasClass('hidden')) {
                    if (is_active) {
                        if(!$(list_id).hasClass('just-hidden')) {
                            $('.list-element').removeClass('active-list-elem');
                        } else {
                            $(list_id).removeClass('just-hidden');
                        }
                    }
                }
                checkActive();
            });

            //on click button is rotated and component list appears
            $('.expand-btn').on('click', function () {
                let id = $(this).attr('id').split('-')[1];
                var list_id = '.prodschema-list-' + id;

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
        @if(isset($schema_data))
            @php
                $name = "Zadania";
            @endphp
            <x-information-panel :viewName="$name">
                {{--    routing for details similar and edit set in java script above   --}}
                <x-nav-button  class="on-select details bg-blue-450 hover:bg-blue-800">
                    {{ __('Szczegóły') }}
                </x-nav-button>
                @if(in_array($user->role,array('admin','manager')))
                    <x-nav-button :href="route('schema.add')" class="bg-green-450 hover:bg-green-700 ml-1 lg:ml-3 ">
                        {{ __('Dodaj') }}
                    </x-nav-button>
                    <x-nav-button class="on-select similar hover:bg-green-700 ml-1 lg:ml-3">
                        {{ __('Dodaj Podobne') }}
                    </x-nav-button>
                    <x-nav-button class="on-select edit bg-orange-500 hover:bg-orange-800 ml-1 lg:ml-3">
                        {{ __('Edytuj') }}
                    </x-nav-button>
                    @php
                        $name = 'zadanie';
                        $route = route('schema.destroy');
                        $button_id = 'remove-schema-modal';
                        $id = '1';
                        $remove_elem_class = 'element-remove';
                        $remove_elem_id = 'schema-remove-';
                        $disabled = 'disabled';
                    @endphp
                   <x-remove-modal :name="$name" :button_id="$button_id" :route="$route" :id="$id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id" :disabled="$disabled">
                        @foreach($schema_data as $prod_schema_tasks)
                            @if(count($prod_schema_tasks) > 0)
                                <div class="{{$remove_elem_class}} hidden" id="{{$remove_elem_id}}{{$prod_schema_tasks[0]->prod_schema_id}}">
                                    <x-list-element class="flex-col mx-5 lg:py-0 py-0">
                                        <div class="w-full flex flex-row justify-center">
                                            <div class="w-full flex flex-col justify-between items-center">
                                                <div class="w-full flex justify-left items-center">
                                                    <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                        {{$prod_schema_tasks[0]->prod_schema}}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </x-list-element>
                                </div>
                            @endif
                        @endforeach
                    </x-remove-modal>
                @endif
            </x-information-panel>
            <div class="max-w-7xl  mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6 flex justify-center">
                <div class="p-4 xl:w-[80%] sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col w-full lg:w-[90%] xl:w-[70%]">
                    @php
                        $inputPlaceholder = "Wpisz nazwę zadania...";
                        $xListElem = "prodschema";
                    @endphp
                    <div id="search-schema" class="prodschema-toggle w-full">
                        <x-search-input class="w-full" :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>
                    </div>
                    <div id="prodschema-dropdown" class="w-full">
                        @php $j = 0; @endphp
                        @foreach($schema_data as $prod_schema_tasks)
                            @if(count($prod_schema_tasks) > 0)
                                <x-list-element class="list-element-{{$xListElem}} list-element w-full flex-col text-md lg:text-lg lg:py-0 py-0" id="prodschema-{{$prod_schema_tasks[0]->prod_schema_id}}">
                                    <div class="w-full flex flex-row justify-center">
                                        <div class="w-[85%] flex flex-col justify-between items-center">
                                            <div class="w-full flex justify-left items-center">
                                                <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                    {{$prod_schema_tasks[0]->prod_schema}}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="w-[15%] flex justify-end items-center">
                                            <div id="expbtn-{{$prod_schema_tasks[0]->prod_schema_id}}" class="expand-btn inline-block  p-0.5 bg-gray-800 rounded-md rotate-0 transition-all mr-1">
                                                <svg width="30px" height="30px" viewBox="0 0 1024 1024" class="w-5 h-5 lg:w-6 lg:h-6"  xmlns="http://www.w3.org/2000/svg">
                                                    <title>szczegóły zadania</title>
                                                    <path d="M903.232 256l56.768 50.432L512 768 64 306.432 120.768 256 512 659.072z" fill="#ffffff" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="production-standard mt-4 w-full ml-[3%] hidden">
                                    </div>
                                    <ul class="prodschema-list-{{$prod_schema_tasks[0]->prod_schema_id}} my-[3%] ml-[3%] relative m-0 w-full hidden list-none overflow-hidden p-0 transition-[height] duration-200 ease-in-out text-xs md:text-sm lg:text-md">
                                        @if(!empty($prod_schema_tasks[0]->prod_std_id))
                                            <table class="w-full text-sm xl:text-lg xl:h-[90%] text-left text-gray-700 dark:text-gray-400 shadow-md mt-3 mb-5">
                                                <thead class="text-gray-950 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-medium">
                                                <tr>
                                                    <td class="px-6">
                                                        Norma Produkcji
                                                    </td>
                                                    <td class="px-6">
                                                        Czas[h]
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
                                                    <td class="px-6 py-2">
                                                        {{$prod_schema_tasks[0]->prod_std_name}}
                                                    </td>
                                                    <td class="px-6 py-2">
                                                        {{$prod_schema_tasks[0]->prod_std_duration}}
                                                    </td>
                                                    <td class="px-6 py-2">
                                                        {{$prod_schema_tasks[0]->prod_std_amount}}
                                                    </td>
                                                    <td class="px-6 py-2">
                                                        {{$prod_schema_tasks[0]->prod_std_unit}}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        @endif
                                        @php $i = 1; @endphp
                                        <div class="my-4 ml-4">
                                            <label for="countable" class="block text-sm xl:text-lg font-medium text-gray-900 dark:text-white">Niemierzalne</label>
                                            <input
                                                class="countable ml-2 mr-2 mt-[0.3rem] h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-neutral-600 dark:after:bg-neutral-400 dark:checked:bg-primary dark:checked:after:bg-primary dark:focus:before:shadow-[3px_-1px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca]"
                                                type="checkbox" role="switch" id="countable" name="countable"
                                                {{ $prod_schema_tasks[0]->non_countable ? 'checked' : '' }} disabled/>
                                        </div>
                                        <div class="my-4 ml-4">
                                            <label for="waste_unit" class="block text-sm xl:text-lg font-medium text-gray-900 dark:text-white">Odpady jednostka</label>
                                            <div class="p-3 shadow-md w-[50px] text-center rounded-lg">
                                                {{$prod_schema_tasks[0]->unit}}
                                            </div>
                                        </div>
                                        <h2 class="text-gray-800 text-sm xl:text-lg ml-4">Lista podzadań:</h2>
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
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
