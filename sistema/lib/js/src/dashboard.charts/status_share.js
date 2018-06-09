(function(){
    
    var _id = 'status_share',
        _parent = 'std_pie',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_pie(x);
        chart.id = 'status_share';
        chart.controls = ['data_table'];
        chart.exportMe = function() {
            cas.hidethis('body');
            cas.ajaxer({
                method: 'GET',
                sendme: this.getArgs(),
                sendto: 'dashboard/' + this.id + '_xls',
                andthen: cas.charts.stdDownloadXLS
            });
        };
        chart.getArgs = cas.charts.stdArgs;
        chart.dataTable = function() {
            cas.hidethis('body');
            var s = this.getArgs();
            cas.ajaxer({
                method: 'GET',
                etc: {
                    s: {
                        hr_val: this.hr_val,
                        status: s.status
                    }
                },
                sendme: s,
                sendto: 'dashboard/' + this.id + '_dtb',
                andthen: function(x) {
                    var t = x.data.table;
                    if (typeof google.visualization === 'undefined') {
                        cas.charts.tbloader(function() {
                            cas.charts.stdDataTableConstructor(t, x.etc.s);
                        });
                    } else {
                        cas.charts.stdDataTableConstructor(t, x.etc.s);
                    }
                }
            });
        };
        chart.opts.legend = {
            enabled: (chart.owner.index === null)
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());