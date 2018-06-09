(function(){
    
    var _id = 'ass_rank',
        _parent = null,
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        cas.charts.initPlot(this, x);
        this.id = 'ass_rank';
        this.stdStr = function(str,descr){
            if(str && str.length){
                return "<div class='dash-std-val'>"+str+"</div><div class='dash-std-descr'>"+descr+"</div>";
            }else{
                return "---<div class='dash-std-descr'>"+descr+"</div>";
            }
        };
        this.openAssAcomp = function(){
            var elem = this;
            if(!cas.acomp){
                LazyLoad.js('/lib/js/'+cas.src+'/dashboard.acomp.js', function(){
                    cas.acomp.openAssAcomp.apply(elem);
                });
                return;
            }
            cas.acomp.openAssAcomp.apply(elem);
        };
        this.plot.css('min-height',500);
        this.arg = 'mes';
        if(x.index !== null){
            this.plot.css('overflow-x','auto').css('overflow-y','auto');
        }
        this.nicescroll = true;
        this.arg_def = 'Corrente';
        this.refresh = cas.charts.fetchChartData;
        this.makeMenu = cas.charts.menuOpts;
        this.dialogBts = [{title: "Imprimir",action: cas.charts.dialogPrint}];
        this.draw = function(response){
            if(!this.owner.enabled){ return; }
            if( !$.contains(document.documentElement, this.plot[0]) )
                return false;
            this.plot.html('<h4>Assinantes Críticos</h4>');
            this.response = response;
            var list = response.data.list;
            var parthtm;
            
            for (var ass in list){
                parthtm = '';
                var asslink = 'dashboard#'+cas.hashbangify({
                    dashboard:{
                        dashboard:'ri',
                        view:'assinante',
                        ind:'imb',
                        item: list[ass].per+':'+list[ass].cod
                    }
                });
                var nodelink = 'dashboard#'+cas.hashbangify(
                    {dashboard:{
                        dashboard:'ri',
                        view:'node',
                        ind:'imb',
                        item: list[ass].node
                    }
                });
                parthtm += "<li class='acomp-item'>"+
                                "<table class='dash-std-tb'>";
                parthtm += "<thead>";
                    parthtm += "<tr>";
                    parthtm += "<th colspan=3 class='ass_name'>";

                    if (list[ass].myarea) 
                        parthtm += "<span class='ac_act'><span class='ass_comp_bt'>Tratar</span></span>";

                    parthtm += "<a href='" + asslink + "'>"+list[ass].nome+"</a></th>";
                    parthtm += "</tr>";
                parthtm += "</thead>";
                parthtm += "<tbody>";
                parthtm += "<tr>";
                parthtm += "<td rowspan=2>"+ this.stdStr(list[ass].visitas,'Visitas') + "</td>";
                parthtm += "<td>";
                parthtm += this.stdStr("<a href='" + asslink + "'>" + list[ass].cod + "</a>",'Código');
                parthtm += "</td>";
                parthtm += "<td>";
                parthtm += this.stdStr("<a href='"+nodelink+"'>" + list[ass].node + "</a>",list[ass].area);
                parthtm += "</td>";
                parthtm += "</tr>";
                parthtm += "</tbody>";
                parthtm += "</table></li>";
                parthtm = $(parthtm);
                parthtm.appendTo(this.plot);
                parthtm.find('.ass_comp_bt')
                .data('per',list[ass].per)
                .data('cod',list[ass].cod)
                .button({
                    text: false,
                    icons: {
                        primary: "ui-icon-notice"
                    }
                }).click(this.openAssAcomp);
            }
            if(this.menu){
                //contagem
                var abc = list.length + " assinante";
                if (list.length > 1) abc += "s";
                this.menu.find('.ass_count').remove();
                $("<div class='ass_count'>").html(abc).appendTo(this.menu);
            }
            //---------------------------------------
            return this;
        };
        this.controls = [];
        this.owner = x;
        return this;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());