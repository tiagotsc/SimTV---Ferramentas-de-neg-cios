(function(){
    
    var _id = 'backlog_aging_r',
        _parent = 'backlog',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.backlog(x);
        cas.charts.resetEvents(chart);
        chart.id = 'backlog_aging_r';
        chart.orientation = 'down';
        chart.hist = false;
        chart.exportMe = false;
        chart.opts.title.text = 'Pendência de Backlog de Reclamação - Dias em Aberto';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());