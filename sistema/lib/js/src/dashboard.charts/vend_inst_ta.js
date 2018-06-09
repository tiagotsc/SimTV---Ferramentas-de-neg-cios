(function(){
    
    var _id = 'vend_inst_ta',
        _parent = 'std_pie',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_pie(x);
        chart.id = 'vend_inst_ta';
        chart.orientation = 'up';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());