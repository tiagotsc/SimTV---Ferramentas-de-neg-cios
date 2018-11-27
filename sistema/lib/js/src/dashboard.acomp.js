(function(){
    var cas = window.cas;
    function Acomp(){
        var __instance = this;
        this.stdStr = function(str,descr){
            if(str && str.length){
                return "<div class='dash-std-val'>"+str+"</div><div class='dash-std-descr'>"+descr+"</div>";
            }else{
                return "---<div class='dash-std-descr'>"+descr+"</div>";
            }
        };
        this.acompStruct = function(a,root){
            var id = a.id;
            if(id){
                __instance.acompUpdates(id,root);
                cas.ajaxer({
                    method: 'GET',
                    etc: { elem: root },
                    sendme: { id: id },
                    sendto: 'dashboard/acomp',
                    andthen: __instance.acompStruct_
                });
            }else{
                __instance.acompStruct_({etc:{elem:root},data:{ass:a.ass}});
            }
        };
        this.assStruct = function(ass){
            var table = $("<table class='dash-ass-acomp-tb dash-std-tb'>"+
                            "<thead></thead></table>"), tr;
            tr = $("<tr>").appendTo(table.find('thead'));
            tr.append('<th colspan=2>'+ass.nome+'</th>');

            tr = $("<tr>").appendTo(table);
            tr.append('<td>'+__instance.stdStr(ass.cel,'Celular')+'</td>');
            tr.append('<td>'+__instance.stdStr(ass.tel,'Telefone')+'</td>');

            tr = $("<tr>").appendTo(table);
            tr.append('<td colspan=2>'+
                __instance.stdStr(ass.logradouro,ass.bairro+' - '+ass.cidade)+
            '</td>');

            tr = $("<tr>").appendTo(table);
            tr.append('<td>'+__instance.stdStr(ass.cep,'CEP')+'</td>');
            tr.append('<td>'+__instance.stdStr(ass.node,'NODE')+'</td>');
            var x = $("<div class='dash-ass-acomp'>").append(table);
            return x;
        };
        this.acompStatusName = function(status){
            switch(status){
                case -1:
                    return 'Não Resolvido';
                case 0:
                    return 'Reaberto';
                case 2:
                    return 'Resolvido';
                case 3:
                    return 'Fechado';
                case 4:
                    return 'Cancelado';
                default:
                    return 'Aberto';
            }
        };
        this.acompStruct_ = function(x){
            if( !$.contains(document.documentElement, x.etc.elem[0]) )
                return false;

            var elem = x.etc.elem;
            var tb = elem.children('table'),tr;
            var stage = 1, status = 1;
            var stageName = {1:'Rede Interna',2:'Fidelização'};
            var flow = [
                    {stage:1,status:1,name:'Manter aberto em '+stageName[stage]},
                    {stage:1,status:-1,name:'Não Resolvido'},
                    {stage:2,status:1,name:'Passar para Fidelização'}
                ];

            var acomp = null;

            if(x.data.acomp){

                acomp = x.data.acomp;
                acomp.stageID = parseInt(acomp.stageID);
                acomp.statusID = parseInt(acomp.statusID);
                status = acomp.statusID; 
                stage = acomp.stageID;

                tr = $("<tr>").appendTo(tb);
                tr.append("<td colspan=2><div class='acomp-sub-header'>Acompanhamento</div></td>");

                tr = $("<tr>").appendTo(tb);
                tr.append('<td>'+__instance.stdStr(acomp.author,'Aberto por')+'</td>');
                tr.append('<td>'+__instance.stdStr(acomp.date,'Data de abertura')+'</td>');

                tr = $("<tr>").appendTo(tb);
                tr.append('<td>'+__instance.stdStr(acomp.status,'Status')+'</td>');
                tr.append('<td>'+__instance.stdStr(acomp.stage,'Etapa')+'</td>');

                if(stage === 1){
                    if(status === 0 || status === 1){
                        flow = [
                            {stage:1,status:1,name:'Manter aberto em '+stageName[stage]},
                            {stage:1,status:-1,name:'Não Resolvido'},
                            {stage:1,status:4,name:'Cancelar acompanhamento'},
                            {stage:2,status:1,name:'Passar para Fidelização'}
                        ];
                    }
                    if(status === -1){
                        flow = [
                            {stage:1,status:1,name:'Manter aberto em '+stageName[stage]},
                            {stage:1,status:-1,name:'Manter Não Resolvido'},
                            {stage:2,status:1,name:'Passar para Fidelização'}
                        ];
                    }
                }
                if(stage === 2){
                    if(status === 0 || status === 1){
                        flow = [
                            {stage:2,status:1,name:'Manter aberto em '+stageName[stage]},
                            {stage:2,status:2,name:'Resolvido'},
                            {stage:2,status:-1,name:'Não Resolvido'},
                            {stage:1,status:1,name:'Voltar para '+stageName[stage-1]}
                        ];
                    }
                    if(status === -1){
                        flow = [
                            {stage:2,status:1,name:'Manter aberto em '+stageName[stage]},
                            {stage:2,status:2,name:'Resolvido'},
                            {stage:2,status:-1,name:'Manter Não Resolvido'},
                            {stage:1,status:1,name:'Voltar para '+stageName[stage-1]}
                        ];
                    }
                }
            }


            var f = 
                $("<fieldset class='fldst'>")
                    .data('ass',x.data.ass.ass)
                    .data('per',x.data.ass.per)
                    .appendTo(elem);
            if(acomp){
                f.data('id',acomp.id);
            }
            
            var d = new Date(),
                next = new Date(),
                prev = new Date();
        
            next.setTime(d.getTime() + (1000*60*60*24*7));
            prev.setTime(d.getTime() - (1000*60*60*24*1));
            var e = $("<div class='dash-acomp-flow'>").appendTo(f);
            var statusName = 
                ((acomp)
                    ?__instance.acompStatusName(status)
                    :'Pendente'
                );
            e.append("<h3 class='dash-acomp-header'>"+
                        "<span class='dah0'>" + statusName + "</span><span class='dah1'>"+
                        stageName[stage]+"</span></h3>");
            var aselect = $("<select name='novo_status' class='dash-acomp-nstatus acomp-arg'>");
            for(var i in flow){
                aselect.append("<option value='"+flow[i].stage+':'+flow[i].status+"'>"+flow[i].name+"</option>");
            }
            aselect.appendTo($("<h4 class='danst'>Ação:</h4>").appendTo(e));

            f.append(
                "<div>"+
                    "<input type='checkbox' name='hasvt' class='acomp-hasvt acomp-arg' />"+
                    "<label for='acomp-hasvt'>Visita Tecnica</label>"+
                "</div>"+
                "<div style='display: none;'>"+
                    "<div class='dash-form-line'>"+
                        "<span>OS: </span>"+
                        "<input autocomplete=off name='os' "+
                            "class='acomp_vt-os acomp-arg' type='text' placeholder='Código da Ordem' />"+
                    "</div>"+
                    "<div class='dash-form-line'>"+
                        "<span>Data da visita: </span>"+
                        "<input autocomplete=off name='schedule' type='date' value='"+
                            d.toYMD()+"' max='"+next.toYMD()+"' min='"+
                            prev.toYMD()+
                        "' class='acomp_vt-schedule acomp-arg' />"+
                    "</div>"+
                    "<div class='dash-form-line'>"+
                        "<span>Janela:</span>"+
                        "<select class='acomp_vt-window acomp-arg' name='window'>"+
                            "<option value='08:00:00'>08~11</option>"+
                            "<option value='11:00:00'>11~14</option>"+
                            "<option value='14:00:00'>14~17</option>"+
                            "<option value='17:00:00'>17~20</option>"+
                            "<option value='20:00:00'>20~23</option>"+
                        "</select>"+
                    "</div>"+
                "</div>"+
                "<div class='acomp-label'>Descrição:</div>"+
                "<div>"+
                    "<textarea name='descr' rows=4 class='acomp-descr acomp-arg'></textarea>"+
                "</div>");
            f.append("<div class='dash-form-line'><button class='acomp-jesus'>Salvar</button></div>");
            f.find('.acomp-jesus').button({
                icons: { primary: "ui-icon-check" }
            }).click(__instance.saveAcomp);
            f.find('.acomp-hasvt').change(function(){
                $(this).parent().next().toggle();
            });
        };
        this.saveAcomp = function(){

            var fakeForm = $(this).closest('fieldset');
            var e_args = fakeForm.find('.acomp-arg');
            var args = {};

            args.id = fakeForm.data('id');
            args.ass = fakeForm.data('ass');
            args.per = fakeForm.data('per');

            if(!args.id)
                delete args.id;

            e_args.each(function(){
                args[$(this).attr('name')] = 
                    (($(this).is(':checkbox'))
                        ?$(this).prop('checked'):$(this).val());
            });

            if(!args.novo_status){
                alert('Não foi possível salvar os dados preenchidos. '+
                        'Cheque os campos e tente novamente.');
                return true;
            }
            if(args.descr.length < 3){
                alert('Sua descrição é muito curta.');
                return true;
            }
            var s = args.novo_status.split(':');

            args.stage = parseInt(s[0]);
            args.status = parseInt(s[1]);
            delete args.novo_status;

            __instance.actuallySaveAcomp(args,$(this));
        };
        this.resetAcomps = function(){
            var charts = cas.charts.chartView();
            for(var i in charts){
                if( charts[i].chart && 
                    ( charts[i].chart.id === 'acomp_table'
                        || charts[i].chart.id === 'ass_rank' ) 
                ){
                    charts[i].update();
                }
            }
        };
        this.actuallySaveAcomp = function(args,bt){
            bt.button( "option", "disabled", true )
                .button( "option", "label", "Aguarde..." );

            cas.ajaxer({
                sendme:args,
                sendto:'dashboard/save_acomp',
                complete:function(){
                    if($.contains(document.documentElement, bt[0]))
                        bt.button( "option", "disabled", false )
                            .button( "option", "label", "Salvar" );
                },
                andthen:function(x){
                    $('.fSCDClose').trigger('click');
                    __instance.resetAcomps();
                }
            });
        };
        this.acompUpdates = function(id,root){
            cas.ajaxer({
                method: 'GET',
                etc: { elem: root },
                sendme: { id: id },
                sendto: 'dashboard/acomp_updates',
                andthen: function(x){
                    if( !$.contains(document.documentElement, x.etc.elem[0]) )
                        return false;
                    var list = x.data.list;
                    var tb = x.etc.elem.children('table'),tr;
                    var root = $("<div class='acomp-show-updates'>").insertAfter(tb);
                    $("<a class='histtoggler'>Histórico</a>").click(function(){
                        $(this).next().toggle();
                    }).appendTo(root);
                    var tr, u = $("<ul class='acomp-updates'>").appendTo(root).hide(), table;

                    for (var i in list){
                        table = $("<table class='dash-std-tb'>").appendTo($("<li class='acomp-item'>").appendTo(u));

                        tr = $('<tr>').appendTo(table);
                        tr.append("<td>"+
                                __instance.stdStr(list[i].stage,'Etapa')+'</td>');
                        tr.append("<td colspan=2 class='acomp-updates-author'>"+
                                __instance.stdStr(list[i].author,'Autor')+'</td>');

                        tr = $('<tr>').appendTo(table);
                        tr.append("<td>"+
                                __instance.stdStr(list[i].date,'Data da atualização')+'</td>');
                        tr.append("<td colspan=2>"+
                            __instance.stdStr('<pre>'+list[i].descr+'</pre>','Descrição')+'</td>');

                        if(list[i].os){
                            tr = $('<tr>').appendTo(table);
                            tr.append("<td>"+__instance.stdStr(list[i].os,'Ordem de Serviço')+'</td>');
                            tr.append("<td>"+__instance.stdStr(list[i].schedule,'Agenda')+'</td>');
                            tr.append("<td>"+__instance.stdStr(list[i].window,'Janela de atendimento')+'</td>');
                        }
                    }
                }
            });
        };
        this.openAssAcomp = function(){
            cas.ajaxer({
                method:'GET',
                sendto: 'dashboard/ass_info',
                sendme: { ass: $(this).data('cod'), per: $(this).data('per') },
                etc:{
                    elem: $(this),
                    acomp: (($(this).data('acomp'))
                                ?$(this).data('acomp')
                                :null
                            )
                },
                andthen: function (x){
                    var container = __instance.assStruct(x.data.assInfo);
                    $('<div>').appendTo('body').fullScreenDialog(
                        {title:'Acompanhamento de Cliente',content: container});
                    __instance.acompStruct({id:x.etc.acomp,ass:x.data.assInfo}, container);
                }
            });
        };
    }
    cas.acomp = new Acomp();
}());

