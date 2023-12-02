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
                    //if products div has display block, then create route to products, else to components

                    newUrl = $(location).attr('href') + '/' + id;
                    similarUrl = $(location).attr('href').replace('produkty','dodaj-produkt') + '/' + id;
                    editUrl = $(location).attr('href').replace('produkty','edytuj-produkt') + '/' + id;

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
                let is_active = ($(this).hasClass('active-list-elem') ? true : false);
                $('.list-element').removeClass('active-list-elem');
                $(this).addClass('active-list-elem');

                let id = $(this).attr('id').split('-')[1];
                let prod_list_id = '.prod-list-' + id;

                if($(prod_list_id).hasClass('hidden')) {
                    if (is_active) {
                        if(!$(prod_list_id).hasClass('just-hidden')) {
                            $('.list-element').removeClass('active-list-elem');
                        } else {
                            $(prod_list_id).removeClass('just-hidden');
                        }
                    }
                }
                checkActive();
            });

            //on click button is rotated and component list appears
            $('.expand-btn').on('click', function () {
                let id = $(this).attr('id').split('-')[1];
                let list_id = '.prod-list-' + id;

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
        @php
            $left = "Produkty";
        @endphp
                <x-information-panel :viewName="$left">
                    {{--    routing for details set in java script above   --}}
                    <x-nav-button  class="on-select details bg-blue-450 hover:bg-blue-800">
                        {{ __('Szczegóły') }}
                    </x-nav-button>
                    @if(in_array($user->role,array('admin','manager')))
                        <x-nav-button :href="route('product.add')" class="ml-1 lg:ml-3">
                            {{ __('Dodaj') }}
                        </x-nav-button>
                        <x-nav-button class="on-select similar hover:bg-gray-700 ml-1 lg:ml-3">
                            {{ __('Dodaj Podobny') }}
                        </x-nav-button>
                        <x-nav-button class="on-select edit bg-orange-500 hover:bg-orange-800 ml-1 lg:ml-3">
                            {{ __('Edytuj') }}
                        </x-nav-button>
                        @php
                            $name = 'produkt';
                            $route = 'product.destroy';
                            $button_id = 'remove-prod-modal';
                            $id = '2';
                            $remove_elem_class = 'element-remove';
                            $remove_elem_id = 'product-remove-';
                        @endphp
                        <x-remove-modal :name="$name" :button_id="$button_id" :route="$route" :id="$id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id">
                            @foreach($products as $prod)
                                <div class="{{$remove_elem_class}} hidden" id="{{$remove_elem_id}}{{$prod->id}}">
                                    <x-list-element class="flex-col">
                                        <div class="w-full flex justify-between items-center">
                                            <div class="w-full flex justify-left items-center">
                                                <div class="border-2 inline-block w-[50px] h-[50px] md:w-[70px] md:h-[70px] lg:w-[100px] lg:h-[100px]">
                                                    @if(!empty($prod->image))
                                                        @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp
                                                        <img src="{{asset('storage/'.$path.$prod->image)}}">
                                                    @endif
                                                </div>
                                                <p class="inline-block list-element-name ml-[3%]  xl:text-lg text-md">{{$prod->name}} - {{$prod->material}}</p>
                                            </div>
                                        </div>
                                    </x-list-element>
                                </div>
                            @endforeach
                        </x-remove-modal>
                    @endif
                </x-information-panel>
                @if(isset($products))
                    <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col">
                            @php
                                $inputPlaceholder = "Wpisz nazwę, surowiec lub kolor...";
                                $xListElemProd = "product";
                            @endphp
                            <x-search-input :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElemProd"></x-search-input>

                            <div class="w-full">
                                @foreach($products as $prod)
                                    <x-list-element class="list-element-{{$xListElemProd}} list-element flex-col" id="product-{{$prod->id}}">
                                        <div class="w-full flex justify-between items-center">
                                            <div class="w-full flex justify-left items-center">
                                                <div class="border-2 inline-block w-[50px] h-[50px] md:w-[70px] md:h-[70px] lg:w-[100px] lg:h-[100px]">
                                                    @if(!empty($prod->image))
                                                        @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp
                                                        <img src="{{asset('storage/'.$path.$prod->image)}}">
                                                    @endif
                                                </div>
                                                @php
                                                    $name = $prod->name;
                                                    if($prod->material) $name .= ' - '.$prod->material;
                                                    if($prod->color) $name .= ' - '.$prod->color;
                                                @endphp
                                                <p class="inline-block list-element-name ml-[3%] xl:text-lg text-md">{{$name}}</p>
                                            </div>
                                            <div id="expbtn-{{$prod->id}}-prod" class="expand-btn inline-block bg-gray-800 w-4 h-4 lg:w-6 lg:h-6 md:w-5 md:h-5 sm:w-4 sm:h-4 mr-2 lg:mr-6 md:rounded-md rounded-sm rotate-0 transition-all">
                                                <img src="{{asset('storage/expand-down.png') }}" >
                                            </div>
                                        </div>
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
                                                            GTIN
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{is_null($prod->gtin) ? '' : $prod->gtin}}
                                                        </td>
                                                    </tr>
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                            Surowiec
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
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                            Akord
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{is_null($prod->piecework_fee) ? '' : $prod->piecework_fee.' zł'}}
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
    @endif
</x-app-layout>
