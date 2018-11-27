(function(){
    
    var _id = 'repair_timeline_groups',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.arg = null;
        chart.orientation = 'down';
        chart.stock = true;
        chart.id = 'repair_timeline_groups';
        chart.opts.rangeSelector.selected = 0;
        chart.opts.legend = {
            enabled: (chart.owner.index === null),
            floating: true,
            align: 'right',
            backgroundColor: '#FCFFC5',
            borderColor: 'black',
            borderWidth: 2,
            y: 30,
            layout: 'vertical',
            verticalAlign: 'top',
            shadow: true
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