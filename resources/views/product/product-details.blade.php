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
                        console.log($(location).attr('href'));
                        var regexp = new RegExp("produkty.*");
                        newUrl = $(location).attr('href').replace(regexp,'komponenty') + '/' + id;
                        console.log(newUrl);
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

    </script>
    @php
        $viewName = 'Szczegóły produktu';
    @endphp
    <x-information-panel :viewName="$viewName">
    </x-information-panel>
    @if(isset($prod) and isset($data) and isset($instruction))
        <div class="w-full md:w-[90%] md:ml-[5%] mt-4 md:mt-8 bg-white border border-gray-200 rounded-md shadow dark:bg-gray-800 dark:border-gray-700">
            <ul class="flex text-sm md:text-lg lg:text-xl font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg  dark:divide-gray-600 dark:text-gray-400" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                <li class="w-full">
                    <button id="info-tab" data-tabs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true" class="aria-selected:text-blue-450 inline-block w-full p-4 rounded-tl-lg bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                        Informacje
                    </button>
                </li>
                <li class="w-full">
                    <button id="production-tab" data-tabs-target="#production" type="button" role="tab" aria-controls="production" aria-selected="false" class="aria-selected:text-blue-450 inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                        Komponenty
                    </button>
                </li>
                <li class="w-full">
                    <button id="manual-tab" data-tabs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false" class="aria-selected:text-blue-450 inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                        Instrukcja
                    </button>
                </li>
            </ul>
            <div id="fullWidthTabContent" class="border-t border-gray-200 dark:border-gray-600">
                <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="info" role="tabpanel" aria-labelledby="info-tab">
                    <dl class="grid grid-cols-1 gap-8 xl:gap-2 p-4 mx-auto text-gray-900 xl:grid-cols-2 dark:text-white sm:p-8">
                        <div class="flex flex-col items-center justify-center xl:w-[95%]">
                            @if(!empty($prod->image))
                                <div class="max-w-[350px] lg:max-w-[450px]">
                                    <img src="{{asset('storage/'.$prod->image)}}">
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-center justify-center md:w-[100%]">
                            <div class="prod-list-{{$prod->id}} w-full">
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
                        </div>
                    </dl>
                </div>
                <div class="hidden p-4 bg-white rounded-lg lg:p-8 dark:bg-gray-800" id="production" role="tabpanel" aria-labelledby="production-tab">
                    @if(count($data) > 0)
                        <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
                            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col">
                                @php
                                    $inputPlaceholder = "Wpisz nazwę lub materiał...";
                                    $xElemComp = "component";
                                @endphp
                                <div class="w-full flex flex-col lg:flex-row justify-center items-center">
                                    <x-search-input :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xElemComp">
                                    </x-search-input>
                                    <div class="w-1/3 flex justify-center items-center mt-3 lg:mt-0">
                                        <x-nav-button  class="ml-1 lg:ml-3 on-select details bg-blue-450 hover:bg-blue-800">
                                            {{ __('Szczegóły') }}
                                        </x-nav-button>
                                    </div>

                                </div>

                                <div class="w-full">
                                    @foreach($data as $comp)
                                        <x-list-element class="list-element-{{$xElemComp}} list-element flex-col" id="component-{{$comp->id}}">
                                            <div class="w-[100%] flex justify-between items-center">
                                                <div class="w-[80%] flex justify-left items-center">
                                                    <div class="border-2 inline-block w-[50px] h-[50px] md:w-[100px] md:h-[100px]">
                                                        @if(!empty($comp->image))
                                                            <img src="{{asset('storage/'.$comp->image)}}">
                                                        @endif
                                                    </div>
                                                    <p class="inline-block list-element-name ml-[3%] xl:text-2xl text-left text-sm md:text-lg">{{$comp->name}} - {{$comp->material}}</p>
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
                </div>
                <div class="hidden p-4 bg-white rounded-lg lg:p-8 dark:bg-gray-800 flex flex-col justify-center items-center" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                    @if($instruction instanceof \App\Models\Instruction)
                        <div class="w-full flex flex-col justify-center items-center mb-12">
                            <p class="w-full lg:w-[80%] mb-4 text-md md:text-xl font-medium pl-2 lg:pl-4 lg:pb-2 text-gray-950 border-l-4 border-blue-450">
                                {{$instruction->name}}
                            </p>
                            @if(!is_null($instruction->video))
                                <video class="w-full lg:w-[80%]" width="320" height="240" controls>
                                    <source src="{{asset('storage/lights_go.mp4')}}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                        @if(!is_null($instruction->instruction_pdf))
                            <div class="w-full flex flex-col justify-center items-center">
                                <embed class="w-full lg:w-[80%] h-[400px] lg:h-[600px] xl:h-[800px]" src="{{asset('storage/DATA_MODEL_prototyp.pdf')}}" width="800px" height="800px"/>
                            </div>
                        @endif
                    @else
                        <p class="w-full text-center text-red-700 text-lg mt-6">Brak instrukcji.</p>
                    @endif
                </div>
            </div>
        </div>
    @elseif(isset($error_msg))
        <p class="w-full text-center text-red-700 text-lg mt-6">{{$error_msg}}</p>
    @endif
</x-app-layout>

