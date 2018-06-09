(function(){
    
    var _id = 'node_dia',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.id = 'node_dia';
        chart.orientation = 'down';
        chart.arg = null;
        chart.stock = true;
        chart.opts.rangeSelector.selected = 0;
        chart.opts.legend.enabled = false;
        if(!chart.opts.plotOptions.line){
            chart.opts.plotOptions.line = {};
        }
        chart.opts.plotOptions.line.tooltip = {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} </b><br/>'+
                '<i>Eventos massivos: {point.options.evCount} </i>'
        };
        chart.opts.plotOptions.line.events = {
            click: function(event) {
                if(event.ctrlKey){
                    cas.charts.nodeOSList.apply(chart, [event]);
                    return;
                }
                var filter = {
                    filter:{
                        l: [dashboard.item],
                        s: ["pendente","dc","fechado","he","ativo","re","ri","noc"],
                        t: ["emergencia","canal","geral","mdu","node"], 
                        d: {
                            from: event.point.options.dia,
                            to: event.point.options.dia
                        }
                    }
                };

                window.open('eventos#'+cas.hashbangify(filter));
            }
        };

        chart.opts.rangeSelector.buttons =
            [{
            type: 'day',
            count: 15,
            text: '15d'
        }, {
            type: 'month',
            count: 3,
            text: '3m'
        }, {
            type: 'month',
            count: 6,
            text: '6m'
        }, {
            type: 'all',
            text: 'Tudo'
        }];


        chart.opts.xAxis = {
            labels: {
                y: 15,
                rotation: -45
            }
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());