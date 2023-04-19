<template>
    <div>
        <div :id="id" style="width: 100%; height: 340px;" class="mt-3"></div>
    </div>
</template>

<script>
    import colorHelper from "../../mixin/colorHelper";
    export default {
        props: ["id", "title", "labels", "datasets"],
        mixins: [colorHelper],
        data() {
            return {
                labelsChart: ['January', 'February', 'March', 'April', 'May', 'June'],
                dataChart:[
                    {
                        name: 'Accepted',
                        data: [20, 13, 48, 78, 40, 90]
                    },
                    {
                        name: 'Pending',
                        data: [70, 6, 90, 84, 5, 60]
                    },
                    {
                        name: 'Declined',
                        data: [70, 80, 120, 72, 70, 50]
                    },
                ]
            }
        },
        mounted() {
            let app = this;
            var series = app.dataChart;

            Highcharts.setOptions({
                colors: ['#A3A1FB', '#54d8ff', '#ff5454', '#ffbc07', '#909db7', '#9DFCFF', '#FFD59D', '#FC46FC']
            });

            var seriesData = series.map(serie => {
                return {
                    name: serie.name,
                    data: serie.data.map(d => parseInt(d)),
                    fillColor: {
                        linearGradient: [0, 0, 0, 300],
                        stops: [
                            [0, Highcharts.getOptions().colors[series.indexOf(serie)]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[series.indexOf(serie)]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: { symbol: 'circle' }
                }
            });
            var options = {
                chart: {
                    type: 'areaspline',
                    style: { fontFamily: 'Poppins, Avenir' },
                    width: null
                },
                title: { text: null },
                xAxis: { 
                    categories: app.labelsChart,
                },
                yAxis: { 
                    title: { text: null },
                },
                tooltip: {
                    shared: true,
                    //valuePrefix: prefix + ' '
                },
                credits: { enabled: false },
                plotOptions: {
                    areaspline: { fillOpacity: 0.5, pointPlacement: 'on' },
                    marker: { symbol: 'circle' },
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    },
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: { enabled: false },
                        showInLegend: true
                    }
                },
                series: seriesData
            };
            Highcharts.chart(app.$props.id, options);

        }
    }

</script>
