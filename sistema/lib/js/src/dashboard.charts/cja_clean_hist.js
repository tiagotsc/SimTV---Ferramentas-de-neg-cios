(function(){
    
    var _id = 'cja_clean_hist',
        _parent = 'cja_hist',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.cja_hist(x);
        chart.id = 'cja_clean_hist';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());