(function(){
    
    var _id = 'ev_timeline',
        _parent = 'std_line',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_line(x);
        chart.orientation = 'down';
        chart.arg = null;
        chart.stock = true;
        chart.id = 'ev_timeline';
        chart.opts.rangeSelector.selected = 0;
        chart.opts.legend.enabled = false;
        chart.opts.plotOptions.series.events.click = function(event){
            if(!cas.checkPerms('e')){
                return;
            }
            var t = event.point.x, d = new Date();
            d.setTime(t);
            var filter = {
                filter:{
                    s: ["pendente","dc","fechado","he","ativo","re","ri","noc"],
                    t: ["emergencia","canal","geral","mdu","node"], 
                    d: {
                        from: d.toYMD(),
                        to: d.toYMD()
                    }
                }
            };
            if( dashboard.item !== 'SIM' ){
                filter.filter.l = [dashboard.item];
            }
            window.open('eventos#'+cas.hashbangify(filter));
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

        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());