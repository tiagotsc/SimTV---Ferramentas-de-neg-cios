(function(){
    
    var _id = 'vend_inst_timeline',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.arg = null;
        chart.orientation = 'up';
        chart.stock = true;
        chart.id = 'vend_inst_timeline';
        chart.opts.rangeSelector.selected = 0;
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: true,
            floating: false,
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.rangeSelector.buttons =
            [{
                type: 'day',
                count: 15,
                text: '15d'
            }, {
                type: 'month',
                count: 3,
                text: '3m'
            }, {
                type: 'month',
                count: 6,
                text: '6m'
            }, {
                type: 'all',
                text: 'Tudo'
            }];
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());