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

                    newUrl = $(location).attr('href').replace('komponenty','komponenty') + '/' + id;
                    similarUrl = $(location).attr('href').replace('komponenty','dodaj-komponent') + '/' + id;
                    editUrl = $(location).attr('href').replace('komponenty','edytuj-komponent') + '/' + id;

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
                var comp_list_id = '.comp-list-' + id;

                if($(comp_list_id).hasClass('hidden')) {
                    if (is_active) {
                        if(!$(comp_list_id).hasClass('just-hidden')) {
                            $('.list-element').removeClass('active-list-elem');
                        } else {
                            $(comp_list_id).removeClass('just-hidden');
                        }
                    }
                }
                checkActive();
            });

            //on click button is rotated and component list appears
            $('.expand-btn').on('click', function () {
                let id = $(this).attr('id').split('-')[1];
                let list_id = '.comp-list-' + id;


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

            $('#remove-comp-modal').on('click', function(){
                $('.element-remove').addClass('hidden');
                let activeElem = $('.list-element.active-list-elem');
                if(typeof activeElem.attr('id') === "string") {
                    let id = activeElem.attr('id').split('-');
                    if(id.length > 1) {
                        id = '#component-remove-' + id[1];
                        $(id).removeClass('hidden')
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
        @if(session('status_err'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{session('status_err')}}
                </p>
            </div>
        @endif
        @php
            $name = "Materiały";
        @endphp
        <x-information-panel :viewName="$name">
            {{--    routing for details similar and edit set in java script above   --}}
            <x-nav-button  class="on-select details bg-blue-450 hover:bg-blue-800">
                {{ __('Szczegóły') }}
            </x-nav-button>
            @if(in_array($user->role,array('admin','manager')))
                <x-nav-button :href="route('component.add')" class="ml-1 lg:ml-3">
                    {{ __('Dodaj') }}
                </x-nav-button>
                <x-nav-button class="on-select similar hover:bg-gray-700 ml-1 lg:ml-3">
                    {{ __('Dodaj Podobny') }}
                </x-nav-button>
                <x-nav-button class="on-select edit bg-orange-500 hover:bg-orange-800 ml-1 lg:ml-3">
                    {{ __('Edytuj') }}
                </x-nav-button>
{{--                <button type="button" id="remove-comp-modal" disabled class="btn btn-primary on-select remove inline-flex items-center ml-1 lg:ml-3 lg:mr-5 px-2 py-1 lg:px-4 lg:py-2 bg-red-600 hover:bg-red-800 border border-transparent rounded-md font-semibold text-md lg:text-xl text-white uppercase tracking-widest focus:bg-gray-700  focus:ring-4 focus:outline-none focus:ring-blue-300  focus:ring-offset-2 transition ease-in-out duration-150">--}}
{{--                    {{ __('Usuń') }}--}}
{{--                </button>--}}
                @php
                    $name = 'materiał';
                    $route = 'component.destroy';
                    $button_id = 'remove-comp-modal';
                    $id = '2';
                    $remove_elem_class = 'element-remove';
                    $remove_elem_id = 'comp-remove-'
                @endphp
{{--                <x-remove-modal-button :name="__('Usuń')" :id="$button_id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id"></x-remove-modal-button>--}}
                <x-remove-modal :name="$name" :button_id="$button_id" :route="$route" :id="$id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id">
                    @foreach($components as $comp)
                        <div class="{{$remove_elem_class}} hidden" id="{{$remove_elem_id}}{{$comp->id}}">
                            <x-list-element class="flex-col">
                                <div class="w-full flex justify-between items-center">
                                    <div class="w-full flex justify-left items-center">
                                        <div class="border-2 inline-block w-[50px] h-[50px] md:w-[70px] md:h-[70px] lg:w-[100px] lg:h-[100px]">
                                            @if(!empty($comp->image))
                                                @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                                                <img src="{{asset('storage/'.$path.$comp->image)}}">
                                            @endif
                                        </div>
                                        <p class="inline-block list-element-name ml-[3%]  xl:text-lg text-md">{{$comp->name}} - {{$comp->material}}</p>
                                    </div>
                                </div>
                            </x-list-element>
                        </div>
                    @endforeach
                </x-remove-modal>
            @endif
        </x-information-panel>
        @if(isset($components))
            <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col">
                    @php
                        $inputPlaceholder = "Wpisz nazwę materiału lub surowiec z jakiego jest wykonany...";
                        $xElemComp = "component";
                    @endphp
                    <x-search-input :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xElemComp"></x-search-input>
                    <div class="w-full">
                        @foreach($components as $comp)
                            <x-list-element class="list-element-{{$xElemComp}} list-element flex-col" id="component-{{$comp->id}}">
                                <div class="w-[100%] flex justify-between items-center">
                                    <div class="w-full flex justify-left items-center">
                                        <div class="border-2 inline-block w-[50px] h-[50px] md:w-[70px] md:h-[70px] lg:w-[100px] lg:h-[100px]">
                                            @if(!empty($comp->image))
                                                @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                                                <img src="{{asset('storage/'.$path.$comp->image)}}">
                                            @endif
                                        </div>
                                        <p class="inline-block list-element-name ml-[3%]  xl:text-lg text-md">{{$comp->name}} - {{$comp->material}}</p>
                                    </div>
                                    <div id="expbtn-{{$comp->id}}-comp" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-6 lg:h-6 md:w-5 md:h-5 sm:w-4 sm:h-4 mr-8 md:rounded-md rounded-sm rotate-0 transition-all">
                                        <img src="{{asset('storage/expand-down.png') }}" >
                                    </div>
                                </div>
                                <div class="comp-list-{{$comp->id}} hidden mt-6 w-full md:w-[60%]">
                                    <div class="relative overflow-x-auto shadow-md">
                                        <table class="w-full text-sm md:text-lg text-left text-gray-500 dark:text-gray-400">
                                            <thead class="text-sm md:text-lg text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
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
                                                    Surowiec
                                                </th>
                                                <td class="px-6 py-4">
                                                    {{is_null($comp->material) ? '' : $comp->material}}
                                                </td>
                                            </tr>
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    @php
                                                        $name = '';
                                                        $dim = '';
                                                        if(!is_null($comp->height)) {
                                                            $name .= 'wys ';
                                                            $dim .= $comp->height.' ';
                                                        }
                                                        if(!is_null($comp->length)) {
                                                            if(!empty($name)) {
                                                                $name .= 'x  ';
                                                                $dim .= 'x  ';
                                                            }
                                                            $name .= 'dług ';
                                                            $dim .= $comp->length.' ';
                                                        }
                                                        if(!is_null($comp->width)) {
                                                            if(!empty($name)) {
                                                                $name .= 'x  ';
                                                                $dim .= 'x  ';
                                                            }
                                                            $name .= 'szer';
                                                            $dim .= $comp->width.' ';
                                                        }
                                                        $name .= empty($name) ? 'Wymiary' : ' [cm]';

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
                                                    @if($comp->independent == 1)
                                                        tak
                                                    @else
                                                        nie
                                                    @endif
                                                </td>
                                            </tr>
                                            @if(!empty($comp->description))
                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        Szczegóły
                                                    </th>
                                                    <td class="px-6 py-4">
                                                        {{$comp->description}}
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </x-list-element>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
{{--    @php--}}
{{--        $header = 'Usuń komponent';--}}
{{--        $route = 'component.destroy';--}}
{{--        $button_id = 'remove-comp-modal';--}}
{{--        $id = '1';--}}
{{--    @endphp--}}
{{--    <x-remove-modal :header="$header" :buttonId="$button_id" :route="$route" :id="$id">--}}
{{--        @foreach($components as $comp)--}}
{{--            <div class="element-remove hidden" id="component-remove-{{$comp->id}}">--}}
{{--                <x-list-element class="flex-col">--}}
{{--                    <div class="w-[100%] flex justify-between items-center">--}}
{{--                        <div class="w-[80%] flex justify-left items-center">--}}
{{--                            <div class="border-2 inline-block w-[50px] h-[50px] md:w-[100px] md:h-[100px]">--}}
{{--                                @if(!empty($comp->image))--}}
{{--                                    @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp--}}
{{--                                    <img src="{{asset('storage/'.$path.$comp->image)}}">--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <p class="inline-block list-element-name ml-[3%] xl:text-2xl text-md lg:text-xl">{{$comp->name}} - {{$comp->material}}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </x-list-element>--}}
{{--            </div>--}}
{{--        @endforeach--}}
{{--    </x-remove-modal>--}}
</x-app-layout>