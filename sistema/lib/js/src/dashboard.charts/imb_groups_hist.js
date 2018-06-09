(function(){
    
    var _id = 'imb_groups_hist',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.id = 'imb_groups_hist';
        chart.orientation = 'down';
        chart.arg = 'mes';
        chart.refresh = cas.charts.multiStepChart;
        chart.opts.title.text = 'Histórico - IMB por Produto';
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
                    var real = (typeof output[x].point.real === 'undefined');
                    result = result + "<b>" + output[x].point.name + ":</b>" + ((real) ? " " : " ~") + output[x].y + ((real) ? "" : "% || Real: " + output[x].point.real) + "% [" + output[x].point.reclamacoes + ' / ' + output[x].point.base + "]" +
                        "<br>";
                }
                return '<b>' + this.x + '</b><br/>' + result;
            }
        };
        chart.drawn = function() {
            this.plot.css('min-height', this.plot.parent().height() - 40);
            this.chartObject.setSize(this.plot.width(), this.plot.height());
            var x = $("<div id='leg_box' style='text-align:center'>" +
                "<span id='toggle-product-series'>" +
                "<input type='checkbox' id='toggle-tv-series' checked/>" +
                "<label for='toggle-tv-series'>TV</label>" +
                "<input type='checkbox' id='toggle-cm-series' checked/>" +
                "<label for='toggle-cm-series'>CM</label>" +
                "</span>" +
                "</div>");
            this.plot.after(x);

            x.find('#toggle-tv-series').on('change', function() {
                for (var i = 0; i < (cas.charts.zoomChart.chart.chartObject.series.length - 1); i += 2) {
                    if ($(this).is(':checked')){
                        cas.charts.zoomChart.chart.chartObject.series[i].show();
                    }else{
                        cas.charts.zoomChart.chart.chartObject.series[i].hide();
                    }
                }
            });
            x.find('#toggle-cm-series').on('change', function() {
                for (var i = 1; i < cas.charts.zoomChart.chart.chartObject.series.length; i += 2) {
                    if ($(this).is(':checked')){
                        cas.charts.zoomChart.chart.chartObject.series[i].show();
                    }else{
                        cas.charts.zoomChart.chart.chartObject.series[i].hide();
                    }
                }
            });
            x.find('#toggle-product-series').buttonset();
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());