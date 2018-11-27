(function(){
    
    var _id = 'causa_rank_hist',
        _parent = 'ev_timeline',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.ev_timeline(x);
        chart.id = 'causa_rank_hist';
        delete chart.opts.plotOptions.series.events.click;
        chart.orientation = 'down';
        chart.stock = false;
        delete chart.opts.rangeSelector;
        chart.opts.legend = {
            align: 'right',
            layout: 'vertical',
            verticalAlign: 'middle'
        };

        chart.opts.yAxis.title = 'Número de Reclamações';
        chart.opts.title.text = 'Histórico - Top 20 Causas';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());