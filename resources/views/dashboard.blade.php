<x-app-layout>
    <script>
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Work', 11],
                ['Eat', 2],
                ['Commute', 2],
                ['Watch TV', 2],
                ['Sleep', 7]
            ]);

            var options = {
                title: 'My Daily Activities'
            };

            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
    <div class="py-4 flex justify-center items-center">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{ __("Witaj w DipMar-produkcja!") }}
            </div>
        </div>
    </div>

@if(isset($works))
    @php
        $name = "Twoja praca";
    @endphp
    <x-information-panel :viewName="$name">
    </x-information-panel>
    <div class="w-full flex justify-center items-center my-4">
        <div class="w-[95%]">
            <div class="shadow-md rounded-xl mb-4 border">
                <div class="relative overflow-x-auto">
                    <table class="block max-h-[400px] overflow-y-scroll w-full text-sm bg-gray-100 rounded-xl text-left rtl:text-right pb-2 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300 ">
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

<div id="chart_div" style="width: 100%; height: 500px;"></div>

{{--    <!-- Table responsive wrapper -->--}}
{{--    <div class="overflow-x-auto h-[380px] overflow-y-scroll w-[80%]">--}}

{{--        <!-- Table -->--}}
{{--        <table class="min-w-full text-left text-xs whitespace-nowrap">--}}

{{--            <!-- Table head -->--}}
{{--            <thead class="uppercase tracking-wider sticky top-0 bg-white dark:bg-neutral-700 outline outline-2 outline-neutral-200 dark:outline-neutral-600">--}}
{{--            <tr>--}}
{{--                <th scope="col" class="px-6 py-4">--}}
{{--                    Product--}}
{{--                </th>--}}
{{--                <th scope="col" class="px-6 py-4">--}}
{{--                    Price--}}
{{--                </th>--}}
{{--                <th scope="col" class="px-6 py-4">--}}
{{--                    Stock--}}
{{--                </th>--}}
{{--                <th scope="col" class="px-6 py-4">--}}
{{--                    Status--}}
{{--                </th>--}}
{{--            </tr>--}}
{{--            </thead>--}}

{{--            <!-- Table body -->--}}
{{--            <tbody>--}}

{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Handbag--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$129.99</td>--}}
{{--                <td class="px-6 py-4">30</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}

{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Shoes--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$89.50</td>--}}
{{--                <td class="px-6 py-4">25</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}

{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Bedding Set--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$69.99</td>--}}
{{--                <td class="px-6 py-4">40</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}

{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Dining Table--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$449.99</td>--}}
{{--                <td class="px-6 py-4">5</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}

{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Soap Set--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$24.95</td>--}}
{{--                <td class="px-6 py-4">50</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}
{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Soap Set--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$24.95</td>--}}
{{--                <td class="px-6 py-4">50</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}
{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Soap Set--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$24.95</td>--}}
{{--                <td class="px-6 py-4">50</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}
{{--            <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">--}}
{{--                <th scope="row" class="px-6 py-4">--}}
{{--                    Soap Set--}}
{{--                </th>--}}
{{--                <td class="px-6 py-4">$24.95</td>--}}
{{--                <td class="px-6 py-4">50</td>--}}
{{--                <td class="px-6 py-4">In Stock</td>--}}
{{--            </tr>--}}
{{--            </tbody>--}}

{{--        </table>--}}

{{--    </div>--}}
{{--    kjuhujkhgjg--}}

</x-app-layout>

