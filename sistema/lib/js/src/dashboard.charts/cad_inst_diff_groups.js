(function(){
    
    var _id = 'cad_inst_diff_groups',
        _parent = 'cad_inst_diff',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.cad_inst_diff(x);
        chart.orientation = 'down';
        chart.id = 'cad_inst_diff_groups';
        chart.hist = false;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());