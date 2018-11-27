(function(){
    
    var _id = 'cja_clean_tipo_am',
        _parent = 'cja_tipo_r',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.cja_tipo_r(x);
        chart.id = 'cja_clean_tipo_am';
        chart.ostipo = ['acessórios', 'mudança'];
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());