cas.controller = function(){
    var xvpos, agenda_v_pos;
    var openByForce = false;
    var searchResult = null;
    var loaded = 0;
    var last_tec, selected_block = null;
    var max_sleep_time = 1000 * 60 * 5;
    var map;
    var trackrun;
    var isToday;
    var activitySelected;
    var pers;
    var perlist;
    var ohnoes;
    var perdialog;
    var container = $('#container');
    var howslow = 0;
    var slowtimer;
    var os = [];
    var index = {};
    var tecbox = $('#tecbox');
    var tecs = {};
    var tec = null;
    var timeline = $('#timeline');
    var shadow = $('#shadowTimer');
    var shadowToggle = false;
    var startHour = 6;
    var collapsetr;
    var d;
    var iniD;
    var endD;
    var timeWidth;
    var memcached = [];
    var closedLeft = false;
    var cH = 0;
    var osSelected = null;
    var tecline = [];
    var pagelocked = false;
    var minPos;
    var turnos = {};
    var mestre = cas.checkPerms('w');
    var triggerback = false;
    
    
    var layer = {
        pers:{
            id:'pers',
            name:'Headends',
            visible:true,
            x:[],
            zIndex:1,
            once: true
        },
        zona:{
            id:'zona',
            name:'Zonas',
            visible:true,
            x:[],
            zIndex:1,
            once: true
        },
        tecpos:{
            id:'tecpos',
            name:'Posição dos Técnicos',
            visible:true,
            x:[],
            zIndex:2
        },
        pending:{
            id:'pending',
            name:'Ordens Pendentes',
            visible:true,
            x:[],
            zIndex:3
        },
        track:{
            id:'track',
            name:'Rastro do Técnico',
            visible:true,
            x:[],
            zIndex:4
        },
        schedule:{
            id:'schedule',
            name:'Agenda do Técnico',
            visible:true,
            x:[],
            zIndex:5
        }
    };
    function recalcDates(){
        d = $('#dselector').datepicker('getDate');
        iniD = new Date(d.getFullYear(), d.getMonth(), d.getDate(), startHour);
        endD = new Date();
        endD.setTime(iniD.getTime() + (1000 * 60 * 60 * 20));
    }
    function osrsz(){
        $('#oswrapper').getNiceScroll().resize();
    }
    function zeroTurnos(){
        for(var i in turnos){
            turnos[i].c = 0;
        }
    }
    function makePicSlide(tb,os){
        tb.find('.slidetr').remove();
        if(!os.pics || !os.pics.length){
            return false;
        }
        var first = tb.find('tbody>tr').first();
        var td = $("<td class='picstd' colspan="+
                    ((tb.attr('id') === 'osptb')?4:3)+
                ">").appendTo($("<tr class='slidetr'>").insertAfter(first));
        var ul = $("<ul class='picsul'>").appendTo(td);
        for(var i in os.pics){
            var pic = os.pics[i];
            
            $("<li>")
                .css('background',
                    "url(/media/tec_reg/72/"+
                        pic.file+") center no-repeat white")
                .attr('data-x',Base64.encode(JSON.stringify(pic)))
                .click(openImgPreview)
                .appendTo(ul);
        }
    }
    function openImgPreview(){
        var elem = $("<div class='img-preview'>");
        cas.weirdDialogSpawn($(this).offset(),elem);
        var pic = JSON.parse(Base64.decode($(this).attr('data-x')));
        
        elem.append("<div class='preview-header'>Enviada por <i>"+
                ((pic.user_name)?pic.user_name:pic.user_email)+"</i><sup>em "+pic.timestamp+"</sup></div>");
        $("<div class='img-box'><img src='/media/tec_reg/480/"+pic.file+"' /></div>")
                .appendTo(elem);
        
        var d = $("<div class='preview-menu'>").appendTo(elem);
        $("<a target='_blank'>Tamanho Real</a>").attr('href',"media/tec_reg/orig/"+pic.file).appendTo(d);
        
        if(canedit())
            $("<a class='img-delete'>Excluir</a>").attr('data-picid',pic.id).click(killPic).appendTo(d);
        
    }
    function killPic(){
        if(!confirm('Deseja realmente remover esta imagem?'))
            return null;
        var me = $(this);
        
        cas.ajaxer({
            method:'GET',
            sendme:{id:me.attr('data-picid')},
            sendto:'agenda/kill_pic',
            andthen:function(x){
                F5();
                me.closest('.weird-dialog').remove();
            }
        });
    }
    var currOSS = null;
    function ordensBaixadas(){
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/ordens_pendentes',
            sendme:{perlist:perlist},
            andthen:
                function(x){
                    var n_os = x.data.oss, ss = JSON.stringify(n_os);
                    if(currOSS !== null && ss === currOSS){
                        return null;
                    }
                    currOSS = ss;
                    
                    $("#pendencias>.ostb").remove();
                    $("#pendencias").hide();
                    var nbaixas = [];
                    for(var i in n_os){
                        
                        var stdnm = 
                                n_os[i].end + ", "
                                + n_os[i].bairro + ", "
                                + n_os[i].cid + ", "
                                + n_os[i].uf + ", " 
                                + n_os[i].cep + ", Brasil";
                        var os_tb =
                        $("<table data-id='"+n_os[i].schedule_id+
                            "' os='"+n_os[i].os+"' per='"+n_os[i].per+"' svc='"+
                                n_os[i].svc+"' class='ostb'>"+
                            "<tbody>" +
                                "<tr>"+
                                    "<td class='ostd osassname' colspan=3>"+n_os[i].assname+"</td>"+
                                "</tr>"+
                                "<tr>"+
                                    "<td class='ostd' colspan=3>"+n_os[i].os_tipo.toUpperCase()+"</td>"+
                                "</tr>"+
                                "<tr>"+
                                    "<td class='ostd'>Início</td>" +
                                    "<td class='ostd' colspan=2>"+n_os[i].ptIni+"</td>"+
                                "</tr>"+
                                "<tr>"+
                                    "<td class='ostd'>Fim</td>" +
                                    "<td class='ostd' colspan=2>"+n_os[i].ptEnd+"</td>"+
                                "</tr>"+
                                "<tr>"+
                                    "<td class='ostd'>Ordem</td>"+
                                    "<td class='ostd osnro' colspan=2>"+
                                        "<a target='_blank' title='Clique para Mais Detalhes sobre a OS' href='"+'os#os='+n_os[i].os+'&per='+n_os[i].per+'&svc='+n_os[i].svc+"'>"+
                                            n_os[i].svc.toUpperCase()+' - '+n_os[i].os+
                                        "</a>"+
                                        "<sup><a target='_blank' class='asuper' title='Clique para abrir OS no Siga' href='"+cas.osSigaLink(n_os[i])+"'> SIGA </a></sup>"+
                                    "</td>" +
                                "</tr>"+
                                "<tr>"+
                                    "<td class='ostd'><div><b>Contrato</b></div>"+okInt(n_os[i].contrato)+"</td>"+
                                    "<td class='ostd' colspan=2><div><b>Cód Assinante</b></div>"+n_os[i].asscod+"</td>"+
                                "</tr>"+
                                "<tr>"+
                                    "<td class='ostd osnro'>"+
                                        ((canedit())?
                                            "<button data-id='"+n_os[i].osackid+"' class='checkBaixa'>Baixar</button>"
                                            :'---'
                                        )+
                                    "</td>" +
                                    "<td class='ostd osnm' colspan=2><p><b>Técnico</b></p>"+n_os[i].tecname+"</td>"+
                                "</tr>"+
                                ((n_os[i].equipamento_in)?
                                    "<tr>"+
                                        "<td class='ostd'>"+((n_os[i].svc === 'tv')?'Decoder':'Modem')+"<br> [ ENTRA ]</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].equipamento_in + "</td>"+
                                    "</tr>":'') +
                                ((n_os[i].equipamento_out)?
                                    "<tr>"+
                                        "<td class='ostd'>"+((n_os[i].svc === 'tv')?'Decoder':'Modem')+"<br> [ SAI ]</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].equipamento_out + "</td>"+
                                    "</tr>":'') +
                                ((n_os[i].causa)?
                                    "<tr>"+
                                        "<td class='ostd'>Causa</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].causa + "</td>"+
                                    "</tr>":'')+

                                ((n_os[i].motivo)?
                                    "<tr>"+
                                        "<td class='ostd'>Motivo</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].motivo + "</td>"+
                                    "</tr>":'')+
                                ((n_os[i].obs_tec)?
                                    "<tr>"+
                                        "<td class='ostd'>Obs Técnico</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].obs_tec + "</td>"+
                                    "</tr>":'')+
                                (parseFloat(n_os[i].tx)?
                                    "<tr>"+
                                        "<td class='ostd'>TX</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].tx + "</td>"+
                                    "</tr>":'')+
                                (parseFloat(n_os[i].rx)?
                                    "<tr>"+
                                        "<td class='ostd'>RX</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].rx + "</td>"+
                                    "</tr>":'')+
                                (parseFloat(n_os[i].ch_baixo)?
                                    "<tr>"+
                                        "<td class='ostd'>CH Baixo</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].ch_baixo + "</td>"+
                                    "</tr>":'')+
                                (parseFloat(n_os[i].ch_alto)?
                                    "<tr>"+
                                        "<td class='ostd'>CH Alto</td>" +
                                        "<td class='ostd' colspan=2>" + n_os[i].ch_alto + "</td>"+
                                    "</tr>":'')+
                                "<tr>" +
                                    "<td class='ostd osend' colspan=3>"+stdnm+"</td>"+
                                "</tr>"+
                                "<tr>" +
                                    "<td class='ostd osnode'>"+n_os[i].node+"</td>"+
                                    "<td  class='ostd osingr'>"+n_os[i].ingr+"</td>" +
                                    "<td class='ostd osag'>"+agendaTurno(n_os[i].ag,n_os[i].turno)+"</td>"+
                                "</tr>"+
                                
                            "</tbody>"+
                        "</table>");
                        makePicSlide(os_tb,n_os[i]);
                        
                        os_tb.appendTo("#pendencias").find('.osnm').effect('highlight');
                        
                        var key = n_os[i].os+':'+n_os[i].per+':'+n_os[i].svc;
                        nbaixas.push(key);
                        var mycallback = function(){
                                    if(!$('#oswrapper').is(':hover'))
                                        $('#oswrapper').scrollTop(0);
                                };
                        if(!cas.inArray(key,alerts.baixa)){
                            var notificationBody = 'O técnico ' +
                                    n_os[i].tecname.toUpperCase()+
                                        ' finalizou uma nova ordem do assinante ' +
                                            n_os[i].assname.toUpperCase();

                            cas.makeNotif('warning',
                                notificationBody,
                                mycallback
                            );
                            
                            desktopNotification(
                                'Agenda Técnica - Nova Baixa',
                                notificationBody,
                                'agenda-baixa-'+key,
                                mycallback
                            );
                        }
                    }
                    alerts.baixa = nbaixas;
                    if(n_os.length > 0){
                        if(closedLeft){
                            openByForce = true;
                            $('#main_splitter').trigger('click');
                        }
                        $("#pendencias").show();
                        //leftPanelToggle(false);
                    }else{
                        if(!closedLeft && openByForce){
                            openByForce = false;
                            $('#main_splitter').trigger('click');
                        }
                    }
                    osrsz();
                    $('.checkBaixa').click(checkBaixaClick);
                }
        });
    }

    function desktopNotification(title,txt,id,callback){
        if(!window.Notification)
            return false;

        var opts = {
            body: txt
        };
        if(typeof callback === 'function')
            opts.callback = callback;

        if(id)
            opts.tag = id;

        cas.createDesktopNotification(
            title, //title
            opts //options
        );

      }
    function checkBaixaClick(){
        if(window.confirm("Certifique-se que você já leu a Causa e Observação submetidos pelo Técnico e baixou a ordem no SIGA.\nDeseja mesmo confirmar a baixa?")){
            cas.ajaxer({
                method:'GET',
                sendto:'agenda/baixar_os',
                sendme:{
                    id:$(this).attr('data-id')
                },andthen:ordensBaixadas
            });
        }
    }
    function tHover(){
        clearTimeout(collapsetr);
        var me = $(this);
        var zid = parseInt($(this).attr('data-id'));
        $(".timeBlock[schedule-id='"+zid+"']").addClass('GenericHoverClass');
        collapsetr = setTimeout(function(){
            me.find('.collapsedtr').slideDown(osrsz);
        },500);
    }
    function tHoverOut(){
        var zid = parseInt($(this).attr('data-id'));
        $(".timeBlock[schedule-id='"+zid+"']").removeClass('GenericHoverClass');
        clearTimeout(collapsetr);
        $(this).find('.collapsedtr').slideUp('slow');
    }
    function loadOSs(){
        

        $('#autoroute').button('disable');
        recountos();
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/list_agenda',
            sendme:
                {
                    perlist:perlist,
                    t:dTime()
                },
            andthen:
                function(x){
                    osPool(x.data.oss,x.data.index);
                }
        });
    }
    function oIndex(z){
        return index[z.os + ':' + z.per + ':' + z.svc];
    }
    
    function agendaTurno(ag,t){
        return ((ag)?ag+'<br>'+((t && t.name)?t.name:''):'FORA DA AGENDA');
    }
    function osPool_(new_os,i){
        var z = oIndex(new_os[i]);
        if(os[z]){
            for(var j in os[z])
                if(!new_os[i][j])
                    new_os[i][j] = os[z][j];
        }
        if(new_os[i].turno && !turnos[new_os[i].turno.id])
            turnos[new_os[i].turno.id] = {
                name: new_os[i].turno.name,
                c: 0
            };
        
        turnos[new_os[i].turno.id].c++;
        if(!new_os[i].stdnm)
            new_os[i].stdnm = new_os[i].end + " - "+ new_os[i].bairro + ", "+ new_os[i].cid + " - "+  new_os[i].uf + ", " + new_os[i].cep +" - Brasil";

        if(!new_os[i].elem)
            new_os[i].elem =
            $("<table id='os_"+new_os[i].os+'_'+new_os[i].per+'_'+new_os[i].svc+"' os='"+new_os[i].os+"' per='"+new_os[i].per+"' svc='"+new_os[i].svc+"' class='ostb'>")
            .appendTo('#'+new_os[i].os_tipo+">.os_tipo_container")
            .click(osTbClick);
        
        new_os[i].elem.attr('list_index',i);
        
        new_os[i].elem.html(
            "<thead>"+
                "<tr>"+
                    "<th class='ostd osnm' colspan=3>"+
                        new_os[i].assname.toUpperCase()+
                    "</th>"+
                "</tr>"+
                "<tr>"+
                    "<th class='ostd osnro'>"+
                        "<a target='_blank' title='Clique para Mais Detalhes sobre a OS' href='"+'os#os='+new_os[i].os+'&per='+new_os[i].per+'&svc='+new_os[i].svc+"'>"+new_os[i].os+"</a>"+
                    "</th>" +
                    "<th class='ostd' colspan=2>"+
                        new_os[i].os_tipo.toUpperCase()+
                    "</th>"+
                "</tr>"+
            "</thead>"+
            "<tbody>"+
                
                "<tr>" +
                    "<td class='ostd' colspan=2>Nº do Assinante</td>"+
                    "<td class='ostd'>"+new_os[i].asscod+"</td>"+
                "</tr>"+
                "<tr>" +
                    "<td class='ostd' colspan=2>Contrato</td>"+
                    "<td class='ostd'>"+okInt(new_os[i].contrato)+"</td>"+
                "</tr>"+
                "<tr>" +
                    "<td class='ostd ossvc'>"+new_os[i].svc.toUpperCase()+"</td>"+
                    "<td  class='ostd osingr'>"+new_os[i].ingr+"</td>" +
                    "<td class='ostd osag'>"+agendaTurno(new_os[i].ag,new_os[i].turno)+"</td>"+
                "</tr>"+
                "<tr>" +
                    "<td class='ostd osnode'>"+new_os[i].node+"</td>"+
                    "<td class='ostd osend' colspan=2>"+new_os[i].stdnm+"</td>"+
                "</tr>"+
            "</tbody>");

        delete os[z];
    }
    function osPool(new_os,new_index){
        
        zeroTurnos();
        
        for(var i in new_os)
            osPool_(new_os,i);
        
        $('#turnotb').empty();
        for(var i in turnos)
            if(turnos[i].c)
                $('#turnotb').append("<tr><td class='turnotbname'>"+
                    turnos[i].name+"</td><td class='turnotbc'>"+
                    turnos[i].c+"</td></tr>");
        
        killLayer(layer.pending);
        killLayer(layer.tecpos);
        
        for(var i in os){
            if(os[i].elem)
                os[i].elem.remove();
            delete os[i];
        }
        
        os = new_os;
        index = new_index;
        
        for(var i in os){
            if(os[i].lat)
                osMark(os[i].stdnm,os[i].lat,os[i].lng,i);
        }
            
        osrsz();
        recountos();
        geoSearch(0);
        $('#autoroute').button('enable');
        new_os = null;
        new_index = null;
    }
    function okInt(x){
        x = parseInt(x);
        return ((x)?x:'---');
    }
    function yetAnotherOsGet(m){
        $('#ospanel-w').empty();
        cas.showthis('body');
        if(m.pack && m.pack.length > 0){
            cas.hidethis('body');
            for(var i in m.pack){
                cas.ajaxer({
                    etc:{
                        i:i
                    },
                    sendme:{
                        'os':{
                            os:m.pack[i].os,
                            per:m.pack[i].per,
                            svc:m.pack[i].svc
                        },
                        'id':m.id
                    },
                    method:'GET',
                    sendto:'agenda/osser',
                    andthen:function(x){
                        if(x.data.os){
                            var tb =
                                $("<table class='ospaneltb'>"+
                                    "<thead>"+
                                        "<tr>"+
                                            "<th class='ostd osp_tipo' colspan='4'>"+x.data.os.os_tipo+"</th>"+
                                        "</tr>"+
                                    "</thead>"+
                                    "<tbody>"+
                                        
                                        "<tr>"+
                                            "<td class='ostd ossvc osp_svc'>"+x.data.os.svc.toUpperCase()+"</td>"+
                                            "<td class='ostd osnro osp_os' colspan='2'>"+
                                                "<a target='_blank' title='Clique para Mais Detalhes sobre a OS' href='"+'os#os='+x.data.os.os+'&per='+x.data.os.per+'&svc='+x.data.os.svc+"'>"+x.data.os.os+"</a>"+
                                                "<sup><a target='_blank' class='asuper' title='Clique para abrir OS no Siga' href='"+cas.osSigaLink(x.data.os)+"'> SIGA </a></sup>"+
                                            "</td>"+
                                            "<td class='ostd osnode osp_node'>"+x.data.os.node+"</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td class='ostd' colspan=2><div><b>Contrato</b></div>"+okInt(x.data.os.contrato)+"</td>"+
                                            "<td class='ostd' colspan=2><div><b>Cód Assinante</b></div>"+x.data.os.asscod+"</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td class='ostd osp_end' colspan='4'>"+x.data.os.end + "</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td class='ostd' colspan='3'>"+ x.data.os.bairro + " - " + x.data.os.cid + "</td>"+
                                            "<td class='ostd'>"+ x.data.os.node + "</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td class='ostd' colspan='4'>"+ x.data.os.uf + ", " + x.data.os.cep +" - Brasil"+"</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td class='ostd osp_tel' colspan='2'>"+x.data.os.tel+"</td>"+
                                            "<td class='ostd osp_cel' colspan='2'>"+x.data.os.cel+"</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td class='ostd osp_ingr' colspan='2'>"+x.data.os.ingr+"</td>"+
                                            "<td class='ostd osp_ag' colspan='2'>"+agendaTurno(x.data.os.ag,x.data.os.turno)+"</td>"+
                                        "</tr>"+
                                        ((x.data.os.real_end)
                                            ?"<tr>"+
                                                "<td class='ostd osp_baixa' colspan=4>"+
                                                    ((x.data.os.baixa)?'Baixado por '+x.data.os.baixa_user+' em '+x.data.os.baixa:'<b>Esperando baixa</b>')+
                                                "</td>" +
                                            "</tr>"
                                            :''
                                        )+
                                        ((x.data.os.causa || x.data.os.motivo)
                                            ?''+
                                            ((x.data.os.equipamento_in)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>"+
                                                        ((x.data.os.svc === 'tv')?'Decoder':'Modem')+
                                                    "<br> [ ENTRA ]</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.equipamento_in + "</td>"+
                                                "</tr>":'')+
                                            ((x.data.os.equipamento_out)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>"+
                                                        ((x.data.os.svc === 'tv')?'Decoder':'Modem')+
                                                    "<br> [ SAI ]</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.equipamento_out + "</td>"+
                                                "</tr>":'')+
                                            ((x.data.os.causa)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>Causa</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.causa + "</td>"+
                                                "</tr>":'')+
                                            ((x.data.os.motivo)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>Motivo</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.motivo + "</td>"+
                                                "</tr>":'')+
                                            ((x.data.os.obs_tec)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>Obs Técnico</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.obs_tec + "</td>"+
                                                "</tr>":'')+
                                            (parseFloat(x.data.os.tx)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>TX</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.tx + "</td>"+
                                                "</tr>":'')+
                                            (parseFloat(x.data.os.rx)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>RX</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.rx + "</td>"+
                                                "</tr>":'')+
                                            (parseFloat(x.data.os.ch_baixo)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>CH Baixo</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.ch_baixo + "</td>"+
                                                "</tr>":'')+
                                            (parseFloat(x.data.os.ch_alto)?
                                                "<tr>"+
                                                    "<td class='ostd' colspan=2>CH Alto</td>" +
                                                    "<td class='ostd' colspan=2>" + x.data.os.ch_alto + "</td>"+
                                                "</tr>":'')
                                            :''
                                        )+
                                    "</tbody>"+
                                "</table>"
                            ).appendTo('#ospanel-w');
                            if( canedit() && mestre ){
                                $('<button title="Desalocar Ordem">&zwnj;</button>')
                                        .attr('data-id',x.data.os.osackid)
                                        .button({icons:{primary: 'ui-icon-close'},text: false})
                                        .click(function(){
                                            cas.ajaxer({
                                                method: 'POST',
                                                sendme:{id: $(this).attr('data-id')},
                                                sendto: 'agenda/os_rm',
                                                andthen: function(x){
                                                    F5();
                                                }
                                            });
                                        }).prependTo(tb.find('.osp_tipo'));
                            }
                        }

                        if(parseInt(x.etc.i) === parseInt(m.pack.length - 1) ){
                            cas.showthis('body');
                        }
                    }
                });
            }
        }
    }
    function notsoslow(ini){
        clearInterval(slowtimer);
        howslow = 0;
    }
    function amislow(){
        howslow += 100;
        $('#timeloaded').html(cas.strpad('','.',((howslow/10)%100),true)+" "+(howslow/1000)+" segundos.");
    }

    function osTbClick(e){
        layerSetVisible(layer.pending,true);
        $('#savemove,#moveactivity').button('disable');
        var target = $(this);
        var li = parseInt(target.attr('list_index'));
        var mrk = layer.pending.x[os[li].marker];
        if(mrk){
            theZoom(mrk.getPosition().lat(), mrk.getPosition().lng());
            google.maps.event.trigger(mrk, 'click');
        }
    }
    function osMapClick(){
        unTec();
        $('#savemove,#moveactivity').button('disable');
        var elem = this.details.elem;
        var $elem = $(elem);
        $('.markerhover').removeClass('markerhover');
        $elem.addClass('markerhover');
        $elem.parent().show();
        leftPanelToggle(false);
        osSelected = this.details;
        
        if(Object.keys(tecs).length > 0)
            $('#osput').button('enable');
        if(!$("#oswrapper").is(':hover'))
            $("#oswrapper").scrollTop($("#oswrapper").scrollTop() + ($elem.offset().top - $("#oswrapper").offset().top) - 50);
    }
    function osMark(addr,lat,lng,i){
        var mark = map.addMarker({
          lat: lat,
          lng: lng,
          details:os[i],
          title:os[i].assname,
          icon:'/lib/img/1368697546_marker_squared_grey_4.png',
          infoWindow:{
              content:
                  "<div style='height:60px;line-height:20px;'>"+
                      "<b>"+os[i].assname+"</b><br>"+
                      addr+
                  "</div>"
          },
          zIndex:layer.pending.zIndex,
          visible: layer.pending.visible
        });
        os[i].marker = parseInt(layer.pending.x.length);
        layer.pending.x.push(mark);
        os[i].lat = lat;
        os[i].lng = lng;
        google.maps.event.addDomListener(mark, 'click',osMapClick);
    }
    function tryCache(end,cep){
        for(var i = (memcached.length - 1);i >= 0;i--)
            if(
                memcached[i].cep == cep
                    &&
                memcached[i].end.toLowerCase() == end.toLowerCase()
            )
                return memcached[i];

        return false;
    }
    
    function geoSearch(i,j){
        i = parseInt(i);
        if(i < os.length && !os[i].marker){
            var addr = os[i].stdnm, nextstep = 1;
            if(j === 1){
                addr = os[i].bairro + ", "+os[i].cid+', '+os[i].uf+", Brasil";
                nextstep = 2;
            }else if(j === 2){
                addr = os[i].cid+', '+os[i].uf+', '+os[i].cep+", Brasil";
                nextstep = 3;
            }else if(j === 3){
                console.log('fail back: '+os[i].stdnm);
                memcached.push({
                    cep: os[i].cep,
                    end: os[i].end,
                    addr: pers[os[i].per].name,
                    lat: pers[os[i].per].lat,
                    lng: pers[os[i].per].lng
                });
                osMark(os[i].stdnm,pers[os[i].per].lat,pers[os[i].per].lng,i);
                geoSearch(i+1,0);
                return false;
            }

            var r = tryCache(os[i].end, os[i].cep);

            if(!r){
                
                GMaps.geocode({
                    address: addr,
                    region: 'Brasil',
                    componentRestrictions: {
                        administrativeArea: os[i].uf,
                        locality: os[i].cid
                    },
                    callback: function(results, status) {
                        if (status === 'OK') {
                            var latlng = results[0].geometry.location;

                            cas.ajaxer({
                                sendme:{
                                    end:os[i].end,
                                    cep:os[i].cep,
                                    lat: latlng.lat(),
                                    lng: latlng.lng()
                                },
                                sendto:'agenda/geo_cache'
                            });

                            memcached.push({
                                cep:os[i].cep,
                                end:os[i].end,
                                addr:results[0].formatted_address,
                                lat: latlng.lat(),
                                lng: latlng.lng()
                            });

                            osMark(results[0].formatted_address,latlng.lat(),latlng.lng(),i);

                            setTimeout(function(){
                                geoSearch(i + 1, 0);
                            },1000);
                        }

                        setTimeout(function(){
                            geoSearch(i,nextstep);
                        },1000);
                    }
                });
            }else{
                osMark(r.addr,r.lat,r.lng,i);
                geoSearch(i+1,0);
            }
        }else if(i === parseInt(os.length)){
            $('body').trigger('finishedLoading');
        }else{
            geoSearch(i+1,0);
        }
    }
    
    function geoloc(pos){
        cas.ajaxer({
            sendme:{
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            },
            method:'GET',
            sendto:'agenda/select_per',
            andthen:function(x){
                cas.set_pref('per',parseInt(x.data.per.id));
                bootOne();
            }
        });
    }
    function geoerr(){
        cas.set_pref('per',61);
        bootOne();
    }
    function autoFit(){
        var ls = new google.maps.LatLngBounds(),lat,lng,diff;
        
        for(var i in perlist){
            
            var tl1 = parseFloat(pers[perlist[i]].lat);
            var tl2 = parseFloat(pers[perlist[i]].lng);
            
            if(lat && tl1 !== lat && tl2 !== lng)
                diff = true;
            
            lat = tl1;
            lng = tl2;
            
            ls.extend(new google.maps.LatLng(lat,lng));
        }
        if(diff)
            map.fitBounds(ls);
        else{
            theZoom(lat,lng);
        }
    }
    var xinterval;
    function boot(){
        
        xinterval = setInterval(function(){
            loadPer();
        },1 * 60 * 1000);
        setInterval(function(){
            if(loaded)
                ordensBaixadas();
        },30 * 1000);
        //detect perlist
        perlist = getPerList();
        if(!perlist){
            var frompref = cas.get_pref('per');
            if(frompref){
                bootOne();
            }else{
                navigator.geolocation.getCurrentPosition(geoloc,geoerr,
                {
                    enableHighAccuracy:false,
                    timeout: 1000 * 60,
                    maximumAge: 1000 * 60 * 5
                });
            }
        }else{
            bootOne();
        }
    }
    function bootOne(){
        if(!perlist){
            cas.set_pref('agenda_perlist',[parseInt(cas.get_pref('per'))]);
        }
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/tipos',
            andthen:function(x){
                var c = $("<ul id='osaccordion'>").appendTo('#oswrapper');
                for(var i in x.data.tipos)
                    c.append("<li class='os_tipo' id='"+x.data.tipos[i].name+"'>"+
                                "<div class='os_tipo_label'>"+x.data.tipos[i].name+"</div>"+
                                "<div class='os_tipo_container' style='display:none'></div>"+
                            "</li>");
                c.sortable({axis:'y',handle:'.os_tipo_label'})
                    .find('.os_tipo_label')
                    .click(function(){
                        $(this).next('.os_tipo_container').slideToggle(function(){
                            osrsz();
                        });
                    });
                fetchPerDialog();
            }
        });
    }
    var minuteSpanControlEvents =
    {
        click:function(){
            if($('#minuteSpanConfig').children().length === 1){
                $("<div id='minuteSpanConfigSlider' style='display:none'>")
                    .appendTo('#minuteSpanConfig');
                $('#minuteSpanSelector').show().appendTo('#minuteSpanConfigSlider');
            }
            $('#minuteSpanConfigSlider').slideToggle();
        },mouseover:function(){
            $('#tracktooltip').show();
        },mouseout:function(){
            $('#tracktooltip').hide();
        }
    };
    function bootTwo(){
        map = new GMaps({
            div: '#map',
            lat: 10,
            lng: 10,
            streetViewControl: true,
            panControl: false,
            rotateControl:false,
            zoomControl: true,
            scaleControl: false,
            mapTypeControl:true
        });
        
        for(var i in pers){
            layer.pers.x.push(
                map.addMarker({
                    lat:pers[i].lat,
                    lng:pers[i].lng,
                    icon:'/lib/img/antenna.png',
                    title:'Headend ' + pers[i].name
                })
            );
        }
        var h = "<div id='layer_control_dropdown'>Camadas</div>"+
                    "<ul id='layer_control_options'>";
        
        for(var i in layer)
            h += "<li>"+
                    "<input "+
                        "id='toggle_layer_"+i+"'"+
                        "data-layer='"+i+"' "+
                        "class='layertoggler' "+
                        "type='checkbox' "+
                            ((layer[i].visible)?'checked ':'')+
                    "/>"+
                    "<label for='toggle_layer_"+i+"'>"+
                        layer[i].name+    
                    "</labe>"+
                "</li>";
        h += '</ul>';
        
        map.addControl({
            content: h,
            id: 'layer_control',
            position: 'TOP_RIGHT',
            style:{
                margin:'5px',
                width:'200px'
            },
            events:{
                mouseover: function(){
                    layerTogglersUpdate();
                    $('#layer_control_options').show();
                },
                mouseout:function(){
                    $('#layer_control_options').hide();
                },
                click:layerControlClick
            }
        });
        map.addControl({
            content: "<div id='minuteSpanConfigButton'>+</div>",
            id: 'minuteSpanConfig',
            position: 'TOP_RIGHT',
            style:{
                'margin':'5px',
                display:'none'
            },
            events:minuteSpanControlEvents
        });
        autoFit();
        fetchZonas();
        ordensBaixadas();
        loadPer();
        setLiner();
    }
    
    function layerTogglersUpdate(){
        $('.layertoggler').each(function(){
            $(this).prop('checked',layer[$(this).attr('data-layer')].visible);
        });
    }
    function layerControlClick(){
        $('.layertoggler').each(function(){
            layerSetVisible(
                layer[$(this).attr('data-layer')],$(this).is(':checked')
            );
        });
    }
    function calcTimeWidth(){
        recalcDates();
        return (timeline.width()/(endD - iniD));
    }
    var sLT;
    function setLiner(){
        clearTimeout(sLT);
        
        if(window.document.hasFocus){
            $('.timeZoneLine').remove();
            recalcDates();
            var d = window.svrtime, xd = $('#dselector').datepicker("getDate"), s,w,l,h;

            isToday = d.getHours() >= startHour
                && xd.getDate() === d.getDate()
                && xd.getMonth() === d.getMonth()
                && xd.getFullYear() === d.getFullYear();
        
            if(isToday){
                $('#shadLine').show();
                s = d.getTime() - iniD.getTime();
                w = Math.min( timeline.width(), s * timeWidth );
                
            }else{
                w = 0;
                $('#shadLine').hide();
            }

            shadow.width(w);
            l = timeline.position().left;
            shadow.css('left',l);
            h = $('#agenda').height() + $('#topbar').outerHeight();
            
            if(isToday){
                minPos = shadow.position().left + w;
                $('#shadLine').height(h).css('left',minPos);
                if(pers){
                    for(var i in perlist){
                        var $per = pers[perlist[i]];
                        var tzPos = minPos + ($per.timeoffset * timeWidth) ;
                        var elem = $('.timeZoneLine[data-diff="'+$per.timeoffset+'"]');
                        if( tzPos > shadow.position().left && tzPos < ( shadow.position().left + timeline.width() )
                            && !elem.length 
                        ){
                            var tzElem = $("<div>").addClass('timeZoneLine')
                                    .attr('data-diff',$per.timeoffset)
                                    .css('left',tzPos).height(h)
                                    .appendTo($('#shadLine').parent());

                            var tzLabel = $("<div class='timeZoneLineLabel'>"+$per.abbr+"</div>");
                            tzLabel.appendTo(tzElem);
                            tzLabel.css('left', 0 - (tzLabel.width()/2) );
                        }else{
                            var tzLabel = elem.find('.timeZoneLineLabel').append(', '+$per.abbr);
                            tzLabel.css('left', 0 - (tzLabel.width()/2) );
                        }
                    }
                }
            }else
                minPos = 0;

            if(shadowToggle){
                shadow.height(h);
            }
        }
        sLT = setTimeout(setLiner,1000);
    }
    function recountos(){
        $('.os_tipo').each(function(){
            var me = $(this);
            if(me.find('.ostb').length > 0){
                me.children('.os_tipo_label').html("<span class='os_tipo_label_count'>"+me.find('.ostb').length+"</span> " +me.attr('id'));
                me.show();
            }else
                me.hide();
        });
    }
    function windowVResize(h){
        var n = $('#agenda').height() + ($(window).height() - h);
        $('#agenda').height(n);
        setLiner();
    }
    
    function vposupdate(e,ui){
        if(ui)
            agenda_v_pos = parseInt(ui.size.height);
        else
            agenda_v_pos = $('#agenda').height();
        if( agenda_v_pos !== parseInt(cas.get_pref('agenda_v_pos')) ){
            cas.set_pref('agenda_v_pos',agenda_v_pos);
        }
    }
    function maxAHeight(){
        return container.height()
                    - $('#toolbar').outerHeight()
                    - $('#topbar').outerHeight();
    }
    function agendaSize(){
        xvpos = cas.get_pref('agenda_v_pos');
        agenda_v_pos = null;
        $('#mapwrapper').height(maxAHeight());
        if(xvpos !== null)
            agenda_v_pos = Math.min(xvpos,maxAHeight());
        else
            agenda_v_pos = $('#rightinner').height()/2;


        $('#agenda')
            .height(agenda_v_pos)
            .resizable({
                containment:'#faketainer',
                handles:'s',
                resize: function(event, ui){
                    setLiner();
                },
                stop: vposupdate
            });
    }
    
    function resizer(){
        $('#content').height(
            $(window).height()
                - $('#head-wrapper').outerHeight()
                - $('#foot').outerHeight()
        );
        
        
        leftPanelToggle();
        if(cH && $(window).height() !== cH)
            windowVResize(cH);
        
        $('#main_left').width($('#main_splitter').position().left);
        $('#main_right').width(
            container.width()
                - ( $('#main_left').outerWidth() 
                        + $('#main_splitter').outerWidth() )
        );
        
        timeline.width(
            $('#agenda').width()
            - $('#tecbox').outerWidth()
            - cas.getScrollbarWidth()
        );

        timeWidth = calcTimeWidth();
        $('#timesheet').width(timeline.width());
        $('#time-rule').empty().width(timeline.width());
        $('#time-rule-wrapper')
                .width(timeline.width())
                .css('left',timeline.position().left);
        mountRuler();
        $('#faketainer')
            .height(maxAHeight())
            .css('top',$('#main_right').position().top);
    
        cH = $(window).height();
        setLiner();
    }
    function mountRuler(){
        var piece = (timeline.width() / ( (endD - iniD) / (1000 * 60 * 60) ) );
        var pick = "style='width:"+((piece/4))+"px;";
        for(var i = iniD.getTime(); i < endD.getTime(); i += 1000 * 60 * 60){
            
            var thisTime = new Date();
            thisTime.setTime(i);
            var j = (thisTime - iniD) / (1000 * 60 * 60);
            $("<div class='rulepiece' style='width:"+(piece - 1)+"px;left:"+(piece*j)+"px'>"+
                    "<div class='rulelabel'>"+cas.strpad(thisTime.getHours(),'0',2,true)+":00"+"</div>"+
                    "<div class='rulepicks'>"+
                        "<div class='rulepick' "+pick+"left:"+(0)+"px'></div>"+
                        "<div class='rulepick' "+pick+"left:"+((piece/4)*1)+"px'></div>"+
                        "<div class='rulepick' "+pick+"left:"+((piece/4)*2)+"px'></div>"+
                        "<div class='rulepick' "+pick+"left:"+((piece/4)*3)+"px'></div>"+
                    "</div>"+
                "</div>").appendTo('#time-rule');
        }
    }
    function killLayer(l){
        for(var i in l.x)
            l.x[i].setMap(null);
        for(var i in l.x)
            l.x.splice(i,1);
        l.x = [];
    }
    function layerSetVisible(l,h){
        h = ((h)?true:false);
        var t = $('.tecSelected'), $tec = null;
        if(t.length){
            $tec = tecs[t.attr('tec-id')];
        }
        for(var i in l.x){
            var shouldBeVisible = h;
            
            if(shouldBeVisible && l.id === 'pending' && $tec){
                var ordem = l.x[i].details;
                shouldBeVisible = $tec.tipos.indexOf(ordem.os_tipo) > -1;
            }
            
            l.x[i].setVisible(shouldBeVisible);
        }
        l.visible = h;
    }
    
    function fetchZonas(){
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/zonas',
            andthen: function(x){
                loadZonas(x.data.zonas);
            }
        });
    }
    function loadZonas(zs){
        killLayer(layer.zona);
        
        for(var i in zs){
            var zid = zs[i].id;
            if(zs[i].path){
                layer.zona.x.push(
                    map.drawPolygon({
                        clickable:false,
                        paths:zs[i].path,
                        fillColor:zs[i].color,
                        fillOpacity:0.2,
                        strokeWeight:0.1,
                        visible: layer.zona.visible,
                        zIndex:layer.zona.zIndex
                    })
                );
            }
        }
    }
    function removeFlyingOS(){
        if(zPack){
            zPack = null;
            $('.flyingOS').remove();
        }
    }
    
    function loadPer(force){
        if(!force)
            force = ((new Date()).getTime() - loaded) > max_sleep_time;
        
        if( (cas.ajaxcount <= 0  && !pagelocked)|| !loaded || force){
            tempLock();
            if(parseInt(cas.get_pref('agenda_d')) !== dTime())
                cas.set_pref('agenda_d',dTime());
            
            checkBts();
            $('#savemove,#moveactivity,#osput').button('disable');
            
            map.cleanRoute();
            for(var i in layer)
                if(!layer[i].once)
                    killLayer(layer[i]);
            
            for(var i in os)
                os[i].marker = null;
            
            
            last_tec = tec;
            var a = $('.activityHover');
            selected_block = null;
            if(a.length)
                selected_block = a.attr('id');
            cas.hidethis('body');
            unTec();
            removeFlyingOS();
            realMarkerKill();
            leftPanelToggle(false);
            recalcDates();
            loadOSs();
            loadTecs();
        }
    }
    function zoomPending(){
        if(os.length > 0 && layer.pending.visible){
            var ls = new google.maps.LatLngBounds(),lat,lng;
            for(var i in os){
                lat = parseFloat(os[i].lat);
                lng = parseFloat(os[i].lng);
                ls.extend(new google.maps.LatLng(lat,lng));
            }
            map.fitBounds(ls);
        }
    }
    $('body').on('finishedLoading', function(){
        cas.showthis('body');
        var d = new Date();
        loaded = d.getTime();
        $('#lupdatelabel').html('Atualizado em ' + hrFormat(d));
        tempUnLock();
        zoomPending();
        
    });
    
    function canedit(){
        for(var i in perlist)
            if(pers[perlist[i]].canedit !== true)
                return false;
        return true;
    }
    function checkBts(){
        if(canedit()){
            $('.edtbt').show();
        }else{
            $('.edtbt').hide();
        }
        if(canedit() && mestre){
            $('#autoroute,#flush').show();
        }else{
            $('#autoroute,#flush').hide();
        }
    }
    
    function dTime(){
        return ($('#dselector').datepicker("getDate").getTime() / 1000);
    }
    function loadTecs(){

        cas.ajaxer({
            sendme:{
                perlist:perlist,
                t:dTime()
            },
            method:'GET',
            sendto:'agenda/get_tecs',
            andthen:function(x){
                tecMerge(x.data.tecs);
                if( $('#tecstats').is(':visible') ){
                    updateTecStats();
                }
            }
        });
    }
    
    function tecWorkStat(i){
        var first = null, last = null;
        
        for(var j in tecs[i].workgrid){
            if(tecs[i].workgrid[j].working){
                last = j;
                if(!first)
                    first = j;
            }
        }
        if(first && last){
            var ini = new Date(), end = new Date();
            ini.setTime(tecs[i].workgrid[first].ini * 1000 + window.svroffset);
            end.setTime(tecs[i].workgrid[last].end * 1000 + window.svroffset);
            
            return "<span class='tecWorkStat'>"+
                        cas.strpad(ini.getHours(),'0',2,true)+':'+cas.strpad(ini.getMinutes(),'0',2,true)+' - '+
                        cas.strpad(end.getHours(),'0',2,true)+':'+cas.strpad(end.getMinutes(),'0',2,true)+
                    "</span>";
        }
        return '';
    }
    function tecTipos(i){
        var tectop = '';
        for(var k  in tecs[i].tipos)
            tectop += 
                "<span class='tecostipo' title='"+
                    tecs[i].tipos[k].toUpperCase()+
                "'>"+tecs[i].tipos[k].substr(0,1).toUpperCase()+"</span>";    
        return tectop;
    }
    function tecHidden(t){
        return (parseInt(t.reports) <= 0 && hideoffline);
    }
    function tecMerge(ts){
        tec = null;
        
        var now = new Date();
        now = now.getTime();
        $('#timeblocks').empty();
        
        for(var i in ts){
            ts[i].hidden = tecHidden(ts[i]);
            if(!tecs[i]){
                tecs[i] = ts[i];
            }else{                
                tecSchedule(i,true);
                for(var k in ts[i]){
                    tecs[i][k] = ts[i][k];
                }
            }
            tecs[i].checkedIn = now;
        }
        ts = null;
        for(var i in tecs){
            if(tecs[i].checkedIn !== now || tecs[i].hidden)
                tecClear(i);
            else{
                if(!tecs[i].elem)
                    tecs[i].elem = 
                        $("<div id='tec_"+tecs[i].id+"' class='tec' tec-id='"+tecs[i].id+"'>").appendTo(tecbox).click(tecClick);
                
                tecStruct(i);
            }
        }
        for(var i in tecs){
            tecSchedule(i);
            tecWorkGrid(i);
            if(last_tec && parseInt(last_tec) === parseInt(i)){
                tecs[i].elem.click();
                last_tec = null;
            }
        }
        if(selected_block){
            $('#'+selected_block).click();
        }
        
        scanClosedOSS();
        tecPoints();
    }
    function tecPoints(){
        for(var i in tecs){
            cas.ajaxer({
                method:'GET',
                sendto:'agenda/my_tec_pos',
                etc:{
                    id:i
                },
                sendme:{
                    id:i,
                    t:dTime()
                },andthen:tecMark
            });
        }
    }
    function tecMarkClick(){
        if(this.details && $.contains(document.documentElement, this.details.elem[0])){
            this.details.elem.trigger("click");
        }
    }
    function tecMark(x){
        var id = x.etc.id, mBounds, c, mark, i;
        if(tecs[id]){
            if(x.data.pos){
                mark = map.addMarker({
                        lat: x.data.pos.lat,
                        lng: x.data.pos.lng,
                        details:tecs[id],
                        icon:'/lib/img/'+
                                ((x.data.pos.real)
                                    ?((x.data.pos.recent)
                                        ?'car-ontrack.png'
                                        :'car-oldtrack.png'
                                    )
                                    :'car-offtrack.png'),
                        zIndex:layer.tecpos.zIndex,
                        visible:layer.tecpos.visible ,
                        title: tecs[id].name
                    });
                layer.tecpos.x.push(mark);
                google.maps.event.addDomListener(mark, 'click',tecMarkClick);
            }
        }
    }
    function scanClosedOSS(){
        for(var i in tecs)
            for(var j in tecs[i].schedule)
                if(tecs[i].schedule[j].activity === 'os' && tecs[i].schedule[j].real_end)
                    closedYet(tecs[i].schedule[j].pack,tecs[i].schedule[j].time_elem);
    }
    $('#searchoss').keydown(function(e){
        if(e.keyCode === 27){
            $(this).hide();
        }
    }).autocomplete({
        source: function( request, response ) {
            cas.ajaxer({
                method:'GET',
                sendto:'agenda/os_auto',
                sendme:{
                    os:request.term,
                    t:dTime(),
                    perlist:perlist
                },andthen:function(x){
                    response(x.data.oss);
                }
            });
        },
        minLength: 2,
        select: function( event, ui ) {
            var z = ui.item.value.split(':');
            $(this).val('').slideUp();
            cas.ajaxer({
                method:'GET',
                sendto:'agenda/os_pack',
                sendme:{
                    os:z[0],
                    per:z[1],
                    svc:z[2],
                    t:dTime()
                },
                andthen:function(x){
                    if(x.data.uDumb)
                        return F5();
                    x.data.pack.addr = ui.item.addr;
                    if(!x.data.pack.lat)
                        easyGeo(x,packer);
                    else
                        packer(x);
                }
            });
        }
        
    });
    function easyGeo(x,callme){
        GMaps.geocode({
            address: x.data.pack.addr,
            callback: function(results, status) {
                if(status == 'OK'){
                    var latlng = results[0].geometry.location;
                    x.data.pack.lat = latlng.lat();
                    x.data.pack.lng = latlng.lng();
                }else{
                    x.data.pack.lat = pers[parseInt(x.data.pack.per)].lat;
                    x.data.pack.lng = pers[parseInt(x.data.pack.per)].lng;
                }
                callme(x);
            }
        });
    }
    $('#searchoss_show').click(function(){
        $('#searchoss').toggle();
    });
    var closedAlready = [];
    function packSignature(p){
        return p.map(function(os){
            return os.os+':'+os.per+':'+os.svc;
        }).join(',');
    }
    function closedYet(p,e){
        var pack = [];
        for(var i in p){
            pack.push({
                os:parseInt(p[i].os),
                per:parseInt(p[i].per),
                svc:p[i].svc
            });
        }
        if( closedAlready.indexOf(packSignature(pack)) > -1){
            return;
        }
        cas.ajaxer({
            method:'GET',
            sendme:{pack:pack},
            etc:{elem:e, pack: pack},
            sendto:'agenda/pack_check',
            andthen:function(x){
                if( !x.data.closed && $.contains(document.documentElement, x.etc.elem[0]) ){
                    x.etc.elem.addClass('NotClosedYet');
                }

                if(x.data.closed){
                    closedAlready.push(packSignature(x.etc.pack));
                }
            }
        });
    }
    function tecBatSig(i){
        if(!tecs[i]){
            return false;
        }

        if(tecs[i].signal === null){
            tecs[i].signal_level = 'Null';
        } else if(tecs[i].signal <= 0.25) {
            tecs[i].signal_level = 'Low';
        } else if(tecs[i].signal <= 0.6) {
            tecs[i].signal_level = 'Half';
        } else {
            tecs[i].signal_level = 'Full';
        }

        if (tecs[i].battery === null) {
            tecs[i].battery_level = 'Null';
        } else if(tecs[i].battery <= 0.2) {
            tecs[i].battery_level = 'Low';
        } else if(tecs[i].battery <= 0.7) {
            tecs[i].battery_level = 'Half';
        } else {
            tecs[i].battery_level = 'Full';
        }
        
        if(tecs[i].gps_status === null){
            tecs[i].gps_status_level = 'Null';
        } else {
            tecs[i].gps_status = parseInt(tecs[i].gps_status);
            if(tecs[i].gps_status === 0){
                tecs[i].gps_status_level = 'Disabled';
            } else if(tecs[i].gps_status === 1) {
                tecs[i].gps_status_level = 'Enabled';
            }
        }
    }
    function tecStruct(i){
        if(!tecs[i])
            return false;
        tecBatSig(i);
        var bat_tt = 
            ((tecs[i].battery === null)
                ?"Nível de Bateria desconhecido"
                :"Bateria em "+cas.roundNumber(tecs[i].battery * 100,2)+"% às "+tecs[i].battery_last_update
            ),
        sig_tt = 
            ((tecs[i].signal === null)
                ?"Nível de sinal desconhecido"
                :"Sinal em "+cas.roundNumber(tecs[i].signal * 100,2)+"% às "+tecs[i].signal_last_update
            ),
        gps_tt = 
            ((tecs[i].gps_status === null)
                ?"Status do GPS desconhecido"
                :"GPS "+((tecs[i].gps_status)?'habilitado':'desabilitado')+" às "+tecs[i].gps_last_update
            )
        ;
        var myw = ((canedit() && mestre) ? 55 + 26 : 0)+((tecs[i].user)?28:0);
        
        tecs[i].elem.empty().html(
            "<div class='tecControls'>"+
                "<div class='tecwrapper'>"+
                    "<div "+
                        " data-tec='"+tecs[i].id+"' data-item='battery' "+
                        " class='tecOpt tecGraph battery"+tecs[i].battery_level+"' title='"+bat_tt+"'></div>"+
                    "<div "+
                        " data-tec='"+tecs[i].id+"' data-item='gps' "+
                        " class='tecOpt tecGraph tecMenu gps"+tecs[i].gps_status_level+"' title='"+gps_tt+"'>"+
                        "<div class='tecSubMenu' style='display:none;width:"+myw+"px'>"+
                            ((canedit() && mestre)
                                ?
                                "<span tec-id='"+i+"' class='tecOpt tecOpenOpt' "+
                                    "title='Controle de ausência'>&zwnj;</span>"+
                                
                                "<span tec-id='"+i+"' class='tecOpt tecCompactSchedule' " +
                                    "title='Compactar agenda'>&zwnj;</span>" +
                             
                                "<span tec-id='"+i+"' class='tecOpt tecCleanAgenda' "+
                                    "title='Limpar agenda do técnico'>&zwnj;</span>"
                             
                                :''
                            )+
                            ((tecs[i].user)
                                ?"<span data-user='" + tecs[i].user +
                                    "' class='tecOpt tecOpenChat chat-user-link' "+
                                        "title='Abrir chat com o técnico'>&zwnj;</span>"
                                :''
                            )+
                        "</div>"+
                    "</div>"+
                    "<div "+
                        " data-tec='"+tecs[i].id+"' data-item='signal' "+
                        " class='tecOpt tecGraph signal"+tecs[i].signal_level+"' title='"+sig_tt+"'></div>"+
                "</div>"+
            "</div>"+
            "<div class='tecName' tec-id='"+tecs[i].id+"'>"+
                "<div class='tecnwrp'>"+
                    "<div class='tecspacer teccolor'style='background-color:"+
                        ((tecs[i].color)?tecs[i].color:'white')+
                    "'></div>"+

                    "<div class='namename' >"+
                        ((perlist.length>1)
                            ?"<span class='tecpername'>"+pers[parseInt(tecs[i].per)].abbr+'</span>':'')+
                        "<span class='namenamename' title='"+tecs[i].name+"'>"+
                            tecs[i].name+
                        "</span>"+
                    "</div>"+
                    "<div class='tectop'>"+tecWorkStat(i)+tecTipos(i)+"</div>"+
                "</div>"+
            "</div>");
        
        
        tecs[i].elem.find('.tecOpenChat').click(openUserChat);
        tecs[i].elem.find('.tecGraph').click(tecStatusHist);
        
        
        tecs[i].elem.find('.tecMenu').hover(function(){
            $(this).children('.tecSubMenu').effect('slide');
        },function(){
            $('#tecbox .tecSubMenu').hide();
        });
        
        if(canedit() && mestre){
            tecs[i].elem.find('.tecOpenOpt').click(tecMenuClick);
            tecs[i].elem.find('.tecCleanAgenda').click(tecCleanAgendaClick);
            tecs[i].elem.find('.tecCompactSchedule').click(tecCompactSchedule);
        }
        
        if(!tecs[i].timeline){
            tecs[i].timeline = 
                    $("<div class='tecline' tec-id='"+tecs[i].id+"'>"+
                        "<div class='workline'></div>"+
                        "<div class='line'></div>"+
                        "<div class='underline'></div>"+
                    '</div>').appendTo(timeline);


            tecs[i].tline = tecs[i].timeline.find('.line');
            tecs[i].uline = tecs[i].timeline.find('.underline');
            tecs[i].workline = tecs[i].timeline.find('.workline');
        }else{
            tecs[i].workline.empty();
        }
    }
    function tecStatusHist(){
        var mytec = parseInt($(this).attr('data-tec')),item = $(this).attr('data-item');
        cas.ajaxer({
            method:'GET',
            sendme:{
                tec: mytec,
                item: item,
                t: dTime()
            },etc:{
                name:tecs[mytec].name,item: item
            },sendto:'agenda/tec_status_hist',
            andthen:tecStatusHist_
        });
        return false;
    }
    function hrFormat(d){
        return cas.strpad(d.getHours(),'0',2,true) + ':' + 
                cas.strpad(d.getMinutes(),'0',2,true) + ':' +
                cas.strpad(d.getSeconds(),'0',2,true);
    }
    function tecStatusHist_(x){
        var fmt = {
            signal:{
                pointFormat: '[{point.y:.1f}%]'
            },
            gps:{
                formatter: function(){
                    var d = new Date();
                    d.setTime(this.x);
                    return '<b>'+ hrFormat(d) +
                            '</b><br>GPS '+(parseInt(this.y)?'Habilitado':'Desabilitado');
                }
            }
        },max = {
            gps:2,
            signal:103,
            battery:103
        };
        fmt.battery = fmt.signal;
        var c = 
            cas.weirdDialogSpawn(null,
                "<div class='chartmodal'>"+
                    "<h3 class='charttitle'>"+x.etc.name+"</h3>"+
                    "<div class='themchart'></div>"+
                "</div>")
                .find('.themchart');
        c.height(c.parent().height() - c.prev('.charttitle').outerHeight());
        c.highcharts('StockChart',{
            rangeSelector: {
                enabled: false
            },
            yAxis:{
                max:max[x.etc.item],
                min:-1
            },
            tooltip:fmt[x.etc.item],
            title: {
                text: x.data.name
            },
            credits: {
                        enabled: false
                    },
            series: [{
                data:x.data.series
            }]
        });
    }
    
    function tecHoverReadClick(){
        $('#tec_'+$(this).attr('tec-id')).trigger('click');
    }
    function tecClear(i){
        if(tecs[i].elem)
            tecs[i].elem.remove();
        if(tecs[i].timeline)
            tecs[i].timeline.remove();
        tecSchedule(i,true);
        delete tecs[i];
    }
    function tecWorkGrid(i){
        var max0 = timeline.width();
        for(var j in tecs[i].workgrid){
            
            var
                xpos = ( ( (tecs[i].workgrid[j].ini * 1000) + window.svroffset - iniD) * timeWidth),
                w = ( ( (tecs[i].workgrid[j].end - tecs[i].workgrid[j].ini) * 1000 + window.svroffset) * timeWidth);

            if(xpos >= 0 && xpos + w < max0)
                $("<div class='workslot "+
                    ((tecs[i].workgrid[j].working)
                        ?'working'
                        :'notworking'
                    )+"' style='width:"+w+"px;'></div>").appendTo(tecs[i].workline).css('left',xpos);
        }
    }
    
    function tecSchedule(i,remove){
        for(var j in tecs[i].schedule){
            tecs[i].schedule[j].marker = null;
            if(remove){
                if(tecs[i].schedule[j].time_elem)
                    tecs[i].schedule[j].time_elem.remove();
                if(tecs[i].schedule[j].real_time)
                    tecs[i].schedule[j].real_time.remove();
            }else{
                tecSchedule_(i,j);
            }
        }
    }
    function triggerDrag(t){
        t.draggable({
            containment:'#timeline',
            snap:'.line',
            snapMode:'inner',
            grid:[1,56],
            disabled:true,
            scroll:false,
            start:function(event,ui){
                $('.hiddenDesloc').removeClass('hiddenDesloc');
                $('#time-drag-label').fadeIn();
                var aux = getSchedule(ui.helper);
                if(!onDrag || onDrag.id !==  aux.id){
                    onDrag = aux;
                    onDrag.newTec = onDrag.tec;
                }
                tecliner(onDrag.newTec);
                onDrag.last = ui.position;
                if(onDrag.desloc)
                    $("[schedule-id="+onDrag.desloc+"]").addClass('hiddenDesloc');
                $('#moveactivity').button('disable');
                $('#savemove').button('disable');
            },
            stop:function(event,ui){
                $('#time-drag-label').fadeOut();
                delete onDrag.last;
                $('#savemove').button('enable');

            },
            drag:dragTime
        });
    }
    function tecSchedule_(i,j){
        
        var mytop,xpos,w,ddsrc,durr,init,tStatus;
        tecs[i].schedule[j].tec = parseInt(tecs[i].schedule[j].tec);
        if(tecs[i].schedule[j].scheduled_duration){
            tecs[i].schedule[j].scheduled_duration = parseInt(tecs[i].schedule[j].scheduled_duration);
            mytop = tecs[i].tline.offset().top - timeline.offset().top;
            init = tecs[i].schedule[j].tini;
            xpos = ( ( ( init * 1000 ) - iniD + window.svroffset) * timeWidth );
            durr = tecs[i].schedule[j].scheduled_duration;

            w = (durr * timeWidth * 1000);

            ddsrc =
                ((tecs[i].schedule[j].assname)
                    ?'['+tecs[i].schedule[j].pack.length+' OS] '+tecs[i].schedule[j].assname
                    :tecs[i].schedule[j].descr+
                        ((tecs[i].schedule[j].distance)
                            ?' de '+
                            cas.fKM(tecs[i].schedule[j].distance)
                            :''
                        )
                )+": "+tecs[i].schedule[j].scheduled_ini.substr(-8)+' às '+tecs[i].schedule[j].scheduled_end.substr(-8)
                + " ("+Math.floor(parseInt(tecs[i].schedule[j].scheduled_duration) / 60)+" minutos)"
                +((tecs[i].schedule[j].obs)
                    ?' [OBS: '+tecs[i].schedule[j].obs+']'
                    :''
                    );
            tStatus = ((tecs[i].schedule[j].real_ini)?((tecs[i].schedule[j].real_end)?'Done':'Active'):'Pending');
            
            tecs[i].schedule[j].time_elem =
                $(
                    "<div"+
                        " id='schedule_"+tecs[i].schedule[j].id+"'"+
                        " tec-id='"+tecs[i].id+"'"+
                        " schedule-id='"+tecs[i].schedule[j].id+"'"+
                        " schedule-index='"+j+"'"+
                        " class='timeBlock tec"+tecs[i].schedule[j].activity.toUpperCase()+"'"+
                        " style='width:"+w+"px;'>"+
                    "</div>"
                ).appendTo('#timeblocks')
                .css('left',xpos)
                .css('top',mytop)
                .click(
                    ((tecs[i].schedule[j].activity === 'desloc')
                        ?deslocClick
                        :blockClick
                    )
                )
                .attr('title',ddsrc)
                .hover(tHover,tHoverOut)
                .addClass(tecs[i].schedule[j].activity + tStatus);
            
            if (tecs[i].schedule[j].assinanteTipo && tecs[i].schedule[j].assinanteTipo.sub_tipo === 'corporativo') {
                tecs[i].schedule[j].time_elem.addClass('assCorporativo');
            }
            
            if (tecs[i].schedule[j].activity === 'os' ) {
                var s = tecs[i].schedule[j],
                    icons = $("<div class='scheduleIcons'>").appendTo(s.time_elem);
                
                if (s.eventos) {
                    var eventoFechado = true;
                    for(var eventoIndex in s.eventos){
                        if(s.eventos[eventoIndex].status !== 'fechado'){
                            eventoFechado = false;
                            break;
                        }
                    }
                    var eventoIcon =
                        $("<span class='travadoManual'>&zwnj;</span>")
                            .attr('title','Node travado no manual')
                            .appendTo(icons);
                    if (eventoFechado) {
                        eventoIcon.attr('title','Atividade criada durante evento massivo').addClass('dead');
                    }
                    
                }

                if (s.revisita) {
                    $("<span class='assinanteRevisita'>&zwnj;</span>")
                        .attr('title','Cliente crítico, '+s.reclamacoes + ' reclamações recentes')
                        .appendTo(icons);
                }

                if (s.schedule_creator) {
                    $("<span class='customSchedule'>&zwnj;</span>")
                        .attr('title','Atividade inserida manualmente por '+s.schedule_creator.user + ' em '+s.schedule_creator.time)
                        .appendTo(icons);
                }
            }
            
            tecs[i].schedule[j].descr = ((tecs[i].schedule[j].assname) ? tecs[i].schedule[j].assname : tecs[i].schedule[j].descr);
            tecs[i].schedule[j].xpos = xpos;
            tecs[i].schedule[j].ypos = mytop;
        }

        if(tecs[i].schedule[j].real_ini){
            var myid = parseInt(tecs[i].schedule[j].id);
            var inner = '';

            if(tecs[i].schedule[j].scheduled_duration)
                tecs[i].schedule[j].time_elem.addClass('started');
            var rac = 'Closed';
            tecs[i].schedule[j].real_duration = tecs[i].schedule[j].real_duration * 1000;
            if(!tecs[i].schedule[j].real_end){
                tecs[i].schedule[j].trend = window.svrtime.getTime();
                tecs[i].schedule[j].real_duration = (window.svrtime.getTime() - window.svroffset - (tecs[i].schedule[j].trini * 1000));
                rac = 'Open';
            }
            
            if(tecs[i].schedule[j].activity === 'tec_status'){
                inner = "<div class='statuswrapper'><div class='statusx "+
                        ((tecs[i].schedule[j].descr.toLowerCase() === 'lanche')
                            ?'statuslunch'
                            :'statusflag'
                        )+"'></div></div>";

                if(tecs[i].schedule[j].ack){
                    cas.kill(myid,alerts.status);
                }else{
                    ackStatus(myid);
                    rac = 'Open';
                }
            }

            mytop = tecs[i].uline.offset().top - timeline.offset().top;
            xpos = ( ((tecs[i].schedule[j].trini * 1000) - iniD + window.svroffset) * timeWidth);
            w = (parseInt(tecs[i].schedule[j].real_duration) * timeWidth);
            if(
                (xpos + w) <= timeline.width() 
                &&
                xpos >= 0
            ){
                tecs[i].schedule[j].real_time = 
                $(
                    "<div class='realActivity real"+rac+"'"+
                        " tec-id='"+tecs[i].id+"'"+
                        " schedule-id='"+tecs[i].schedule[j].id+"'"+
                        " schedule-index='"+j+"'>"
                ).append(inner)
                .appendTo('#timeblocks')
                .css('top',mytop)
                .css('left',xpos)
                .width(w)
                .attr('title',
                    ((tecs[i].schedule[j].assname)
                    ?tecs[i].schedule[j].assname
                    :tecs[i].schedule[j].descr)
                    +" - iniciado às "+tecs[i].schedule[j].real_ini.substr(-8)+
                        ((tecs[i].schedule[j].real_end)
                        ?
                            ', terminado às '
                                    + tecs[i].schedule[j].real_end.substr(-8) 
                                    + ' (' 
                                    + Math.floor(tecs[i].schedule[j].real_duration / 1000 / 60)
                                    + ' minutos)'
                        :' (em andamento)')+
                    ((tecs[i].schedule[j].obs)
                        ?' [OBS: '+tecs[i].schedule[j].obs+"]"
                        :''
                    )
                ).hover(tHover,tHoverOut)
                .click(tClick);
            }
        }
    }
    function hoverSelect_(me){
        var x;
        if(me.is('.realActivity'))
            x = '.timeBlock';
        else
            x = '.realActivity';
        
        return $(x+"[schedule-id='"+parseInt(me.attr('schedule-id'))+"']");
    }
    function tClick(){
        var x = hoverSelect_($(this));
        if(x.length > 0)
            x.trigger('click');
        else{
            var 
                i = parseInt($(this).attr('tec-id')),
                j = parseInt($(this).attr('schedule-index'));
            selectTec(i);
            leftPanelToggle(true);
            ospFill(i,j);
            $('#osp-unschedule').button('disable');
        }
    }
    function tHover(){
        hoverSelect_($(this)).addClass('GenericHoverClass');
    }
    function tHoverOut(){
        hoverSelect_($(this)).removeClass('GenericHoverClass');
    }
    function ackStatus(z){
        z = getActivityById(z);
        var id = parseInt(z.id);
        var mycallback = function(){killStatus(id,z);};
        if(!cas.inArray(id,alerts.status)){
            alerts.status.push(id);
            cas.makeNotif(
                'warning','Novo Status: '+z.descr,
                mycallback
            );
            desktopNotification(
                'Agenda - Nova Atualização de Status',
                'Técnico informa novo status que precisa de confirmação',
                'agenda-status-'+id,
                mycallback
            );
        }
    }
    function killStatus(id,z){
        $('.realActivity[schedule-id="'+z.id+'"]').trigger('click');
        var t = tecs[parseInt(z.tec)];
        if(confirm(t.name+" informa um novo status: \n"+z.descr+" iniciado às "+z.real_ini.substr(-8)+", ok?")){
            cas.kill(id,alerts.status);
            cas.ajaxer({
                method:'GET',
                sendto:'agenda/ack_status',
                sendme:{
                    id:z.id
                }
            });
        }
    }
    var onDrag = {};
    function unShift(force){
        for(var i in tecs)
            for(var j in tecs[i].schedule)
                if(
                        tecs[i].schedule[j].time_elem
                        &&
                        (
                            tecs[i].schedule[j] !== onDrag 
                            || force
                        )
                    )
                    tecs[i].schedule[j].time_elem.css('left',tecs[i].schedule[j].xpos).css('top',tecs[i].schedule[j].ypos).removeClass('movedInTime');
    }
    function tecliner(i){
        tecline = [];
        for(var j in tecs[i].schedule)
            if(
                tecs[i].schedule[j].activity !== 'desloc'
                &&
                tecs[i].schedule[j].scheduled_duration
                &&
                tecs[i].schedule[j].time_elem
                &&
                parseInt(tecs[i].schedule[j].id) !== parseInt(onDrag.id)
                &&
                (onDrag.id === null || tecs[i].schedule[j].destination !== onDrag.id)
            )
                tecline.push(tecs[i].schedule[j]);
    }
    function dragTime(event,ui){
        var vdiff = 100,j;
        var x =  ui.position.left / timeWidth;
        var d = new Date();
        d.setTime(window.svrtime.getTime());
        d.setTime(x + iniD.getTime());
        $('#time-drag-label').html(
            cas.strpad(d.getHours(),'0',2,true)
            + ':' + cas.strpad(d.getMinutes(),'0',2,true) + ':'
            + cas.strpad(d.getSeconds(),'0',2,true)
        );
        if(onDrag.last.top !== ui.helper.position().top){
            for(var i in tecs){
                vdiff = Math.abs(ui.helper.position().top 
                            - ( tecs[i].timeline.offset().top - timeline.offset().top )
                );
                if(vdiff < 3 && i !== onDrag.newTec){
                    unShift();
                    onDrag.newTec = i;
                    tecliner(i);
                    break;
                }
            }
            onDrag.last.top = ui.position.top;
        }

        onDrag.fitsat = 0;
        j = 0;

        var midpoint = ( (ui.position.left) + (ui.position.left + ui.helper.width()) ) / 2;
        while(
            j < tecline.length
            &&
            (
                midpoint >
                    ( ( tecline[j].time_elem.position().left + ( tecline[j].time_elem.position().left + tecline[j].time_elem.width() ) ) / 2 )
            )
        ){
            j++;
        }
        var err = false;
        if(tecline[j-1])
            if(!collisionCheck(tecline[j-1],onDrag,true))
                err = true;

        if(tecline[j])
            if(!collisionCheck(onDrag,tecline[j]))
                err = true;

        onDrag.fitsat = j;
        if(!collisionLoop())
            err = true;

        if(err){
            shiftBack();
        }

    }
    function shiftBack(){
        for(var i in tecline){
            tecline[i].time_elem.css('left',tecline[i].last_left);
            
            var t = getActivityById(tecline[i].desloc);
            if(t)
                t.time_elem.css('left',t.last_left);
        }
    }
    function collisionLoop(){
        
        var k;

        for(k = onDrag.fitsat;k < (tecline.length-1);k++)
            if(!(tecline[k+1].real_ini || tecline[k].real_ini)
                    && !collisionCheck(tecline[k],tecline[k+1]))
                return false;

        for(k = (onDrag.fitsat - 1); k > 0;k--)
            if(!(tecline[k-1].real_ini || tecline[k].real_ini)
                    && !collisionCheck(tecline[k-1],tecline[k],true))
                return false;
        return true;
    }
    function getSchedule(x){
        var tec = parseInt(x.attr('tec-id'));
        var s = x.attr('schedule-index');
        if(typeof s === 'undefined')
            return zPack;
        else{
            s = parseInt(s);
            return tecs[tec].schedule[s];
        }
    }

    function collisionCheck(a,b,left){
        var aX = null,bX = null;
        if(a.desloc && a.id !== onDrag.id)
            aX = getActivityById(a.desloc);
        
        if(b.desloc && b.id !== onDrag.id)
            bX = getActivityById(b.desloc);
        
        
        var hdiff =  
            (
                ((aX)
                    ?aX.time_elem.position().left
                    :a.time_elem.position().left
                )
                + a.time_elem.width() 
                +((aX)
                    ?aX.time_elem.width()
                    :0
                )
            ) 
            - 
                ((bX)
                    ?bX.time_elem.position().left
                    :b.time_elem.position().left
                )
        ,z;
        
        if(hdiff > timeWidth * 1000){
            var MinPos = minPos - $('#timeblocks').offset().left;
            if(left){
                z = ((aX)?aX.time_elem.position().left:a.time_elem.position().left) - hdiff;
                var realini = ((aX)?aX.real_ini:a.real_ini);
                var xpos = ((aX)?aX.xpos:a.xpos);
                if( !realini && z >= 0 /* ( ( xpos <= MinPos && z >= 0 ) || z >= MinPos ) */ ){
                    a.last_left = a.time_elem.position().left;
                    a.time_elem.css('left', z + ((aX)?aX.time_elem.width():0)).addClass('movedInTime');
                    if(aX){
                        aX.last_left = aX.time_elem.position().left;
                        aX.time_elem.css('left',z).addClass('movedInTime');
                    }
                }else{
                    return false;
                }
                
            }else{
                z = ((bX)?bX.time_elem.position().left:b.time_elem.position().left) + hdiff;
                var realini = ((bX)?bX.real_ini:b.real_ini);
                var width = ((bX)?bX.time_elem.width():0);
                if(!realini && (z +  width + b.time_elem.width()) <= timeline.width()){
                    b.last_left = b.time_elem.position().left;
                    b.time_elem.css('left',z + ((bX)?bX.time_elem.width():0)).addClass('movedInTime');
                    if(bX){
                        bX.last_left = bX.time_elem.position().left;
                        bX.time_elem.css('left',z).addClass('movedInTime');
                    }
                }else{
                    return false;
                }
                
            }
        }
        return true;
    }
    var this_tec;
    function tecMenuClick(){
        var i = parseInt($(this).attr('tec-id'));
        $('#tec_timeout_dlg').dialog("option", "title", tecs[i].name.toUpperCase()).dialog('open');
        this_tec = i;
        loadTimeouts();
        return false;
    }
    function tecCleanAgendaClick(e){
        
        var i = parseInt($(this).attr('tec-id'));
        
        if (!confirm('Você deseja realmente esvaziar a agenda do técnico '+tecs[i].name.toUpperCase() +"?")) {
            return false;
        }
        
        someMenJustWanttoWatchTheWorldBurn(i);
        return false;
    }
    function tecCompactSchedule () {
        
        var tec = parseInt($(this).attr('tec-id'));

        cas.ajaxer({
            method: 'GET',
            sendto: 'agenda/compact_schedule',
            sendme: {
                tec: tec,
                t: dTime()
            },
            andthen: F5
        });

        return false;
    }
    function getActivityById(x){
        x = parseInt(x);
        for(var i in tecs)
            for(var zz in tecs[i].schedule)
                if(parseInt(tecs[i].schedule[zz].id) === x)
                    return tecs[i].schedule[zz];
        return null;
    }
    function deslocClick(){
        $('#savemove,#moveactivity').button('disable');
        activitySelected = null;
        var i = parseInt($(this).attr('tec-id')),
            j = parseInt($(this).attr('schedule-index'));

        selectTec(i);
        leftPanelToggle(true);
        ospFill(i,j);
        blockSelect($(this));
        deslocRoute(i,j);
    }
    function deslocRoute(i,j){
        if(!tecs[i].schedule[j].route){
            var dest = getActivityById(tecs[i].schedule[j].destination);
            var r  = {
                origin: [tecs[i].schedule[j].lat, tecs[i].schedule[j].lng],
                destination: [dest.lat, dest.lng],
                travelMode: 'driving',
                strokeColor: '#3A3D75',
                strokeOpacity: 0.7,
                strokeWeight: 6
            };
            tecs[i].schedule[j].route = true;
            map.drawRoute(r);
            theZoom(dest.lat,dest.lng);
        }
    }
    function selectTec(i){
        i = parseInt(i);
        if(!tecs[i].elem.is('.tecSelected'))
            tecs[i].elem.trigger('click');
    }
    var realMarker = null;
    function realMarkerKill(){
        if(realMarker)
            realMarker.setMap(null);
    }
    function blockClick(){
        
        if(!$(this).is('.activityHover')){
            agendaAutoScroll();
            unShift(true);
            removeFlyingOS();
            
            var 
                i = parseInt($(this).attr('tec-id')),
                j = parseInt($(this).attr('schedule-index'));
            
            selectTec(i);
            leftPanelToggle(true);
            ospFill(i,j);
            yetAnotherOsGet(tecs[i].schedule[j]);
            
            
            $('.timeBlock.ui-draggable').draggable('disable');
            
            activitySelected = null;
            $('#moveactivity').button('disable');
            $('#osp-unschedule').button('disable');
            
            if($(this).is('.tecOS'))
                activitySelected = $(this);    
            
            var t = getActivityById($(this).attr('schedule-id'));
            var td = ((t.desloc)?getActivityById(t.desloc):{real_ini:false});
            
            realMarkerKill();
            if(t.real_lat){
                realMarker = map.addMarker({
                    lat: t.real_lat,
                    lng: t.real_lng,
                    icon:'/lib/img/Black_MapDrop.png',
                    title: 'Local de baixa'
                });
            }
            if(!t.real_ini && !td.real_ini && canedit() 
                    || ( canedit() && mestre && !t.real_ini && td.real_ini && td.real_end ) )
                $('#osp-unschedule').button('enable');
            
            if(!t.real_ini && !td.real_ini && canedit() && $(this).is('.tecOS') )
                $('#moveactivity').button('enable');
            
            $('#savemove').button('disable');
            
            blockSelect($(this));
            var m = layer.schedule.x[tecs[i].schedule[j].marker];
            if(m)
                google.maps.event.trigger(m, 'click');
        }
    }
    function blockSelect(block){
        $('.activityHover').removeClass('activityHover');
        block.addClass('activityHover');
    }
    function ospFill(i,j){
        $('#ospanel-w').empty();
        $('#osp-tecname').html(tecs[i].name);
        $('#osp-descr').html(
            (tecs[i].schedule[j].assname)
                ?"<a target='_blank' href='dashboard#"+cas.hashbangify({dashboard:
                        {dashboard:'ri',
                        view:'assinante',
                        ind:'imb',
                        item:tecs[i].schedule[j].per+':'+tecs[i].schedule[j].asscod}
                    })+"'>"+tecs[i].schedule[j].assname+"</a>"
                :tecs[i].schedule[j].descr
        );
        cas.weirdDialogClose();
        $('#osp-hist-show').hide().attr('schedule-id',tecs[i].schedule[j].id);
        
        checkScheduleHist(tecs[i].schedule[j].id);
        tecScheduleItem(tecs[i].schedule[j].id);
        eventosRelacionados(tecs[i].schedule[j].eventos)
        cas.ajaxer({
            method:'GET',
            sendme:{id:tecs[i].schedule[j].id},
            sendto:'agenda/tec_schedule_pics',
            andthen:function(result){
                makePicSlide($('#osptb'),result.data);
            }
        });
        
        $('#osp-unschedule').attr('schedule-id',tecs[i].schedule[j].id);
        $('#osp-tini').html(defDtStr(tecs[i].schedule[j].scheduled_ini));
        
        $('#osp-tend').html(defDtStr(tecs[i].schedule[j].scheduled_end));
        $('#osp-rini').html(defDtStr(tecs[i].schedule[j].real_ini));
        
        if(canedit() && mestre && tecs[i].schedule[j].real_ini && !tecs[i].schedule[j].real_end ){
            $("<button data-id='"+tecs[i].schedule[j].id+"'>FIM</button>").click(setEND)
                    .appendTo($('#osp-rend').empty());
        }else{
            $('#osp-rend').html(defDtStr(tecs[i].schedule[j].real_end));
        }
        if(tecs[i].schedule[j].obs)
            $('#osp-obs').html(tecs[i].schedule[j].obs).parent().show();
        else
            $('#osp-obs').parent().hide();
        
    }
    function tecScheduleItem(id){
        cas.ajaxer({
            method:'GET',
            sendme:{
                tec_schedule:id
            },sendto:'agenda/tec_schedule_item',
            andthen:tecScheduleItem_
        });
    }
    function eventosRelacionados(eventos){
        $('.schedule-evento').remove();
        var $item = $('#tecscheduleitem'), tb;
        for(var i in eventos){
            tb = $('<table>').addClass('ospaneltb').addClass('schedule-evento').insertBefore($item);
            
            var link = 'display';
            if(cas.checkPerms('e'))
                link = 'eventos#'+cas.hashbangify({tt:eventos[i].id});
            
            tb.prepend("<thead><tr><th colspan=2 class='osptd'>"+
                "Travado no manual:<br> "+
                    "<a class='eventolink' href='"+link+"' target='_blanl'>"+eventos[i].descr+"</a></th></tr></thead>");
            if(eventos[i].status === 'fechado'){
                tb.append("<tr>"+
                    "<td class='osptd'>Início</td>"+
                    "<td class='osptd green'>Fechado em</td>"+
                "</tr>");
                tb.append("<tr>"+
                    "<td class='osptd'>"+eventos[i].ini+"</td>"+
                    "<td class='osptd green'>"+eventos[i].last_update+"</td>"+
                "</tr>");
            }else{
                tb.append("<tr>"+
                    "<td class='osptd'>Início</td>"+
                    "<td class='osptd'>Previsão</td>"+
                "</tr>");
                tb.append("<tr>"+
                    "<td class='osptd'>"+eventos[i].ini+"</td>"+
                    "<td class='osptd'>"+eventos[i].deadline+"</td>"+
                "</tr>");
            }
            tb.append("<tr><td colspan=2 class='osptd'>"+eventos[i].location+"</td></tr>");
        }
        
    }
    function tecScheduleItem_(x){
        $('#tecscheduleitem').empty().hide();
        if(x.data.item && x.data.item.length){
            $('#tecscheduleitem').html("<div class='tci_descr'>Itens utlizados</div>").show();
            var t = null,i,c = {},lt = null;
            for(i in x.data.item){
                if(lt === null || lt !==  x.data.item[i].almox_type_id){
                    lt =  x.data.item[i].almox_type_id;
                    t = 
                        $("<table class='tci_type' id='tci_type_" + x.data.item[i].almox_type_id + "'>" +
                            "<thead>"+
                                "<tr>"+
                                    "<td class='tci_t_count'>0</td>"+
                                    "<td class='tci_t_name'>" + x.data.item[i].almox_type_name + "</td>"+
                                "</tr>"+
                            "</thead><tbody></tbody>"+
                        "</table>").appendTo('#tecscheduleitem');
                    t.find('thead').click(tciHeadClick);
                    c[x.data.item[i].almox_type_id] = 0;
                }
                c[x.data.item[i].almox_type_id]++;
                $("<tr>"+
                    "<td>"+x.data.item[i].almox_item_id+"</td>"+
                    "<td>"+((x.data.item[i].almox_item_name)?x.data.item[i].almox_item_name:'---')+"</td>"+
                "</tr>").appendTo(t.find('tbody'));
            }
            for(i in c)
                $('#tci_type_'+i).find('.tci_t_count').html(c[i]);
        }
    }
    function tciHeadClick(){
        $(this).next('tbody').slideToggle();
    }
    function checkScheduleHist(id){
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/has_tec_schedule_hist',
            sendme:{
                id:id
            },andthen:function(x){
                if(parseInt(x.data.c) > 0)
                    $('#osp-hist-show').show();
            }
        });
    }
    function tecScheduleHist(id){
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/tec_schedule_hist',
            sendme:{
                id:id
            },etc:{
                id:id
            },
            andthen:_tecScheduleHist
        });
    }
    function _tecScheduleHist(x){
        if(parseInt(x.etc.id) === parseInt($('#osp-hist-show').attr('schedule-id'))){
            var ini1,end1,htm = '';
            for(var i in x.data.hist){
                ini1 = (x.data.hist[i].old_ini && x.data.hist[i].old_end);
                end1 = (x.data.hist[i].new_ini && x.data.hist[i].new_end);
                htm +=
                    "<tr>"+
                        "<td class='osp_hist_time' colspan=1>"+
                            x.data.hist[i].time+
                        "</td>"+
                        "<td class='osp_hist_time' colspan=2>"+
                            x.data.hist[i].descr+
                        "</td>"+
                    "</tr>"+
                    "<tr>"+
                        "<td class='osp_hist_user' colspan=3>"+
                            x.data.hist[i].user+
                        "</td>"+
                    "</tr>"+
                    ((ini1 || end1)
                        ?"<tr>"+
                            "<td class='osp_hist_center'></td>"+
                            "<td class='osp_hist_center'>Início</td>"+
                            "<td class='osp_hist_center'>Fim</td>"+
                        "</tr>"
                        :''
                    )+
                    ((ini1)?
                        "<tr>"+
                            "<td class='osp_hist_schedule1'>Antes</td>"+
                            "<td class='osp_hist_schedule2'>"+
                                x.data.hist[i].old_ini+
                            "</td>"+
                            "<td class='osp_hist_schedule2'>"+
                                x.data.hist[i].old_end+
                            "</td>"+
                        "</tr>"
                    :'')+
                    ((end1)?
                        "<tr>"+
                            "<td class='osp_hist_schedule1'>Depois</td>"+
                            "<td class='osp_hist_schedule2'>"+
                                x.data.hist[i].new_ini+
                            "</td>"+
                            "<td class='osp_hist_schedule2'>"+
                                x.data.hist[i].new_end+
                            "</td>"+
                        "</tr>"
                    :'')+
                    ((x.data.hist[i].new_tec || x.data.hist[i].old_tec)
                        ?"<tr>"+
                            "<td class='osp_hist_center' colspan=3>Técnico</td>"+
                        "</tr>"
                        :''
                    )+
                    ((x.data.hist[i].old_tec)?
                        "<tr>"+
                            "<td class='osp_hist_schedule1'>Antes</td>"+
                            "<td class='osp_hist_schedule2' colspan=2>"+
                                x.data.hist[i].old_tec_x.name.toUpperCase()+
                            "</td>"+
                        "</tr>"
                    :'')+
                    ((x.data.hist[i].new_tec)?
                        "<tr>"+
                            "<td class='osp_hist_schedule1'>Depois</td>"+
                            "<td class='osp_hist_schedule2' colspan=2>"+
                                x.data.hist[i].new_tec_x.name.toUpperCase()+
                            "</td>"+
                        "</tr>"
                    :'');
            }
            cas.weirdDialogSpawn(
                    {left: $('#osp-hist-show').offset().left + 100,
                    top:$('#osp-hist-show').offset().top - 20},
                    "<table id='osp-hist'>"+htm+'</table>'
            ,null,true);
        }
    }
    function setEND(){
        var obs = prompt('Só clique em OK caso você realmente queira finalizar esta atividade. Você pode deixar uma observação explicando porque foi necessário o fechamento.','Fechado por '+cas.user.login);
        if(obs !== null){
            cas.ajaxer({
                sendto:'agenda/set_end',
                sendme:{id:$(this).attr('data-id'),obs:obs},
                andthen:function(x){
                    loadPer(true);
                }
            });
        }
    }
    
    function defDtStr(x){
        if(x)
            return x.substr(-8,5);
        else
            return '---';
    }
    
    function activityMapClick(){
        var $elem = this.details.time_elem;
        $elem.trigger('click');
    }
    function ageToHour(age){
        var s = '';
        if(layer.track.x.length > 0){
            var x = layer.track.x[0].details.time, 
                d = new Date(), 
                t = ( parseInt(x)*1000 - (age * 60 * 1000) );
            d.setTime(t);
            s = 
                cas.strpad(d.getHours(),'0',2,true)+':'+cas.strpad(d.getMinutes(),'0',2,true);
        }
        return s;
    }
    function tecTrackMark(list,i,v){
        var track = list[i];
        var marker =
            map.addMarker({
                lat: track.lat,
                lng: track.lng,
                icon:'/lib/img/'+
                    ( ( i === 0 )
                        ?'arrow_down_black.png'
                        :((track.speed > 80)
                            ?'marker_red.png'
                            :'ok-pin.png'
                        )
                    ),
                title: track.time,
                details: {
                    age:list[i].age,
                    time:list[i].xtime,
                    track: track
                },
                visible: v,
                zIndex: layer.track.zIndex
            });
        layer.track.x.push(marker);
        google.maps.event.addDomListener(marker, 'click',tecTrackMarkClick);//activityMapClick);
    }
    function tecTrackMarkClick(){
        var track = this.details.track;
        var marker = this;
        
        if(track.battery || track.signal)
            return showTrackInfo(track,marker);
        
        cas.ajaxer({
            method: 'GET',
            sendme: {tec: track.tec, timestamp: track.timestamp},
            sendto: 'agenda/tec_track_data',
            andthen: function(x){
                $.extend(track,x.data.extra);
                showTrackInfo(track,marker);
                
            }
        });
    }
    function showTrackInfo(track,marker){
        var infowindow = new google.maps.InfoWindow({maxWidth:700});
        infowindow.setContent(
            '<h3><b>Rastreado às <i>'+track.time+'</i></b></h3>'+
            '<p><b>Velocidade:</b> '+track.speed+" km/h</p>"+

            ((track.battery)
                ?'<p><b>['+track.battery_time+'] Bateria:</b> '+track.battery+"%<p>"
                :''
            )+
            ((track.signal)
                ?'<p><b>['+track.signal_time+'] Sinal:</b> '+track.signal+"%</p>"
                :''
            )+
            ((track.battery)
                ?'<p><b>['+track.battery_time+'] Bateria:</b> '+track.battery+"%<p>"
                :''
            )+
            ((track.signal)
                ?'<p><b>['+track.signal_time+'] Sinal:</b> '+track.signal+"%</p>"
                :''
            )
        );
        infowindow.open(map.map, marker);
    }
    var minuteSpan = [0,60 * 2];
    function minuteSpanChange(event,ui){
        minuteSpan = ui.values;
    }
    var minuteSpanSlideOKT;
    function minuteSpanSlideOK(){
        if(layer.track.visible)
            for(var i in layer.track.x)
                layer.track.x[i].setVisible( inMinuteSpan(layer.track.x[i].details.age) );
    }
    function minuteSpanSlideUpdate(event,ui){
        clearTimeout(minuteSpanSlideOKT);
        if(ui)
            minuteSpan = ui.values;
        else
            $("#minuteSpanSelector" ).slider('values',minuteSpan);
        
        $('#tracktooltip').html(
                "Mostrar de "+ageToHour(minuteSpan[0])
                +" à "+ageToHour(minuteSpan[1])+".");
        minuteSpanSlideOKT = setTimeout(minuteSpanSlideOK,100);
    }
    function loadTecTrack(){
        clearTimeout(trackrun);
        
        layerSetVisible(layer.track,true);
        cas.ajaxer({
                method:'GET',
                sendto:'agenda/tec_track',
                sendme:{
                    tec: tec,
                    t: dTime()
                },
                andthen:trackIndex_
            });
    }
    function inMinuteSpan(x){
            return x >= minuteSpan[0] && x <= minuteSpan[1];
    }
    function trackIndex_(x,i){
        if(layer.track.visible){
            if(!i){
                i = 0;
            }
            
            if(i === 0){
                var len = x.data.track.length;
                if(len > 0){
                    cas.makeNotif('information','Rastro do técnico carregado, contendo '+len+' posiç'+ ((len>1)?'ões':'ão') +'.');
                }else{
                    cas.makeNotif('warning','Nenhuma posição registrada.');
                }
            }
            
            if(x 
                && x.data 
                && x.data.track 
                && x.data.track[i]
            ){
                
                
                var poly,
                    v = 
                        inMinuteSpan(x.data.track[i].age)
                        && layer.track.visible;

                tecTrackMark(x.data.track, i, v);
                minuteSpan = x.data.minuteSpan;
                minuteSpanSlideUpdate();
                $('#minuteSpanConfig').fadeIn();
                
                if(i > 0){
                    poly = map.drawPolyline({
                        path: 
                        [
                            [x.data.track[i-1].lat,x.data.track[i-1].lng],
                            [x.data.track[i].lat,x.data.track[i].lng]
                        ],
                        strokeColor: '#3D863E',
                        strokeOpacity: 0.5
                    });
                    poly.details = {age:x.data.track[i].age};
                    poly.setVisible(v);
                    layer.track.x.push(poly);
                }
                var f = function(){trackIndex_(x,i+1);};
                if(x.data.track[i+1]){
                    if(v)
                        trackrun = setTimeout(f,50);
                    else
                        f();
                }
            }
        }
    }
    function theZoom(lat,lng){
        map.setCenter(lat,lng);
        /*if(map.zoom < 10)
            map.setZoom(10);*/
    }
    function tecAgendaMark(){
        var mBounds = new google.maps.LatLngBounds(),bcount = 0;
        for(var j in tecs[tec].schedule){
            tecs[tec].schedule[j].route = null;
            if(tecs[tec].schedule[j].lat && tecs[tec].schedule[j].activity !== 'desloc'){
                var mark =
                    map.addMarker({
                        lat: tecs[tec].schedule[j].lat,
                        lng: tecs[tec].schedule[j].lng,
                        details: tecs[tec].schedule[j],
                        icon:'/lib/img/'+((tecs[tec].schedule[j].real_end)?'home-marker-done.png':'home-marker.png'),
                        title:tecs[tec].schedule[j].descr,
                        zIndex:layer.schedule.zIndex,
                        visible:layer.schedule.visible,
                        infoWindow:{
                            content:
                                     "<div style='height:60px;line-height:20px;'>"+
                                        "<h4>"+tecs[tec].schedule[j].descr+"</h4>"+
                                    "</div>"
                                
                        }
                    });
                mBounds.extend(new google.maps.LatLng(tecs[tec].schedule[j].lat,tecs[tec].schedule[j].lng));
                bcount++;
                theZoom(tecs[tec].schedule[j].lat,tecs[tec].schedule[j].lng);
                tecs[tec].schedule[j].marker = layer.schedule.x.length;
                layer.schedule.x.push(mark);
                google.maps.event.addDomListener(mark, 'click',activityMapClick);
            }
        }
        if(bcount)
            map.fitBounds(mBounds);
        else
            theZoom(pers[parseInt(tecs[tec].per)].lat,pers[parseInt(tecs[tec].per)].lng);
    }
    function cleanTecSelection(){
        map.cleanRoute();
        
        killLayer(layer.track);
        layerSetVisible(layer.track,false);
        
        realMarkerKill();
        $('.activityHover').removeClass('activityHover');
        
        killLayer(layer.schedule);
        $('.tecSelected').removeClass('tecSelected');
        leftPanelToggle(false);
        cas.weirdDialogClose();
        aReWind();
        $('.osp-goback').button('disable');
    }
    
    function unTec(){
        $('.tecSelected').trigger('click');
    }
    function tecClick(){
        cleanTecSelection();
        
        if(tec !== parseInt($(this).attr('tec-id'))){
            tecSelect($(this));
        }else{
            tecNull();
        }
    }
    function tecNull(){
        $('#minuteSpanConfig').fadeOut();
        tempUnLock();
        layerSetVisible(layer.pending,true);
        layerSetVisible(layer.tecpos,true);
        zoomPending();
        $('#savemove,#moveactivity').button('disable');
        leftPanelToggle(false);
        tec = null;
    }
    function tecSelect(me){
        tempLock();
        tec = parseInt(me.attr('tec-id'));
        for(var i in map.polynes){
            map.polynes[i].setMap(null);
        }
        layerSetVisible(layer.pending,false);
        layerSetVisible(layer.tecpos,false);
        loadTecTrack();
        me.addClass('tecSelected');
        tecAgendaMark();
        agendaAutoScroll(me);
        $('.osp-goback').button('enable');
    }
    

    function leftPanelToggle(open){
        if(open){
            if($('#oswrapper').is(':visible'))
                $('#oswrapper').hide();
            if(!$('#ospanel').is(':visible'))
                $('#ospanel').show();
        }else{
            if(!$('#oswrapper').is(':visible'))
                $('#oswrapper').show();
            if($('#ospanel').is(':visible'))
                $('#ospanel').hide();
        }
    }
    function autoRoute(){
        var dlg = $('#autoroutedlg');
        if(!dlg.length){
            dlg = 
                $("<div id='autoroutedlg'>")
                    .append("<h5>Deseja alocar também o almoço dos técnicos?</h5>")
                    .append("<div class='auto-route-radio-lunch'>"+
                        "<input type='radio' id='arradio1' name='auto-route-radio-lunch' value=true checked='checked'><label for='arradio1'>Sim</label>"+
                        "<input type='radio' id='arradio2' name='auto-route-radio-lunch' value=false><label for='arradio2'>Não</label>"+
                    "</div>");
                    /*.append("<h5>Escolha o Método de Roteamento:</h5>")
                    .append("<div class='auto-route-radio-method'>"+
                        "<input type='radio' id='arradio3' name='auto-route-radio-method' value='magic'><label for='arradio3'>Tradicional</label>"+
                        "<input type='radio' id='arradio4' name='auto-route-radio-method' value='fancy_router' checked='checked'><label for='arradio4'>Novo método</label>"+
                    "</div>");*/
            dlg.dialog({
                autoOpen: false,
                modal: true,
                closeOnEscape: false,
                width: 400,
                dialogClass: 'noTitleDialog',
                resizable: false,
                open: function(event,ui){
                    tempLock();
                },
                buttons: [ 
                    { 
                        text: "Gerar Rota", 
                        click: function() { 
                            $(this).dialog('close');
                            cas.hidethis('body');
                            cas.ajaxer({
                                method:'GET',
                                sendme:{
                                    perlist: perlist,
                                    t: dTime(),
                                    lunch: $('.auto-route-radio-lunch').find(':checked').val()
                                },
                                sendto:'agenda/fancy_router',// + $('.auto-route-radio-method').find(':checked').val(),
                                andthen:function(x){
                                    cas.showthis('body');
                                    F5();
                                    if(x.data.log){
                                        if(!$('#routerlog').length){
                                            $("<a target='_blank' id='routerlog'>&zwnj;</a>")
                                                .insertAfter($('#autoroute'))
                                                .button({icons:{primary: 'ui-icon-mail-open'},
                                                    text: false,label:'Abrir log do roteamento'});
                                        }
                                        $('#routerlog').effect('highlight',5 * 1000).attr('href','/'+x.data.log);
                                    }
                                }
                            });
                        } 
                    }, { 
                        text: "Fechar", 
                        click: function() {
                            $(this).dialog( "close" ); 
                        } 
                    }
                ]
            });
            dlg.find('.auto-route-radio-lunch').buttonset();
            //dlg.find('.auto-route-radio-method').buttonset();
        }
        dlg.dialog('open');    
    }
    var hideoffline;
    $('#toggletecs')
        .button({icons:{primary: 'ui-icon-minus'},text: false})
        .click(function(){
            if(!hideoffline){
                $('#toggletecs').button('option','icons',{primary: 'ui-icon-plus'});
                hideoffline = true;
            }else{
                $('#toggletecs').button('option','icons',{primary: 'ui-icon-minus'});
                hideoffline = false;
            }
            for(var i in tecs)
                tecs[i].hidden = tecHidden(tecs[i]);
            F5();
        });
    $('#pagelock').button({icons:{primary: 'ui-icon-unlocked'},text: false,label:'Auto'})
        .click(function(){
            if(!pagelocked){
                $('#pagelock').button('option','icons',{primary: 'ui-icon-locked'});
                pagelocked = true;
            }else{
                $('#pagelock').button('option','icons',{primary: 'ui-icon-unlocked'});
                pagelocked = false;
            }
        });
    
    $('#autoroute')
        .button({icons:{primary: "ui-icon-circle-arrow-e"},text: false,label: "Rotear"})
        .click(autoRoute);
    var zPack = null;

    function osPutClick(){
        tempLock();
        $(this).button('disable');
        $('.activityHover').removeClass('activityHover');
        $('.timeBlock.ui-draggable').draggable('disable');
        removeFlyingOS();
        unShift(true);
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/os_pack',
            sendme:{
                ass:osSelected.asscod,
                per:osSelected.per,
                t:dTime()
            },
            andthen:packer
            
        });
    }
    function packer(x){
        if(x.data.pack){
            tempLock();
            var t = $('.tec').filter(function(){
                return $(this).position().top >= $('#agenda').scrollTop();
            }).first();
            if(!t)
                return false;
            var ftec = t.attr('tec-id');
            zPack = x.data.pack;
            var mytop = tecs[ftec].tline.offset().top - timeline.offset().top;
            var xpos = 0, w = (parseInt(zPack.scheduled_duration) * timeWidth * 1000);
            var ddsrc =

                '['+zPack.pack.length+' OS] '+zPack.assname + " ("+Math.floor(parseInt(zPack.scheduled_duration) / 60)+" minutos)";

            zPack.time_elem =
                $("<div class='flyingOS timeBlock osPending tec"+zPack.activity.toUpperCase()+"' style='width:"+w+"px'></div>").appendTo('#timeblocks').css('left',xpos).css('top',mytop).attr('title',ddsrc);

            zPack.descr = ((zPack.assname)?zPack.assname:zPack.descr);
            zPack.xpos = xpos;
            zPack.ypos = mytop;
            zPack.tec = parseInt(tecs[ftec].id);
            $('#moveactivity').button('disable');
            $('#savemove').button('disable');


            $('.hiddenDesloc').removeClass('hiddenDesloc');

            zPack.time_elem.draggable({
                containment:'#timeline',
                snap:'.line',
                snapMode:'inner',
                grid:[1,56],
                start:function(event,ui){
                    $('#time-drag-label').fadeIn();
                    onDrag = zPack;
                    onDrag.id = null;
                    if(!onDrag.newTec)
                        onDrag.newTec = onDrag.tec;
                    tecliner(onDrag.newTec);
                    onDrag.last = ui.position;
                    $('#moveactivity').button('disable');
                    $('#savemove').button('disable');
                },
                stop:function(event,ui){
                    $('#time-drag-label').fadeOut();
                    delete onDrag.last;
                    $('#savemove').button('enable');

                },
                drag:dragTime
            });
            activitySelected = zPack.time_elem;
        }
    }
    if(cas.args.agenda_d){
        $('#dselector').val(cas.args.agenda_d);
        delete cas.args.agenda_d;
        cas.pushArgs();
    }else{
        if(cas.get_pref('agenda_d')){
            var dnow = new Date();
            dnow.setTime(parseInt(cas.get_pref('agenda_d')) * 1000);

            var df =  
                cas.strpad(dnow.getDate(),'0',2,true) +
                '/' + cas.strpad((dnow.getMonth() + 1),'0',2,true) +
                '/' + dnow.getFullYear();

            $('#dselector').val(df);
        }
    }
    
    function shadowClickerClick(){
        if(shadowToggle){
            shadowToggle = false;
            shadow.stop(true,true).height(30);
        }else{
            shadow.stop(true,true).height($('#agenda').height()+$('#topbar').outerHeight());
            shadowToggle = true;
        }
    }
    function agendaCsvClick(){
        cas.ajaxer({
            method:'GET',
            sendme:{
                perlist:perlist,
                t:dTime()
            },sendto:'agenda/agenda_csv',
            andthen:function(x){
                window.location.href = x.data.path;
                /*if(confirm('Deseja abrir todas as '+x.data.urls.length+' OSs no SIGA? Isso pode ser demorado.'))
                    for(var i in x.data.urls)
                        window.open(x.data.urls[i]);*/
            }
        });
    }
    function updateTecStats(){
        $('#tectb').empty();
        for(var i in tecs){
            $('#tectb').append("<tr id='tec-stat-"+i+"' class='cnttr'></tr>");
            $('#tectb').append("<tr id='tec-statl-"+i+"' class='cnttr'></tr>");
            cas.ajaxer({
                method:'GET',
                sendme:{tec:tecs[i].id,t:dTime()},
                sendto:'agenda/tecstats',
                andthen:function(x){
                    var aline = $('#tec-stat-'+x.data.tec),bline = $('#tec-statl-'+x.data.tec);
                    for(var j in x.data.os){
                        aline.append("<td class='cnttd tdup'>"+x.data.os[j].c+"</td>");
                        bline.append("<td class='cnttd'>"+x.data.os[j].os_tipo+"</td>");
                    }
                }
            });
        }
    }
    function toggleStats(){
        $('#tecstats').toggle();
        if($('#tecstats').is(':visible')){
            updateTecStats();
        }
    }
    var searchTimeout;
    function inPack(x,y){
        if(x.pack)
            for(var i in x.pack)
                if(parseInt(x.pack[i].os) === parseInt(y))
                    return true;
        
        return false;
    }
    function searchAgenda(){
        clearTimeout(searchTimeout);
        
        var result = [];
        var x = $('#searchass').val().toLowerCase();
        var fnd = function(a,b){
            if(!b)
                return false;
            return (''+b).toLowerCase().indexOf(a) > -1;
        };
        if(x.length > 0){
            for(var i in tecs){
                for(var j in tecs[i].schedule){
                    if( 
                        (tecs[i].schedule[j].assname 
                            && fnd(x,tecs[i].schedule[j].assname))
                        || fnd(x,tecs[i].schedule[j].asscod)
                        || inPack(tecs[i].schedule[j],parseInt(x))
                    ){
                        result.push({
                            id: "schedule_"+tecs[i].schedule[j].id,
                            aloc:true,
                            name: tecs[i].schedule[j].assname,
                            cod: tecs[i].schedule[j].asscod,
                            os: tecs[i].schedule[j].pack
                        });
                    }
                }
            }
            for(var i in os){
                if( 
                    fnd(x,os[i].assname)
                    || fnd(x,os[i].asscod)
                    || fnd(x,os[i].os)
                ){
                    result.push({
                        id: "os_"+os[i].os+'_'+os[i].per+'_'+os[i].svc,
                        aloc:false,
                        name: os[i].assname,
                        cod: os[i].asscod,
                        os: [{
                            os: os[i].os,
                            svc: os[i].svc
                        }]
                    });
                }
            }
            if(result.length === 0){
                searchResult = cas.weirdDialogSpawn(null,'<h3 class="resulttt">Sua busca por <i>"'+x+'"</i> não retornou resultados.</h3>',searchResult);
                return false;
            }
            mountResult(result);
        }
    }
    
    function mountResult(r){
        var h = $('<div><h3 class="resulttt">Resultado</h3></div>');
        var f = function(x){
            var k = [];
            for(var j in x)
                k.push(x[j].os + ' ['+x[j].svc.toUpperCase()+']');
            return ((k.length > 0)?
                        '<tbody>'+
                            '<tr>'+
                                '<td class="rtbos" colspan=3>'+k.join('; ')+'</td>'+
                            '</tr>'+
                        '</tbody>'
                    :'');
        };
        for(var i in r){
            $("<table title='Clique para abrir' class='resulttb' data-id="+r[i].id+">")
                .append(
                '<thead>'+
                    '<tr>'+
                        "<th class='"+((r[i].aloc)?'':'n')+"rtaloc'></th>"+
                        "<th class='rtbcod'>"+r[i].cod+'</th>'+
                        "<th class='rtbname'>"+r[i].name+'</th>'+
                    '</tr>'+
                '</thead>')
                .append(f(r[i].os)).click(clickThemResult)
                .appendTo(h);
                
        }
        
        searchResult = cas.weirdDialogSpawn(null,h,searchResult,true);
    }
    function clickThemResult(){
        var x = $('#'+$(this).attr('data-id'));
        if(x.length > 0)
            x.trigger('click');
        $('#searchbox').hide();
        cas.weirdDialogClose();
    }
    $('#searchass').keyup(function(e){
        clearTimeout(searchTimeout);
        if(e.keyCode === 27){
            $('#searchassbt').trigger('click');
            return false;
        }
        searchTimeout = setTimeout(searchAgenda,300);
    });
    function someMenJustWanttoWatchTheWorldBurn(t){
        var s = {perlist:perlist,t:dTime()};
        if(t)
            s.tec = t;
        
        cas.hidethis('body');
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/flush_agenda',
            sendme: s,
            andthen:
                function(x){
                    cas.showthis('body');
                    F5();
                }
        });
    }
    
    function mostDangerousButtonInTheWest(){
        var nope = 
        $('<h5>Toda as ordens não iniciadas do dia serão desalocadas.<br> Deseja realmente prosseguir?</h5>')
        .dialog({
            autoOpen: true,
            modal: true,
            closeOnEscape: true,
            dialogClass: 'noTitleDialog',
            width: 600,
            height:150,
            resizable: false,
            open: function(event,ui){
                clearTimeout(ohnoes);
                tempLock();
            },
            close: function (){
                tempUnLock();
                $(this).dialog('destroy').remove();
            }
        }).append($( '<div id="progressbar">' ).progressbar({value: false}));
        ohnoes = setTimeout(function(){
            $('#progressbar').remove();
            nope.dialog( "option", "buttons",
            [
                {
                    text: "Sim, eu tenho certeza.", 
                    click: function(){
                        $(this).dialog('close');
                        someMenJustWanttoWatchTheWorldBurn();
                    }
                },
                { 
                    text: "Não, me tire daqui!", 
                    click: function() { 
                        $( this ).dialog( "close" );
                    } 
                } 
            ]);
        },1000 * 3);
    }
    
    
    function tempLock(){
        if(!pagelocked){
            $('#pagelock').trigger('click');
            triggerback = true;
        }
    }
    function tempUnLock(){
        if(triggerback){
            $('#pagelock').trigger('click');
            triggerback = false;
        }
    }
    
    
    function moveActivityClick(){
        aReWind();
        tempLock();
        onDrag = null;
        $(this).button('disable');
        $('#savemove').button('disable');
        $('.timeBlock.ui-draggable').draggable('disable');
        triggerDrag(activitySelected);
        activitySelected.draggable('enable');
    }
    function saveMoveClick(){
        $(this).button('disable');
        $('#moveactivity').button('enable');
        activitySelected.draggable('disable');
        var x = getSchedule(activitySelected);
        var t1 = tecs[x.newTec].tipos;
        var match = true;
        var matchPER = true;
        
        for(var i in x.pack){
            if(t1.indexOf(x.pack[i].os_tipo) === -1)
                match = false;
            
            if(parseInt(x.pack[i].per) !== parseInt(tecs[x.newTec].per))
                matchPER = false;
            
        }
        var fine = confirm('Você realmente deseja realocar as ordens selecionadas?');
        if(!match)
            fine = fine && confirm('Este técnico não está habilitado a fazer este tipo de OS. '+
                                    'Deseja ignorar a regra e forçar a alocação?');
        if(!matchPER)
            fine = fine && confirm('O técnico '+tecs[x.newTec].name.toUpperCase()+
                                    ' atua em uma cidade diferente do assinante que você está tentando alocar. '+
                                    'Deseja mesmo prosseguir?');
        if( fine ){
            var slist = [];
            if(onDrag.fitsat > 0 && onDrag.fitsat < (tecline.length-1))
                var from = tecline[onDrag.fitsat].id;
            for(var i=0;i<tecline.length;i++){
                var y = tecline[i];
                var jesus = getActivityById(tecline[i].desloc);
                if(jesus)
                    slist.push({
                        id: jesus.id,
                        ini: blockTimestamp(jesus.time_elem).getTime(),
                        duration: jesus.scheduled_duration
                    });
                slist.push({
                    id: y.id,
                    ini: blockTimestamp(y.time_elem).getTime(),
                    duration: y.scheduled_duration
                });    
            }
            var sfake = 
                [{
                    ini: blockTimestamp(activitySelected).getTime(),
                    duration: x.scheduled_duration
                }];
            for(var i in slist)
                sfake.push(slist[i]);
            
            var d0 = $('#dselector').datepicker('getDate');
            function differ(a,b){
                return ( a.ini + (a.duration * 1000) ) - b.ini;
            }
            for(var i=0;i<sfake.length;i++){
                for(var j=i+1; j<sfake.length; j++){
                    if(
                        i !== j &&
                        ((sfake[i].ini < sfake[j].ini && differ(sfake[i],sfake[j]) > 1000 * 60 * 3)
                        ||
                        (sfake[i].ini > sfake[j].ini && differ(sfake[j],sfake[i]) > 1000 * 60* 3))
                    ){
                        F5();
                        return alert('Sobreposição de atividades, movimentação cancelada');
                    }
                }
            }
            
            if(onDrag.fitsat > 0 && onDrag.fitsat < (tecline.length-1)){
                for(var i in slist){
                    if(slist[i].id === from)
                        onDrag.fitsat = i - ((tecline[onDrag.fitsat].desloc)?1:0);
                }
            }else if(onDrag.fitsat > 0){
                onDrag.fitsat = onDrag.fitsat + (slist.length - tecline.length);
            }
            var me =
                {
                    id:x.id,
                    tec:x.newTec,
                    ini:blockTimestamp(activitySelected).getTime()
                };

            if(zPack){
                me.scheduled_duration = zPack.scheduled_duration;
                me.lat = zPack.lat;
                me.lng = zPack.lng;
                me.per = zPack.per;
                me.activity = zPack.activity;
                me.day = zPack.day;
                me.assname = zPack.assname;
                me.asscod = zPack.asscod;
                me.pack = zPack.pack;
                me.id = null;
            }
            
            cas.hidethis('body');
            cas.ajaxer({
                sendme:{
                    me:me,
                    others:slist,
                    t:dTime(),
                    i:onDrag.fitsat
                },
                sendto:'agenda/moveschedule',
                complete:function(){
                    cas.showthis('body');
                    F5();
                    onDrag = null;
                }
            });
            tempUnLock();
            activitySelected = null;
        }else{
            unShift();
            $('.hiddenDesloc').removeClass('hiddenDesloc');
            activitySelected.css('left',x.xpos).css('top',x.ypos);
            onDrag = null;
            return false;
        }
    }
    function blockTimestamp(x){
        var left =  x.position().left / timeWidth;
        var d = new Date();
        d.setTime(left + iniD.getTime() - window.svroffset);
        return d;
    }
    function clearTimeoutClick(){
        cas.ajaxer({
            method:'GET',
            sendme:{
                tec:this_tec,
                id:$(this).attr('timeout-id')
            },
            sendto:'agenda/clear_timeout',
            andthen:loadTimeouts
        });
    }
    function loadTimeouts(){
        cas.ajaxer({
            method:'GET',
            sendme:
                {tec:this_tec},
            sendto:'agenda/tec_timeout',
            andthen:function(x){
                var t = $('#tec_timeouts>tbody').empty();

                for(var i in x.data.timeouts)
                    $("<tr>"+
                                "<td>"+x.data.timeouts[i].ini+"</td>"+
                                "<td>"+x.data.timeouts[i].end+"</td>"+
                                "<td><div class='clear_timeout' timeout-id='"+x.data.timeouts[i].id+"'>&zwnj;</div></td>"+
                            "</tr>").appendTo(t).find('.clear_timeout').click(clearTimeoutClick);
            }
        });
    }
    function fetchSavedPerlist(){
        var p = null;
        if(cas.args.agenda_perlist){
            p = cas.args.agenda_perlist;
            delete cas.args.agenda_perlist;
            cas.pushArgs();
        }
        
        if(!p){
            p = cas.get_pref('agenda_perlist');
        }
        return p;
    }
    var getPerList = function(){
        var p = null;
        
        if(perlist){
            p = perlist;
        }else{
            p = fetchSavedPerlist();
        }
        
        var z = [];
        for(var i in p){
            p[i] = parseInt(p[i]);
            if(pers){
                z.push(pers[parseInt(p[i])].abbr);
            }
        }
        $('#perdialogopen').button('option','label', (z.length > 0)?z.join(', '):'Sem seleção');
        return p;
    };
    function perOptClick(){
        var me = $(this);
        if(me.is('.selected')){
            if(me.parent().find('.selected').length > 1)
                me.removeClass('selected');
        }else
            me.addClass('selected');
    }
    function fetchPerDialog(){
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/pers',
            andthen:function(x){
                perlist = getPerList();
                perdialog = $("<div class='perdialog'>");
                var opts = $("<ul class='perdialogselect'>").appendTo(perdialog);
                pers = [];
                for(var i in x.data.per){
                    var p = x.data.per[i];
                    p.id = parseInt(p.id);
                    pers[parseInt(p.id)] = $.extend({},p);
                    $("<li title='Clique para selecionar' class='"+
                            ((perlist.indexOf(p.id) > -1)
                                ?'selected'
                                :''
                            )+"' value='"+
                            p.id+"'>"+p.name+"</li>"
                    ).click(perOptClick).appendTo(opts);
                }
                perlist = getPerList();
                perdialog.appendTo('body').dialog({
                    autoOpen: false,
                    modal: true,
                    closeOnEscape: true,
                    dialogClass: 'noTitleDialog',
                    width: 300,
                    height:420,
                    resizable: false,
                    open: function(event,ui){
                        tempLock();
                    },
                    close: function (){
                        
                        perlist = [];
                        var z = $(this).find('.perdialogselect>.selected');
                        z.each(function(){
                            perlist.push(parseInt($(this).attr('value')));
                        });
                        cas.set_pref('agenda_perlist',perlist);
                        perlist = getPerList();
                        autoFit();
                        tempUnLock();
                        loadPer(true);
                    },
                    buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ]
                });
                bootTwo();
            }
        });
    }
    function setTimeoutClick(){
        var
            ini = $('#timeout_ini').datepicker('getDate'),
            end = $('#timeout_end').datepicker('getDate');
        cas.ajaxer({
            sendto:'agenda/set_timeout',
            sendme:{
                tec:tecs[this_tec].id,
                ini:ini.getFullYear()
                    + '/'
                    + (ini.getMonth() + 1)
                    + '/'
                    + ini.getDate() + ' '
                    + $('#timeout_ini_h').val()
                    + ':'
                    + $('#timeout_ini_m').val() + ':00'
                ,
                end:end.getFullYear()
                    + '/'
                    + (end.getMonth() + 1)
                    + '/'
                    + end.getDate() + ' '
                    + $('#timeout_end_h').val()
                    + ':'
                    + $('#timeout_end_m').val() + ':00'
            },
            andthen: loadTimeouts
        });
    }
    function perDialogOpenClick(){
        tempLock();
        perdialog.dialog('open');
    }
    function ospUnscheduleClick(){
        if(window.confirm('Você realmente deseja desalocar este item?')){
            cas.ajaxer({
                method:'GET',
                sendme:{
                    id:$(this).attr('schedule-id')
                },
                sendto:'agenda/unschedule',
                andthen:F5
            });
        }
    }
    function mainSplitterClick(){
        if(!closedLeft && $("#pendencias>.ostb").length === 0){
            $('#main_left').hide().width(0);
            $(this).css('left',0);
            closedLeft = true;
            resizer();
            F5();
        }else if(closedLeft){
            $('#main_left').removeAttr('style');
            $(this).css('left',200);
            closedLeft = false;
            resizer();
            F5();
        }
    }
    function turnoBTShowClick(){
        var t = $('#turnotb').toggle();
        $(this).toggleClass('turnotdnotshowing turnotdshowing');
    }
    
    var messtimeout;
    function agendaAutoScroll(me){
        var t = ((me)?me:$('.tecSelected')),
            a = $('#agenda');
        
        if(t.length > 0){
                a.height(t.outerHeight()).scrollTop(t.position().top);
            clearTimeout(messtimeout);
            messtimeout = setTimeout(function(){
                a.attr('mess',true);
            },500);
        }
    }
    
    function aReWind(){
        if($('#agenda').attr('mess') !== undefined && $('#agenda').height() < agenda_v_pos){
            $('#agenda').height(agenda_v_pos).removeAttr('mess');
        }
    }
    function F5(){
        ordensBaixadas();
        loadPer(true);
    }
    
    $('#osput').button({icons:{primary: "ui-icon-circle-triangle-e"},label: "Manual",text: false,disabled:true}).click(osPutClick);
    $('#updatelist').button({icons: {primary: "ui-icon-refresh"},text: false}).click(F5);
    $('#dselector').datepicker({ dateFormat: "dd/mm/yy" }).change(loadPer);
    $('#shadowClicker').click(shadowClickerClick);
    $('#searchassbt').button({icons: {primary: "ui-icon-search"},label: 'Buscar',text: false}).click(function(){$('#searchbox').toggle();});
    $('#togglestats').button({icons: {primary: "ui-icon-info"},label: "Estatísticas",text: false}).click(toggleStats);
    $('#agendacsv').button({icons: {primary: "ui-icon-arrowthickstop-1-s"},label: "Exportar",text: false}).click(agendaCsvClick);
    $('#flush').button({icons: {primary: "ui-icon-trash"},label: "Limpar",text: false}).click(mostDangerousButtonInTheWest);
    $('#moveactivity').button({icons: {primary: "ui-icon-arrow-4"},label: "Mover",disabled: true,text: false}).click(moveActivityClick);
    $('#savemove').button({icons: {primary: "ui-icon-circle-check"},label: "Salvar",disabled: true,text: false}).click(saveMoveClick);
    $('#timeout_ini,#timeout_end').datepicker({ dateFormat: "dd/mm/y" });
    $('#tec_timeout_dlg').dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: true,
        width: 700,
        height:400,
        resizable: false,
        close: F5
    });
    function loadAllUsers(){
        cas.ajaxer({
            method:'GET',
            sendto:'agenda/all_users',
            andthen:loadAllUsers_
        });
    }
    function loadAllUsers_(x){
        $('#all_users').empty();
        for(var i in x.data.user){
            $("<li class='tec-user"+
                    ((parseInt(x.data.user[i].c) > 0)
                        ?" tec-used' title='Visualização restrita'"
                        :"'"
                    )+">"+x.data.user[i].login+'</li>')
                    .draggable({
                        delay:300,
                        helper:'clone',
                        containment:'#tec_group_wrap',
                        appendTo:'#tec_group_wrap',
                        revert:'invalid',
                        start:function(event,ui){
                            ui.helper.addClass('user-floating');
                        },
                        stop:function(event,ui){
                            ui.helper.removeClass('user-floating');
                        }
                    }).appendTo('#all_users');
        }
    }
    function dropOnMe(event,ui){
        var my_tec = parseInt($(this).attr('data-id'));
        cas.ajaxer({
            sendme:{
                tec:my_tec,
                user: ui.helper.html()
            },sendto:'agenda/new_tec_user',
            andthen:function(){
                loadAllUsers();
                loadUTec(my_tec);
            }
        });
    }
    function unTecUser(){
        var my_tec = parseInt($(this).attr('data-id'));
        cas.ajaxer({
            sendme:{
                tec:my_tec,
                user:$(this).html()
            },sendto:'agenda/rm_tec_user',
            andthen: function(){
                loadAllUsers();
                loadUTec(my_tec);
            }
        });
    }
    function loadUTec(t){
        var tt = $('#tec_group>.them-tec[data-id="'+t+'"]'),ttt = tt.find('.tec-users');
        tt.find('.minime').addClass('loading');
        cas.ajaxer({
            method:'GET',
            sendme:{
                tec:t
            },sendto:'agenda/tec_users',
            andthen:function(x){
                ttt.empty().hide();
                if(x.data.user && x.data.user.length)
                    ttt.show();
                for(var i in x.data.user)
                    $("<li class='tec-user' data-id='"+x.data.user[i].tec+"' title='Clique para remover'>"+
                            x.data.user[i].user+'</li>')
                                .click(unTecUser).appendTo(ttt);
                
                tt.find('.minime').removeClass('loading');
            }
        });
    }
    
    function loadUTecs(){
        $('#tec_group').empty();
        for(var i in tecs){
            $("<li class='them-tec' data-id='"+tecs[i].id+"'>"+
                    "<div class='them-tec-title'>"+
                        "<span class='minime'>&zwnj;</span>"+
                            tecs[i].name.toUpperCase()+
                    '</div>'+
                    "<div class='tec-users' style='display:none;'></div>"+
                '</li>')
                .droppable({
                    accept:'.tec-user',
                    hoverClass:'user-floating-over',
                    drop:dropOnMe
                }).appendTo('#tec_group');
            loadUTec(tecs[i].id);
        }
    }
    $('#setTimeout').button().click(setTimeoutClick);
    if(cas.checkPerms('p') && canedit()){
        $('#tec_group_dialog').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: true,
            width: 800,
            height:570,
            resizable: false,
            open:function(){
                tempLock();
                loadUTecs();
                loadAllUsers();
            },close:tempUnLock
        });
        $('#distr_tecs').button({icons:{primary: 'ui-icon-person'},text: false}).click(function(){
            $('#tec_group_dialog').dialog('open');
        });
    }else{
        $('#distr_tecs,#tec_group_dialog').remove();
    }
    $('#perdialogopen').button({icons:{primary: 'ui-icon-home'}}).click(perDialogOpenClick);
    $('#osp-unschedule').button({icons:{primary: 'ui-icon-close'},text: false}).click(ospUnscheduleClick);
    $('#osp-hist-show').button({icons:{primary: 'ui-icon-clock'},text: false}).click(function(){
        $('#osp-hist').empty();
        tecScheduleHist($(this).attr('schedule-id'));
    });
    $('.osp-goback').button({icons:{primary: 'ui-icon-arrowreturnthick-1-w'},disabled:true,text: false}).click(function(){
        unTec();
    });
    $('#main_splitter').click(mainSplitterClick);
    $('#turnotbshow').click(turnoBTShowClick);
    $("#minuteSpanSelector")
        .slider({
            min:0, max: 24*60,
            range: true,
            orientation:'vertical',
            values:minuteSpan,
            change:minuteSpanChange,
            slide:minuteSpanSlideUpdate
        });
    
    resizer();
    agendaSize();
    setLiner();
    function aResizableFix(){
        var n = Math.max(0 - $('#agenda').scrollTop()  - 5, 
                            0 - 
                                ( Math.max($('#tecbox').height(),$('#agenda').height()) 
                                - $('#agenda').height() ) - 5 
        );
        
        $('#agenda').find('.ui-resizable-s').css('bottom',n);
    }
    $('#oswrapper').niceScroll({horizrailenabled:false});
    $('#agenda').scroll(function(){
        aResizableFix();
        aReWind();
    });

    $('#actuallyHideAgenda').button({icons:{primary: 'ui-icon-triangle-1-n'},text: false}).click(function(){
        $('#agenda').height(0);
        setLiner();
        vposupdate();
    });

    $('#actuallyHideMap').button({icons:{primary: 'ui-icon-triangle-1-s'},text: false}).click(function(){
        $('#agenda').height(maxAHeight());
        setLiner();
        vposupdate();
    });
    
    $('#tecbox').scroll(function(){
        if($(this).scrollLeft() > 0)
            $(this).scrollLeft(0);
    });
    
    
    cas.resizer.push(resizer);
    boot();
    
};
