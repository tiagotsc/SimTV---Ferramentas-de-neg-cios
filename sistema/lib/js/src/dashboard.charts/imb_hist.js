(function(){
    
    var _id = 'imb_hist',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.id = 'imb_hist';
        chart.orientation = 'down';
        chart.arg = 'mes';
        chart.refresh = cas.charts.multiStepChart;
        chart.opts.title.text = 'Histórico -IMB';
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
                    if (typeof output[x].point.real !== 'undefined')
                        result =
                            result + "<b>" +
                            output[x].series.name + ":</b> ~" + output[x].y + " || <b>Real: </b>" + output[x].point.real + "% [" + output[x].point.reclamacoes + ' / ' + output[x].point.base + "]" +
                            "<br>";
                    else
                        result =
                            result + "<b>" +
                            output[x].series.name + ":</b> " + output[x].y + "% [" + output[x].point.reclamacoes + ' / ' + output[x].point.base + "]" +
                            "<br>";
                }
                return '<b>' + this.x + '</b><br/>' + result;
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());