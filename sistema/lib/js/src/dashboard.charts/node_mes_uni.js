(function(){
    
    var _id = 'node_mes_uni',
        _parent = 'node_mes',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.node_mes(x);
        chart.id = 'node_mes_uni';
        chart.orientation = 'down';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());