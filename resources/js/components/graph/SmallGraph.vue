<template>
    <div>
        <div :id="id" style="width: 100%; height: 60px;"></div>
    </div>
</template>

<script>
    export default {
        data() {
          return {
              dataChart: [12, 31, 27, 36, 65, 42, 76, 23, 19, 37, 16],
              color: '#62dbfe'
          }
        },
        props: ["id", "title", "data", "type"],
        mounted() {
            let app = this;
            var options = {
                chart: {
                    type: 'column',
                    style: { fontFamily: 'Poppins, Avenir' },
                    width: null
                },
                title: { text: null },
                xAxis: { 
                    categories: null, 
                    labels: { enabled: false }, 
                    gridLineWidth: 0,
                    lineWidth: 0,
                    tickWidth: 0
                },
                yAxis: { 
                    title: { text: null }, 
                    labels: { enabled: false }, 
                    gridLineWidth: 0, 
                    minorGridLineWidth: 0,
                    startOnTick: false,
                    endOnTick: false,                        
                    tickPositions: [],
                    lineWidth: 0,
                    tickWidth: 0,
                    visible: false
                },
                tooltip: {
                    shared: true,
                },
                credits: { enabled: false },
                legend: { enabled: false },
                plotOptions: {
                    areaspline: { fillOpacity: 0.5 },  
                    marker: { symbol: 'circle' },
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0,
                        groupPadding: 0
                    },
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: { enabled: false },
                        showInLegend: true
                    }
                },
                series: [
                    {
                        name: null,
                        data: app.dataChart.map(d => parseInt(d)),
                        color: app.color,
                        fillColor: {
                            linearGradient: [0, 0, 0, 300],
                            stops: [
                                [0, app.color],
                                [1, Highcharts.Color(app.color).setOpacity(0).get('rgba')]
                            ]
                        },
                    }
                ]
            };
            Highcharts.chart(app.$props.id, options);
        },
    }
</script>
