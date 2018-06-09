(function(){
    
    var _id = 'vend_inst_fi',
        _parent = 'std_col_pie',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_col_pie(x);
        chart.limitX = true;
        chart.id = 'vend_inst_fi';
        chart.orientation = 'up';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());