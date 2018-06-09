(function(){
    
    var _id = 'os_cache_dia',
        _parent = 'cache_perf',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.cache_perf(x);
        chart.id = 'os_cache_dia';
        chart.arg = null;
        chart.opts.legend = {enabled: false};
        chart.opts.rangeSelector.selected = 0;
        chart.opts.rangeSelector.buttons =
            [{
            type: 'day',
            count: 15,
            text: '15d'
        }, {
            type: 'month',
            count: 1,
            text: '1m'
        },{
            type: 'all',
            text: 'Tudo'
        }];
        chart.opts.tooltip = {
            pointFormat: 
                '<span style="color:{series.color}">'+
                    '{series.name}</span>: <b>{point.y}</b>'+
                '<br/> <i>Atualizadas em media há {point.options.avgUpdateTime} atrás</i>'
        };
        chart.autoupdate = getRandomInt(40,50) * 1000;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());