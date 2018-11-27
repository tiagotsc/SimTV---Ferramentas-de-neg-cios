(function(){
    
    var _id = 'backlog_r_realtime',
        _parent = 'backlog_r',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.backlog_r(x);
        chart.arg = 'status';
        chart.orientation = 'down';
        chart.info = false;
        chart.exportMe = false;
        chart.hist = false;
        chart.id = 'backlog_r_realtime';
        chart.autoupdate = 120 * 1000;

        chart.refresh = function(list, step) {
            var chart = this;
            if(!chart.owner.enabled){ return; }
            var s = {
                dashboard: dashboard
            };

            if (!step)
                step = 0;
            s.step = step;
            if (list) {
                s[chart.arg] = list[step - 1];
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

                    if (!$.contains(document.documentElement, x.etc.chart.plot[0]))
                        return false;

                    if (x.etc.step === 0) {
                        x.etc.chart.draw(x);
                        //x.etc.chart.chartObject.showLoading('Carregando...');
                        list = x.data.sts;
                    } else {
                        list = x.etc.list;
                        var k = x.etc.step - 1;
                        for (var j in x.data.x) {
                            var tmp = $.extend({}, x.data.x[j]);
                            if (x.etc.chart.chartObject.series[k].data[j] === undefined){
                                x.etc.chart.chartObject.series[k].addPoint(tmp);
                            } else {
                                if (x.etc.chart.chartObject.series[k].data[j].y !== tmp.y 
                                        || x.etc.chart.chartObject.series[k].data[j].corp !== tmp.corp) 
                                {
                                    x.etc.chart.chartObject.series[k].data[j].update(tmp);
                                }
                            }
                        }
                    }
                    x.etc.step++;
                    if (x.etc.step <= list.length) {
                        setTimeout(function() {
                            x.etc.chart.refresh(list, x.etc.step);
                        }, 10);
                    }else{
                        //x.etc.chart.chartObject.hideLoading();
                    }
                }
            });
            return this;
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());