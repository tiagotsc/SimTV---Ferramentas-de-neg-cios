(function(){
    
    var _id = 'cja_mes',
        _parent = 'cja_hist',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.cja_hist(x);
        cas.charts.resetEvents(chart);
        delete chart.opts.xAxis.plotLines;
        chart.id = 'cja_mes';

        chart.opts.tooltip.formatter = function(event) {
            var htm = '<b> Semana ' + (this.point.x + 1) + '</b><br>';
            for (var i in this.point.options.totals) {
                var t = this.point.options.totals[i];
                htm += '<b>' + t.name + ':</b> ' + t.total + ' ordens ' + ((t.percent) ? '<i> ~' + t.percent + '%</i>' : '') + '<br>';
            }
            return htm;
        };
        chart.opts.xAxis.labels = {
            enabled: false
        };
        chart.opts.plotOptions.series.events.click = function(event) {
            var id = dashboard.item.split(':');
            if (!event.ctrlKey) {
                cas.args.dashboard = {
                    dashboard: dashboard.dashboard,
                    view: 'semana',
                    ind: 'cja',
                    item: id[0] + ':' + event.point.options.semana +
                        ((event.point.options.tipo) ? ':' +
                        ((event.point.options.tipo instanceof Array) ? event.point.options.tipo.join(',') : event.point.options.tipo) : ''
                    )
                };
                cas.pushArgs();
                return true;
            }

            var s = {
                id: event.point.series.options.id,
                area: id[0],
                semana: event.point.options.semana
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
                        title: dashTitle + ' - ' + event.point.series.name + ', Semana ' + (event.point.x + 1) + ': ' +
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

        chart.list_arg = 'semana';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());