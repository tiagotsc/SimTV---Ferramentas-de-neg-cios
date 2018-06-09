(function(){
    
    var _id = 'revisita_hist',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.id = 'revisita_hist';
        chart.arg = 'mes';
        chart.refresh = cas.charts.multiStepChart;
        chart.opts.title.text = 'Histórico - IRM';
        chart.opts.yAxis.labels.format = '{value}%';
        chart.opts.xAxis.plotLines =
            [{
            value: 20,
            width: 1,
            color: 'green',
            dashStyle: 'dash',
            label: {
                text: 'Início',
                align: 'right'
            }
        }];

        chart.opts.tooltip = {
            shared: true,
            crosshairs: true,
            valueSuffix: '%',
            formatter: function() {
                var output = this.points;
                output.sort(function(a, b) {
                    if (a.y > b.y) {
                        return -1;
                    } else if (a.y < b.y) {
                        return 1;
                    } else {
                        return 0;
                    }
                });
                var result = '';
                for (var x in output) {
                    result = result + "<b>" + output[x].point.name +
                        ":</b> " + output[x].point.y + "% <i>[" + output[x].point.nope +
                        "</i><b>/</b><i>" + output[x].point.total + "]</i><br>";
                }
                return '<b>' + this.x + '</b><br/>' + result;
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());