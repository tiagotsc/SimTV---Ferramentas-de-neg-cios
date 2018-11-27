(function(){
    
    var _id = 'tbo_users',
        _parent = 'tec_prod',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.tec_prod(x);
        chart.id = 'tbo_users';
        chart.arg = 'mes';
        chart.opts.plotOptions.scatter.marker = {radius: 5};
        function TBOPlotTb(x){
            var point = x.etc.point;
            cas.showthis('body');
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
                title: 
                    '<b style="color: black">'+point.name + '</b>, '+
                    point.series.chart.owner.hr_val + ': ' +
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
        chart.opts.plotOptions.series.events.click =
            function(event) {
                var point = event.point;
                if (point.series.name !== 'bkg') {

                    cas.hidethis('body');
                    cas.ajaxer({
                        sendme: {
                            area: point.options.user.id,
                            mes: this.chart.owner.val
                        },
                        etc: {point: point},
                        method: 'GET',
                        sendto: 'dashboard/tbo_oslistget',
                        andthen: TBOPlotTb
                    });
                }
        };

        chart.opts.tooltip = {
            valueSuffix: '%',
            formatter: function(event) {
                if (this.series.name !== 'bkg') {
                    return "<b>" + this.point.name + "</b><br>" +
                        '<b>Produção:</b> ' + this.y + " por dia<br>" +
                        '<b>Qualidade:</b> ' + this.x + "%<br>" + '<b>Baixas:</b> ' +
                        this.point.baixas + "<br>" + '<b>Baixadas dentro do prazo:</b> ' +
                        this.point.baixas_conforme + "<br>" + 
                        '<b>Dias Trabalhados: </b> ' + this.point.dias + "<br>";
                } else {
                    return false;
                }
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());