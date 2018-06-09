(function(){
    
    var _id = 'revisita',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'revisita';
        chart.orientation = 'down';
        chart.hist = true;
        chart.opts.title.text = 'IRM';
        chart.opts.plotOptions.series.events.click = function(event) {
            if (!cas.isValidArea(event.point.category.toUpperCase()))
                return true;

            cas.args.dashboard = {
                dashboard: dashboard.dashboard,
                view: 'cidade',
                ind: 'irm',
                item: event.point.category
            };
            cas.pushArgs();
        };
        chart.opts.yAxis.labels.format = '{value}%';
        chart.opts.tooltip.formatter = function() {
            if (this.series.name === 'Meta IRM')
                return "<b>" + this.point.name + "</b><br><b>" + this.series.name + ':</b> ' + this.y + "%";
            else
                return "<b>" + this.point.name +
                    "</b><br><b>" + this.series.name + ':</b> ' + this.y +
                    "% <br><b>Reclamações:</b> " + this.point.total +
                    "<br><b>Não Conforme:</b> " + this.point.nope;
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());