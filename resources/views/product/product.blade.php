<x-app-layout>
    @section('page-js-script')
        <script type="module">
            function checkActive() {
                if($('.list-element.active').length === 0) {
                    $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
                }
                else {
                    var id = $('.list-element.active').attr('id').split('_');
                    if(id.length > 1) {
                        id = id[1];
                        var newUrl = $(location).attr('href') + '/' + id;
                        $('.details').css('background-color','rgb(31 41 55)').attr('href', newUrl);
                    }
                    else {
                        $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
                    }
                }
            }

            $(document).ready(function() {
                checkActive();

                $('.list-element').on('click', function (){
                    if($(this).hasClass('active')) {
                        $('.list-element').removeClass('active');
                    }
                    else {
                        $('.list-element').removeClass('active');
                        $(this).addClass('active');
                    }
                    checkActive();

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
            <x-nav-button class="ml-3 mr-5 details">
                {{ __('Szczegóły') }}
            </x-nav-button>
        </div>
    </div>
    @if(isset($products) and isset($components))
        <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    @foreach($products as $prod)
{{--                        <div class="space-x-8  my-3 flex bg-gray-50 border-gray-300  justify-between">--}}
{{--                            <a class ='block pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>--}}
{{--                                <img class="border-2 inline-block" src="{{asset('storage/Lozko-dzieciece-DOMEK-Bialy-z-barierka.jpg') }}" width="80px">--}}
{{--                                {{$prod->name.' - '.$prod->material.' - '.$prod->color}}--}}
{{--                                <div class="py-10 pr-7 flex justify-center align-middle">--}}
{{--                                    <x-nav-button class="border-0" :href="route('product.details', $prod->id)">--}}
{{--                                        {{ __('Szczegóły produktu') }}--}}
{{--                                    </x-nav-button>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </div>--}}
                        <x-list-element class="list-element" id="product_{{$prod->id}}">
                            <div class="w-[60%]">
                                <img class="border-2 inline-block" src="{{asset('storage/Lozko-dzieciece-DOMEK-Bialy-z-barierka.jpg') }}" width="80px">
                                <p class="inline-block">{{$prod->name.' - '.$prod->material.' - '.$prod->color}}</p>
                            </div>
                        </x-list-element>
                    @endforeach
                </div>
            </div>
        </div>
    @endif




</x-app-layout>
