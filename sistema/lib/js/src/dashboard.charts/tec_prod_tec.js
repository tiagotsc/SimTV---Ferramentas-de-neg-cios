(function(){
    
    var _id = 'tec_prod_tec',
        _parent = 'tec_prod',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;

    function ChartConstructor(x) {
        var chart = new chartFactory.tec_prod(x);
        chart.id = 'tec_prod_tec';
        chart.arg = null;
        chart.opts.plotOptions.series.events.click =
            function(event) {
                if (event.point.series.name !== 'bkg') {
                    cas.ajaxer({
                        method: 'GET',
                        sendto: "dashboard/tec_mes",
                        sendme: {
                            dashboard: dashboard,
                            mes: event.point.series.options.id
                        },
                        etc: {
                            m: event.point.series.name
                        },
                        andthen: function(x) {
                            
                            if(cas.charts.tecDiag){
                                return cas.charts.tecDiag(x);
                            }
                            $.get('/lib/html/dashboard.tec.diag.html').done(function(data){
                                $('body').append(data);
                                LazyLoad.js('/lib/js/'+cas.src+'/dashboard.tec.diag.js', function(){
                                    cas.charts.tecDiag(x);
                                });
                            });
                            
                            
                        }
                    });
                }
        };

        chart.opts.tooltip = {
            valueSuffix: '%',
            formatter: function(event) {
                if (this.series.name !== 'bkg') {
                    return "<b>" + this.series.name + "</b><br>" +
                        '<b>Produção:</b> ' + this.y + " por dia<br>" +
                        '<b>Qualidade:</b> ' + this.x + "%<br>" + '<b>Visitas:</b> ' +
                        this.point.vts + "<br>" + '<b>Visitas que geraram revisitas:</b> ' +
                        this.point.xvts + "<br>" + '<b>Dias Trabalhados: </b> ' + this.point.ds + "<br>";
                } else return false;
            }
        };
        chart.refresh = function(list, step){
            var chart = this;
            if(!chart.owner.enabled){ return; }
            var s = {
                dashboard: dashboard
            };

            if (!step)
                step = 0;
            s.step = step;
            if (list) {
                if (chart.arg)
                    s[chart.arg] = list[step - 1];
                else
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
                    var list, k;

                    if (!$.contains(document.documentElement, x.etc.chart.plot[0]))
                        return false;
                    if (x.etc.step === 0) {
                        x.etc.chart.draw(x);
                        list = x.data.list;
                    } else {

                        list = x.etc.list;
                        if (x.data.series) {
                            x.etc.chart.chartObject.addSeries(x.data.series);
                            x.etc.chart.response.series.push(x.data.series);
                        }

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
        chart.opts.legend.enabled = true;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());