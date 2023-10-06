<x-app-layout>
    @section('page-js-script')
        <script type="module">
            function checkActive() {
                //check if any element is active, if not details button's href is set to current url
                if($('.list-element.active-list-elem').length === 0) {
                    $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
                }
                //else if id is set properly, url is set to be classified as product.details route
                else {
                    var id = $('.list-element.active-list-elem').attr('id').split('-');
                    if(id.length > 1) {
                        id = id[1];
                        var newUrl = $(location).attr('href') + '/' + id;
                        $('.details').css('background-color','#1ca2e6').attr('href', newUrl);
                    }
                    else {
                        $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
                    }
                }
            }

            $(document).ready(function() {
                checkActive();

                $('.list-element').on('click', function () {
                    console.log('div klikniety');
                    var is_active = ($(this).hasClass('active-list-elem') ? true : false);
                    $('.list-element').removeClass('active-list-elem');
                    $(this).addClass('active-list-elem');

                    var id = $(this).attr('id').split('-')[1];
                    var comp_list_id = '#comp-list-' + id;

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
                    console.log('buton klikniety');
                    var id = $(this).attr('id').split('-')[1];
                    var comp_list_id = '#comp-list-' + id;
                    var prod_id = '#product-' + id;

                    // if(! $(prod_id).hasClass('active-list-elem')) {
                    //     $(prod_id).addClass('active-list-elem')
                    // }

                    if($(this).hasClass('rotate-180')) {
                        $(this).removeClass('rotate-180');
                        $(this).addClass('rotate-0');
                    } else {
                        $(this).removeClass('rotate-0');
                        $(this).addClass('rotate-180');
                    }

                    if($(comp_list_id).hasClass('hidden')) {
                        $(comp_list_id).removeClass('hidden');
                    } else {
                        $(comp_list_id).addClass('hidden');
                        $(comp_list_id).addClass('just-hidden');
                    }
                });
            });

        </script>
    @endsection

    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
        <a class ='block w-1/2 pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
            {{ __('Produkty') }}
        </a>
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-nav-button :href="route('product.add')">
                {{ __('Dodaj') }}
            </x-nav-button>
            <x-nav-button class="ml-3 mr-5 details bg-blue-450">
                {{ __('Szczegóły') }}
            </x-nav-button>
        </div>
    </div>
    @if(isset($products) and isset($components) and isset($prod_comp_list))
        <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    @foreach($products as $prod)
                        <x-list-element class="list-element flex-col" id="product-{{$prod->id}}">
                            <div class="w-[100%] flex justify-between items-center">
                                <div class="w-[60%]">
                                    <img class="border-2 inline-block" src="{{asset('storage/Lozko-dzieciece-DOMEK-Bialy-z-barierka.jpg') }}" width="80px">
                                    @php
                                        $name = $prod->name;
                                        if($prod->material) $name .= ' - '.$prod->material;
                                        if($prod->color) $name .= ' - '.$prod->color;
                                    @endphp
                                    <p class="inline-block">{{$name}}</p>
                                </div>
                                <div id="expbtn-{{$prod->id}}" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-8 lg:h-8 md:w-6 md:h-6 sm:w-4 sm:h-4 mr-8 md:rounded-md rounded-sm rotate-0 transition-all">
                                    <img src="{{asset('storage/expand-down.png') }}" >
                                </div>
                            </div>
                            <ul id="comp-list-{{$prod->id}}" class="w-[80%] mt-[3%] relative m-0 w-full hidden list-none overflow-hidden p-0 transition-[height] duration-200 ease-in-out text-lg"  data-te-stepper-init data-te-stepper-type="vertical">
                                @php $i = 1; @endphp
                                <h2 class="text-gray-800">Lista komponentów:</h2>
                                @foreach($prod_comp_list[$prod->id] as $comp)
                                    <li data-te-stepper-step-ref class="relative h-fit after:absolute after:left-[2.45rem] after:top-[3.6rem] after:mt-px after:h-[calc(100%-2.45rem)] after:w-px after:bg-[#e0e0e0] after:content-[''] dark:after:bg-neutral-600">
                                        <div data-te-stepper-head-ref class="w-[80%] flex cursor-pointer items-center p-6 leading-[1.3rem] no-underline after:bg-[#e0e0e0] after:content-[''] hover:bg-[#f9f9f9] focus:outline-none dark:after:bg-neutral-600 dark:hover:bg-[#3b3b3b]">
                                            <span data-te-stepper-head-icon-ref class="mr-3 flex h-[1.938rem] w-[1.938rem] items-center justify-center rounded-full bg-[#ebedef] text-lg font-medium text-[#40464f]">
                                                {{$i}}
                                            </span>
                                            <span data-te-stepper-head-text-ref class="text-gray-800 after:absolute after:flex after:text-[0.8rem] after:content-[data-content] dark:text-neutral-300">
                                                {{$comp->name}}
                                            </span>
                                        </div>
                                        <div data-te-stepper-content-ref class="transition-[height, margin-bottom, padding-top, padding-bottom] left-0 overflow-hidden pb-6 pl-[3.75rem] pr-6 duration-300 ease-in-out text-[16px] text-neutral-500 ">
                                            {{$comp->description}}
                                        </div>
                                    </li>
                                    @php $i++; @endphp
                                @endforeach
                            </ul>
                        </x-list-element>
                    @endforeach
                </div>
            </div>
        </div>
    @endif




</x-app-layout>
