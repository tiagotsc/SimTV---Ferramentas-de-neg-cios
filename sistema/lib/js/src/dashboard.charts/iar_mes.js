(function(){
    
    var _id = 'iar_mes',
        _parent = 'iar_hist',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.iar_hist(x);
        chart.id = 'iar_mes';
        chart.opts.xAxis.labels.enabled = false;
        chart.opts.chart.type = 'column';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());