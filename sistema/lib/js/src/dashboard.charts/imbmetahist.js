(function(){
    
    var _id = 'imbmetahist',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.id = 'imbmetahist';
        chart.orientation = 'down';
        chart.arg = null;
        chart.refresh = function(list, step) {

            var chart = this;
            if(!chart.owner.enabled){ return; }
            
            if (!$.contains(document.documentElement, chart.plot[0])){
                return false;
            }
            
            var s = {
                dashboard: dashboard
            };
            if (!step){
                step = 0;
            }
            s.step = step;
            if (list) {
                s['mes'] = list[step - 1];
            }

            cas.ajaxer({
                method: 'GET',
                sendme: s,
                etc: {
                    chart: chart,
                    list: list,
                    step: step
                },
                sendto: 'dashboard/' + chart.id,
                andthen: function(x) {
                    var list;

                    if (!$.contains(document.documentElement, x.etc.chart.plot[0])){
                        return false;
                    }
                    if (x.etc.step === 0) {
                        x.etc.chart.draw(x);
                        list = x.data.list;
                    } else {
                        list = x.etc.list;
                        x.etc.chart.chartObject.series[0].addPoint(x.data.total);
                        x.etc.chart.chartObject.series[1].addPoint(x.data.meta_total);
                        x.etc.chart.chartObject.series[2].addPoint(x.data.tv);
                        x.etc.chart.chartObject.series[3].addPoint(x.data.meta_tv);
                        x.etc.chart.chartObject.series[4].addPoint(x.data.cm);
                        x.etc.chart.chartObject.series[5].addPoint(x.data.meta_cm);
                    }

                    x.etc.step++;
                    if (x.etc.step <= list.length) {
                        setTimeout(function() {
                            x.etc.chart.refresh(list, x.etc.step);
                        }, 10);
                    }
                }
            });
            return this;
        };
        chart.opts.title.text = 'Histórico -IMB';
        chart.opts.yAxis.labels.format = '{value}%';
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: true,
            floating: false,
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.xAxis = {
            labels: {
                y: 15,
                rotation: -45
            }
        };

        chart.opts.tooltip = {
            formatter: function() {
                if (typeof this.point.reclamacoes === 'undefined')
                    return "<b>" + this.point.category + "</b><br><b>" +
                        this.series.name + ":</b>" + this.y + "%";
                else
                    return "<b>" + this.point.category + "</b><br><b>" +
                        this.series.name + ":</b>" + this.y + "%" +
                        "<br><b>Reclamações:</b> " + this.point.reclamacoes +
                        "<br><b>Base:</b> " + this.point.base + "<br><b>";
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());