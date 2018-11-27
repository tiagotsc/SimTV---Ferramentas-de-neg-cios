(function(){
    
    var _id = 'vend_inst_base',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'vend_inst_base';
        chart.orientation = 'up';
        chart.arg = null;
        chart.opts.plotOptions.column.stacking = 'normal';
        chart.opts.xAxis.labels = {
            enabled: (chart.owner.index === null),
            rotation: -45,
            y: 15
        };

        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: true,
            floating: false,
            layout: 'horizontal',
            verticalAlign: 'bottom',
            align: 'center'
        };
        chart.opts.tooltip.formatter = function() {
            return '<b>' + this.x + '</b><br/>' +
                this.series.name + ': ' + this.y + '<br/>' +
                'Total: ' + this.point.stackTotal;
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());