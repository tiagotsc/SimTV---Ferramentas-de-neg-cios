(function(){
    
    var _id = 'backlog_timeline_r',
        _parent = 'ev_timeline',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.ev_timeline(x);
        cas.charts.resetEvents(chart);
        chart.orientation = 'down';
        chart.id = 'backlog_timeline_r';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());