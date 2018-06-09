(function(){
    
    var _id = 'iar_hist',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'iar_hist';
        chart.opts.plotOptions.column = {
            cursor: 'pointer',
            stacking: 'percent',
            lineWidth: 0
        };
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: true,
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.yAxis.labels = {format: '{value}%'};
        chart.opts.chart.type = 'column';
        chart.opts.chart.zoomType = 'xy';
        chart.opts.tooltip.formatter = cas.charts.iarToolTip;
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
        chart.opts.plotOptions.series.events.click = function(event) {

            var chart = event.point.series.chart.owner;

            if (event.point.series.name === 'Meta') 
                return true;

            var arg = 'mes',
                id = dashboard.item;

            if (chart.id === 'iar_mes') {
                arg = 'semana';

                id = dashboard.item.split(':');
                id = id[0];

            }
            if (chart.id === 'iar_semana') {
                arg = 'dia';

                id = dashboard.item.split(':');
                id = id[0];
            }

            if (!event.ctrlKey && arg !== 'dia') {
                cas.args.dashboard = {
                    dashboard: dashboard.dashboard,
                    view: arg,
                    ind: 'iar',
                    item: id + ':' + event.point.options[arg]
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
                    event: event,
                    arg: arg
                },
                sendto: 'dashboard/oslistget',
                andthen: function(x) {
                    var arg = x.etc.arg;
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

                    var titleplus;
                    if (arg === 'mes')
                        titleplus = event.point.category;
                    if (arg === 'semana')
                        titleplus = 'Semana ' + (event.point.x + 1);
                    if (arg === 'dia')
                        titleplus = event.point.category;

                    var container = $("<div>");
                    $('<div>').appendTo('body').fullScreenDialog({
                        content: container,
                        title: dashTitle + ' - ' + event.point.series.name +
                            ', ' + titleplus + ': ' +
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

            var arg = 'mes';
            if (chart.id === 'iar_mes')
                arg = 'semana';
            if (chart.id === 'iar_semana')
                arg = 'dia';


            if (list && list.length)
                s[arg] = list[step - 1];

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