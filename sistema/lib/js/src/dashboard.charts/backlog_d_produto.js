(function(){
    
    var _id = 'backlog_d_produto',
        _parent = 'backlog_d',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.backlog_d(x);
        chart.id = 'backlog_d_produto';
        chart.orientation = 'down';
        chart.hist = false;
        chart.opts.title.text = 'Backlog de Desconexão por Produto';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());