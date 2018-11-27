(function(){
    
    var _id = 'std_pie',
        _parent = 'std',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new cas.charts.chartFactory.std(x);
        chart.opts = {
            chart: {
                renderTo: chart.plot[0],
                type: 'pie'
            },
            credits: {
                enabled: false
            },
            title: {
                text: null
            },
            legend: {
                backgroundColor: 'white',
                enabled: true,
                floating: false,
                layout: 'vertical',
                verticalAlign: 'middle',
                align: 'right',
                labelFormatter: function() {
                    return this.name + ": " + this.y;
                }
            },
            plotOptions: {
                series: {marker: {enabled: false},dataLabels:{enabled: false},events:{}},
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    showInLegend: true
                }
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());