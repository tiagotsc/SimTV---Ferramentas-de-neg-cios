(function(){
    
    var _id = 'causa_rank',
        _parent = 'std_col_pie',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_col_pie(x);
        chart.arg = 'mes';
        chart.orientation = 'down';
        chart.hist = true;

        chart.opts.yAxis.allowDecimals = false;
        chart.opts.yAxis.title = {
            text: 'Nº de Reclamações'
        };
        chart.opts.xAxis.labels = {
            enabled: false
        };
        chart.opts.scrollbar = {
            enabled: true
        };

        chart.id = 'causa_rank';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());