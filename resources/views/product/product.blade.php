<x-app-layout>
    <script type="module">
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
                if($(this).attr('id').includes('prod')) {
                    var list_id = '.prod-list-' + id;
                } else {
                    var list_id = '.comp-list-' + id;
                }

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

        $('#rightBtn').on('click', function (){
            $('.list-element').removeClass('active-list-elem');
            checkActive();
        });

        $('#leftBtn').on('click', function (){
            $('.list-element').removeClass('active-list-elem');
            checkActive();
        });

    </script>
    @if(isset($user) and $user instanceof \App\Models\User)
        @php
            $left = "Produkty";
            $right = "Komponenty";
        @endphp
        <x-toggle-buttons :leftBtn="$left" :rightBtn="$right">
            <x-slot name="leftContent">
                <x-information-panel :viewName="$left">
                    {{--    routing for details set in java script above   --}}
                    <x-nav-button  class="on-select details bg-blue-450 hover:bg-blue-800">
                        {{ __('Szczegóły') }}
                    </x-nav-button>
                    @if(in_array($user->role,array('admin','manager')))
                        <x-nav-button :href="route('product.add')" class="ml-1 lg:ml-3">
                            {{ __('Dodaj') }}
                        </x-nav-button>
                        <x-nav-button  class="ml-1 lg:ml-3 lg:mr-5 on-select remove bg-red-600">
                            {{ __('Usuń') }}
                        </x-nav-button>
                    @endif
                </x-information-panel>
                @if(isset($products))
                    <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col">
                            @php
                                $inputPlaceholder = "Wpisz nazwę, materiał lub kolor...";
                                $xListElemProd = "product";
                            @endphp
                            <x-search-input :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElemProd"></x-search-input>

                            <div class="w-full">
                                @foreach($products as $prod)
                                    <x-list-element class="list-element-{{$xListElemProd}} list-element flex-col" id="product-{{$prod->id}}">
                                        <div class="w-[100%] flex justify-between items-center">
                                            <div class="w-[80%] md:w-[60%] flex justify-left items-center">
                                                <div class="border-2 inline-block w-[50px] h-[50px] md:w-[100px] md:h-[100px]">
                                                    @if(!empty($prod->image))
                                                        <img src="{{asset('storage/'.$prod->image)}}">
                                                    @endif
                                                </div>
                                                @php
                                                    $name = $prod->name;
                                                    if($prod->material) $name .= ' - '.$prod->material;
                                                    if($prod->color) $name .= ' - '.$prod->color;
                                                @endphp
                                                <p class="inline-block list-element-name ml-[3%]">{{$name}}</p>
                                            </div>
                                            <div id="expbtn-{{$prod->id}}-prod" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-8 lg:h-8 md:w-6 md:h-6 sm:w-4 sm:h-4 mr-8 md:rounded-md rounded-sm rotate-0 transition-all">
                                                <img src="{{asset('storage/expand-down.png') }}" >
                                            </div>
                                        </div>
                                        {{--                                    <ul class="comp-list-{{$prod->id}} w-[80%] mt-[3%] relative m-0 w-full hidden list-none overflow-hidden p-0 transition-[height] duration-200 ease-in-out text-lg"  data-te-stepper-init data-te-stepper-type="vertical">--}}
                                        {{--                                        @php $i = 1; @endphp--}}
                                        {{--                                        <h2 class="text-gray-800">Lista komponentów:</h2>--}}
                                        {{--                                        @foreach($prod_comp_list[$prod->id] as $comp)--}}
                                        {{--                                            <li data-te-stepper-step-ref class="relative h-fit after:absolute after:left-[2.45rem] after:top-[3.6rem] after:mt-px after:h-[calc(100%-2.45rem)] after:w-px after:bg-[#e0e0e0] after:content-[''] dark:after:bg-neutral-600">--}}
                                        {{--                                                <div data-te-stepper-head-ref class="w-[80%] flex cursor-pointer items-center p-6 leading-[1.3rem] no-underline after:bg-[#e0e0e0] after:content-[''] hover:bg-[#f9f9f9] focus:outline-none dark:after:bg-neutral-600 dark:hover:bg-[#3b3b3b]">--}}
                                        {{--                                            <span data-te-stepper-head-icon-ref class="mr-3 flex h-[1.938rem] w-[1.938rem] items-center justify-center rounded-full bg-[#ebedef] text-lg font-medium text-[#40464f]">--}}
                                        {{--                                                {{$i}}--}}
                                        {{--                                            </span>--}}
                                        {{--                                                    <span data-te-stepper-head-text-ref class="text-gray-800 after:absolute after:flex after:text-[0.8rem] after:content-[data-content] dark:text-neutral-300">--}}
                                        {{--                                                {{$comp->name}}--}}
                                        {{--                                            </span>--}}
                                        {{--                                                </div>--}}
                                        {{--                                                <div data-te-stepper-content-ref class="transition-[height, margin-bottom, padding-top, padding-bottom] left-0 overflow-hidden pb-6 pl-[3.75rem] pr-6 duration-300 ease-in-out text-[16px] text-neutral-500 ">--}}
                                        {{--                                                    {{$comp->description}}--}}
                                        {{--                                                </div>--}}
                                        {{--                                            </li>--}}
                                        {{--                                            @php $i++; @endphp--}}
                                        {{--                                        @endforeach--}}
                                        {{--                                    </ul>--}}
                                        <div class="prod-list-{{$prod->id}} hidden mt-6 w-full md:w-[60%]">
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
                                                            Nazwa
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{$prod->name}}
                                                        </td>
                                                    </tr>
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                            Materiał
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{is_null($prod->material) ? '' : $prod->material}}
                                                        </td>
                                                    </tr>
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                            Kolor
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{is_null($prod->color) ? '' : $prod->color}}
                                                        </td>
                                                    </tr>
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                            Cena
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{is_null($prod->price) ? '' : $prod->price.' zł'}}
                                                        </td>
                                                    </tr>
                                                    @if(!empty($prod->description))
                                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                Szczegóły
                                                            </th>
                                                            <td class="px-6 py-4">
                                                                {{$prod->description}}
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
            </x-slot>
            <x-slot name="rightContent">
                <x-information-panel :viewName="$right">
                    {{--    routing for details set in java script above   --}}
                    <x-nav-button  class="on-select details bg-blue-450">
                        {{ __('Szczegóły') }}
                    </x-nav-button>
                    @if(in_array($user->role,array('admin','manager')))
                        <x-nav-button :href="route('product.add_component')" class="ml-1 lg:ml-3">
                            {{ __('Dodaj') }}
                        </x-nav-button>
                        <x-nav-button  class="ml-1 lg:ml-3 lg:mr-5 on-select remove bg-red-600">
                            {{ __('Usuń') }}
                        </x-nav-button>
                    @endif
                </x-information-panel>
                @if(isset($components))
                    <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col">
                            @php
                                $inputPlaceholder = "Wpisz nazwę lub materiał...";
                                $xElemComp = "component";
                            @endphp
                            <x-search-input :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xElemComp"></x-search-input>
                            <div class="w-full">
                                @foreach($components as $comp)
                                    <x-list-element class="list-element-{{$xElemComp}} list-element flex-col" id="component-{{$comp->id}}">
                                        <div class="w-[100%] flex justify-between items-center">
                                            <div class="w-[80%] flex justify-left items-center">
                                                <div class="border-2 inline-block w-[50px] h-[50px] md:w-[100px] md:h-[100px]">
                                                    @if(!empty($comp->image))
                                                        <img src="{{asset('storage/'.$comp->image)}}">
                                                    @endif
                                                </div>
                                                <p class="inline-block list-element-name ml-[3%]">{{$comp->name}} - {{$comp->material}}</p>
                                            </div>
                                            <div id="expbtn-{{$comp->id}}-comp" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-8 lg:h-8 md:w-6 md:h-6 sm:w-4 sm:h-4 mr-8 md:rounded-md rounded-sm rotate-0 transition-all">
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
                                                            Materiał
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
            </x-slot>
        </x-toggle-buttons>
    @endif
</x-app-layout>
