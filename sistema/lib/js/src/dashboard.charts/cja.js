(function(){
    
    var _id = 'cja',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'cja';
        chart.orientation = 'up';
        chart.opts.chart.zoomType = 'xy';
        chart.opts.yAxis.max = 100;
        chart.opts.plotOptions.column.stacking = 'normal';
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: (chart.owner.index === null),
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.tooltip.formatter = function(event) {
            if (this.point.series.name === 'Meta') {
                return "<b>" + cas.areaName(this.point.category) + '</b><br><b>' +
                    this.point.series.name + ":</b> " + this.y + '%';
            }
            var htm = '<b>' + this.point.name + '</b><br>';
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
                    view: 'cidade',
                    ind: 'cja',
                    item: event.point.category
                };
                cas.pushArgs();
                return true;
            }
            cas.hidethis('body');
            cas.ajaxer({
                sendme: {
                    id: event.point.series.options.id,
                    area: event.point.category,
                    mes: this.chart.owner.val
                },
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
                        title: "<a href='dashboard#" +
                            cas.hashbangify({
                                dashboard: {
                                    dashboard: dashboard.dashboard,
                                    view: 'cidade',
                                    ind: 'cja',
                                    item: event.point.category
                                }
                            }) + "'>" +
                            event.point.name + "</a>" +
                            ' - ' + event.point.series.name +
                            ', ' + event.point.series.chart.owner.hr_val + ': ' +
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
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());