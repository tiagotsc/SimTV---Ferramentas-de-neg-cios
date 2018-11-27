(function(){
    
    var _id = 'online_users',
        _parent = 'siga_conns',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.siga_conns(x);
        chart.id = 'online_users';
        chart.opts.title.text = 'Usuários Online';
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());