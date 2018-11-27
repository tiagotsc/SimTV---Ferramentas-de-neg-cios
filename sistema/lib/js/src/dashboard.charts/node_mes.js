(function(){
    
    var _id = 'node_mes',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'node_mes';
        chart.orientation = 'down';
        chart.arg = null;
        chart.opts.legend.enabled = false;
        chart.opts.xAxis = {
            labels: {
                y: 15,
                rotation: -45
            }
        };
        chart.opts.plotOptions.series.events = {
            click: function(event) {
                cas.charts.nodeOSList.apply(chart, [event]);
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());