(function(){
    
    var _id = 'cja_hist',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'cja_hist';
        chart.opts.yAxis.max = 100;
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: true,
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.yAxis.labels = {
            format: '{value}%'
        };
        chart.opts.xAxis.plotLines =
            [{
                value: 24,
                width: 1,
                color: 'green',
                dashStyle: 'dash',
                zIndex: 7,
                label: {
                    text: 'Início',
                    align: 'right'
                }
            }];
        chart.opts.chart.zoomType = 'xy';
        chart.opts.plotOptions.column.stacking = 'normal';

        chart.opts.tooltip.formatter = function(event) {
            if (this.point.series.name === 'Meta') {
                return "<b>" + this.point.category + '</b><br><b>' +
                    this.point.series.name + ":</b> " + this.y + '%';
            }
            var htm = '<b>' + this.point.category + '</b><br>';
            for (var i in this.point.options.totals) {
                var t = this.point.options.totals[i];
                htm += '<b>' + t.name + ':</b> ' + t.total + ' ordens ' + ((t.percent) ? '<i> ~' + t.percent + '%</i>' : '') + '<br>';
            }
            return htm;
        };
        chart.opts.yAxis.labels = {
            format: '{value}%'
        };
        chart.opts.plotOptions.series.events.click = function(event) {
            if(event.point.series.name === 'Meta')
                return true;
            if (!event.ctrlKey) {
                cas.args.dashboard = {
                    dashboard: dashboard.dashboard,
                    view: 'mes',
                    ind: 'cja',
                    item: dashboard.item + ':' + event.point.options.mes +
                        ((event.point.options.tipo) ? ':' +
                        ((event.point.options.tipo instanceof Array) ? event.point.options.tipo.join(',') : event.point.options.tipo) : ''
                    )
                };
                cas.pushArgs();
                return true;
            }

            var s = {
                id: event.point.series.options.id,
                area: dashboard.item,
                mes: event.point.options.mes
            };
            if (event.point.options.tipo)
                s.tipo = event.point.options.tipo;

            cas.hidethis('body');
            cas.ajaxer({
                sendme: s,
                etc: {
                    event: event
                },
                method: 'GET',
                sendto: 'dashboard/cja_oslistget',
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
                        title: event.point.name +
                            ' - ' + event.point.series.name +
                            ', ' + event.point.category + ': ' +
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
        chart.arg = null;
        chart.list_arg = 'mes';
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

            if (list && list.length)
                s[chart.list_arg] = list[step - 1];

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
                        for (var i in x.data.series)
                            x.etc.chart.chartObject.series[i].addPoint(x.data.series[i], true, false);
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
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());