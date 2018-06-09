(function(){
    
    var _id = 'backlog_r',
        _parent = 'backlog',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.backlog(x);
        cas.charts.resetEvents(chart);
        chart.id = 'backlog_r';
        chart.orientation = 'down';
        chart.info = true;
        chart.exportMe = function() {
            cas.hidethis('body');
            cas.ajaxer({
                method: 'GET',
                sendme: {
                    'dia': this.val
                },
                sendto: 'dashboard/backlog_r_xls',
                andthen: cas.charts.stdDownloadXLS
            });
        };
        chart.opts.title.text = 'Pendência de Backlog de Reclamação';
        chart.opts.plotOptions.series.events.click = function(event) {
            if (!cas.isValidArea(event.point.category.toUpperCase()))
                return true;

            cas.args.dashboard = {
                dashboard: dashboard.dashboard,
                view: 'cidade',
                ind: 'backlog_r',
                item: event.point.category
            };
            cas.pushArgs();
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());