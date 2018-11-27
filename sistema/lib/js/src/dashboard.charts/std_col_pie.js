(function(){
    
    var _id = 'std_col_pie',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.opts.xAxis = {
            max: 8,
            labels: {
                enabled: false
            }
        };
        chart.opts.legend = {
            enabled: (chart.owner.index === null),
            floating: false,
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right',
            labelFormatter: function() {
                return this.name + ": " + this.y;
            }
        };
        chart.opts.scrollbar = {
            enabled: true
        };

        chart.predraw = function(data) {
            var pieseries = [];
            for (var k = 0; k < data.series.data.length; k++) {
                if (k < 5) {
                    pieseries[k] = data.series.data[k];
                } else {
                    if (k === 5) {
                        pieseries[5] = {
                            color: '#BDBDBD',
                            name: 'Outros',
                            y: data.series.data[k].y
                        };
                    } else {
                        pieseries[5].y += data.series.data[k].y;
                    }
                }
            }
            data.series =
                [{
                type: 'column',
                name: data.series.name,
                data: data.series.data,
                showInLegend: false
            }, {
                type: 'pie',
                name: data.series.name,
                lineWidth: 0,
                borderWidth: 1,
                tooltip: {
                    pointFormat: '<b>{point.y}</b> [{point.percentage:.1f}%]'
                },
                data: pieseries,
                center: ['90%', '10%'],
                size: '30%',
                showInLegend: true
            }];
            return data;
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());