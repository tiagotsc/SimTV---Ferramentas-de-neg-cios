(function(){
    
    var _id = 'node_ev_rank',
        _parent = 'std_column',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_column(x);
        chart.limitX = true;
        chart.orientation = 'down';
        chart.id = 'node_ev_rank';
        chart.opts.title.text = 'Nodes Ofensores';
        chart.opts.plotOptions.series.events.click = function(event){
            if(!cas.checkPerms('e')){
                return;
            }
            var s = this.chart.owner.val.split('-'), 
                y = parseInt(s[0]), 
                m = parseInt(s[1]) - 1;
            var from = new Date(y, m, 1),
                to = new Date(y, m+1, 1);
            var filter = {
                filter:{
                    l: [event.point.category],
                    s: ["pendente","dc","fechado","he","ativo","re","ri","noc"],
                    t: ["emergencia","canal","geral","mdu","node"], 
                    d: {
                        from: from.toYMD(),
                        to: to.toYMD()
                    }
                }
            };
            window.open('eventos#'+cas.hashbangify(filter));
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());