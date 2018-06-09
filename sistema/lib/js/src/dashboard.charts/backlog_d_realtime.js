(function(){
    
    var _id = 'backlog_d_realtime',
        _parent = 'backlog',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.backlog(x);
        cas.charts.resetEvents(chart);
        chart.id = 'backlog_d_realtime';
        chart.orientation = 'down';
        chart.hist = false;
        chart.arg = 'area';
        chart.exportMe = cas.charts.stdFileList;
        chart.refresh = cas.charts.multiStepChart;
        chart.opts.title.text = 'Backlog de Desconexão';
        chart.opts.plotOptions.series.events.click = function(event) {
            if (!cas.isValidArea(event.point.category.toUpperCase())){
                return true;
            }
            cas.args.dashboard = {
                dashboard: dashboard.dashboard,
                view: 'cidade',
                ind: 'backlog_d',
                item: event.point.category
            };
            cas.pushArgs();
        };
        if (chart.owner.index !== null){
            chart.opts.yAxis.stackLabels = {
                enabled: true,
                rotation: -40,
                y: -15,
                x: 10
            };
        }
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());