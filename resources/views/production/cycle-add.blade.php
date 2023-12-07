<x-app-layout>
    @if(isset($category) and isset($category_name) and isset($elements) and isset($pack_product_id))
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

            function addCycleStyles() {
                let cycles = $('.cycle');
                cycles.each(function() {
                    let styles = $(this).find('.cycle_styles').text();
                    styles = styles.split(';');
                    if(styles.length === 2) {
                        let cycleClasses = '';
                        let cycleTagBg = '';
                        let cycleTagText = '';
                        let status = parseInt(styles[0]);

                        if(status === 0) {
                            //cycleClasses = 'ring-green-450 ring-4 ring-offset-4';
                            cycleTagBg = 'bg-green-450';
                            cycleTagText = 'Zakończony';
                        } else if(status === 3) {
                            //cycleClasses = 'ring-red-500 ring-4 ring-offset-4';
                            cycleTagBg = 'bg-red-500';
                            cycleTagText = 'Po terminie';
                        } else if(status === 1) {
                            //cycleClasses = 'ring-blue-450 ring-4 ring-offset-4';
                            cycleTagBg = 'bg-blue-450';
                            cycleTagText = 'Aktywny';
                        } else if(status === 2) {
                            //cycleClasses = 'ring-yellow-300 ring-4 ring-offset-4';
                            cycleTagBg = 'bg-yellow-300';
                            cycleTagText = 'Nierozpoczęty';
                        }
                        //$(this).addClass(cycleClasses);
                        let cycleTag = $(this).find('.cycle-tag');
                        if(cycleTag.length === 1) {
                            cycleTag.addClass(cycleTagBg).text(cycleTagText);
                        }
                        let progress = $(this).find('.progress');
                        if(progress.length === 1) {
                            let width = parseInt(styles[1]);
                            if(width === '100') {
                                $(progress).addClass('rounded-lg');
                            } else {
                                $(progress).addClass('rounded-l-lg');
                            }
                            if(width < 5) {
                                width = 'w-0';
                            } else if(width < 20) {
                                width = 'w-1/6';
                            } else if(width < 30) {
                                width = 'w-1/4';
                            } else if(width < 40) {
                                width = 'w-1/3';
                            } else if(width < 60) {
                                width = 'w-1/2';
                            } else if(width < 70) {
                                width = 'w-2/3';
                            } else if(width < 85) {
                                width = 'w-3/4';
                            } else if(width < 100) {
                                width = 'w-5/6';
                            } else {
                                width = 'w-full';
                            }
                            $(progress).addClass(width);
                        }
                    }


                });
            }

            $(document).ready(function() {
                addCycleStyles();



                $('.list-element').on('click', function () {
                    var is_active = ($(this).hasClass('active-list-elem') ? true : false);
                    $('.list-element').removeClass('active-list-elem');
                    $(this).addClass('active-list-elem');
                    let category = 2;
                    let pack_product_id = 1;
                    let id = $(this).attr('id').split('-')[1];

                    if (is_active) {
                        console.log(category === 2 && id === pack_product_id);
                        if(category === 2 && id === pack_product_id) {
                            $('#pack-product-table').addClass('hidden');
                        }
                        $('.list-element').removeClass('active-list-elem');
                        $('#new-cycle-name-input').val(null).removeClass('bg-blue-150 ring-2 ring-blue-600');
                    } else {
                        if(category === 2 && id === 1) {
                            $('#pack-product-table').removeClass('hidden');
                        }
                        $('#new-cycle-name-input').val($(this).find('.list-element-name').text());
                        $('#new-cycle-name-input').addClass('bg-blue-150 ring-2 ring-blue-600');
                    }
                    checkActive();
                });

                $('.cycle-details').on('click',function (){
                    let id = $(this).attr('id');
                    id = id.split('-');
                    id = id[id.length - 1];

                    let cycle = $('#cycle-'+id);
                    if(cycle.length === 1) {
                        let additionalInfo = cycle.find('.additional-info');
                        if(additionalInfo.length > 0 && additionalInfo.hasClass('hidden')) {
                            additionalInfo.removeClass('hidden');
                        } else {
                            additionalInfo.addClass('hidden');
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
            @if(isset($status_err))
                <div class="flex justify-center items-center">
                    <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                        {{$status_err}}
                    </p>
                </div>
            @endif
            @php
                $name = "Dodaj cykl";
            @endphp
            <x-information-panel :viewName="$name">
            </x-information-panel>
            <div class="w-full flex justify-center">
                <div class="w-full sm:w-4/5 md:w-[90%] lg:w-4/5 flex justify-center items-center flex-col">
                    <form method="POST" action="{{ route('production.store-cycle') }}" enctype="multipart/form-data" class="w-full">
                        @csrf
                        <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-visible text-left rounded-t-xl">
                            <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                                <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[70%] rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                    <input id="new-cycle-cat-input" name="category" type="number" class="hidden" value="{{$category}}">
                                    <div id="new-cycle-cat" class="p-1">
                                        {{$category_name}}
                                    </div>
                                    <div class="text-xs lg:text-sm flex justify-center items-center">
                                        <div class="p-1 mx-2 text-white bg-blue-800 rounded-md">
                                            Nowy
                                        </div>
                                    </div>
                                </dt>
                                <dd class=" text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4">
                                    <div class="p-1 flex justify-center items-center h-full">
                                        @php $unique_id = 'name' @endphp
                                        <input type="search" id="new-cycle-name-input" value="" disabled
                                               class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               name="{{$unique_id}}" placeholder="Nazwa" required>
                                    </div>
                                </dd>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50 border-r">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Planowany start</dt>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div
                                        id="exp-end-time-start"
                                        class="relative w-full xl:mt-4"
                                        data-te-input-wrapper-init
                                        data-te-format="yyyy-mm-dd">
                                        <input type="text" name="exp_end_start"
                                               class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Start"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Zakładany termin</dt>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div
                                        id="exp-end-time-end"
                                        class="relative w-full xl:mt-4"
                                        data-te-input-wrapper-init
                                        data-te-format="yyyy-mm-dd">
                                        <input type="text" name="exp_end_end"
                                               class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Termin"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Pracownicy</dt>
                                <div class="p-1 pl-5 flex justify-center items-center h-full">
                                    @php $unique_id = 'employees' @endphp
                                    <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')">
                                        <x-slot name="options">
                                            @if(isset($users))
                                                @foreach($users as $u)
                                                    <option value="{{$u->id}}">{{$u->employeeNo}}</option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif

                                        </x-slot>
                                    </x-select-multiple>
                                </div>
                            </div>
                            <div class="col-span-4 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Dodatkowe Uwagi</dt>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div class="p-1 flex justify-center items-center h-full w-full">
                                        @php $unique_id = 'comment' @endphp
                                        <textarea id="new-cycle-comm-input"
                                               class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                  name="{{$unique_id}}" placeholder="Dodtakowe uwagi">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </dl>
                        <button
                            class="inline-block rounded-b-lg px-6 py-2 md:py-4 text-xs font-medium uppercase w-full text-md md:text-lg xl:text-xl bg-blue-800 hover:bg-blue-950 leading-normal text-white focus:ring-4 focus:outline-none focus:ring-blue-300 shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]"
                            type="submit"
                            data-te-ripple-init
                            data-te-ripple-color="light">
                            {{ __('Dodaj') }}
                        </button>
                    </form>
                    <div class="w-full flex justify-center items-center flex-col mt-10 bg-white rounded-xl">
                        <div class="w-full rounded-t-xl bg-gray-800 text-white text-sm lg:text-lg p-4 font-semibold mb-4">
                            Wybierz {{$category_name}}
                        </div>
                        <div class="w-4/5 flex flex-col justify-center items-center">
                            @php
                                if($category == 1) {
                                    $input_placeholder = "Wpisz nazwę materiału lub surowiec z jakiego jest wykonany...";
                                } else if ($category == 2) {
                                    $input_placeholder = "Wpisz nazwę zadania...";
                                } else {
                                    $input_placeholder = "Wpisz nazwę produktu lub surowiec z jakiego jest wykonany...";
                                }

                                $elem = "elem";
                                $route = route('production.add-cycle-filter',['category' => $category]);
                                $input_value = isset($filter_elem)? $filter_elem : '';
                            @endphp
                            <x-filter-input class="mb-3" :placeholder="$input_placeholder" :value="$input_value" :element_id="$elem" :route="$route"></x-filter-input>
                            @foreach($elements as $el)
                            <x-list-element id="elem-{{$el->id}}" class="list-element flex-col w-full px-3">
                                <div class="w-full flex justify-between items-center">
                                    <div class="w-full flex justify-left items-center">
                                        <div class="border-2 inline-block w-[50px] h-[50px] lg:w-[70px] lg:h-[70px]">
                                            @if($category != 2 and !empty($el->image))
                                                @php $path = (isset($storage_path_products) and $category == 0) ? $storage_path_products.'/' : ((isset($storage_path_components) and $category == 1)? $storage_path_components : ''); @endphp
                                                <img src="{{asset('storage/'.$path.$el->image)}}">
                                            @endif
                                        </div>
                                        <p class="inline-block list-element-name ml-[3%]  xl:text-lg text-md">{{$category != 2? $el->name.' - '.$el->material : $el->production_schema}}</p>
                                    </div>
                                </div>
                                @if(isset($products) and isset($pack_product_id ) and $category == 2 and $el->id == $pack_product_id->value )
                                    <div id="pack-product-table" class="relative overflow-x-auto shadow-md sm:rounded-xl w-full mt-6 hidden">
                                        <table class="w-full text-sm text-left rtl:text-right sm:rounded-xl text-gray-500 bg-white dark:text-gray-400 border-separate border-spacing-1 border border-slate-300">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 sm:rounded-md">
                                                    Nazwa
                                                </th>
                                                <th scope="col" class="px-6 py-3 sm:rounded-md">
                                                    Zdjęcie
                                                </th>
                                                <th scope="col" class="px-6 py-3 sm:rounded-md">
                                                    Materiał
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($products as $prod)
                                                <tr class="bg-gray-200 font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-slate-300 ">
                                                    <td class="px-6 py-4 whitespace-nowrap sm:rounded-md">
                                                        {{$prod->name}}
                                                    </td>
                                                    <td class="p-1 sm:rounded-md">
                                                        @if(!is_null($prod->image))
                                                            <div class="flex justify-center">
                                                                <div class="max-w-[100px]">
                                                                    @php $path = isset($storage_path_products)? $storage_path_products.'/' : 'products/'; @endphp
                                                                    <img src="{{asset('storage/'.$path.$prod->image)}}" alt="">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-center sm:rounded-md">
                                                        {{$prod->material}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                @endif
                            </x-list-element>
                            @endforeach
                        </div>
                        <div class="w-4/5">
                            {{ $elements->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

</x-app-layout>
