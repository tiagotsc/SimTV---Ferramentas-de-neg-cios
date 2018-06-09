(function(){
    var _id = 'backlog',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;

    function doBacklogXLS() {

        var index = parseInt($(this).data('index'));
        var x = cas.charts.chartView();
        var item = x[index];
        var parent = $(this).parent();
        var s = {
            area: parent.children('.xls_area').val(),
            status: parent.children('.xls_status').val(),
            dia: item.chart.val
        };
        $(this).closest('.weird-dialog').remove();
        cas.hidethis('body');
        cas.ajaxer({
            method: 'GET',
            sendme: s,
            sendto: 'dashboard/backlog_xls',
            andthen: cas.charts.stdDownloadXLS
        });

    }

    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'backlog';
        chart.orientation = 'down';
        chart.hist = true;
        chart.arg = 'dia';
        chart.exportMe = function(){
            var x = this.menu.find('.chart-export'),
                pos = null;
            if (x.length) {
                pos = x.offset();
                pos.top -= 30;
                //pos.top -= $(window).scrollTop();
                pos.left += 30;
            }
            cas.hidethis('body');
            cas.ajaxer({
                method: 'GET',
                etc: {
                    pos: pos,
                    index: this.owner.index
                },
                sendto: 'dashboard/backlog_xls_opts',
                andthen: function(x) {
                    cas.showthis('body');
                    var sts =
                        $("<select class='xls_status'>")
                        .append("<option value=''>Todos</option>");
                    var areas =
                        $("<select class='xls_area'>")
                        .append("<option value=''>Todas</option>");
                    for (var i in x.data.sts)
                        sts.append("<option value='" + x.data.sts[i].name + "'>" + x.data.sts[i].name + "</option>");
                    for (var i in x.data.areas)
                        areas.append("<option value='" + x.data.areas[i].id + "'>" + x.data.areas[i].name + "</option>");
                    var y = $("<div class='chart-xls-opts'>").append("<span>Área:</span>")
                        .append(areas).append('<span>Status:</span>').append(sts);
                    $("<span class='chart-menu-button chart-do-export' style='margin-left: 10px;'>Exportar</span>")
                        .data('index', x.etc.index).click(doBacklogXLS).appendTo(y);

                    cas.weirdDialogSpawn(pos, y, null, false);
                }
            });
        };
        chart.opts.title.text = 'Backlog de Instalação';
        chart.opts.chart.zoomType = 'xy';
        chart.opts.legend = {
            backgroundColor: 'white',
            enabled: (chart.owner.index === null),
            floating: false,
            layout: 'vertical',
            verticalAlign: 'middle',
            align: 'right'
        };
        chart.opts.plotOptions.series.events.click = function(event) {
            if (!cas.isValidArea(event.point.category.toUpperCase())){
                return true;
            }
            cas.args.dashboard = {
                dashboard: dashboard.dashboard,
                view: 'cidade',
                ind: 'backlog',
                item: event.point.category
            };
            cas.pushArgs();
        };
        chart.opts.plotOptions.column.stacking = 'normal';
        chart.opts.yAxis.stackLabels = {
            enabled: true
        };
        chart.opts.tooltip.formatter = function() {
            return "<b>" + this.point.name + "</b><br>" + "<b>" + this.series.name + ':</b> ' + this.y +
                ((typeof this.point.corp !== 'undefined') ? "<br><b>Corporativo:</b>" + this.point.corp : '');
        };
        return chart;
    };


    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());