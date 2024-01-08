<x-app-layout>
    @if(isset($chart_data))
        <script type="text/javascript">
            // Pass PHP data to JavaScript
            var chartData = @json($chart_data);
            console.log(chartData);
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(chartData);

                var options = {
                    title: 'My Daily Activities'
                };

                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>

        <div id="chart_div" style="width: 100%; height: 500px;"></div>
    @endif
</x-app-layout>
