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
                similar.removeClass('bg-gray-800').addClass('bg-gray-400').attr('href', $(location).attr('href'));
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
                    similar.removeClass('bg-gray-400').addClass('bg-gray-800').attr('href', similarUrl);
                    edit.removeClass('bg-gray-400').addClass('bg-orange-500').attr('href', editUrl);
                }
                else {
                    remove.removeClass('bg-red-600').addClass('bg-gray-400')
                    details.removeClass('bg-blue-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                    similar.removeClass('bg-gray-800').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                    edit.removeClass('bg-orange-500').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                }
            }
        }

        $(document).ready(function() {
            checkActive();

            $('.list-element').on('click', function () {
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
                $name = "Schematy produkcji";
            @endphp
            <x-information-panel :viewName="$name">
                {{--    routing for details similar and edit set in java script above   --}}
                <x-nav-button  class="on-select details bg-blue-450 hover:bg-blue-800">
                    {{ __('Szczegóły') }}
                </x-nav-button>
                @if(in_array($user->role,array('admin','manager')))
                    <x-nav-button :href="route('schema.add')" class="ml-1 lg:ml-3">
                        {{ __('Dodaj') }}
                    </x-nav-button>
                    <x-nav-button class="on-select similar hover:bg-gray-700 ml-1 lg:ml-3">
                        {{ __('Dodaj Podobny') }}
                    </x-nav-button>
                    <x-nav-button class="on-select edit bg-orange-500 hover:bg-orange-800 ml-1 lg:ml-3">
                        {{ __('Edytuj') }}
                    </x-nav-button>
                    @php
                        $name = 'schemat produkcji';
                        $route = 'schema.destroy';
                        $button_id = 'remove-schema-modal';
                        $id = '1';
                        $remove_elem_class = 'element-remove';
                        $remove_elem_id = 'schema-remove-'
                    @endphp
                   <x-remove-modal :name="$name" :button_id="$button_id" :route="$route" :id="$id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id">
                        @foreach($schema_data as $prod_schema_tasks)
                            @if(count($prod_schema_tasks) > 0)
                                <div class="{{$remove_elem_class}} hidden" id="{{$remove_elem_id}}{{$prod_schema_tasks[0]->prod_schema_id}}">
                                    <x-list-element class="flex-col">
                                        <div class="w-[100%] flex justify-between items-center">
                                            <div class="w-full flex justify-between items-center">
                                                <div class="w-full flex justify-left items-center">
                                                    <p class="inline-block list-element-name ">{{$prod_schema_tasks[0]->prod_schema}}</p>
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
            <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6 flex justify-center">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col w-full lg:w-[90%] xl:w-[70%]">
                    @php
                        $inputPlaceholder = "Wpisz nazwę schematu...";
                        $xListElem = "prodschema";
                    @endphp
                    <div id="search-schema" class="prodschema-toggle w-full">
                        <x-search-input class="w-full" :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>
                    </div>
                    <div id="prodschema-dropdown" class="w-full">
                        @php $j = 0; @endphp
                        @foreach($schema_data as $prod_schema_tasks)
                            @if(count($prod_schema_tasks) > 0)
                                <x-list-element class="list-element-{{$xListElem}} list-element w-full flex-col text-md lg:text-lg lg:py-4 my-3" id="prodschema-{{$prod_schema_tasks[0]->prod_schema_id}}">
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
{{--                                        <label for="production-standard-{{$prod_schema_tasks[0]->prod_schema_id}}" class="block mb-2 text-sm lg:text-md font-medium text-gray-900 dark:text-white">--}}
{{--                                            Norma Produkcji--}}
{{--                                        </label>--}}
{{--                                        <div id="production-standard-{{$prod_schema_tasks[0]->prod_schema_id}}" class="flex flex-row justify-start items-center w-full xl:w-full">--}}
{{--                                            <div class="w-[15%] mr-[3%]">--}}
{{--                                                @php $duration = 'duration_'.$prod_schema_tasks[0]->prod_schema_id @endphp--}}
{{--                                                <label for="{{$duration}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Czas[h]<span class="text-red-700">*</span></label>--}}
{{--                                                @if(!empty($selected_comp_schemas) and count($selected_comp_schemas) > 0 and $prod_schema_tasks[0]->prod_schema_id == $selected_comp_schemas[$j]->production_schema_id)--}}
{{--                                                    <input type="number" id="{{$duration}}" name="{{$duration}}" value="{{old($duration) ? old($duration) : (empty($selected_comp_schemas[$j]) ? '' : $selected_comp_schemas[$j]->duration_hours )}}"--}}
{{--                                                           class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">--}}
{{--                                                @else--}}
{{--                                                    <input type="number" id="{{$duration}}" name="{{$duration}}" value="{{old($duration)}}"--}}
{{--                                                           class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                            <div class="w-[15%] mr-[3%]">--}}
{{--                                                @php $amount = 'amount_'.$prod_schema_tasks[0]->prod_schema_id @endphp--}}
{{--                                                <label for="{{$amount}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Ilość <span class="text-red-700">*</span></label>--}}
{{--                                                @if(!empty($selected_comp_schemas) and count($selected_comp_schemas) > 0 and $prod_schema_tasks[0]->prod_schema_id == $selected_comp_schemas[$j]->production_schema_id)--}}
{{--                                                    <input type="number" id="{{$amount}}" name="{{$amount}}" value="{{old($amount) ? old($amount) : (empty($selected_comp_schemas[$j]) ? '' : $selected_comp_schemas[$j]->amount)}}"--}}
{{--                                                           class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">--}}
{{--                                                @else--}}
{{--                                                    <input type="number" id="{{$amount}}" name="{{$amount}}" value="{{old($amount)}}"--}}
{{--                                                           class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                            <div class="w-[30%] mr-[3%]">--}}
{{--                                                @php $unit_name = 'unit_'.$prod_schema_tasks[0]->prod_schema_id @endphp--}}
{{--                                                <label for="{{$unit_name}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Jednostka<span class="text-red-700">*</span></label>--}}
{{--                                                <select id="{{$unit_name}}" name="{{$unit_name}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">--}}
{{--                                                    @if(isset($units) and count($units) > 0)--}}
{{--                                                        @foreach($units as $u)--}}
{{--                                                            @if(!empty($selected_comp_schemas)--}}
{{--                                                                    and count($selected_comp_schemas) > 0--}}
{{--                                                                    and $prod_schema_tasks[0]->prod_schema_id == $selected_comp_schemas[$j]->production_schema_id--}}
{{--                                                                    and $u->unit == $selected_comp_schemas[$j]->unit )--}}
{{--                                                                <option value="{{$u->unit}}" selected>{{$u->unit}}</option>--}}
{{--                                                            @else--}}
{{--                                                                <option value="{{$u->unit}}">{{$u->unit}}</option>--}}
{{--                                                            @endif--}}
{{--                                                        @endforeach--}}
{{--                                                    @else--}}
{{--                                                        <option value=""></option>--}}
{{--                                                    @endif--}}
{{--                                                </select>--}}
{{--                                            </div>--}}
{{--                                            <div class="w-[20%] mr-[3%]">--}}
{{--                                                @php $sequenceno = 'sequenceno_'.$prod_schema_tasks[0]->prod_schema_id @endphp--}}
{{--                                                <label for="{{$sequenceno}}" class="block mb-2 text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Kol wyk<span class="text-red-700">*</span></label>--}}
{{--                                                @if(!empty($selected_comp_schemas) and count($selected_comp_schemas) > 0 and $prod_schema_tasks[0]->prod_schema_id == $selected_comp_schemas[$j]->production_schema_id)--}}
{{--                                                    <input type="number" id="{{$sequenceno}}" name="{{$sequenceno}}" value="{{old($sequenceno) ? old($sequenceno) : (empty($selected_comp_schemas[$j]) ? '' : $selected_comp_schemas[$j]->sequence_no)}}"--}}
{{--                                                           class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">--}}
{{--                                                    @php if($j + 1 < count($selected_comp_schemas)) $j++ @endphp--}}
{{--                                                @else--}}
{{--                                                    <input type="number" id="{{$sequenceno}}" name="{{$sequenceno}}" value="{{old($sequenceno)}}"--}}
{{--                                                           class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                    </div>
                                    <ul class="prodschema-list-{{$prod_schema_tasks[0]->prod_schema_id}} mt-[3%] ml-[3%] relative m-0 w-full hidden list-none overflow-hidden p-0 transition-[height] duration-200 ease-in-out text-xs md:text-sm lg:text-md">
                                        @if(!empty($prod_schema_tasks[0]->prod_std_id))
                                            <table class="w-full text-md xl:text-lg xl:h-[90%] text-left text-gray-700 dark:text-gray-400 mt-3 mb-5">
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
                                                    <td class="px-6">
                                                        {{$prod_schema_tasks[0]->prod_std_name}}
                                                    </td>
                                                    <td class="px-6">
                                                        {{$prod_schema_tasks[0]->prod_std_duration}}
                                                    </td>
                                                    <td class="px-6">
                                                        {{$prod_schema_tasks[0]->prod_std_amount}}
                                                    </td>
                                                    <td class="px-6">
                                                        {{$prod_schema_tasks[0]->prod_std_unit}}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        @endif
                                        @php $i = 1; @endphp
                                        <h2 class="text-gray-800 text-md xl:text-lg">Lista zadań:</h2>
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
