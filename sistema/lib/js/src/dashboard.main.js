var cas = window.cas;
cas.controller = function(){
    
    window.dashboard = {};
    window.dashTitle = null;
    var dashBadges = [];
    var hierarchy = {
        1: ['monitor'], // dashboards
        2: [null], // indicadores
        3: ['cidade','cluster'], // cidades
        4: ['node','assinante','tecnico','mes','semana','dia'] // variado
    },opts = {},opt = {};
    
    for(var i in hierarchy){
        opts[i] = [];
        opt[i] = null;
    }

    function viewHierarchy(v){
        for(var i in hierarchy){
            if(hierarchy[i].indexOf(v) > -1){
                return i;
            }
        }
        return null;
    }
    function searchInOpts(i,x){

        for(var j in opts[i]){
            if(opts[i][j].id === x){
                return j;
            }
        }
        return null;
    }
    $(window).on('hashchange', function(){
        var d_now =  cas.args.dashboard;
        var a = ['view','item','dashboard','ind'];
        if(!d_now){
            main(true);
        }else{
            for(var i in a){
                if(dashboard[a[i]] !== d_now[a[i]]){
                    main(true);
                    break;
                }
            }
        }
    });
    function main(reset){
        $('.fullScreenDialog').find('.fSCDClose').trigger('click');
        if(reset){
            cas.charts.killChartView(cas.charts.chartTree);
        }
        dashboard = $.extend({},cas.args.dashboard);

        //~~~~~~~~~~~~~~~~~~~~~~
        if(!dashboard.dashboard){
            dashboard = {};
        }
        if(dashboard.view === 'monitor'){
            delete dashboard.ind;
        }
        cas.args.dashboard = $.extend({},dashboard);;
        cas.pushArgs();
        //~~~~~~~~~~~~~~~~~~~~~~
        dashBadges = [];
        
        if(reset){
            delete cas.args.charts;
            delete cas.args.zoomIn;
            delete cas.args.histOf;
            cas.pushArgs();
        }
        loadView(reset);
    }
    var mticdt;
    function mticdtIn(){
        clearTimeout(mticdt);
        var me = $(this);
        mticdt = setTimeout(function(){
            me.stop(true,true).animate({
                left: 0, top: 0
            },700);
        },300);
    }
    function mticdtOut(){
        clearTimeout(mticdt);
        var me = $(this);
        mticdt = setTimeout(function(){
            me.stop(true,true).animate({
                left: -30, top: -20
            },700);
        },300);
    }
    function redrawAll(){
        var charts = cas.charts.chartView();
        for(var i in charts){
            if(charts[i].chart && charts[i].chart.type === 'highchart')
                charts[i].chart.chartObject.setSize(
                    charts[i].chart.plot.width(),
                    charts[i].chart.plot.height()
                );
        }
    }
    function updateCharts(reset, units){
        if( ! $('#dash-body').length )
            $("<div id='dash-body'>")
                .appendTo(
                    $("<div id='dash-body-wrapper'>").appendTo('#container')
                );
        if($('#dash-badges').is(':visible'))
            $('#dash-body').append(
                $("<div class='dash-toggler'>&zwnj;</div>")
                    .hover(mticdtIn,mticdtOut).css('left',-30).css('top',-20).click(function(){
                        $(this).toggleClass('right');
                        $('#dash-badges').toggle();
                        redrawAll();
                    })
            );
        if( !dashboard.dashboard || !dashboard.view ){
            return false;
        }

        var charts = cas.charts.chartView(units);
        
        //--------------------
        if(reset || !cas.args.charts){
            cas.args.charts = [];
            for(var i in charts){
                cas.args.charts.push({});
            }
            //cas.pushArgs();
        }
        
        //--------------------
        for(var i in charts){
            charts[i].update();
        }
    }

    function rootLinks(){
        var i,h,s;
        $('.dashboard_list').remove();
        var root = $("<ul class='dashboard_list'>").appendTo('#yetanotherwrapper');
        $('#container').hide();
        for(i in opts[1]){
            h = opts[1][i];
            s = cas.hashbangify(
                            {dashboard: 
                                {
                                    dashboard: h.id,
                                    view: 'monitor'
                                }
                            }
                        );
            $("<li>"+
                "<a class='dashboard_link' href='#"+s+"'>"+
                    "<div class='dashboard_icon "+h.id+"_icon'></div>"+
                    "<h3 class='dashboard_name'>"+h.name+"</h3>"+
                "</a>"+
            "</li>").appendTo(root);
        }
    }
    function loadView(reset){
        cas.ajaxer({
            method:'GET',
            sendme:dashboard,
            etc:{reset:reset},
            sendto:'dashboard/view',
            andthen: viewData
        });
    }
    cas.isValidArea = function(x){
        if(x === 'SIM' || x === 'SIM TV')
            return true;
        for(var i in opts[3]){
            if(opts[3][i].id === x || opts[3][i].name === x){
                return true;
            }
        }
        return false;
    };
    cas.areaName = function(x){
        if(x === 'SIM' || x === 'SIM TV'){
            return 'SIM TV';
        }
        for(var i in opts[3]){
            if(opts[3][i].id === x){
                return opts[3][i].name;
            }
        }
        return null;
    };
    cas.areaAbbr = function(x){
        if(x === 'SIM' || x === 'SIM TV')
            return 'SIM';
        for(var i in opts[3]){
            if(opts[3][i].name === x){
                return opts[3][i].id;
            }
        }
        return null;
    };
    function viewData(x){
        $('.hasNiceScroll').getNiceScroll().remove();
        $('#container').empty();
        $(document.documentElement).animate({scrollTop:0},700);
        dashTitle = null;

        if(x.data.title){
            dashTitle = x.data.title;
        }

        opts[1] = x.data.dashboard;
        opts[2] = x.data.ind;
        opts[3] = x.data.area;
        
        cas.charts.chartSelector.mes = {l: x.data.mes,x: x.data.currmonth};
        if(x.data.acomp_status)
            cas.charts.chartSelector.acomp_status = 
                        {
                            l: x.data.acomp_status,
                            x: x.data.acomp_status[0].value
                        };
        cas.charts.chartSelector.dia = {x: x.data.dia};

        if(!dashboard.view){
            rootLinks();
        }else{
            $('.dashboard_list').remove();
            $('#container').show();
        }
        breadCrumbMaker(x.data.path);
        pageTitle();
        badgeMaker(x.data.badges);
        updateCharts(x.etc.reset, x.data.units);
    }
    function pageTitle(){
        var ts = ['Dashboard'];
        $('.dash_breadcrumb').children('.bread').each(function(){
            var t = $(this).children('a').text().trim();
            if(t.length)
                ts.push(t);
        });
        
        window.document.title = ts.reverse().join(' : ');
    }
    function badgeMaker(badges,units){
        if(!badges || badges.length === 0)
            return false;
        dashBadges = badges;
        var root = $("<div id='dash-badges'>")
                        .appendTo('#container'),li,table,colspan,badge;
        root = $('<ul>').appendTo(root);
        for(var i in badges){
            badge = badges[i];
            li = $("<li class='dash_badge'>").data('badge',badge.id).appendTo(root);
            table = $("<table class='dash-std-tb'>").appendTo(li);
            colspan = badge.vals.length - 1;
            colspan = ((colspan > 1)?" colspan='"+colspan+"' ":'');
            $("<thead>").append("<tr><th"+colspan+">"+badge.title+"</th></tr>").appendTo(table);
            $("<tr>").append(
                $("<td"+colspan+">").append(
                    $("<div class='dash-std-val'>").html(
                        ((typeof badge.vals[0].val === 'undefined')
                            ?"<div class='badge-placeholder'></div>"
                            :badge.vals[0].val)
                    ).data('val',badge.vals[0].id)
                ).append("<div class='dash-std-descr'>"+badge.vals[0].descr+"</div>")
            ).appendTo(table);
            fetchBadge(badge.id,badge.vals[0].id);
            var tr = $('<tr>').appendTo(table);
            for(var j = 1; j < badge.vals.length; j++){

                $("<td>").append(
                    $("<div class='dash-std-val'>").html(
                            ((typeof badge.vals[j].val === 'undefined')
                            ?"<div class='badge-placeholder'></div>"
                            :badge.vals[j].val)
                        ).data('val',badge.vals[j].id)
                ).append("<div class='dash-std-descr'>"+badge.vals[j].descr+"</div>").appendTo(tr);
                fetchBadge(badge.id,badge.vals[j].id);
            }
        }
    }
    var scrollbT;
    $(window).scroll(function(){
        
        if(!$('#dash-badges').length)
            return false;
        
        clearTimeout(scrollbT);
        scrollbT = setTimeout(function(){
            if($(window).scrollTop() + $('#head-wrapper').outerHeight() > $('#dash-badges').offset().top 
                && $('#container').is(':visible')
                && $('#dash-badges').is(':visible')
            ){

                var ul = $('#dash-badges>ul');
                var min = $(window).scrollTop() - $('#dash-badges').offset().top;
                var top = Math.max(0,min + $('#head-wrapper').outerHeight());
                var max = $('#dash-badges').height() - ul.outerHeight() - 10;
                if(top < max){
                    ul.css({
                            width: ul.width(),
                            height: ul.height(),
                            position:'absolute'
                        }).animate({top: top},300);
                }else if(ul.attr('style')){
                        ul.css({
                                width: ul.width(),
                                height: ul.height(),
                                position:'absolute'
                            }).animate({top: max},300);
                }
            }else{
                $('#dash-badges>ul').stop(true,true).removeAttr('style');
            }
        },100);
    });
    function fetchBadge(badge,val){
        var myval = badgeValFind(badge,val);
        
        if(!myval)
            return false;
        
        if(typeof myval.val !== 'undefined')
            return false;
        cas.ajaxer({
            method:'GET',
            sendme:{
                dashboard: dashboard,
                badge: badge,
                val: val
            },etc:{badge: badge,val: val},
            sendto:'dashboard/badge_val',
            andthen:function(x){
                for(var i in x.data.badges)
                    badgeFill(i,x.data.badges[i]);
            }
        });
    }
    function badgeFill(badge, x){
        var a = $('#dash-badges').find('.dash_badge').filter(function(){
            return $(this).data('badge') === badge;
        }), c, myval;
        for(var i in x){
            myval = badgeValFind(badge,i);
            if(myval){
                c = a.find('.dash-std-val').filter(function(){
                    return $(this).data('val') === i;
                }).html(x[i].val);
                myval.val = x[i].val;
                if(x[i].descr){
                    c.next('.dash-std-descr').html(x[i].descr);
                    myval.descr = x[i].descr;
                }
            }
        }
    }
    function badgeValFind(badge,val){
        var mybadge = null;
        for(var i in dashBadges)
            if(dashBadges[i].id === badge)
                mybadge = dashBadges[i];
        
        if(mybadge)
            for(var j in mybadge.vals)
                if(mybadge.vals[j].id === val)
                    return mybadge.vals[j];
        return null;
    }
    function breadCrumbMaker(path){
        if(!dashboard.view)
            return false;
        $('.dash_breadcrumb').remove();
        var root = $("<ul class='dash_breadcrumb'>")
                        .insertBefore('#container')
                        .append("<li class='homebread'>"+
                                    "<a href='dashboard'>&zwnj;</a></li>");
        var vH = viewHierarchy(dashboard.view), l, j, o, ul, url, x;
        opt[1] = dashboard.dashboard;
        if(dashboard.ind){
            opt[2] = dashboard.ind;
        }
        if(hierarchy[3].indexOf(dashboard.view) > -1){
            opt[3] = dashboard.item;
        }else if(path){
            if(!path.area)
                path.area ={id:'SIM','name':'SIM TV'};
            opt[3] = path.area.id;
            opt[4] = dashboard.item;
            o = searchInOpts(4,opt[4]);
            if(o === null){
                opts[4] = 
                    [{
                        id: dashboard.item,
                        name: dashTitle,
                        view: dashboard.view,
                        parent: path.area.id
                    }].concat(opts[4]);
            }
        }



        for(var i=1; i <= vH; i++){
            if(opt[i]){
                o = searchInOpts(i,opt[i]);
                if(o !== null){
                    l = $("<li class='bread'>").data('level',i)
                        .append("<a>"+opts[i][o].name+"</a>")
                        .appendTo(root);
                    l.hover(lBreadCrumbIn,lBreadCrumbOut);
                    ul = $("<ul class='dash_breadcrumb_sub'>").appendTo(l);
                    for(j = 0; j < opts[i].length; j++){
                        if(i <= 3 
                            || ( 
                                    opts[i][j].view === dashboard.view 
                                    && opts[i][j].parent === path.area.id
                                ) 
                        ){
                            x = {dashboard:{view: opts[i][j].view}};

                            if(opts[i][j].view === 'monitor'){
                                x.dashboard.dashboard = opts[i][j].id;
                            }else if (dashboard.dashboard) {
                                x.dashboard.dashboard = dashboard.dashboard;
                            }

                            if(i === 2){
                                x.dashboard.ind = opts[i][j].id;
                                if(parseInt(vH) === 3){
                                    x.dashboard.item = dashboard.item;
                                }else{
                                    x.dashboard.item = path.area.id;
                                }
                            }else if(dashboard.ind){
                                x.dashboard.ind = dashboard.ind;
                            }

                            if(i >= 3){
                                x.dashboard.item = opts[i][j].id;
                            }

                            url = cas.hashbangify(x);
                            if(j === parseInt(o))
                                l.children('a').attr('href',"dashboard#"+url);

                            ul.append("<li"+
                                ((j === parseInt(o))?" class='current_bread'":'')+">"+
                                    "<a href='dashboard#"+url+"'>"+
                                        opts[i][j].name+"</a></li>");

                        }
                    }
                }
            }
        }
        var putAfter = null;
        if(path && path.others)
            for(var i in path.others){
                var item = path.others[i];
                if(!putAfter)
                    putAfter = root.find('.bread').filter(function(){
                        return parseInt($(this).data('level'), 10) === 3;
                    });

                putAfter = $("<li class='bread'>").data('level',i)
                    .append($("<a>"+item.name+"</a>")
                            .attr('href',"dashboard#"+cas.hashbangify({
                                dashboard:{
                                    dashboard: (item.dashboard)?item.dashboard:dashboard.dashboard,
                                    ind: (item.ind)?item.ind:dashboard.ind,
                                    view: item.view,
                                    item: item.id
                                }
                            }))
                    )
                    .insertAfter(putAfter);

                putAfter.hover(lBreadCrumbIn,lBreadCrumbOut);
            }
    }
    var lbt;
    function lBreadCrumbIn(){
        clearTimeout(lbt);
        var me = $(this);
        lbt = setTimeout(function(){
            var x = me.find('.dash_breadcrumb_sub');
            $('.dash_breadcrumb_sub').not(x).fadeOut();
            x.fadeIn();
        },500);
    }
    function lBreadCrumbOut(){
        clearTimeout(lbt);
        var me = $(this);
        lbt = setTimeout(function(){
            me.find('.dash_breadcrumb_sub').fadeOut();
        },500);
    }
    main(false);
};