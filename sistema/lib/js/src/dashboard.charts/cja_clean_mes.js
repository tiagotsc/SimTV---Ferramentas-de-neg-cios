(function(){
    
    var _id = 'cja_clean_mes',
        _parent = 'cja_mes',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.cja_mes(x);
        chart.id = 'cja_clean_mes';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());