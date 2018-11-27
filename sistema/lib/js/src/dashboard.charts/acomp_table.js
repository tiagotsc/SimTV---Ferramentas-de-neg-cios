(function(){
    
    var _id = 'acomp_table',
        _parent = 'ass_rank',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new cas.charts.chartFactory.ass_rank(x);
        chart.arg = 'acomp_status';
        delete chart.arg_def;
        chart.id = 'acomp_table';
        chart.draw = function(response){
            if(!this.owner.enabled){ return; }
            if( !$.contains(document.documentElement, this.plot[0]) )
                return false;
            this.plot.html('<h4>Acompanhamentos</h4>');
            this.response = response;
            var list = response.data.list;
            var parthtm;
            var mine = $('<ul>').appendTo(this.plot);
            
            var theirs = $('<ul>').hide().appendTo(this.plot);
            
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
                var me = $("<li class='acomp-item'>"), tr;
                
                
                if (list[ass].myarea){
                    me.appendTo(mine);
                    me = $("<table class='dash-std-tb'>").appendTo(me);
                    tr = $('<tr>').appendTo($('<thead>').appendTo(me));
                    tr = $("<th class='ass_name' colspan=3>").appendTo(tr);
                    
                    var bt = 
                    $("<span class='ass_comp_bt'>Tratar</span>")
                        .data('per',list[ass].per)
                        .data('cod',list[ass].cod)
                        .data('acomp',list[ass].acomp)
                        .button({
                            text: false,
                            icons: {
                                primary: "ui-icon-notice"
                            }
                        }).click(this.openAssAcomp);
                    $("<span class='ac_act'>").append(bt).appendTo(tr);
                    
                }else{
                    me.appendTo(theirs);
                    me = $("<table class='dash-std-tb'>").appendTo(me);
                    tr = $('<tr>').appendTo($('<thead>').appendTo(me));
                    tr = $("<th class='ass_name' colspan=3>").appendTo(tr);
                }
                tr.append("<a href='" + asslink + "'>"+list[ass].nome+"</a>");
                $("<tr>"+
                    "<td>"+this.stdStr(list[ass].stage.toUpperCase(),'Etapa')+"</td>"+
                    "<td>"+this.stdStr(list[ass].status.toUpperCase(),'Status')+"</td>"+
                    "<td>"+this.stdStr(list[ass].last_update,'Última atualização')+"</td>"+
                "</tr>").appendTo(me);
            }
            //contagem
            
            theirs = theirs.children().length;
            if(theirs){
                $("<a title='Mostrar mais' class='acomp-more'>...</a>").click(function(){
                    var z = $(this).next().toggle();
                    var y = $(this).closest('.hasNiceScroll');
                    
                    if(z.is(':visible')){
                        y.animate({
                            scrollTop:'+=100'
                        });
                    }
                }).insertAfter(mine);
            }
            
            if(this.menu){
                mine = mine.children().length;
                this.menu.find('.ass_count').remove();
                $("<div class='ass_count'>").html(
                    mine+' a tratar'+
                        ((theirs)?', '+theirs+' outros':'')
                ).appendTo(this.menu);
            }
            
            //---------------------------------------
            return this;
        };
        chart.controls = ['acomp_plus'];
        chart.owner = x;
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());