(function(){
    
    var _id = 'backlog_r_hist',
        _parent = 'backlog',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.backlog(x);
        cas.charts.resetEvents(chart);
        chart.id = 'backlog_r_hist';
        chart.orientation = 'down';
        chart.arg = null;
        chart.opts.title.text = 'Histórico Semanal - Pendência de Backlog de Reclamação';
        chart.opts.plotOptions.series.events.click = function (e) {
            cas.args.dashboard = {
                dashboard: dashboard.dashboard,
                view: 'cidade',
                ind: 'backlog_r',
                item: 'SIM'
            };
            cas.pushArgs();
        };
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: true,
            floating: false,
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.scrollbar = {
            enabled: true
        };
        chart.opts.xAxis.max = 8;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());