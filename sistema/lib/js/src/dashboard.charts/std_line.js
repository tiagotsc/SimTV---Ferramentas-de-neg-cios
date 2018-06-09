(function(){
    
    var _id = 'std_line',
        _parent = 'std',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new cas.charts.chartFactory.std(x);
        chart.opts = {
            chart: {
                renderTo: chart.plot[0],
                type: 'line',
                zoomType: 'x'
            },
            rangeSelector: {
                enabled: true,
                selected: 1,
                inputEnabled: false,
                buttons: [{
                    type: 'hour',
                    count: 1,
                    text: '1h'
                },{
                    type: 'hour',
                    count: 3,
                    text: '3h'
                }, {
                    type: 'all',
                    text: 'Tudo'
                }]
            },
            plotOptions: {
                series: {marker: {enabled: false},dataLabels:{enabled: false},events:{}}

            },
            credits: {
                enabled: false
            },
            title: {
                text: null
            },
            tooltip: {},
            legend: {
                enabled: true,
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            yAxis: {
                min: 0,
                labels: {
                    enabled: true
                },
                title: {
                    text: null
                }
            },
            xAxis: {
                labels: {
                    staggerLines: 2
                },
                tickPixelInterval: 50
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());