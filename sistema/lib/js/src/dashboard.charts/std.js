(function(){
    
    var _id = 'std',
        _parent = null,
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        cas.charts.initPlot(this, x);
        this.arg = 'mes';
        this.autoupdate = null;//getRandomInt(50,80) * 1000;
        this.hist = false;
        this.stock = false;
        this.response = null;
        this.type = 'highchart';    
        this.dialogBts = [{
            title: "Imprimir",
            action: cas.charts.dialogPrint
        }];
        this.controls = [];
        this.owner = x;
        return this;
    };
    
    ChartConstructor.prototype.refresh = cas.charts.fetchChartData;
    ChartConstructor.prototype.makeMenu = cas.charts.menuOpts;
    ChartConstructor.prototype.fetchInfo = function() {
        cas.hidethis('body');
        $.get("/lib/html/chart_info/" + this.id + ".html",
            function(x){
                cas.showthis('body');
                cas.weirdDialogSpawn(null,
                    $("<div class='chart-info-txt'>").append(x), null, true);
            }
        );
    };
    ChartConstructor.prototype.draw = function (response) {
        if(!this.owner.enabled){ return; }
        if (!$.contains(document.documentElement, this.plot[0])){
            return false;
        }
        if (response.data) {
            if (typeof this.predraw === 'function') {
                response.data = this.predraw(response.data);
            }
            response = response.data;
        }
        //saved
        this.response = response;
        var chartOpts = $.extend(true, {}, this.opts);

        if (response.name) {
            chartOpts.title.text = response.name;
        }

        chartOpts.title.text = cas.charts.makeChartName(this, chartOpts);
        chartOpts.series = response.series;
        if (response.categories){
            chartOpts.xAxis.categories = response.categories;
        }
        if (this.limitX && response.categories) {
            chartOpts.xAxis.max = Math.min((response.categories.length - 1), 8);
            chartOpts.scrollbar = {
                enabled: (response.categories.length - 1 > 8)
            };
        }

        if (this.stock) {
            this.chartObject = new Highcharts.StockChart(chartOpts);
        } else {
            this.chartObject = new Highcharts.Chart(chartOpts);
        }

        this.chartObject.owner = this;
        switch(this.orientation){
            case 'blueup':
                this.chartObject.renderer.image('/lib/img/arrow_up_blue.png', 15, 10, 13, 16).add();
                break;
            case 'greenup':
                this.chartObject.renderer.image('/lib/img/arrow_up_green.png', 15, 10, 13, 16).add();
                break;
            case 'up':
                this.chartObject.renderer.image('/lib/img/arrow_up_black.png', 15, 10, 13, 16).add();
                break;
            case 'down':
                this.chartObject.renderer.image('/lib/img/arrow_down_black.png', 15, 10, 13, 16).add();
                break;
            default:
                //nothing to do
                break;
        }

        if (this.owner.index !== null) {
            if (
                typeof cas.args.zoomIn !== 'undefined' && cas.args.zoomIn === this.owner.index
            ) {
                cas.charts.zoomToChart(this.owner);
            } else if (
                typeof cas.args.histOf !== 'undefined' && cas.args.histOf === this.owner.index
            ) {
                cas.charts.showHist(this.owner);
            }
        }
        if (typeof this.drawn === 'function') {
            this.drawn();
        }
        if(this.autoupdate){
            var me = this, plot = me.plot;

            if(this.myTimeCounter)
                clearTimeout(this.myTimeCounter);

            var fff = function(){
                clearTimeout(me.myTimeCounter);
                if( me.plot && me.plot.length ){
                    me.refresh();
                } else {
                    me.myTimeCounter = setTimeout(fff, me.autoupdate);
                }
            };
            this.myTimeCounter = setTimeout(fff,this.autoupdate);
        }
        return this;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());