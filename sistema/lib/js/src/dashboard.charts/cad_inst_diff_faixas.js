(function(){
    
    var _id = 'cad_inst_diff_faixas',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'cad_inst_diff_faixas';
        chart.orientation = 'blueup';
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: (chart.owner.index === null),
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.plotOptions.series.events.click = function(event) {

            var chart = this.chart;
            if (event.point.series.name === 'Meta'){
                return true;
            }

            cas.hidethis('body');
            cas.ajaxer({
                method:'GET',
                sendme: { 
                    faixa: event.point.options.faixa, 
                    area: event.point.options.area, 
                    mes: chart.owner.val 
                },
                etc: { event: event, mes: chart.owner.hr_val },
                sendto: 'dashboard/cadinstoslistget',
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
                        title: event.point.name  + ' - ' + 
                            event.point.series.name + ', ' + x.etc.mes + ': ' + 
                                table.rows.length + ' Ordens'
                    });

                    if (google.visualization === undefined) {
                        cas.charts.tbloader(function() {
                            makeTB(table, container[0]);
                        });
                    } else {
                        makeTB(table, container[0]);
                    }
                }
            });
        };
        chart.opts.plotOptions.column.stacking = 'percent';
        chart.opts.yAxis.labels.format = '{value}%';
        chart.opts.plotOptions.column.tooltip = {
            pointFormat: "<span style='color: {series.color}'><b>{series.name}:</b>"+
                    "</span> {point.options.percent}%"+
                "<br><i>{point.y} instalações</i>"
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());