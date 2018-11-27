(function(){
    
    var _id = 'cad_inst_diff',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'cad_inst_diff';
        chart.orientation = 'down';
        chart.hist = true;
        delete chart.opts.plotOptions.series.events.click;
            /* = function(event) {
                if (!cas.isValidArea(event.point.category.toUpperCase())){
                    return true;
                }

                cas.args.dashboard = {
                    dashboard: dashboard.dashboard,
                    view: 'cidade',
                    ind: 'cad_inst',
                    item: event.point.category
                };
                cas.pushArgs();
            };*/

        chart.opts.plotOptions.column.tooltip = {
            pointFormat: "<b>{series.name}: {point.y} dias</b><br><i>{point.options.instals} instalações</i>"
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());