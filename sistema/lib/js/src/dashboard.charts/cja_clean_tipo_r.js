(function(){
    
    var _id = 'cja_clean_tipo_r',
        _parent = 'cja_tipo_r',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.cja_tipo_r(x);
        chart.id = 'cja_clean_tipo_r';
        chart.ostipo = 'reclamação';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());