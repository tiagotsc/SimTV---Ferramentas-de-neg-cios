(function($) {
    jQuery.fn.extend({
        myticker: function(options){

            var defaults = {
                v:50,
                s: 5
            },o = {};
            var o = $.extend(defaults, options);

            return this.each(function(){
                var root = $(this), container = false;
                root.show();
                boot();

                function boot(){
                    var s = root.children('li');
                    root.css('position','relative');
                    var t = 0;
                    s.each(function(){
                        t+= $(this).outerWidth();
                    });
                    root.css('width',t);
                    root.css('height','100%');
                    s.each(
                        function(cntr,value){
                            $(this).css('position','absolute');
                            $(this).css('display','block');
                            $(this).css('width','auto');
                            $(this).css('left',0);
                            $(this).css('top',(0 - root.offset().top) + ( (cntr+1) * root.height )  );
                            $(this).data('auxw',$(this).outerWidth());
                        }
                    );
                    s.each(function(cntr,value){
                        $(this).css('top',0);
                        if(cntr == 0){
                            $(this).css('left',0);
                        }else{
                            $(this).css('left',
                                $(this).prev().offset().left
                                + parseInt($(this).prev().data('auxw'))
                                + o.s
                                - root.offset().left
                            );
                        }
                    });
                    s.each(roll);
                }
                function roll(){
                    var a = $(this);
                    if($.contains(document.documentElement, a[0])){
                        a.css('width',parseInt(a.data('auxw')));
                        if(a.offset().left + parseInt(a.data('auxw')) < root.offset().left){
                            var ll = root.children('li:last');
                            var lft = ll.offset().left
                                + parseInt(ll.data('auxw'))
                                + o.s
                                - root.offset().left ;
                            a.css('left',lft);
                            a.appendTo(root);
                        }
                        a.animate({left: '-='+ o.v}, 1000, "linear",roll);
                    }else{
                        //console.log('im dead');
                        a = null;
                        return false;
                    }
                }
                return this;
            });
        }
    });

})(jQuery);


(function($) {
    function diffHM(a,b){
        var d = (a - b);
        return {h: Math.floor(Math.abs(d)/(1000*60*60)), m: Math.floor(  Math.abs(d)%(1000*60*60) / (1000 * 60) ),t:d};
    }
    jQuery.fn.extend({
        nocDisplay: function(options){

            var defaults = {
                link:false,
                scroller:true,
                alwaysScroll:false,
                header:false,
                type:false
            };

            var o = $.extend(defaults, options);
            function smoth(x){
                var sp = "<li class='hitem' style='padding:0 10px'> ~ </li>";
                return "<li class='hitem'>"+x+"</li>" + sp + "<li class='hitem'>"+x+"</li>" + sp;
            }
            return this.each(function(){
                $(window).resize(function(){
                    var waitabit = setTimeout(update,1000);
                });
                var getPerList = function(){
                            var p = cas.get_pref('display_perlist');
                            if(p)
                                for(var i in p){
                                    p[i] = parseInt(p[i]);
                                }
                            return p;
                        };
                var mythread = false, refresher = false, ticker = false, main_ticker = false,servertime=0,perlist = getPerList();
                var resizer = false;
                var list = [];
                var container = $(this);
                container.css('position','relative');
                container.css('overflow','hidden');
                container.html("<ul border='0' class='nocDisplay'></ul>");
                var me = container.children('.nocDisplay');
                me.addClass('nocDisplay');
                boot();
                function recalc(mytt){
                    mytt.remaining = diffHM(mytt.t_deadline,servertime);
                    mytt.remaining.str = ((mytt.remaining.t < 0)?'-':'') + cas.strpad(mytt.remaining.h,'0',2,true)+"h:"+cas.strpad(mytt.remaining.m,'0',2,true)+"m";
                    var theitem = me.find(".nocDisplayItem").filter(function(){
                            return parseInt($(this).data('iid')) === parseInt(mytt.id);
                        });
                    var iclock = theitem.find('.itemprev>ul>.aclock');
                    iclock.html(mytt.remaining.str);
                    if(mytt.remaining.t < (1000 * 60 * 30)){
                        iclock.css('text-shadow', '1px 1px 1px rgb(30,30,30),-1px -1px 1px rgb(30,30,30)');
                        iclock.css('color','red');
                    }else if(mytt.remaining.t < (1000 * 60 * 60)){
                        iclock.css('text-shadow', '1px 1px 1px rgb(30,30,30),-1px -1px 1px rgb(30,30,30)');
                        iclock.css('color','yellow');
                    }else{
                        iclock.css('text-shadow', '1px 1px 1px rgb(30,30,30),-1px -1px 1px rgb(30,30,30)');
                        iclock.css('color','rgb(0,255,0)');
                    }
                    mytt = null; theitem = null;
                }
                function boot(){
                    clearInterval(mythread);
                    fetch();
                    mythread = setInterval(fetch,60000);
                }
                function refresh(){
                    for(var i in list){
                        recalc(list[i]);
                    }
                }
                function main_tick(revert){
                    if(revert){
                        me.children('.row:last').slideUp(
                            function(){
                                if(me.find('.header').length > 0){
                                    $(this).remove();
                                    me.find('.header').after($(this));
                                    $(this).slideDown();
                                }else{
                                    $(this).prependTo(me).slideDown();
                                }
                            }
                        );
                    }else{
                        me.children('.row:first').slideUp(
                            function(){
                                $(this).remove();
                                $(this).appendTo(me).slideDown();
                            }
                        );
                    }
                }
                function tick(){
                    me.find('.vticker').each(
                        function(){
                            var vticker = $(this);
                            var countto = (
                                                (typeof $(this).children('li:first').data('countto') !== 'undefined')
                                                ?
                                                    parseInt($(this).children('li:first').data('countto'))
                                                :
                                                    0
                                                );
                            if(countto > 0){
                                var counter = (
                                                (typeof $(this).children('li:first').data('counter') !== 'undefined')
                                                ?
                                                    parseInt($(this).children('li:first').data('counter'))
                                                :
                                                    0
                                                ) + 1;
                                $(this).children('li:first').data('counter',counter);
                            }else{
                                counter = 0;
                            }
                            if(counter === countto){
                                $(this).children('li:first').data('counter',0);
                                $(this).children('li:first').slideUp(
                                    function(){
                                        $(this).remove();
                                        $(this).appendTo(vticker).slideDown();
                                    }
                                );
                            }
                        }
                    );
                }
                var perdialog = null;
                function perheaderclick(){
                    if(!perdialog)
                        fetchperdialog();
                    else
                        perdialog.dialog('open');
                }
                function optclick(){
                    $(this).toggleClass('selected');
                }
                function objIndexOf(o,val) {  
                    for (var i in o) {
                        if(o[i] === val){
                            return i;
                        }
                    }
                    return -1;
                }
                function fetchperdialog(){
                    cas.ajaxer({
                        method:'GET',
                        sendto:'display/perlist',
                        andthen:function(x){
                            perdialog = $("<div class='nocdisplaydialogper'>");
                            var opts = $("<ul class='nocdisplaydialogperselect'>").appendTo(perdialog);
                            for(var i in x.data.per){
                                $("<li class='"+
                                        ((objIndexOf(perlist,parseInt(x.data.per[i].id)) > -1)
                                            ?'selected'
                                            :''
                                        )+"' value='"+
                                        x.data.per[i].id+"'>"+x.data.per[i].name+"</li>"
                                ).click(optclick).appendTo(opts);
                            }
                            perdialog.appendTo('body').dialog({
                                autoOpen: false,
                                modal: true,
                                closeOnEscape: true,
                                dialogClass: 'noTitleDialog',
                                width: 300,
                                height:420,
                                resizable: false,
                                close: function (){
                                    perlist = [];
                                    var z = $(this).find('.nocdisplaydialogperselect>.selected');
                                    z.each(function(){
                                        perlist.push(parseInt($(this).attr('value')));
                                    });
                                    cas.set_pref('display_perlist',perlist);
                                    clearInterval(mythread);
                                    fetch();
                                    mythread = setInterval(fetch,60000);
                                },
                                buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ]
                            }).dialog('open');
                        }
                    });
                }
                function update(){
                    me.empty();
                    container.find('.scroller').remove();
                    
                    var ws = {id:50,ini:100,per:50,type:60,loc:0,descr:0,st:100,prev:100},
                        mwidth = me.width(),ls = null;
                    for(var l in ws)
                        if(ws[l])
                            mwidth -= (ws[l] + 2);
                    mwidth -= 4;
                    ws.loc = mwidth * (3/5);
                    ws.descr = mwidth * (2/5);
                    if(o.header){
                        me.append(
                            $("<li class='nocDisplayItem header'>"+
                                "<span class='ndi itemid'>Nº</span>"+
                                "<span class='ndi itemini'>INICÍO</span>"+
                                "<span class='ndi itemper openperdialog"+
                                    ((perlist && perlist.length)?" filter' title='[FILTRO ATIVO] ":"' title='")+
                                    "Clique para selecionar permissoras'>PER</span>"+
                                "<span class='ndi itemtype'>TIPO</span>"+
                                "<span class='ndi itemloc'>LOCALIZAÇÃO</span>"+
                                "<span class='ndi itemdescr'>DESCRIÇÃO</span>"+
                                "<span class='ndi itemst'>STATUS</span>"+
                                "<span class='ndi itemprev'>PREVISÃO</span>"+
                            "</li>").data('tindex','header'));
                        me.find('.itemper').click(perheaderclick);
                    }
                    for(var i in list){
                        var myper = ((list[i].pper)?list[i].pper:'SIM');
                        ls = {};
                        var theitem = 
                            $("<li tindex='"+i+"' class='nocDisplayItem row'>"+"</li>")
                                .data('tindex',i)
                                .data('iid',list[i].id)
                                .appendTo(me),
                                lastOne = null;
                        
                        for(l in ws)
                            ls[l] = $("<span class='ndi item"+l+"'>&nbsp;</span>").appendTo(theitem).css('width',ws[l]);
                        
                        if(o.link)
                            ls['id'].html("<a target='_blank' href='eventos#"+cas.hashbangify({tt:list[i].id})+"'>"+list[i].id+"</a>");
                        else
                            ls['id'].html(list[i].id);
                        ls['ini'].html(list[i].ini_d);
                        //------------------------
                        //------------------------
                        //------------------------
                        var htm = "<ul class='hticker'>";
                        ls['per'].attr('title',myper);
                        var per_s = myper.split(', ');
                        for(var j in per_s){
                            htm += "<li class='hitem'>"+per_s[j]+"</li>";
                        }
                        ls['per'].html(htm+"</ul>");
                        var w = 0;
                        ls['per'].find('.hticker>li').each(function(){
                            w += $(this).outerWidth();
                        });
                        if(w > ls['per'].width()){
                            ls['per'].find('.hticker').append("<li class='hitem' style='padding:0 10px'> ~ </li>");
                            ls['per'].find('.hticker').myticker();
                        }else{
                            ls['per'].empty();
                            ls['per'].html("<div>"+myper+"</div>");
                        }
                        //------------------------
                        //------------------------
                        //------------------------
                        ls['type'].html(list[i].t_abbr);
                        var htm = "<ul class='hticker'>", htmC = "<ul class='hticker'>";
                        var ns = [], nsC = [];
                        for(var j in list[i].locations){
                            if(list[i].locations[j].location_type !== 'cidade'){
                                htm += "<li class='hitem'>"+list[i].locations[j].location+"</li>";
                                ns.push(list[i].locations[j].location);
                            }else{
                                htmC += "<li class='hitem'>"+list[i].locations[j].location+"</li>";
                                nsC.push(list[i].locations[j].location);
                            }
                        }
                        if(ns.length){
                            ls['loc'].attr('title',ns.join('\n'));
                            ls['loc'].html(htm+"</ul>");
                        }else{
                            ls['loc'].attr('title',nsC.join('\n'));
                            ls['loc'].html(htmC+"</ul>");
                        }
                        var w = 0;
                        ls['loc'].find('.hticker>li').each(function(){
                            w += $(this).outerWidth();
                        });
                        if(w > ls['loc'].width()){
                            ls['loc'].find('.hticker').append("<li class='hitem' style='padding:0 10px'> ~ </li>");
                            ls['loc'].find('.hticker').myticker({s:2});
                        }
                        //------------------------
                        //------------------------
                        //---------descr----------

                        var htm = "<ul class='hticker'>";
                        var descr_s = list[i].descr.split(' ');
                        for(var j in descr_s){
                            htm += "<li class='hitem1'>"+descr_s[j]+"</li>";
                        }
                        ls['descr'].html(htm+"</ul>");
                        ls['descr'].attr('title',list[i].descr);
                        var w = 0;
                        ls['descr'].find('.hticker>li').each(function(){
                            w += $(this).outerWidth();
                        });
                        if(w > ls['descr'].width()){
                            ls['descr'].find('.hticker').html(smoth(list[i].descr));
                            ls['descr'].find('.hticker').myticker();
                        }else{
                            ls['descr'].empty();
                            ls['descr'].html("<div style='padding-left:5px'>"+list[i].descr+"</div>");
                        }
                        htm = '';

                        ls['st'].html(list[i].stname);
                        ls['prev'].html(
                                "<ul class='vticker'>"+
                                    "<li class='al aclock'></li>"+
                                    "<li class='al adeadline'>"+list[i].deadline+"</li></ul>");
                        ls['prev'].find('.aclock').data('countto',2);
                        ls['prev'].find('.adeadline').data('countto',1);
                        recalc(list[i]);
                        theitem = null;
                    }
                    if(ls === null){
                        ls = {};
                        for(l in ws)
                            ls[l] = me.find('.item'+l);
                    }
                        
                    for(l in ls){
                        var left = 0;
                        if(lastOne)
                            left = ls[lastOne].position().left + ls[lastOne].outerWidth();

                        me.find('.item'+l).css('width',ws[l]).css('left',left);
                        lastOne = l;
                    }

                    clearInterval(ticker);
                    ticker = setInterval(tick, 1000 * 5);
                    clearInterval(main_ticker);
                    if(o.alwaysScroll || (list && list.length > 1 && me.height() > container.height())){
                        main_ticker = setInterval(main_tick, 1000 * 10);
                    }
                    if(o.scroller && (list && list.length > 1 && me.height() > container.height())){
                        var scrl = $("<div class='scroller'>"+
                                "<span class='scrollb up'>&#x25B2;</span>"+
                                "<span class='scrollb down'>&#x25BC;</span>"+
                            "</div>");
                        scrl.appendTo(container);
                        scrl.find('.up').on('click',function(){
                            if(main_ticker){
                                clearInterval(main_ticker);
                                main_ticker = setInterval(main_tick, 1000 * 10);
                            }
                            main_tick();
                        });
                        scrl.find('.down').on('click',function(){
                            if(main_ticker){
                                clearInterval(main_ticker);
                                main_ticker = setInterval(main_tick, 1000 * 10);
                            }
                            main_tick(true);
                        });
                        scrl = null;
                    }

                }
                function hideme(){
                    container.prepend('<div class="mutethem"><div class="loading-huge"></div></div>');
                }
                function showme(){
                    container.children('.mutethem').remove();
                }
                function fetch(){
                    cas.ajaxer({
                        dataType: 'json',
                        method:'GET',
                        sendme: {
                            type: o.type,
                            perlist: perlist
                        },
                        sendto: 'display/update_display',
                        andthen: function(x){
                            servertime = parseInt(x.data.time);
                            list = x.data.d;
                            update();
                        }
                    });
                }
                return this;
            });
        }
    });

    jQuery.fn.extend({
        nocDisplay: jQuery.fn.nocDisplay
    });

})(jQuery);
