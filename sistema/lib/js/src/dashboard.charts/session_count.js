(function(){
    
    var _id = 'session_count',
        _parent = 'siga_conns',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.siga_conns(x);
        chart.id = 'session_count';
        chart.opts.title.text = 'Sessões Ativas';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());