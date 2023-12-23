<x-app-layout>
    @if(isset($category) and isset($category_name) and isset($elements))
        <script type="module">

            function checkActive() {

            }

            function formatTimeSpent(timeSpent) {
                if (timeSpent < 60) {
                    let duration = "0:"
                    if(timeSpent < 10) {
                        duration += "0";
                    }
                    return duration + timeSpent.toString();
                } else {
                    let duration = Math.floor(timeSpent / 60).toString() + ":";
                    if(timeSpent % 60 < 10) {
                        duration += "0";
                    }
                    return duration + (timeSpent % 60).toString();
                }
            }

            function getPackProdDuration() {
                let prod_id = $('.pack-prod-row.active');
                if(prod_id.length > 0) {
                    prod_id = prod_id.attr('id').split('-')[3];;
                    let element = $('#pack-prod-row-'+prod_id);
                    return element.find('.pack-prod-minutes-per-pcs').text();
                }
            }

            function removeActiveProdRows() {
                $('.pack-prod-row').removeClass('bg-gray-800 text-white active').addClass('bg-gray-200 text-gray-600');
            }

            $(document).ready(function() {
                $('.list-element').on('click', function (event) {
                    var is_active = ($(this).hasClass('active-list-elem') ? true : false);
                    $('.list-element').removeClass('active-list-elem');
                    $(this).addClass('active-list-elem');
                    let category = $('#new-cycle-cat-input').val();
                    let pack_product_id = 1;
                    let id = $(this).attr('id').split('-')[1];
                    let packProdRowClicked = $(event.target).closest('.pack-prod-row').length > 0;
                    let packProdTable = $('#pack-product-table');

                    if (is_active && !packProdRowClicked) {
                        if(category == 3 && id == pack_product_id) {
                            packProdTable.addClass('hidden');
                            let activeProdRow = packProdTable.find('.active')
                            if(activeProdRow.length > 0) {
                                activeProdRow.removeClass('bg-gray-800 text-white active').addClass('bg-gray-200 text-gray-600');
                            }
                        }
                        else {
                            removeActiveProdRows();
                            if(!packProdTable.hasClass('hidden')) {
                                packProdTable.addClass('hidden');
                            }
                        }
                        $('#new-cycle-id-input').val(null);
                        $('#new-cycle-name-input').val(null).removeClass('bg-blue-150 ring-2 ring-blue-600');
                        $('.list-element').removeClass('active-list-elem');
                        $('#new-cycle-exp-duration').text('0:00');

                    } else if(!packProdRowClicked){
                        if(category == 3 && id == pack_product_id) {
                            packProdTable.removeClass('hidden');
                            $('#new-cycle-id-input').val(null);
                            $('#new-cycle-name-input').val(null).removeClass('bg-blue-150 ring-2 ring-blue-600');
                        } else {
                            removeActiveProdRows();
                            if(!packProdTable.hasClass('hidden')) {
                                packProdTable.addClass('hidden');
                            }
                            $('#new-cycle-id-input').val($(this).find('.list-element-id').val());
                            $('#new-cycle-name-input').val($(this).find('.list-element-name').text());
                            $('#new-cycle-name-input').addClass('bg-blue-150 ring-2 ring-blue-600');
                        }
                        let minutesPerPcs = $(this).find('.list-element-minute-per-pcs').val();
                        let duration = formatTimeSpent(Math.ceil($('#new-cycle-amount-input').val() * minutesPerPcs));
                        $('#new-cycle-exp-duration').text(duration);
                    }
                });

                $('.pack-prod-row').on('click', function() {
                    let isActive = $(this).hasClass('active');
                    removeActiveProdRows();
                    if(!isActive) {
                        $(this).removeClass('bg-gray-200 text-gray-600').addClass('bg-gray-800 text-white active');
                        let parentListElem = $(this).closest('.list-element')
                        let name = parentListElem.find('.list-element-name').text();
                        name += ': ' + $(this).find('.pack-prod-name').text().trim();
                        let minutesPerPcs = getPackProdDuration();
                        let duration = formatTimeSpent(Math.ceil($('#new-cycle-amount-input').val() * minutesPerPcs));
                        $('#new-cycle-exp-duration').text(duration);
                        $('#new-cycle-name-input').val(name).addClass('bg-blue-150 ring-2 ring-blue-600');
                        $('#new-cycle-id-input').val(parentListElem.find('.list-element-id').val());
                        $('#new-cycle-pack-prod-id-input').val($(this).find('.pack-prod-id').text());
                    } else {
                        $(this).removeClass('bg-gray-800 text-white active').addClass('bg-gray-200 text-gray-600');
                        $('#new-cycle-id-input').val(null);
                        $('#new-cycle-name-input').val(null).removeClass('bg-blue-150 ring-2 ring-blue-600');
                        $('#new-cycle-pack-prod-id-input').val(null);
                        $('#new-cycle-exp-duration').text('0:00');
                    }
                });

                $('#new-cycle-amount-input').change(function() {
                    let id = $('#new-cycle-id-input').val();
                    let element = $('#elem-'+id);
                    if(element.length > 0) {
                        let category = $('#new-cycle-cat-input').val();
                        let pack_product_id = 1;
                        let minutesPerPcs = 0;
                        if(category == 3 && id == pack_product_id) {
                             minutesPerPcs = getPackProdDuration();
                        } else {
                            minutesPerPcs = element.find('.list-element-minute-per-pcs').val();
                        }
                        let duration = formatTimeSpent(Math.ceil($(this).val() * minutesPerPcs));
                        $('#new-cycle-exp-duration').text(duration);
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
                $name = "Dodaj cykl";
            @endphp
            <x-information-panel :viewName="$name">
            </x-information-panel>
            <div class="w-full flex justify-center mt-4">
                <div class="w-full sm:w-4/5 md:w-[90%] lg:w-4/5 flex justify-center items-center flex-col">
                    <x-input-error :messages="$errors->all()" class="m-4" />
                    <form method="POST" action="{{ route('production.store-cycle') }}" enctype="multipart/form-data" class="w-full">
                        @csrf
                        <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-visible text-left rounded-t-xl">
                            <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2 xl:rounded-tl-xl">
                                <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[70%] xl:rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                    <input id="new-cycle-cat-input" name="category" type="number" class="hidden" value="{{$category}}">
                                    <input type="number" name="id" id="new-cycle-id-input" class="hidden">
                                    <input type="number" name="pack_prod_id" id="new-cycle-pack-prod-id-input" class="hidden">
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
                                        <input type="text" id="new-cycle-name-input" disabled
                                               class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               name="name" placeholder="Nazwa" required>
                                    </div>
                                </dd>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50 border-r">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Planowany start</dt>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div id="exp-start-time" class="relative w-full xl:mt-4"
                                         data-te-datepicker-init
                                         data-te-format="yyyy-mm-dd"
                                         data-te-input-wrapper-init>
                                        <input name="exp_start" value="{{old('exp_start')}}"
                                               class="exp-start-time p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Start"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50 xl:rounded-tr-xl">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2 xl:rounded-tr-xl">Zakładany termin</dt>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div id="exp-end-time2" class="exp-end-time relative w-full xl:mt-4"
                                        data-te-datepicker-init
                                         data-te-format="yyyy-mm-dd"
                                        data-te-input-wrapper-init>
                                        <input name="exp_end" value="{{old('exp_end')}}"
                                               class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Termin"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 xl:col-span-4 flex flex-col bg-gray-200/50 border-r">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Pracownicy</dt>
                                <div class="p-1 pl-5 flex justify-center items-center h-full">
                                    @php $unique_id = 'employees' @endphp
                                    <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')">
                                        <x-slot name="options">
                                            @if(isset($users))
                                                @foreach($users as $u)
                                                    <option value="{{$u->id}}" {{(old($unique_id) and preg_match("/(^|,)$u->id($|,)/",old($unique_id)))? 'selected' : ''}}>
                                                        {{$u->employeeNo}}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif

                                        </x-slot>
                                    </x-select-multiple>
                                </div>
                            </div>
                            <div class="col-span-1 xl:col-span-2 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2 border-r">Ilość (szt)</dt>
                                <div class="p-1 flex justify-center flex-row items-center h-full">
                                    <div class="w-full p-1 flex justify-center items-center h-full w-full">
                                        <input id="new-cycle-amount-input" type="number" min="0" value="{{old('amount')}}"
                                                  class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                  name="amount" placeholder="Ilość (szt)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-1 xl:col-span-2 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Czas [h]</dt>
                                <div class="p-1 flex justify-center flex-row items-center h-full">
                                    <div class="w-full p-1 flex flex-row justify-center items-center h-full">
                                        <div class="w-4/5 px-2 bg-blue-800 text-white rounded-md font-semibold h-[30px] xl:h-[42px] flex justify-center items-center">
                                            <div id="new-cycle-exp-duration" class="text-sm">
                                                0:00
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-4 xl:col-span-8 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Dodatkowe Uwagi</dt>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div class="p-1 flex justify-center items-center h-full w-full">
                                        @php $unique_id = 'comment' @endphp
                                        <textarea id="new-cycle-comm-input"
                                               class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                  name="{{$unique_id}}" placeholder="Dodtakowe uwagi">
                                            {{old($unique_id)}}
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
                                if($category == 2) {
                                    $input_placeholder = "Wpisz nazwę materiału lub surowiec z jakiego jest wykonany...";
                                } else if ($category == 3) {
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
                                <x-list-element id="elem-{{$el->id}}" class="list-element flex-col w-full p-3">
                                    <input type="number" class="list-element-id hidden" value="{{$el->id}}">
                                    <input type="number" class="list-element-minute-per-pcs hidden" value="{{$el->minutes_per_pcs}}">
                                    <div class="w-full flex justify-between items-center">
                                        <div class="w-full flex justify-left items-center">
                                            <p class="inline-block list-element-name ml-[3%] py-3  xl:text-lg text-md">{{$category != 3? $el->name.' - '.$el->material : $el->production_schema}}</p>
                                        </div>
                                    </div>
                                    @if(isset($products) and isset($pack_product_id ) and $category == 3 and $el->id == $pack_product_id->value )
                                        <div id="pack-product-table" class="relative overflow-x-auto sm:rounded-b-xl w-full mt-6 {{(isset($pack_prod_show) and $pack_prod_show)? '' : 'hidden'}}">
                                            <p id="" class="w-full text-sm lg:text-lg font-medium text-left text-gray-900 dark:text-white p-2">
                                                <span class="text-green-500 text-xs lg:text-sm"><em>Żeby dodać cykl produkcji dla pakowania, wybierz produkt, który będzie pakowany.</em></span>
                                            </p>
                                            <table class="w-full text-sm text-left rtl:text-right rounded-t-md shadow-md sm:rounded-t-xl text-gray-500 bg-white dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
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
                                                    <tr id="pack-prod-row-{{$prod->id}}" class="pack-prod-row bg-gray-200 font-medium text-gray-600 border border-slate-300 ">
                                                        <td class="pack-prod-id hidden">{{$prod->id}}</td>
                                                        <td class="pack-prod-minutes-per-pcs hidden">{{$prod->minutes_per_pcs}}</td>
                                                        <td class="pack-prod-name px-6 py-4 whitespace-nowrap sm:rounded-md">
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
                                            <div class="px-3 bg-white">
                                                {{$products->appends(['dodaj-cykl' => $elements->currentPage()])->links()}}
                                            </div>
                                        </div>
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
