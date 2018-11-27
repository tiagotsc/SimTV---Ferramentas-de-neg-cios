(function(){
    
    var _id = 'os_cache_status',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'os_cache_status';
        chart.opts.chart.zoomType = 'xy';
        chart.opts.legend = {
            enabled: (chart.owner.index === null),
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        };
        chart.opts.plotOptions.column.stacking = 'normal';
        chart.opts.yAxis.stackLabels = {
            enabled: true
        };
        chart.opts.tooltip = {
            pointFormat: 
                '<b>{point.y} ordens</b>'+
                '<br/><i>Atualizadas em media há {point.options.avgUpdateTime} atrás</i>'
        };
        chart.arg = null;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());