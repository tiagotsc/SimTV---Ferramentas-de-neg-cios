(function(){
    
    var _id = 'os_cache_uptime_status',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.arg = null;
        chart.opts.chart.zoomType = 'x';
        chart.opts.chart.type = 'areaspline';
        chart.opts.xAxis.labels.enabled = false;
        chart.opts.plotOptions.areaspline = {
            stacking: 'normal'
        };

        chart.opts.legend = {
            enabled: (chart.owner.index === null),
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        };
        chart.opts.plotOptions.series.events.legendItemClick = function(event){
            var series = event.target.chart.series, me = this;
            setTimeout(function(){
                for(var i in series){
                    if(series[i].name === me.name && me.options.descr !== series[i].options.descr){
                        series[i].setVisible(!series[i].visible);
                    }
                }
            },100);
            return true;
        };
        chart.id = 'os_cache_uptime_status';
        delete chart.opts.tooltip;
        chart.opts.tooltip = {
            shared: false,
            pointFormat: 
                '<span style="color:{series.color}">{series.options.descr}</span><br>'+
                    '<b>{point.name}</b>: {point.y} ordens<br>'
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());