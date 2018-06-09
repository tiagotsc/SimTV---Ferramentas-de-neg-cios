cas.controller = function(){
    var
        howslow = 0,
        slowtimer,
        os = {corp:[],ind:[]},
        stopAt = 0,
        tz,
        backlog_thread = false,
        tinytd = true;
    var t0 = 0;
    
    $('#remove_dialog').dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: true,
        width: 500,
        height:200,
        dialogClass: 'noTitleDialog',
        resizable: false,
        open: function(){
            $('#remove_obs').val("");
        },
        close: function(){

        },
        buttons: {
            "Salvar": function(){
                removeFromBacklog($('#remove_obs').val());
            },
            "Cancelar": function(){
                $('#remove_dialog').dialog('close');
            }
        }
    });

    function schedule(){
        clearInterval(backlog_thread);
        backlog_thread = setInterval(refreshBacklog, 60000);
    }
    function histCheck(dCheck){
        if(typeof dCheck === 'undefined')
            dCheck = true;
        if($('#hist_checkall').prop('checked'))
            $('.os_check').prop('checked',true);
        else if(dCheck)
            $('.os_check').prop('checked',false);
    }
    function attOSs(){
        var oss = [];
        $('.os_check').each(function(){
            if($(this).prop('checked'))
                oss.push(
                    {
                        per: $(this).attr('per'),
                        os: $(this).attr('os'),
                        svc: $(this).attr('svc')
                    }
                );
        });
        return oss;
    }
    $('#remove_from_backlog').on('click',function(){
        if(confirm("Ao remover as ordens do Backlog elas serão devolvidas para a análise do NOC. Clique em 'Ok' somente se você entende que tais ordens não são pertinentes à Rede Interna e PRECISAM ser tratadas por uma outra área.")){
            $('#remove_dialog').dialog('open');
        }
    });
    function onF(){
        $('#remove_dialog').dialog('close');
        cas.showthis('body');
    }
    function removeFromBacklog(obs){
        var oss = attOSs();
        $('#hist_checkall').prop('checked',false);
        $('#hist_checkall').trigger('change');
        cas.hidethis('body');
        cas.ajaxer({
            sendto: 'backlog_r/remove_acks',
            sendme:{'oss':oss,'obs':obs},
            error:onF,
            complete:onF,
            andthen: function(data){
                refreshBacklog();
                cas.makeNotif('success','Todas as ordens foram encaminhadas para uma nova análise do NOC.');
            }
        });
    }


    function osSort(a,b){
        if(a.ingr < b.ingr)
            return -1;
        if(a.ingr > b.ingr)
            return 1;
        return 0;
    }
    function refreshBacklog(){
        stopAt = 0;
        t0 = new Date();
        $('#loadmore').hide();
        cas.hidethis('body');
        $('#bklog_hist>tbody').empty();
        cas.ajaxer({
            sendto:'backlog_r/backlog_update',
            method:'GET',
            etc:{
                't0':t0
            },
            sendme:
                {
                    'per':sList['per'],
                    'st':sList['status'],
                    'corp':true,
                    'show_future': $('#show_future').prop('checked')
                },
            andthen:
                function(x){
                    os.corp = x.data.oss;
                    os.corp.sort(osSort);
                    cas.ajaxer({
                        sendto:'backlog_r/backlog_update',
                        method:'GET',
                        etc: {'t0':t0},
                        sendme: {
                            'per': sList['per'],
                            'st': sList['status'],
                            'corp':false,
                            'show_future': $('#show_future').prop('checked')
                        },
                        andthen:
                            function(z){
                                cas.showthis('body');
                                os.ind = z.data.oss;
                                os.ind.sort(osSort);
                                $('#loadmore').trigger('click');
                            }
                    });
                }
        });
    }
    $('#loadmore').click(function(){
        stopAt += 10;
        notsoslow();
        tz = new Date();
        $('#hist_count,#time_loaded').empty();
        slowtimer = setInterval(amislow,100);
        $(this).hide();
        getNewOs('corp',0,t0.getTime());
        getNewOs('ind',0,t0.getTime());
    });
    function getNewOs(list,i,t1){
        if(typeof i === 'undefined')
            i = 0;
        if(t1 === t0.getTime()){
            if(typeof os[list][i] === 'undefined' || $('#bklog_hist>tbody>tr').length >= stopAt){
                var atbottom =
                    (
                        ( os['ind'].length < 1 || os['ind'][(os['ind'].length - 1)].loaded !== false)
                        &&
                        ( os['corp'].length < 1 || os['corp'][(os['corp'].length - 1)].loaded !== false )
                    );
                if(
                    atbottom
                    || $('#bklog_hist>tbody>tr').length >= stopAt
                ){
                    notsoslow();
                    trCount(tz);
                    if(!atbottom)
                        $('#loadmore').show();
                    if(atbottom)
                        cas.makeNotif('warning','Fim da pilha de pendências atingido.');
                }
                return false;
            }else{
                if(os[list][i].loaded === false){
                    osFetch(i,list,t1);
                }else if(os[list][i].loaded === true || os[list][i].loaded === null){
                    getNewOs(list,i+1,t1);
                }
            }
        }
    }
    function moreOBS(){
        var td = $(this).closest('td'), t = td.find('.os-obs-txt');
        if(t.length > 1){
            t.not(td.find('.os-obs-txt').last()).remove();
        }else
            reloadOBS(td);
    }
    function reloadOBS(td){
        var x = td.attr('data-id');
        x = x.split(':');
        var os = {os:x[0],per:x[1],svc:x[2]};

        cas.ajaxer({
            method:'GET',
            sendme:os,
            sendto:'backlog_r/load_obs',
            andthen:function(x){
                if(!$.contains(document.documentElement, td[0]) )
                    return false;
                makeOSOBS(td.parent(),x.data.os);
            }
        });
    }

    function sendOBS(e){
        if (parseInt(e.which) === 13 && !e.shiftKey && !e.ctrlKey){
            e.preventDefault();
            var txt = $(this).val();
            if(txt && txt.length){
                var td = $(this).closest('td'), os = td.attr('data-id');
                os = os.split(':');
                $(this).addClass('sendingOBS').off('keydown',sendOBS);
                cas.ajaxer({
                    sendme:{os:os[0],per:os[1],svc:os[2],txt:txt},
                    sendto:'backlog_r/save_obs',
                    andthen:function(){
                        reloadOBS(td);
                    }
                });
            }
        }
    }
    
    function makeOSOBS(tr,os){
        tr.find('.os-obs').remove();
        var elem = 
            $("<td class='bklogtbtd os-obs'>").attr('data-id',os.os+':'+os.per+':'+os.svc)
                .appendTo(tr);
        if(os.b_obs.length)
            $("<a class='os-obs-more' title='Mostrar mais/menos'>...</a>").click(moreOBS).appendTo(elem);
        for(var i in os.b_obs){
            $("<div class='os-obs-txt'>").html(
                "["+os.b_obs[i].t+"] <b>"+os.b_obs[i].user+"</b>: <i>"+os.b_obs[i].obs+"</i>"
            ).appendTo(elem);
        }
        var t = 
            $("<div class='os-obs-input'>").appendTo(elem);
        $('<textarea placeholder="Nova observação" rows=2>').keydown(sendOBS).appendTo(t);
    }
    function osFetch(i,list,t1){
        if(typeof i === undefined)
            i = 0;

        if(typeof os[list][i] !== 'undefined'){
            cas.ajaxer({
                method:'GET',
                etc:{
                    't1':t1
                },
                sendme:{
                    'os':os[list][i],
                    'show_future': $('#show_future').prop('checked')
                },
                sendto:'backlog_r/osser',
                andthen:function(x){
                    if(x.etc.t1 === t0.getTime() && $('#bklog_hist>tbody>tr').length < stopAt){
                        if(x.data.os){
                            os[list][i].loaded = true;
                            var tr = '';
                            tr += "<tr ackid="+x.data.os.ackID+" class='tr"+ list +
                                ((x.data.os.noc && 1 == 2)?" trnoc'":"'")+">";
                            
                            if(x.data.os.mygroup){
                                tr +=
                                    "<td class='bklogtbtd tinytd'>"+
                                            "<input class='os_check' type='checkbox' os='"+x.data.os.os+"' per='"+
                                                x.data.os.per + "' svc='"+x.data.os.svc.toLowerCase()+"'/>"
                                    +"</td>";
                            }else{
                                tr += "<td class='bklogtbtd tinytd'></td>";
                            }

                            x.data.os.os_status = x.data.os.os_status.toUpperCase();
                            if(x.data.os.os_status === 'PENDENTE'){
                                tr += "<td class='bklogtbtd tdtime1'>"+
                                        "Encaminhado por <i>"+
                                            x.data.os.author+
                                        "</i> em:<br><b>"+
                                            ((x.data.os.since)
                                                ?x.data.os.since:'---')+"<b>"+
                                "</td>";
                            }else{
                                tr += "<td class='bklogtbtd tdtime1'>Agenda<br> <b>"+((x.data.os.ag)?x.data.os.ag:'---')+"</b></td>";
                            }

                            tr += "<td class='bklogtbtd tdos'>"+
                                x.data.os.cid+"<br>"+
                                "<a target='_blank' href='os#os="+x.data.os.os+"&per="+x.data.os.per+
                                        "&svc="+x.data.os.svc.toLowerCase()+"'>"+
                                    x.data.os.svc+" - "+x.data.os.os +"</a>"+
                                "<br><i>"+x.data.os.os_status+"</i>"+
                            "</td>";
                            tr += "<td class='bklogtbtd'>"+x.data.os.assname+" <b>["+x.data.os.asscod+"]</b></td>";
                            tr += "<td class='bklogtbtd'>"+
                                x.data.os.end+"<br>"+
                                x.data.os.bairro+"<br>"+
                                "<b>"+x.data.os.node+"</b>"+
                            "</td>";
                            tr += "<td class='bklogtbtd tdtel'>"+x.data.os.tel+"<br>"+x.data.os.cel+"</td>";
                            tr += "<td class='bklogtbtd'>"+x.data.os.falha+"</td>";
                            tr += "<td class='bklogtbtd'>"+x.data.os.obs_origem+"</td>";
                            tr += "<td class='bklogtbtd tdtime'>"+x.data.os.ingr+"</td>";
                            tr += "</tr>";
                            tr = $(tr);
                            //if(x.data.os.mygroup){
                            makeOSOBS(tr,x.data.os);
                            /*}else{
                                tr.append("<td class='bklogtbtd'>---</td>");
                            }*/
                            var ltr = $('#bklog_hist>tbody>.tr'+list).last();
                            if(ltr.length > 0){
                                tr.insertAfter(ltr);
                            }else{
                                if(list === 'ind')
                                    tr.appendTo('#bklog_hist>tbody');
                                else
                                    tr.prependTo('#bklog_hist>tbody');
                            }
                            tr.effect('highlight',5000);
                            tr = null;
                        }else{
                            os[list][i].loaded = null;
                        }
                        getNewOs(list,i+1,x.etc.t1);
                        histCheck(false);
                    }
                }
            });
        }
    }
    function trCount(ini){
        var xxx = osCount();
        $('#hist_count').html(xxx);
        $('#timeloaded').html("Dados carregados em "+((new Date()-ini)/1000)+" segundos");
    }
    function notsoslow(ini){
        clearInterval(slowtimer);
        howslow = 0;
    }
    function osCount(){
        return $('#bklog_hist').children('tbody').children('tr').length + " de <span title='Aproximadamente'>" + 
            (os.corp.length + os.ind.length)  + "*</span> reclamações ";
    }
    var amislow = function (){
        howslow += 100;
        var xxx = osCount();
        $('#hist_count').html(xxx);
        $('#timeloaded').html(cas.strpad('','.',((howslow/10)%100),true)+" "+(howslow/1000)+" segundos.");
    };
   
    $('#bklog_hist_updt').click(function(){
        refreshBacklog();
    });

    $('#auto_update').change(function(){
        if($('#auto_update').prop('checked')){
            schedule();
        }else{
            clearInterval(backlog_thread);
        }
    });
    $('#show_future').change(refreshBacklog);
    $('#hist_checkall').on('click',function(){
        histCheck();
    });
    
    if($('#auto_update').prop('checked')){
        schedule();
    }

    $('#content').css('padding-top',64 + $('#theader').outerHeight() - 2);


    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    
    $('.selectbutton').click(function(){
        var me = $(this), 
                elem = me.closest('.multiselect'), 
                p = elem.find('.listwrapper');

        if(p.is(':visible')){
            $('.listwrapper').hide();
        }else{
            $('.listwrapper').hide();
            p.css('top',me.position().top + me.outerHeight() + 5).show();
        }
    });
    var svpt;
    function sListAll(x){
        var l = [];
        for(var i in sListItems[x])
            l.push(sListItems[x][i].id);
        return l;
    }
    function selectItemClick(){
        var elem = $(this).closest('.multiselect');
        var x = elem.attr('data-id');
        $(this).toggleClass('selected');
        var me = $(this).attr('data-id');

        if(sList[x].indexOf(me) === -1)
            sList[x].push(me);
        else
            cas.kill(me,sList[x]);
        
        if(!sList[x].length){
            sList[x] = sListAll(x);
            $(this).parent().find('.myitem').addClass('selected');
        }

        sList[x].sort();
        elem.find('.selectbutton').button('option','label',pLabel(x));
        
        clearTimeout(svpt);
        svpt = setTimeout(svP,2000);
        return false;
    }
    function svP(){
        for(var i in sList){
            cas.set_pref('bklg_' + i + 'list',sList[i]);
        }
        refreshBacklog();
    }
    function pLabel(x){
        var l = sListItems[x].length;
        
        if(x === 'per')
            var pp = 'SIM TV';
        else
            var pp = 'Todos';

        if(sList[x] 
            && sList[x].length 
                && sList[x].length < l
        ){
            pp = [];
            for(var i in sList[x]){
                var y = sListFind(x,sList[x][i]);
                pp.push(y.name);
            }
            pp = pp.join(', ');
        }

        return pp;
    }
    function sListFind(i,x){
        for(var j in sListItems[i]){
            if(sListItems[i][j].id === x)
                return sListItems[i][j];
        }
    }
    
    function updatePs(){
        for(var s in sList){
            if(!sList[s].length)
                sList[s] = sListAll(s);
            var elem = $('.multiselect[data-id="'+s+'"]'),
                this_list = elem.find('.actuallist').empty();
            for(var i in sListItems[s]){
                var t = 
                        $("<li class='myitem' data-id='"+sListItems[s][i].id+"'>" +
                            sListItems[s][i].name +
                        "</li>")
                            .appendTo(this_list)
                            .click(selectItemClick);

                if( sList[s].indexOf(sListItems[s][i].id) > -1 )
                    t.addClass('selected');
            }
            elem.find('.selectbutton').button('option','label',pLabel(s));
        }
    }
    $('.bklog_hist_selector button').button();

    //LOAD PERLIST FROM PREFS
    var sList = {per:[],status:[]};
    var sListItems = {per: [], 
        status:[
            {id:'A',name:'Agendada'},
            {id:'E',name:'Emitida'},
            {id:'P',name:'Pendente'},
            {id:'R',name:'Reagendada'},
            {id:'S',name:'Suspensa'}
        ]
    };
    
    for(var i in cas.permissora)
        sListItems['per'].push({id:''+cas.permissora[i].id,
            name:cas.permissora[i].abbr});

    for(var s in sList){
        var tmpp = cas.get_pref('bklg_'+s+'list');
        for(var i in tmpp){
            sList[s].push(tmpp[i]);
        }
        sList[s].sort();
    }
    updatePs();
    refreshBacklog();

};