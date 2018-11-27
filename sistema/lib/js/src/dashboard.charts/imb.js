(function(){
    "use strict";
    var _id = 'imb',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.id = 'imb';
        chart.orientation = 'down';
        chart.hist = true;
        chart.opts.title.text = 'IMB';
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
        chart.opts.yAxis.labels.format = '{value}%';
        chart.opts.tooltip.formatter = function imbFormatter() {
            if (this.point.real) {
                return "<b>" + cas.areaName(this.x) + "</b>" +
                    "<br><b>" + this.series.name + ' Projeção:</b> ' + this.y + "%<br>" +
                    "<b>" + this.series.name + " Real: </b>" + this.point.real + '%' +
                    ((typeof this.point.reclamacoes !== 'undefined') ? "<br><b>Reclamações:</b> " +
                    this.point.reclamacoes + "<br><b>Base:</b> " + this.point.base : "");
            } else {
                return "<b>" + cas.areaName(this.x) + "</b>" +
                    "<br><b>" + this.series.name + ':</b> ' + this.y + "%" +
                    ((typeof this.point.reclamacoes !== 'undefined') ? "<br><b>Reclamações:</b> " + this.point.reclamacoes + "<br><b>Base:</b> " + this.point.base : "");
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());