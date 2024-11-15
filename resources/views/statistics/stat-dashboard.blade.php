<x-app-layout>
    @if(isset($status_err))
        <div class="flex justify-center items-center">
            <p class="w-full !text-md lg:text-xl font-medium text-center px-6 pt-6  text-red-700 space-y-1">
                {{$status_err}}
            </p>
        </div>
    @endif
    <form method="GET" action="{{ route('stastistics.index') }}" enctype="multipart/form-data">
        @if(isset($chart_data_1) and isset($users))
            <script type="module">
                // Pass PHP data to JavaScript
                let chartData = @json($chart_data_1);
                chartData = JSON.parse(chartData);

                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    let data = new google.visualization.DataTable();
                    data.addColumn('string', 'Dzień pracy');
                    data.addColumn('number', 'Średnia produktywność');
                    data.addColumn('number', 'Sumaryczny czas pracy');

                    for (let i = 0; i < chartData.length; i++) {
                        let entry = chartData[i];
                        data.addRow([entry.work_date,
                            parseFloat(entry.weighted_productivity_average),
                            parseFloat(entry.work_duration)
                        ]);
                    }
                    //var data = google.visualization.arrayToDataTable(chartData);

                    var options = {
                        curveType: 'function',
                        series: {
                            0: { type: 'line', targetAxisIndex: 0, color: 'rgb(48, 188, 252)' }, // Line chart
                            1: { type: 'bars', targetAxisIndex: 1, color: 'rgb(242, 59, 22)' }
                        },
                        hAxis: {
                            title: 'Dzień pracy',
                            titleTextStyle: {
                                fontSize: 20,
                                fontName: 'Arial',
                                italic: false
                            },
                            slantedText: true,
                            slantedTextAngle: 45
                        },
                        vAxes: {
                            0: {
                                title: 'Średnia produktywność (%)',
                                titleTextStyle: {
                                    fontSize: 20,
                                    fontName: 'Arial',
                                    italic: false
                                },
                                viewWindow: { min: 0 },
                            },
                            1: {
                                title: 'Czas pracy (h)',
                                titleTextStyle: {
                                    fontSize: 20,
                                    fontName: 'Arial',
                                    italic: false
                                },
                                minValue: 0,
                                gridlines: { count: 0 }
                            } // Set the minimum value for the bar chart axis
                        },
                        legend: { position: 'bottom' },
                        chartArea: {
                            top: '5%',
                            height: '67%'  // Adjust the width as needed
                        }
                        // vAxis: {
                        //     title: 'Średnia produktywność',
                        //     titleTextStyle: {
                        //         fontSize: 20,
                        //         fontName: 'Arial',
                        //         italic: false
                        //     },
                        //     viewWindow: {
                        //         min: 0  // set the minimum value to 0
                        //     }
                        // }
                    };

                    var chart = new google.visualization.LineChart(document.getElementById('chart-div'));
                    chart.draw(data, options);
                }
            </script>
            <div class="mt-2 flex flex-col xl:flex-row border-gray-300 xl:bg-white justify-between items-center z-[50]">
                <a class ='block w-full xl:w-1/3 py-4 pl-3 pr-4 bg-white border-blue-450 border-l-4 lg:border-l-8 text-md md:text-lg lg:text-2xl text-left font-medium text-gray-800  transition duration-150 ease-in-out'>
                    {{isset($chart_title_1)? $chart_title_1 :'Praca firmy'}}
                </a>
            </div>
            <div class="w-full my-4 flex justify-center">
                <div id="filters" class="flex flex-row justify-start w-[80%] border-2 rounded-lg">
                    <dl class="grid grid-cols-3 bg-white text-left rounded-l-lg w-4/5">
                        <div class="col-span-1 flex flex-col justify-center h-full">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white rounded-tl-lg'>
                                Pracownicy
                            </a>
                            <div class="p-1">
                                @php $unique_id = 'employees' @endphp
                                <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')">
                                    <x-slot name="options">
                                        @foreach($users as $u)
                                            <option value="{{$u->id}}" {{(isset($filt_users_1) and is_array($filt_users_1) and in_array($u->id, $filt_users_1))? 'selected' : ''}}>
                                                {{$u->employeeNo}}
                                            </option>
                                        @endforeach
                                    </x-slot>
                                </x-select-multiple>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-start">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Praca od
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="start-date-1" class="relative w-full">
                                    <input type="date" name="start_date_1" value="{{isset($filt_start_date_1)? $filt_start_date_1 : null}}"
                                           class=" p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           placeholder="Start" min="2023-01-01"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-center border-r-2">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Praca do
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="end-date-1" class=" relative w-full">
                                    <input name="end_date_1" type="date" value="{{isset($filt_end_date_1)? $filt_end_date_1 : null}}"
                                           class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           placeholder="Koniec" min="2023-01-01"/>
                                </div>
                            </div>
                        </div>
                    </dl>
                    <div class="flex flex-col justify-center items-center bg-white xl:border-r-2 rounded-r-lg w-1/5">
                        <button type="submit" class ='w-[60%] xl:w-[40%] text-sm md:text-lg bg-gray-800 hover:bg-gray-600 font-medium text-center text-white rounded-lg'>
                            Filtruj
                        </button>
                    </div>
                </div>
            </div>
            <div id="chart-div" class="w-full h-[500px]"></div>
        @endif
        @if(isset($chart_data_2))
            <script type="module">
                $(document).ready(function() {
                    $('#category-2').change(function () {
                        // Get the selected option value
                        let currentInput = $('#category-2-'+$(this).val().toString());
                        let inputs = $('.category-input');
                        inputs.addClass('hidden');
                        if(currentInput.length > 0) {
                            currentInput.removeClass('hidden')
                        }
                    });
                });

                // Pass PHP data to JavaScript
                let chartData = @json($chart_data_2);
                chartData = JSON.parse(chartData);
                console.log(chartData);
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    let data = new google.visualization.DataTable();
                    data.addColumn('string', 'Tydzień pracy');
                    data.addColumn('number', 'Średnia produktywność');
                    data.addColumn('number', 'Wyprodukowana ilość');

                    for (let i = 0; i < chartData.length; i++) {
                        let entry = chartData[i];
                        data.addRow([entry.work_date,
                            parseFloat(entry.weighted_productivity_average),
                            parseFloat(entry.amount)
                        ]);
                    }
                    //var data = google.visualization.arrayToDataTable(chartData);

                    var options = {
                        curveType: 'function',
                        series: {
                            0: { type: 'bars', targetAxisIndex: 0, color: 'rgb(48, 188, 252)' }, // Line chart
                            1: { type: 'bars', targetAxisIndex: 1, color: 'rgb(52, 235, 76)' }
                        },
                        hAxis: {
                            title: 'Tydzień pracy',
                            titleTextStyle: {
                                fontSize: 20,
                                fontName: 'Arial',
                                italic: false
                            },
                            slantedText: true,
                            slantedTextAngle: 45
                        },
                        vAxes: {
                            0: {
                                title: 'Średnia produktywność (%)',
                                titleTextStyle: {
                                    fontSize: 20,
                                    fontName: 'Arial',
                                    italic: false
                                },
                                viewWindow: { min: 0 },
                            },
                            1: {
                                title: 'Wyprodukowana Ilość (szt)',
                                titleTextStyle: {
                                    fontSize: 20,
                                    fontName: 'Arial',
                                    italic: false
                                },
                                minValue: 0,
                                gridlines: { count: 0 }
                            } // Set the minimum value for the bar chart axis
                        },
                        legend: { position: 'bottom' },
                        chartArea: {
                            top: '5%',
                            height: '67%'  // Adjust the width as needed
                        }
                        // vAxis: {
                        //     title: 'Średnia produktywność',
                        //     titleTextStyle: {
                        //         fontSize: 20,
                        //         fontName: 'Arial',
                        //         italic: false
                        //     },
                        //     viewWindow: {
                        //         min: 0  // set the minimum value to 0
                        //     }
                        // }
                    };

                    var chart = new google.visualization.LineChart(document.getElementById('chart-div-2'));
                    chart.draw(data, options);
                }
            </script>

            <div class="mt-5 w-full flex flex-col xl:flex-row border-gray-300 xl:bg-white justify-between items-center z-[50]">
                <a class ='block w-full py-4 pl-3 pr-4 bg-white border-blue-450 border-l-4 lg:border-l-8 text-md md:text-lg lg:text-2xl text-left font-medium text-gray-800  transition duration-150 ease-in-out'>
                    {{isset($chart_title_2)? $chart_title_2 :'Praca firmy'}}
                </a>
            </div>
            <div class="w-full my-4 flex justify-center">
                <div id="filters-2" class="flex flex-row justify-start w-[80%] border-2 rounded-lg">
                    <dl class="grid grid-cols-2 bg-white text-left rounded-l-lg w-4/5">
                        <div class="col-span-1 flex flex-col justify-start">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white rounded-tl-lg'>
                                Praca od
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="start-date-2" class="relative w-full">
                                    <input type="date" name="start_date_2" value="{{isset($filt_start_date_2)? $filt_start_date_2 : null}}"
                                           class=" p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           placeholder="Start" min="2023-01-01"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-center border-r-2">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Praca do
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="end-date-2" class=" relative w-full">
                                    <input name="end_date_2" type="date" value="{{isset($filt_end_date_2)? $filt_end_date_2 : null}}"
                                           class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           placeholder="Koniec" min="2023-01-01"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-start">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Rodzaj elementu
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <select id="category-2"
                                        name="category_2"
                                        class=" min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                    <option value="1" {{(isset($filt_category_2) and $filt_category_2 == 1)? 'selected' : ''}}>
                                        Produkt
                                    </option>
                                    <option value="2" {{(isset($filt_category_2) and $filt_category_2 == 2)? 'selected' : ''}}>
                                        Materiał
                                    </option>
                                    <option value="3" {{(isset($filt_category_2) and $filt_category_2 == 3)? 'selected' : ''}}>
                                        Zadanie
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-center border-r-2">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Nazwa elementu
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <select id="category-2-1"
                                        name="category_2_1"
                                        class="category-input min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded
                                        {{(isset($filt_category_2) and $filt_category_2 == 1)? '' : 'hidden'}}">
                                    @if(isset($products))
                                        @foreach($products as $prod)
                                            <option value="{{$prod->id}}" {{(isset($filt_element_2) and $filt_element_2 == $prod->id and isset($filt_category_2) and $filt_category_2 == 1)? 'selected' : ''}}>
                                                {{$prod->name}}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value=""></option>
                                    @endif
                                </select>
                                <select id="category-2-2"
                                        name="category_2_2"
                                        class="category-input min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded
                                        {{(isset($filt_category_2) and $filt_category_2 == 2)? '' : 'hidden'}}">
                                    @if(isset($components))
                                        @foreach($components as $comp)
                                            <option value="{{$comp->id}}" {{(isset($filt_element_2) and $filt_element_2 == $comp->id and isset($filt_category_2) and $filt_category_2 == 2)? 'selected' : ''}}>
                                                {{$comp->name}}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value=""></option>
                                    @endif
                                </select>
                                <select id="category-2-3"
                                        name="category_2_3"
                                        class="category-input min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded
                                        {{(isset($filt_category_2) and $filt_category_2 == 3)? '' : 'hidden'}}">
                                    @if(isset($prod_schemas))
                                        @foreach($prod_schemas as $schema)
                                            <option value="{{$schema->id}}" {{(isset($filt_element_2) and $filt_element_2 == $schema->id and isset($filt_category_2) and $filt_category_2 == 3)? 'selected' : ''}}>
                                                {{$schema->production_schema}}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value=""></option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </dl>
                    <div class="flex flex-col justify-center items-center bg-white xl:border-r-2 rounded-r-lg w-1/5">
                        <button type="submit" class ='w-[60%] xl:w-[40%] text-sm md:text-lg bg-gray-800 hover:bg-gray-600 font-medium text-center text-white rounded-lg'>
                            Filtruj
                        </button>
                    </div>
                </div>
            </div>
            <div id="chart-div-2" class="w-full h-[500px]"></div>
        @endif
        @if(isset($chart_data_3))
            <script type="module">

                // Pass PHP data to JavaScript
                let chartData = @json($chart_data_3);
                chartData = JSON.parse(chartData);
                console.log(chartData);
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                    let data = new google.visualization.DataTable();
                    data.addColumn('string', 'Pracownicy');
                    data.addColumn('number', 'Średnia produktywność');
                    data.addColumn('number', 'Czas pracy');

                    for (let i = 0; i < chartData.length; i++) {
                        let entry = chartData[i];
                        data.addRow([entry.employee_no,
                            parseFloat(entry.weighted_productivity_average),
                            parseFloat(entry.work_duration)
                        ]);
                    }
                    //var data = google.visualization.arrayToDataTable(chartData);

                    var options = {
                        curveType: 'function',
                        series: {
                            0: { type: 'bars', targetAxisIndex: 0, color: 'rgb(48, 188, 252)' }, // Line chart
                            1: { type: 'bars', targetAxisIndex: 1, color: 'rgb(242, 59, 22)' }
                        },
                        hAxis: {
                            title: 'Pracownicy',
                            titleTextStyle: {
                                fontSize: 20,
                                fontName: 'Arial',
                                italic: false
                            },
                            slantedText: true,
                            slantedTextAngle: 45
                        },
                        vAxes: {
                            0: {
                                title: 'Średnia produktywność (%)',
                                titleTextStyle: {
                                    fontSize: 20,
                                    fontName: 'Arial',
                                    italic: false
                                },
                                viewWindow: { min: 0 },
                            },
                            1: {
                                title: 'Czas pracy (h)',
                                titleTextStyle: {
                                    fontSize: 20,
                                    fontName: 'Arial',
                                    italic: false
                                },
                                minValue: 0,
                                gridlines: { count: 0 }
                            } // Set the minimum value for the bar chart axis
                        },
                        legend: { position: 'bottom' },
                        chartArea: {
                            top: '5%',
                            height: '67%'  // Adjust the width as needed
                        }
                    };

                    var chart = new google.visualization.LineChart(document.getElementById('chart-div-3'));
                    chart.draw(data, options);
                }
            </script>

            <div class="mt-5 w-full flex flex-col xl:flex-row border-gray-300 xl:bg-white justify-between items-center z-[50]">
                <a class ='block w-full py-4 pl-3 pr-4 bg-white border-blue-450 border-l-4 lg:border-l-8 text-md md:text-lg lg:text-2xl text-left font-medium text-gray-800  transition duration-150 ease-in-out'>
                    {{isset($chart_title_3)? $chart_title_3 :'Praca firmy'}}
                </a>
            </div>
            <div class="w-full my-4 flex justify-center">
                <div id="filters-3" class="flex flex-row justify-start w-[80%] border-2 rounded-lg">
                    <dl class="grid grid-cols-3 bg-white text-left rounded-l-lg w-4/5">
                        <div class="col-span-1 flex flex-col justify-center h-full">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white rounded-tl-lg'>
                                Pracownicy
                            </a>
                            <div class="p-1">
                                @php $unique_id = 'employees_3' @endphp
                                <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')">
                                    <x-slot name="options">
                                        @foreach($users as $u)
                                            <option value="{{$u->id}}" {{(isset($filt_users_3) and is_array($filt_users_3) and in_array($u->id, $filt_users_3))? 'selected' : ''}}>
                                                {{$u->employeeNo}}
                                            </option>
                                        @endforeach
                                    </x-slot>
                                </x-select-multiple>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-start">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Praca od
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="start-date-3" class="relative w-full">
                                    <input type="date" name="start_date_3" value="{{isset($filt_start_date_3)? $filt_start_date_3 : null}}"
                                           class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           placeholder="Start" min="2023-01-01"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-center border-r-2">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Praca do
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="end-date-3" class=" relative w-full">
                                    <input name="end_date_3" type="date" value="{{isset($filt_end_date_3)? $filt_end_date_3 : null}}"
                                           class="p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           placeholder="Koniec" min="2023-01-01"/>
                                </div>
                            </div>
                        </div>
                    </dl>
                    <div class="flex flex-col justify-center items-center bg-white xl:border-r-2 rounded-r-lg w-1/5">
                        <button type="submit" class ='w-[60%] xl:w-[40%] text-sm md:text-lg bg-gray-800 hover:bg-gray-600 font-medium text-center text-white rounded-lg'>
                            Filtruj
                        </button>
                    </div>
                </div>
            </div>
            <div id="chart-div-3" class="w-full h-[500px]"></div>
        @endif
    </form>
</x-app-layout>
