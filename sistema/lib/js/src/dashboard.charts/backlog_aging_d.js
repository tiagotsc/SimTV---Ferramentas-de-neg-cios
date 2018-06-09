(function(){
    
    var _id = 'backlog_aging_d',
        _parent = 'backlog',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.backlog(x);
        cas.charts.resetEvents(chart);
        chart.arg = null;
        chart.orientation = 'down';
        chart.id = 'backlog_aging_d';
        chart.hist = false;
        chart.exportMe = false;
        chart.opts.title.text = 'Backlog de Desconexão - Dias em Aberto';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());