(function(){
    
    var _id = 'iar',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'iar';
        chart.orientation = 'greenup';
        chart.opts.plotOptions.column.stacking = 'percent';
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: (chart.owner.index === null),
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.yAxis.labels = {
            format: '{value}%'
        };
        chart.opts.chart.zoomType = 'xy';
        chart.opts.tooltip.formatter = cas.charts.iarToolTip;

        chart.opts.plotOptions.series.events.click = function(event) {

            if (event.point.series.name === 'Meta') 
                return true;

            if (!event.ctrlKey) {
                cas.args.dashboard = {
                    dashboard: dashboard.dashboard,
                    view: 'cidade',
                    ind: 'iar',
                    item: event.point.category
                };
                cas.pushArgs();
                return true;
            }

            var osList = event.point.options.os;
            osList.sort(function(a, b) {
                if (a.ingr === b.ingr)
                    return 0;
                return (a.ingr < b.ingr) ? -1 : 1;
            });
            cas.hidethis('body');
            cas.ajaxer({
                sendme: {
                    os: Base64.encode(JSON.stringify(osList))
                },
                etc: {
                    event: event
                },
                sendto: 'dashboard/oslistget',
                andthen: function(x) {
                    cas.showthis('body');
                    var event = x.etc.event;
                    var table = x.data.table;

                    var makeTB = function(table, container) {
                        var data = new google.visualization.DataTable();
                        for (var i in table.cols) {
                            data.addColumn(table.cols[i].type, table.cols[i].title);
                        }
                        data.addRows(table.rows);
                        var t = new google.visualization.Table(container);
                        t.draw(data, {
                            showRowNumber: false,
                            allowHtml: true
                        });
                    };

                    var container = $("<div>");
                    $('<div>').appendTo('body').fullScreenDialog({
                        content: container,
                        title: "<a href='dashboard#" +
                            cas.hashbangify({
                                dashboard: {
                                    dashboard: dashboard.dashboard,
                                    view: 'cidade',
                                    ind: 'iar',
                                    item: event.point.category
                                }
                            }) + "'>" +
                            cas.areaName(event.point.category) + "</a>" +
                            ' - ' + event.point.series.name + ': ' +
                            table.rows.length + ' Ordens'
                    });

                    if (typeof google.visualization === 'undefined') {
                        cas.charts.tbloader(function() {
                            makeTB(table, container[0]);
                        });
                    } else {
                        makeTB(table, container[0]);
                    }
                }
            });
        };

        chart.refresh = function(list, step) {
            var chart = this;
            if(!chart.owner.enabled){ return; }
            var s = {
                dashboard: dashboard
            };
            if (!$.contains(document.documentElement, chart.plot[0]))
                return false;
            if (!step)
                step = 0;
            s.step = step;
            s[chart.arg] = chart.val;
            if (list && list.length)
                s['area'] = list[step - 1];

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
                    var chart = x.etc.chart;
                    if (!$.contains(document.documentElement, chart.plot[0]))
                        return false;
                    if (x.etc.step === 0) {
                        chart.draw(x);
                        list = x.data.list;
                    } else {

                        list = x.etc.list;
                        for (var i in x.data.series) {
                            chart.chartObject.series[i].addPoint(x.data.series[i], true, false);
                        }
                    }

                    x.etc.step++;
                    if (x.etc.step <= list.length) {
                        setTimeout(function() {
                            chart.refresh(list, x.etc.step);
                        }, 10);
                    } else {
                        chart.iarSort();
                        chart.fetchMetas();
                    }
                }
            });
            return this;
        };
        chart.fetchMetas = function(){
            var chart = this;
            if(chart.id === 'iar'){
                var s = {areas: chart.response.categories};
                s[chart.arg] = chart.val;
                cas.ajaxer({
                    method:'GET',
                    sendme:s,
                    sendto:'dashboard/'+chart.id+'_metas',
                    andthen: function(x){
                        var a = [];
                        for(var i in x.data.metas){
                            a.push(x.data.metas[i]);
                            //chart.response.series.push(x.data.metas[i]);
                            //chart.chartObject.addSeries(x.data.metas[i]);
                        }
                        chart.response.series = 
                            a.concat(chart.response.series);
                        chart.draw(chart.response);

                    }
                });
            }
            return chart;
        };
        chart.iarSort = function(){
            var chart = this;
            var series = chart.response.series;
            var categories = chart.response.categories;

            var l = series.length - 2,
                max, tmp;

            for (var i = 1; i < series[l].data.length - 1; i++) {
                max = i;
                for (var j = i + 1; j < series[l].data.length; j++) {
                    if (series[l].data[j].stackPercent > series[l].data[max].stackPercent) {
                        max = j;
                    }
                }

                if (i !== max) {
                    for (var k in series) {
                        tmp = series[k].data[max];
                        series[k].data[max] = series[k].data[i];
                        series[k].data[i] = tmp;
                    }
                    tmp = categories[max];
                    categories[max] = categories[i];
                    categories[i] = tmp;
                }
            }

            chart.draw(chart.response);
            return chart;
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());