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
            $name = "Produkcja";
        @endphp
        <x-information-panel :viewName="$name">
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
{{--                @php--}}
{{--                    $name = 'produkt';--}}
{{--                    $route = 'product.destroy';--}}
{{--                    $button_id = 'remove-prod-modal';--}}
{{--                    $id = '2';--}}
{{--                    $remove_elem_class = 'element-remove';--}}
{{--                    $remove_elem_id = 'product-remove-';--}}
{{--                @endphp--}}
{{--                <x-remove-modal :name="$name" :button_id="$button_id" :route="$route" :id="$id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id">--}}
{{--                    @foreach($products as $prod)--}}
{{--                        <div class="{{$remove_elem_class}} hidden" id="{{$remove_elem_id}}{{$prod->id}}">--}}
{{--                            <x-list-element class="flex-col">--}}
{{--                                <div class="w-full flex justify-between items-center">--}}
{{--                                    <div class="w-full flex justify-left items-center">--}}
{{--                                        <div class="border-2 inline-block w-[50px] h-[50px] md:w-[70px] md:h-[70px] lg:w-[100px] lg:h-[100px]">--}}
{{--                                            @if(!empty($prod->image))--}}
{{--                                                @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp--}}
{{--                                                <img src="{{asset('storage/'.$path.$prod->image)}}">--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                        <p class="inline-block list-element-name ml-[3%]  xl:text-lg text-md">{{$prod->name}} - {{$prod->material}}</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </x-list-element>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </x-remove-modal>--}}
            @endif
        </x-information-panel>
        @if(isset($parent_cycles) and isset($child_cycles))

        @endif
    @endif
</x-app-layout>
