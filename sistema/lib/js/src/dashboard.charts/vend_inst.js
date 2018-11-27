(function(){
    
    var _id = 'vend_inst',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'vend_inst';
        chart.orientation = 'up';
        chart.opts.plotOptions.series.events.click = function(event) {
            if (!cas.isValidArea(event.point.category.toUpperCase()))
                return true;

            cas.args.dashboard = {
                dashboard: dashboard.dashboard,
                view: 'cidade',
                ind: 'vend_inst',
                item: event.point.category
            };
            cas.pushArgs();
        };
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: true,
            floating: false,
            layout: 'horizontal',
            verticalAlign: 'bottom',
            align: 'center'
        };
        chart.opts.plotOptions.series.dataLabels = {
            enabled: (chart.owner.index === null),
            y: -10,
            color: '#424242',
            rotation: -30,
            style: {
                'font-weight': 'bold'
            }
        };
        chart.exportMe = cas.charts.stdFileList;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());