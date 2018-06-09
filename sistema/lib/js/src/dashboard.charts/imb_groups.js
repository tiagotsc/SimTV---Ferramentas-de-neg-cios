(function(){
    
    var _id = 'imb_groups',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'imb_groups';
        chart.orientation = 'down';
        chart.hist = true;
        chart.opts.title.text = 'IMB por Produto';
        chart.opts.plotOptions.series.events.click = function(event) {
            if (!cas.isValidArea(event.point.category.toUpperCase()))
                return true;

            cas.args.dashboard = {
                dashboard: dashboard.dashboard,
                view: 'cidade',
                ind: 'imb',
                item: event.point.category
            };
            cas.pushArgs();
        };

        chart.opts.plotOptions.column.stacking = 'normal';
        chart.opts.yAxis.labels.format = '{value}%';
        chart.opts.tooltip.formatter = function() {
            var txt = '';
            txt += "<b>" + cas.areaName(this.x) + "</b>" + "<br>";
            txt += "<b>" + this.point.name + " - " + this.series.name + ":</b> ";
            txt += ((this.point.p > 0) ? this.point.stackTotal : this.point.y) + "%<br>";
            txt += ((this.point.real) ?
                "<b>Real:</b> " + this.point.real + "%<br>" : "");
            txt += ((this.point.reclamacoes) ?
                "<b>Reclamações:</b> " + this.point.reclamacoes + "<br>" : "");
            txt += ((this.point.base) ?
                "<b>Base:</b> " + this.point.base + "<br>" : "");
            return txt;
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());