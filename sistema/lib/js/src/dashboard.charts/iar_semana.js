(function(){
    
    var _id = 'iar_semana',
        _parent = 'iar_mes',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.iar_mes(x);
        chart.id = 'iar_semana';
        chart.opts.xAxis.labels.enabled = true;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());