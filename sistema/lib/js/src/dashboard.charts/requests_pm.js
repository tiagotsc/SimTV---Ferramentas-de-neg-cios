(function(){
    
    var _id = 'requests_pm',
        _parent = 'siga_conns',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.siga_conns(x);
        chart.id = 'requests_pm';
        chart.opts.title.text = 'Requisições por Minuto';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());