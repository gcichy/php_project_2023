<x-app-layout>
    <script type="module">
        function getRowData(id) {
            let row = $('#row-'+id).find('.col-value');
            let modalDetailsTable = $('#modal-work-table');
            let productivityStyle = getProductivityStyle(id);
            let defectStyle = getDefectStyle(id);
            row.each(function () {
                // Get the class attribute of the current element
                let classNames = $(this).attr('class').split(' ');
                if(classNames.length >= 2) {
                    let colName = classNames[1];
                    let elem = modalDetailsTable.find('.col-value.'+colName);
                    if(elem.length === 1) {
                        if(colName === 'component-image' || colName === 'product-image') {
                            elem.attr('src',$(this).attr('src'));
                        }
                        else {
                            modalDefectStyles(colName, elem, defectStyle);
                            modalProductivityStyles(colName, elem, productivityStyle);
                            elem.text($(this).text().trim());
                        }

                    }
                }
            });
        }
        function getProductivityStyle(id) {
            let productivity = $('#row-'+id).find('.col-value.productivity');
            let productivityStyle = '';
            if(productivity.length === 1 && parseInt(productivity.text().trim()) > 100 ) {
                productivityStyle = 'text-green-450';
            }
            else if(productivity.length === 1 && parseInt(productivity.text().trim()) < 80) {
                productivityStyle = 'text-red-500';
            }
            return productivityStyle;
        }

        function modalProductivityStyles(colName, elem, style) {
            let productivityList = ['productivity','amount','exp-amount-per-time-spent']
            if(productivityList.includes(colName)) {
                elem.addClass(style);
            }
        }
        function getDefectStyle(id) {
            let defectPercent = $('#row-'+id).find('.col-value.defect-percent');
            let defectStyle = '';
            if(defectPercent.length === 1 && parseInt(defectPercent.text().trim()) > 10) {
                defectStyle = 'text-red-500';
            }
            return defectStyle;
        }
        function modalDefectStyles(colName, elem, style) {
            let defectList = ['defect-amount','defect-percent']
            if(defectList.includes(colName)) {
                elem.addClass(style);
            }
        }

        $(document).ready(function() {
            $('.next-modal').on('click', function (){
                $('.next-modal').removeClass('selected bg-blue-800 ring-2');
                $(this).addClass('bg-blue-800 ring-2');
                $(this).delay(300).queue(function() {
                    // $("#close-modal-button-1").trigger( "click" );
                    // $("#modal-background-2, #modal-2").removeClass("hidden");
                    // $(this).removeClass('bg-blue-800 ring-2');
                    // $('#new-cycle-cat').text($(this).text());
                    let category = 3;
                    if($(this).attr('id') === 'category-1') {
                        category = 1;
                    } else if($(this).attr('id') === 'category-2') {
                        category = 2;
                    }
                    $('#new-category').val(category);
                    $('#add-cycle-sumbit').trigger('click');
                    $(this).dequeue();
                });
            });

            $('#filter-btn').on('click',function() {
                let filterGrid = $('#filters');
                if(filterGrid.hasClass('hidden')) {
                    filterGrid.removeClass('hidden');
                } else {
                    filterGrid.addClass('hidden');
                }
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

            $("#close-modal-work-button").on('click', function () {
                $("#modal-work-background").addClass("hidden");
                $('#work-modal-prod-img').attr('src',null);
                $('#work-modal-comp-img').attr('src',null);
                $('dd.col-value').removeClass('text-green-450 text-red-500');
            });

            $(".open-modal").on('click', function () {
                $("#modal-work-background").removeClass("hidden");
                let idArr = $(this).attr('id').split('-');
                getRowData(idArr[idArr.length-1]);
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
        @if(session('status_err'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{session('status_err')}}
                </p>
            </div>
        @endif
        @php
            $name = "Praca";
        @endphp
        <x-information-panel :viewName="$name">
            <x-nav-button  id="add-work-btn" class="on-select details bg-green-450 hover:bg-green-700 xl:mr-4">
                {{ __('Dodaj') }}
            </x-nav-button>
            <x-nav-button  id="filter-btn" class="on-select details bg-yellow-300 hover:bg-yellow-600 xl:mr-4">
                {{ __('Filtry') }}
            </x-nav-button>
        </x-information-panel>
        @if(isset($works) and isset($users) and isset($filt_items))
            <form method="GET" action="{{ route('work.index') }}" enctype="multipart/form-data">
                <div class="w-full mt-4 flex justify-center">
                    <div id="filters" class="flex flex-row justify-start w-[90%] border-2 rounded-lg hidden">
                        <dl class="grid grid-cols-3 bg-white text-left rounded-l-lg w-4/5">
                            <div class="col-span-1 flex flex-col justify-center border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Kategoria
                                </a>
                                <div class="p-1 h-full">
                                    @php $unique_id = 'cycle_category' @endphp
                                    <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Kategoria')">
                                        <x-slot name="options">
                                            <option value="1" {{(array_key_exists($unique_id,$filt_items) and in_array('1', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Produkty
                                            </option>
                                            <option value="2" {{(array_key_exists($unique_id,$filt_items) and in_array('2', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Materiały
                                            </option>
                                            <option value="3" {{(array_key_exists($unique_id,$filt_items) and in_array('3', $filt_items[$unique_id]))? 'selected' : ''}}>
                                                Zadania
                                            </option>
                                        </x-slot>
                                    </x-select-multiple>
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-start border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Start pracy od
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div id="start-time-work" class="relative w-full"
                                         data-te-datepicker-init
                                         data-te-format="yyyy-mm-dd"
                                         data-te-input-wrapper-init>
                                        <input name="work_start_from" value="{{isset($filt_start_time)? $filt_start_time : null}}"
                                               class="exp-start-time p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Start pracy"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-center border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Start pracy do
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    <div id="end-time-work" class="exp-end-time relative w-full"
                                         data-te-datepicker-init
                                         data-te-format="yyyy-mm-dd"
                                         data-te-input-wrapper-init>
                                        <input name="work_start_to" value="{{isset($filt_end_time)? $filt_end_time : null}}"
                                               class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                               placeholder="Start pracy"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 flex flex-col justify-center h-full border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Pracownicy
                                </a>
                                <div class="p-1">
                                    @php $unique_id = 'employees' @endphp
                                    <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')">
                                        <x-slot name="options">
                                            @foreach($users as $u)
                                                <option value="{{$u->id}}" {{(array_key_exists($unique_id,$filt_items) and in_array($u->id, $filt_items[$unique_id]))? 'selected' : ''}}>
                                                    {{$u->employeeNo}}
                                                </option>
                                            @endforeach
                                        </x-slot>
                                    </x-select-multiple>
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-start">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Produkt
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    @php $unique_id = 'product_name' @endphp
                                    <input type="search" id="{{$unique_id}}" value="{{array_key_exists($unique_id,$filt_items)? $filt_items[$unique_id] : ''}}"
                                           class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           name="{{$unique_id}}" placeholder="Nazwa">
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-start  border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Materiał
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    @php $unique_id = 'component_name' @endphp
                                    <input type="search" id="{{$unique_id}}" value="{{array_key_exists($unique_id,$filt_items)? $filt_items[$unique_id] : ''}}"
                                           class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           name="{{$unique_id}}" placeholder="Nazwa">
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-start  border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Zadanie
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    @php $unique_id = 'production_schema' @endphp
                                    <input type="search" id="{{$unique_id}}" value="{{array_key_exists($unique_id,$filt_items)? $filt_items[$unique_id] : ''}}"
                                           class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           name="{{$unique_id}}" placeholder="Nazwa">
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col justify-start  border-r-2">
                                <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                    Podzadanie
                                </a>
                                <div class="p-1 flex justify-center items-center h-full">
                                    @php $unique_id = 'task_name' @endphp
                                    <input type="search" id="{{$unique_id}}" value="{{array_key_exists($unique_id,$filt_items)? $filt_items[$unique_id] : ''}}"
                                           class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           name="{{$unique_id}}" placeholder="Nazwa">
                                </div>
                            </div>
                            @if(isset($order))
                                <div class="col-span-3 flex flex-col justify-start  border-r-2">
                                    <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                        Sortuj według
                                    </a>
                                    <div class="p-1 flex justify-center items-center h-full">
                                        @php $unique_id = 'order' @endphp
                                        <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Sortuj według')">
                                            <x-slot name="options">
                                                @if(isset($order_items))
                                                    @foreach($order_items as $item)
                                                        @php
                                                            $item_exploded = explode(';', $item);
                                                            $value = array_key_exists($item_exploded[0], $order)? $order[$item_exploded[0]] : '';
                                                        @endphp
                                                        @if(!empty($value))
                                                            <option value="{{$item}}" selected>
                                                                {{$value}} {{$item_exploded[1] == 'asc'? '(rosnąco)' : '(malejąco)'}}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @foreach($order as $key => $val)
                                                    @if(isset($order_items) and is_array($order_items))
                                                        @if(!in_array($key.';asc',$order_items))
                                                            <option value="{{$key}};asc">
                                                                {{$val}} (rosnąco)
                                                            </option>
                                                        @endif
                                                        @if(!in_array($key.';desc',$order_items))
                                                            <option value="{{$key}};desc">
                                                                {{$val}} (malejąco)
                                                            </option>
                                                        @endif
                                                    @else
                                                            <option value="{{$key}};asc">
                                                                {{$val}} (rosnąco)
                                                            </option>
                                                            <option value="{{$key}};desc">
                                                                {{$val}} (malejąco)
                                                            </option>
                                                    @endif
{{--                                                        {{(isset($order_items) and in_array($key.';desc', $order_items))? 'selected' : ''}}--}}
                                                @endforeach
                                            </x-slot>
                                        </x-select-multiple>
                                    </div>
                                </div>
                            @endif
                        </dl>
                        <div class="flex flex-col justify-center items-center bg-white xl:border-r-2 rounded-r-lg w-1/5">
                            <button type="submit" class ='w-[60%] xl:w-[40%] text-sm md:text-lg bg-gray-800 hover:bg-gray-600 font-medium text-center text-white rounded-lg'>
                                Filtruj
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @if(session('edit_err'))
                <div class="flex justify-center items-center flex-col">
                    @foreach(session('edit_err') as $err)
                        <p class="w-full text-xs text-center text-red-700 space-y-1">
                            {{$err}}
                        </p>
                    @endforeach
                </div>
            @endif
            @if(session('delete_err'))
                <div class="flex justify-center items-center flex-col">
                    @foreach(session('edit_err') as $err)
                        <p class="w-full text-xs text-center text-red-700 space-y-1">
                            {{$err}}
                        </p>
                    @endforeach
                </div>
            @endif
            <div class="w-full flex justify-center items-center my-4">
                <div class="w-[95%]">
                    <div class="shadow-md rounded-xl mb-4 border">
                        <div class="relative overflow-x-scroll">
                            <table class="w-full text-sm bg-gray-100 rounded-xl text-left rtl:text-right pb-2 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300 ">
                                @php
                                    $storage_path_components = isset($storage_path_components)? $storage_path_components : null;
                                    $storage_path_products = isset($storage_path_products)? $storage_path_products : null;
                                @endphp
                                <x-work-table :work_array="$works->all()"
                                              :storage_path_components="$storage_path_components"
                                              :storage_path_products="$storage_path_products">
                                </x-work-table>
                            </table>
                        </div>
                        <div class="w-full p-2 bg-gray-50 rounded-b-xl">
                            {{ $works->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
