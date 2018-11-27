(function(){
    
    var _id = 'cache_perf',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.id = 'cache_perf';
        chart.arg = 'dia';
        chart.arg_def = (new Date()).toYMD();
        chart.stock = true;
        chart.opts.tooltip = {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} segundos</b><br/>'
        };
        chart.opts.title.text = 'Duração da Atualização';
        chart.autoupdate = 30 * 1000;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());