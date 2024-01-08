<x-app-layout>
    @if(isset($status_err))
        <div class="flex justify-center items-center">
            <p class="w-full !text-md lg:text-xl font-medium text-center px-6 pt-6  text-red-700 space-y-1">
                {{$status_err}}
            </p>
        </div>
    @endif
    @if(isset($chart_data_1) and isset($users) and isset($filt_items))
        <script type="module">
            $('#filter-btn').on('click',function() {
                let filterGrid = $('#filters');
                if(filterGrid.hasClass('hidden')) {
                    filterGrid.removeClass('hidden');
                } else {
                    filterGrid.addClass('hidden');
                }
            });
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
                data.addColumn({ type: 'string', role: 'annotation' });

                for (let i = 0; i < chartData.length; i++) {
                    let entry = chartData[i];
                    data.addRow([entry.work_date,
                        parseFloat(entry.weighetd_productivity_average),
                        parseFloat(entry.work_duration),
                        null
                    ]);
                }
                //var data = google.visualization.arrayToDataTable(chartData);

                var options = {
                    curveType: 'function',
                    series: {
                        0: { type: 'line', targetAxisIndex: 0 }, // Line chart
                        1: { type: 'bars', targetAxisIndex: 1, bar: { groupWidth: '50%' } },
                        2: { type: 'bars', targetAxisIndex: 1, color: 'transparent' } // Hidden series for spacing
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
                            title: 'Średnia produktywność',
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

                var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>
        @php
            $name = "Praca firmy ogółem";
        @endphp
        <x-information-panel :viewName="$name">
            <x-nav-button  id="filter-btn" class="on-select details bg-yellow-300 hover:bg-yellow-600 ml-2 xl:mr-4">
                {{ __('Filtry') }}
            </x-nav-button>
        </x-information-panel>
        <form method="GET" action="{{ route('stastistics.index') }}" enctype="multipart/form-data">
            <div class="w-full my-4 flex justify-center">
                <div id="filters" class="flex flex-row justify-start w-[70%] border-2 rounded-lg hidden">
                    <dl class="grid grid-cols-2 bg-white text-left rounded-l-lg w-4/5">
{{--                        <div class="col-span-1 flex flex-col justify-center h-full">--}}
{{--                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>--}}
{{--                                Pracownicy--}}
{{--                            </a>--}}
{{--                            <div class="p-1">--}}
{{--                                @php $unique_id = 'employees' @endphp--}}
{{--                                <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')">--}}
{{--                                    <x-slot name="options">--}}
{{--                                        @foreach($users as $u)--}}
{{--                                            <option value="{{$u->id}}" {{(array_key_exists($unique_id,$filt_items) and in_array($u->id, $filt_items[$unique_id]))? 'selected' : ''}}>--}}
{{--                                                {{$u->employeeNo}}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </x-slot>--}}
{{--                                </x-select-multiple>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="col-span-1 flex flex-col justify-start">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white rounded-tl-lg'>
                                Praca od
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="exp-start-time" class="relative w-full">
                                    <input type="date" name="start_date_1" value="{{isset($filt_start_date_1)? $filt_start_date_1 : null}}"
                                           class="exp-start-time p-2 xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                           placeholder="Start" min="2023-01-01"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1 flex flex-col justify-center border-r-2">
                            <a class ='block px-2 text-xs md:text-sm font-medium bg-gray-800 text-center text-white'>
                                Praca do
                            </a>
                            <div class="p-1 flex justify-center items-center h-full">
                                <div id="exp-end-time" class="exp-end-time relative w-full">
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
        </form>
        <div id="chart_div" class="w-full h-[500px]"></div>
    @endif
</x-app-layout>
