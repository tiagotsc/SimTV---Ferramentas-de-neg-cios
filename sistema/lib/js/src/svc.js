cas.controller = function(){
    var terceira,
        tecnico,
        agenda,
        osCount,osLoaded,
        x_osCount,x_agCount,
        osZ,osPerRequest = 5,
        slave = null,
        selectedPers = [];
    var openAgendas = [];
    var leftShow = true;
    var regra = [];
    var regraCheck = {
        //Adesao - Instalação De Assinatura (Pay TV + Banda Larga)
        1: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var i,
                ts = ['instalação'], 
                sts = ['contrato','contrato novo'],
                svc = {tv:false,cm:false};
            for(i in a.os){
                if(
                    a.os[i].os_tipo 
                    && a.os[i].sub_tipo
                    && ts.indexOf(a.os[i].os_tipo.toLowerCase()) > -1 
                    && sts.indexOf(a.os[i].sub_tipo.toLowerCase()) > -1
                ){
                    svc[a.os[i].svc.toLowerCase()] = true;
                    if(svc.tv && svc.cm)
                        return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Adesao - Instalação De Assinatura (Pay TV)
        2: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var i,
                ts = ['instalação'], 
                sts = ['contrato','contrato novo'];
            for(i in a.os){
                if(
                    a.os[i].os_tipo 
                    && a.os[i].sub_tipo
                    && a.os[i].svc.toLowerCase() === 'tv'
                    && ts.indexOf(a.os[i].os_tipo.toLowerCase()) > -1 
                    && sts.indexOf(a.os[i].sub_tipo.toLowerCase()) > -1
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Adesao - Internet Banda Larga
        10: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var i,
                ts = ['instalação'], 
                sts = ['contrato','contrato novo'];
            for(i in a.os){
                if(
                    a.os[i].os_tipo 
                    && a.os[i].sub_tipo
                    && a.os[i].svc.toLowerCase() === 'cm'
                    && ts.indexOf(a.os[i].os_tipo.toLowerCase()) > -1 
                    && sts.indexOf(a.os[i].sub_tipo.toLowerCase()) > -1
                ){
                    return this.val[a.per][a.terceira];
                }
            }    
            return null;
        },
        //Refazer Instalação
        14: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].falha 
                    && a.os[i].falha.toLowerCase().indexOf('refazer') > -1
                    && (!a.os[i].pacote_tipo || a.os[i].pacote_tipo.toLowerCase() === 'principal')
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Refazer Instalação Do Ponto (pa)
        15: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].falha 
                    && a.os[i].pacote_tipo 
                    && a.os[i].falha.toLowerCase().indexOf('refazer') > -1
                    && a.os[i].pacote_tipo.toLowerCase() !== 'principal'
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            
            return null;
        },
        //Reinstalação Assinatura
        16: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].sub_tipo 
                    && a.os[i].sub_tipo.toLowerCase().indexOf('reinstalar') > -1
                    && (!a.os[i].pacote_tipo || a.os[i].pacote_tipo.toLowerCase() === 'principal')
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Reinstalacao - Ponto Adicional
        17: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].sub_tipo 
                    && a.os[i].sub_tipo.toLowerCase().indexOf('reinstalar') > -1
                    && a.os[i].pacote_tipo 
                    && a.os[i].pacote_tipo.toLowerCase() !== 'principal'
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Substituição Do Controle Remoto
        31: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var i;
            
            for(i in a.os){
                if(
                    a.os[i].falha 
                    && a.os[i].falha.toLowerCase().indexOf('controle remoto') > -1
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Visita Tecnica Pay TV         
        29:function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'tv'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'reclamação' 
                    && !(a.os[i].falha 
                        && a.os[i].falha.toLowerCase().indexOf('controle remoto') > -1)
                    
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Visita Tecnica Internet Banda Larga
        30:function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'cm'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'reclamação' 
                    && !(a.os[i].falha 
                        && a.os[i].falha.toLowerCase().indexOf('controle remoto') > -1)
                    
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Desconexão Pay TV
        35:function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'tv'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'desconexão'
                    
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Desconexão CM
        36:function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'cm'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'desconexão'
                    
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Desconexão Combo
        37:function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var svc = {tv:false,cm:false};
            
            for(var i in a.os){
                
                if(
                    a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'desconexão'
                    
                ){
                    svc[a.os[i].svc.toLowerCase()] = true;
                    if(svc.tv && svc.cm)
                        return this.val[a.per][a.terceira];
                }
            }
            
            return null;
        },
        //Desconexão Com Retirada De Equipamentos
        32: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'desconexão'
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Mudança De Endereço - Instalação De Assinatura  (Pay TV + Banda Larga)
        11: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var svc = {tv:false,cm:false},check = false;
            
            for(var i in a.os){
                svc[a.os[i].svc.toLowerCase()] = true;
                if(
                    a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'instalação'
                    && a.os[i].sub_tipo 
                    && a.os[i].sub_tipo.toLowerCase() === 'mudança de endereço'
                    
                ){
                    check = true;
                }
            }
            if(check && svc.tv && svc.cm)
                return this.val[a.per][a.terceira];
            return null;
        },
        //Mudança De Endereço - Instalar Assinatura (Pay TV)
        12: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'tv'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'instalação'
                    && a.os[i].sub_tipo 
                    && a.os[i].sub_tipo.toLowerCase() === 'mudança de endereço'
                    
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Mudança De Endereço - Internet Banda Larga
        26: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'cm'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'instalação'
                    && a.os[i].sub_tipo 
                    && a.os[i].sub_tipo.toLowerCase() === 'mudança de endereço'
                    
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Swap Pay TV - Mudança De Pacote Digital - Ose Mudança De Nivel
        18: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var onlyMe = false;
            for(var i in a.os){
                onlyMe = 
                        (a.os[i].svc.toLowerCase() === 'tv'                    
                        && a.os[i].sub_tipo 
                        && a.os[i].sub_tipo.toLowerCase() === 'mudança de nível');
            }
            if(onlyMe)
                return this.val[a.per][a.terceira];
            return null;
        },
        //Swap Pay TV - Mudança De Pacote Digital - Ose Mudança De Nivel
        19: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'tv'                    
                    && a.os[i].sub_tipo 
                    && a.os[i].sub_tipo.toLowerCase() === 'mudança de nível'
                    
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Instalar Ponto Adicional
        3: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var onlyMe = false;
            for(var i in a.os){
                onlyMe = 
                    (a.os[i].svc.toLowerCase() === 'tv' 
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'instalação');
            }
            if(onlyMe)
                return this.val[a.per][a.terceira];
            return null;
        },
        //Instalar Ponto - Agregada A Outro Serviço
        8: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'tv'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'instalação'
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Instalar Ponto Internet Banda Larga
        9: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    a.os[i].svc.toLowerCase() === 'cm'
                    && a.os[i].os_tipo 
                    && a.os[i].os_tipo.toLowerCase() === 'instalação'
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Mudança De Local De Ponto - Deslocamento
        20: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var onlyMe = false;
            for(var i in a.os){
                onlyMe = 
                    (a.os[i].svc.toLowerCase() === 'tv'
                        && a.os[i].falha 
                        && a.os[i].falha.toLowerCase().indexOf('mudança') > -1
                        && a.os[i].falha.toLowerCase().indexOf('local') > -1
                        && a.os[i].falha.toLowerCase().indexOf('ponto') > -1
                    );
            }
            if(onlyMe)
                return this.val[a.per][a.terceira];
            return null;
        },
        //Mudança De Local De Ponto - Agregada
        21: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(a.os[i].svc.toLowerCase() === 'tv'
                    && a.os[i].falha 
                    && a.os[i].falha.toLowerCase().indexOf('mudança') > -1
                    && a.os[i].falha.toLowerCase().indexOf('local') > -1
                    && a.os[i].falha.toLowerCase().indexOf('ponto') > -1
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Mudança De Local De Ponto Internet Banda Larga Com  Deslocamento
        27: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            var onlyMe = false;
            for(var i in a.os){
                onlyMe = 
                    (a.os[i].svc.toLowerCase() === 'cm'
                        && a.os[i].falha 
                        && a.os[i].falha.toLowerCase().indexOf('mudança') > -1
                        && a.os[i].falha.toLowerCase().indexOf('local') > -1
                        && a.os[i].falha.toLowerCase().indexOf('ponto') > -1
                    );
            }
            if(onlyMe)
                return this.val[a.per][a.terceira];
            return null;
        },
        //Mudança De Local De Ponto  Internet Banda Larga - Agregada
        28: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(a.os[i].svc.toLowerCase() === 'cm'
                    && a.os[i].falha 
                    && a.os[i].falha.toLowerCase().indexOf('mudança') > -1
                    && a.os[i].falha.toLowerCase().indexOf('local') > -1
                    && a.os[i].falha.toLowerCase().indexOf('ponto') > -1
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Swap - Troca De Tecnologia - Agregada
        24: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            for(var i in a.os){
                if(
                    (a.os[i].sub_tipo 
                    && a.os[i].sub_tipo.toLowerCase() === 'troca')
                    ||
                    (
                        a.os[i].os_tipo
                        && (a.os[i].os_tipo.toLowerCase() === 'mudança' 
                            || a.os[i].os_tipo.toLowerCase() === 'acessórios')
                        && a.os[i].falha 
                        && a.os[i].falha.toLowerCase().indexOf('controle remoto') === -1
                        && (a.os[i].falha.toLowerCase().indexOf('migração') > -1
                            || a.os[i].falha.toLowerCase().indexOf('troca') > -1)
                    )
                ){
                    return this.val[a.per][a.terceira];
                }
            }
            return null;
        },
        //Swap - Troca De Tecnologia - Agregada
        25: function(a){
            if(!this.val[a.per] || !this.val[a.per][a.terceira])
                return null;
            
            var onlyMe = false;
            for(var i in a.os){
                onlyMe = 
                    (
                        (a.os[i].sub_tipo && a.os[i].sub_tipo.toLowerCase() === 'troca')
                        ||
                        (
                            a.os[i].os_tipo
                            && (a.os[i].os_tipo.toLowerCase() === 'mudança' 
                                || a.os[i].os_tipo.toLowerCase() === 'acessórios')
                            && a.os[i].falha 
                            && a.os[i].falha.toLowerCase().indexOf('controle remoto') === -1
                            && (a.os[i].falha.toLowerCase().indexOf('migração') > -1
                                || a.os[i].falha.toLowerCase().indexOf('troca') > -1)
                        )
                    );
            }
            if(onlyMe)
                return this.val[a.per][a.terceira];
            return null;
        }
    };
    function defStr(x){
        return ((x && (''+x).length > 0)?x:'---');
    }
    function resizer(){
        $('#container').height(
            $(window).height() 
                - $('#head-wrapper').outerHeight()
                - $('#topbar').outerHeight()
                - $('#foot').outerHeight()
        );
    }
    $('#regras').click(function(){
        $(this).toggleClass('ok');
        $('#regras,#salvar_regras').button('disable');
        
        if($("#regra_config").is(':visible'))
            $("#regra-root").empty();
        $('#salvar_regras').toggle();
        
        $("#regra_config").slideToggle(function(){
            $('#regras,#salvar_regras').button('enable');
            if($("#regra_config").is(':visible')){
                populateRegras();
            }
        });
        
    });
    function salvarRegras(){
        var z = [];
        $('.regra-val.changed').each(function(){
            var me = $(this).parent();
            z.push({
                val:$(this).val(),
                regra: me.attr('data-regra'),
                terceira: me.attr('data-terc'),
                per: me.attr('data-per')
            });
        });
        
        $('#regras,#salvar_regras').button('disable');
        cas.ajaxer({
            sendme:{
                val:z
            },sendto:'svc/regra_val',
            andthen:function(){
                $('#regras,#salvar_regras').button('enable');
                $('.regra-val.changed').removeClass('changed');
                loadRegras();
            }
        });
    }
    function rScroll(){
        $('#regrarow').scrollTop($(this).scrollTop());
        $('#regracol').scrollLeft($(this).scrollLeft());
    }
    function populateRegras(){
        var one_width = 120;
        var root = 
            $('#regra-root').html(
                "<div id='regratb'><div id='regratbinner'></div></div>"+
                "<div id='regracol'></div>"+
                "<div id='regrarow'>"+
                    "<ul class='urtb ver' id='regra-row'></ul>"+
                "</div>"+
                "<div id='regrafiller'><h3>Configuração de Preços</h3></div>"
            );
        var h = $('#regra-root').height() - $('#regracol').height();
        $('#regratb,#regrarow').height(h);
        
        $('#regratbinner').niceScroll();
        $('#regratbinner').scroll(rScroll);
        
        
        var r,p,t,p0,colspan;
        var t1 = $("<ul class='urtb hor'>").appendTo('#regracol'),
            t2 = $("<ul class='urtb hor'>").appendTo('#regracol');
    
        for(p in cas.permissora){
            colspan = 0;
            for(t in terceira){
                if(terceira[t].per.indexOf(parseInt(p)) > -1){
                    t2.append("<li class='rtdval inli rtdval1'"+
                                " data-per='"+cas.permissora[p].id+"'"+
                                " data-terc='"+terceira[t].id+"'"+
                                "><span class='inliner'>"+terceira[t].name+'</span></li>');
                    colspan++;
                }
            }
            if(colspan > 0)
                $("<li data-per='"+cas.permissora[p].id+"' class='rtdper'>"+cas.permissora[p].abbr+'</li>').width(one_width * colspan).appendTo(t1);
        }
        var w = t2.children().length * one_width;
        t1.width(w);
        t2.width(w);
        for(r in regra){
            t1 = $("<ul data-regra='"+r+"' class='urtb hor'>").width(w).appendTo('#regratbinner');
            $('#regra-row').append(
                "<li class='inli rtdval1'"+
                    " data-regra='"+r+"' title='"+regra[r].name+"'>"+
                        "<span class='inliner'>"+regra[r].name+"</span>"+
                '</li>');
            for(p in regra[r].val)
                for(t in regra[r].val[p])
                    $("<li class='rtdval inli'"+
                        " data-per='"+p+"'"+
                        " data-terc='"+t+"'"+
                        " data-regra='"+r+"'"+">"+
                            "<input class='regra-val' type='number' min='0.0' step='0.1' value='"+regra[r].val[p][t]+"' /></li>")
                        .mouseenter(rtdvalHoverIn).mouseleave(rtdvalHoverOut).appendTo(t1);
        }
        $('#regratbinner .regra-val').focus(regraVal);
    }
    function regraVal(){
        $(this).addClass('changed');
    }
    function rtdvalHoverIn(){
        var me = $(this), 
            r = me.attr('data-regra'),
            t = me.attr('data-terc'),
            p = me.attr('data-per');
        $('.lihover').removeClass('lihover');
        $('#regra-root').find('.rtdper[data-per="'+p+'"]').addClass('lihover');
        $('#regra-root').find('.inli[data-regra="'+r+'"]').addClass('lihover');
        $('#regra-root').find('.inli[data-terc="'+t+'"]').filter('[data-per="'+p+'"]').addClass('lihover');
    }
    function rtdvalHoverOut(){
        $('.lihover').removeClass('lihover');
    }
    $('#tecterdist').click(function(){
        cas.ajaxer({
            method:'GET',
            sendme:_args(),
            sendto:'svc/auto_assign',
            andthen:loadTecs
        });
    });
    $('#tectergroup').click(function(){
        cas.ajaxer({
            method:'GET',
            sendme:_args(),
            sendto:'svc/slave_all',
            andthen:loadTecs
        });
    });
    
    function _args(){
        return {
                ini:$('#ini_selector').val(),
                end:$('#end_selector').val(),
                perlist:selectedPers
            };
    }
    function loadTecs(){
        cas.ajaxer({
            method:'GET',
            sendme:_args(),
            sendto:'svc/all_tecs',
            andthen: loadTecs_
        });
    }
    function loadTecs_(x){
        putTecs(x);
        populateTecs();
    }
    function putTecs(x){
        var t;
        tecnico = [];
        for(var i in x.data.tec){
            t = x.data.tec[i];
            if(t.id)
                t.id = parseInt(t.id);
            t.sigaID = parseInt(t.sigaID);
            t.per = parseInt(t.per);
            tecnico.push(t);
        }
    }
    function populateTecs(){
        var t,e,i;
        slave = null;
        $('#terceira_config .tec').remove();
        for(i in tecnico){
            t = tecnico[i];
            t.uniID = 
                Base64.encode(
                    JSON.stringify({
                        sigaID: t.sigaID,
                        per: t.per,
                        svc: t.svc,
                        name: t.name
                    })
                );
            if(!t.equals_to){
                e = $("<li class='tec' data-tec='"+t.uniID+"'>"+
                        "<h4 class='th4 tec-header'>"+
                            "<a class='tec-sub-toggle' style='display:none;'><span>&zwnj;<span></a>"+
                            "<span class='tec-svc'>"+t.svc.toUpperCase()+"</span>"+
                            t.name+
                        "</h4>"+
                        "<ul class='sub-tecs' style='display:none'></ul>"+
                    "</li>")
                    .click(clickToEnslavement)
                    .draggable({
                        delay:300,
                        helper:'clone',
                        //containment:'body',
                        appendTo:'body',
                        revert:'invalid',
                        scroll:false,
                        start:function(event,ui){
                            ui.helper.addClass('tec-floating');
                        },
                        stop:function(event,ui){
                            ui.helper.removeClass('tec-floating');
                        }
                    });
            
                e.find('.tec-header>.tec-sub-toggle').click(function(){
                    $(this).toggleClass('alt');
                    $(this).closest('.tec').children('.sub-tecs').slideToggle();        
                    return false;
                });
                
                if(t.id)
                    e.attr('data-id',t.id);
                
                if(t.terceira){
                    e.appendTo(terceira[t.terceira].elem.children('.tec-container'));
                    $('<div class="un-tec">').click(unTercTec).appendTo(e.find('.tec-header'));
                }else{
                    e.appendTo('#teclist');
                }
            }
        }
            
        var w;
        for(i in tecnico){
            t = tecnico[i];
            if(t.equals_to){
                e = $("<li class='tec' data-tec='"+t.uniID+"'>"+
                        "<h4 class='th4 tec-header'>"+
                            "<span class='tec-svc'>"+t.svc.toUpperCase()+"</span>"+
                            t.name+
                        "</h4>"+
                    "</li>");
                
                $('<div class="un-tec">').click(freeTec).appendTo(e.find('.tec-header'));
                w = $('#terceira_config .tec[data-id="'+t.equals_to+'"]');
                e.attr('data-id',t.id).appendTo(w.children('.sub-tecs'));
                w.find('.tec-header>.tec-sub-toggle').show();
            }
        }
    }
    function freeTec(){
        var t = $(this).closest('.tec');
        cas.ajaxer({
            sendto:'svc/tec_free',
            sendme:{slave:t.attr('data-id')},
            andthen:loadTecs
        });
        return false;
    }
    function clickToEnslavement(){
        var t = $(this);
        var me = 
            (t.attr('data-id'))
                ?Base64.encode(
                    JSON.stringify(
                        {id:t.attr('data-id')}
                    )
                )
                :t.attr('data-tec');
        
        if(slave){
            cas.ajaxer({
                sendto:'svc/tec_equals_to',
                sendme:{slave:slave,master:me},
                andthen:loadTecs
            });
            t.children('.tec-header').find('.tec-svc').addClass('tec-master');
        }else{
            slave = me;
            t.children('.tec-header').find('.tec-svc').addClass('tec-slave');
        }
    }
    function unTercTec(){
        var t = $(this).closest('.tec');
        cas.ajaxer({
            sendme:{
                tec:t.attr('data-id')
            },sendto:'svc/un_tec_terc',
            andthen:loadTecs
        });
        return false;
    }
    function loadRegras(){
        cas.ajaxer({
            method:'GET',
            sendto:'svc/regras',
            andthen: function(x){
                var i,id; 
                regra = {};
                for(i in x.data.regra){
                    x.data.regra[i].id = parseInt(x.data.regra[i].id);
                    id = x.data.regra[i].id;
                    regra[id] = $.extend({},x.data.regra[i]);
                    regra[id].plus = JSON.parse(regra[id].plus);
                }
                loadTerceiras();
            }
        });
    }
    function loadTerceiras(){
        cas.ajaxer({
            method:'GET',
            sendto:'svc/terceiras',
            andthen: function(x){
                terceira = {};
                terceirasOK = true;
                for(var i in x.data.terceira){
                    x.data.terceira[i].id = parseInt(x.data.terceira[i].id);
                    terceira[x.data.terceira[i].id] = x.data.terceira[i];
                }
                populateTerceiras();
                $('.ui-button').button('enable');
            }
        });
    }
    function populateTerceiras(){
        $('#terclist').empty();
        for(var i in terceira){
            terceira[i].elem = 
                $("<li data-terc="+terceira[i].id+">"+
                    "<h4 class='th4 terc-header' >"+
                        terceira[i].name +
                    "</h4>"+
                    "<ul class='tec-container terc-tecs' data-terc="+terceira[i].id+"></ul>"+
                "</li>").appendTo('#terclist').droppable({
                    accept:'.tec',
                    hoverClass:'tec-floating-over',
                    drop:dropOnMe
                });
            terceira[i].elem.find('.terc-header').click(function(){
                $(this).next('.terc-tecs').slideToggle();
            });
        }
    }
    function dropOnMe(event,ui){
        var t1 = $(this).attr('data-terc');
        cas.ajaxer({
            sendme:{
                terc:t1,
                tec: ui.helper.attr('data-tec'),
                tecID: ui.helper.attr('data-id')
            },sendto:'svc/tec_to_terceira',
            andthen: loadTecs
        });
    }
    function searchTec(t){
        for(var i = 0;i<tecnico.length;i++){
            var x = tecnico[i];
            if(t.id){
                if(x.id !== null && parseInt(t.id) === parseInt(x.id))
                    return i;
            }else{
                if((''+t.sigaID+t.per+t.svc) === (''+x.sigaID+x.per+x.svc))
                    return i;            
            }
        }
        return null;
    }
    function searchAgenda(id){
        for(var i = 0;i<agenda.length;i++)
            if(id === agenda[i].id)
                return i;
        return null;
    }
    function reset(){
        x_agCount = 0;
        x_osCount = 0;
        osZ = [];
        $('#register').hide();
    }
    function loadAgenda(){
        filters = {};
        reset();
        $('#results-box').empty();
        $('.ui-button').button('disable');
        $('#progressbar').remove();
        
        $("<div id='progressbar' "+
                "style=''>").appendTo('body').progressbar({
            value:0
        });
        killEmAll();
        cas.ajaxer({
            method:'GET',
            sendme:_args(),
            sendto:'svc/agenda',
            andthen:loadAgenda_
        });
    }
    
    function loadAgenda_(x){
        agenda = [];
        osCount = 0;
        osLoaded = 0;
        var i,pos,oss = x.data.os,aid;
        for(i in oss){
            aid = Base64.encode(
                        JSON.stringify({
                            cod:oss[i].cod,
                            per:oss[i].per,
                            ag:oss[i].ag
                    }));
            pos = searchAgenda(aid);
            if(pos === null){
                pos = agenda.length;
                agenda.push({
                    id:aid,
                    visible:true,
                    cod:oss[i].cod,
                    per:oss[i].per,
                    ag:oss[i].ag,
                    agD:oss[i].agD,
                    os:[],
                    tecnicos:[],
                    tercs:[]
                });
            }
            osZ.push({
                os: oss[i].os,
                per: oss[i].per,
                svc: oss[i].svc,
                id: aid
            });
            agenda[pos].os.push({
                os:oss[i].os,
                per:oss[i].per,
                svc:oss[i].svc
            });
            osCount++;
        }
        agenda.sort(function(a,b){
            if(a.ag > b.ag){
                return 1;
            }else if(a.ag < b.ag){
                return -1;
            }else{
                return 0;
            }
        });
        $('#agCount').html(agenda.length);
        checkProgress();
        populateAgenda();
    }
    function slideUpdate(i){
        $('#x-slider-x').slider( "option", "value", agenda.length - i );
        $('#x-slider-x').slider('option', 'max', agenda.length );
    }
    var lload = null;
    function finishedLoading(){
        assocTecs();
        finishHim();
    }
    function finishHim(){
        $('#progressbar').remove();
        filterAgenda();
        calcTotal();
        $('.ui-button').button('enable');
        lload = (new Date()).getTime();
        if(cas.checkPerms('d'))
            $('#register').show();
        populateAgenda();
    }
    $('#register').click(regResult);
    $('#openreg').click(openReg);
    var wDialog;
    function openReg(){
        cas.ajaxer({
            method:'GET',
            sendme:_args(),
            sendto:'svc/load_register',
            andthen:openReg_
        });
    }
    function openReg_(x){
        var reg = x.data.reg,z,tmp;
        if(reg.length > 0)
            z = $("<ul class='reg-tb'>");
        else
            z = $("<h3 style='width:900px;color:#BCBCBC;text-align:center'>Registro vazio.</h3>");

        for(var i in reg){
            tmp = $("<li>")
                    .attr('data-file',reg[i].file)
                    .attr('data-ini',reg[i].ini)
                    .attr('data-end',reg[i].end)
                    .attr('data-pers',Base64.encode(reg[i].perlist))
                .append("<span class='reg-dt'>"+reg[i].ini+' <> '+reg[i].end+"</span>")
                .append("<b class='reg-p'>"+pLabel(JSON.parse(reg[i].perlist))+"</b>")
                .append("<b class='reg-nm'>"+defStr(reg[i].name)+"</b>")
                .append("<i class='reg-usr'>"+reg[i].user+"</i>")
                .click(clickReg).appendTo(z);
            
            if(cas.checkPerms('x'))
                $("<a class='reg-rm' title='Remover'>")
                    .attr('data-id',reg[i].id).click(rmReg).appendTo(tmp);
            
            if(reg[i].xls)
                $("<a href='/media/xls/"+reg[i].xls+"' class='reg-xls'>[Baixar]</a>")
                    .click(function(){
                        window.location.href = $(this).attr('href');
                        return false;
                    }).prependTo(tmp);
        }
        wDialog = cas.weirdDialogSpawn({
            top: $('#x-left').offset().top + 30,
            left: Math.max(100,($(window).width() - 900)/2)
        },z,wDialog,true);
    }
    function rmReg(){
        
        $(this).closest('weird-dialog').remove();
        cas.hidethis('body');
        
        cas.ajaxer({
            sendme:{
                id: $(this).attr('data-id')
            },sendto:'svc/rm_reg',
            andthen:function(x){
                cas.showthis('body');
                $('#openreg').trigger('click');
            }
        });
        return false;
    }
    function clickReg(){
        
        var me = $(this);
        $('#ini_selector').val(me.attr('data-ini'));
        $('#end_selector').val(me.attr('data-end'));
        
        selectedPers = JSON.parse(Base64.decode(me.attr('data-pers')));
        for(var i in selectedPers)
            selectedPers[i] = parseInt(selectedPers[i]);
        
        updatePs();
        cas.hidethis('body');
        $.getJSON('/media/json/' + me.attr('data-file'),loadReg);
        me.closest('.weird-dialog').remove();
    }
    function loadReg(x){
        cas.showthis('body');
        reset();
        regra = x.regra;
        agenda = x.agenda;
        terceira = x.terceira;
        tecnico = x.tecnico;
        filters = x.filters;
        osCount = 0;
        for(var i in agenda)
            osCount += agenda[i].os.length;
        osLoaded = osCount;
        populateTerceiras();
        makeProgress();
        finishHim();
        
    }
    function serializeAll(){
        return JSON.stringify(
                $.extend(true,{},
                    {regra: regParse(regra),
                    terceira: regParse(terceira),
                    tecnico: regParse(tecnico),
                    agenda: regParse(agenda),
                    filters: filters}
                )
            );
    }
    var hugeName,hugeTxt,tmpPath;
    function regResult(){
        hugeName = prompt('Você pode digitar um nome para este registro.',''+(new Date()).getTime());
        if(hugeName === null)
            return false;
        
        
        $('#progressbar').remove();
        $("<div id='progressbar' "+
                "style=''>").appendTo('body').progressbar({
            value:0
        });
        
        
        hugeTxt = serializeAll();
        onTheRun = 0;
        tmpPath = null;
        bitSend(0);
    }
    var onTheRun = 0;
    var bitLen = 100 * 1000;
    function bitSend_(x){
        var val = Math.floor(100 * (x.etc.index/hugeTxt.length));
        if(val > $( '#progressbar' ).progressbar( "value" ))
            $('#progressbar').progressbar("option", "value", val);
        var prev = tmpPath;
        tmpPath = x.data.path;
        onTheRun--;
        var t_next = hugeTxt.substr(x.etc.index + bitLen, bitLen);
        if(!t_next || t_next.length <= 0){
            setTimeout(commitReg,100);
        }else if(prev === null){
            setTimeout(function(){
                bitSend(x.etc.index + bitLen);
            },10);
        }
        
    }
    function bitRetry(x){
        setTimeout(function(){
            bitSend(x.etc.index);
        },10);
    }
    function bitSend(index){
        if(cas.ajaxcount < 5){
            var t = hugeTxt.substr(index,bitLen);
            if(t && t.length > 0){
                onTheRun++;
                cas.ajaxer({
                    sendme:{path: tmpPath, index:index,txt: t},
                    etc:{index:index},
                    sendto:'svc/r_uploader',
                    andthen:bitSend_
                });
                
                if(tmpPath !== null)
                    setTimeout(function(){
                        bitSend(index + bitLen);
                    },10);
            }
        }else{
            setTimeout(function(){
                bitSend(index);
            },10);
        }
    }

    function commitReg(){
        if(onTheRun > 0){
            setTimeout(commitReg,10);
            return false;
        }
        hugeTxt = '';
        cas.ajaxer({
            sendme: $.extend(_args(),
                {
                    name: hugeName, 
                    path: tmpPath
                }),
            sendto:'svc/register',
            andthen:function(){
                
                $('#progressbar').remove();
            }
        });
    }
    function meArray(x){
        return Object.prototype.toString.call( x ) === '[object Array]';
    }
    function regParse(o){
        var isObj = !(meArray(o));
        var z = ((isObj)?{}:[]),x,j,i;
        for(i in o){
            x = {};
            for(j in o[i])
                if(j !== 'elem')
                    x[j] = o[i][j];
            if(isObj)
                z[i] = x;
            else
                z.push(x);
        }
        return z;
    }
    function safeDiv(a,b){
        return (b>0)?a/b:0;
    }
    function calcTotal(){
        
        var Val = function(id,name,filtered){
            this.filtered = (filtered);
            this.name = name;
            this.id = parseInt(id);
            this.val = 0.0;
            this.qtde = 0;
            this.sum = 0;
            return this;
        };
        var X = function(name){
            this.name = name;
            this.x = [];
            this.val = 0.0;
            this.qtde = 0;
            this.sum = 0;
            return this;
        };
        var i, j, k, y, z, d, index,
            x = {}, 
            w = ['regra','terceira','tecnico'];
    
        x.regra = new X('Regras');
        x.terceira = new X('Terceiras');
        x.tecnico = new X('Técnicos');
        
        //POR REGRA
        for(i in regra){
            regra[i].total = 0.0;
            regra[i].qtde = 0;
            regra[i].sum = 0;
            regra[i].i = x.regra.x.length;
            x.regra.x.push(new Val(regra[i].id,regra[i].name,inFilters(regra[i].id,'regra')));
        }
        x.regra.x.push(new Val(-1,'#Indefinido',inFilters(-1,'regra')));
        
        //POR TERCEIRA
        for(i in terceira){
            terceira[i].total = 0.0;
            terceira[i].qtde = 0;
            terceira[i].sum = 0;
            terceira[i].i = x.terceira.x.length;
            x.terceira.x.push(new Val(terceira[i].id,terceira[i].name,inFilters(terceira[i].id,'regra')));
        }
        x.terceira.x.push(new Val(-1,'#Indefinido',inFilters(-1,'terceira')));
        
        //POR TECNICO
        for(i in tecnico){
            tecnico[i].total = 0.0;
            tecnico[i].qtde = 0;
            tecnico[i].sum = 0;
            tecnico[i].i = x.tecnico.x.length;
            x.tecnico.x.push(new Val(i,tecnico[i].name,inFilters(i,'tecnico')));
        }
        x.tecnico.x.push(new Val(-1,'#Indefinido',inFilters(-1,'tecnico')));
        
        for(i in agenda){
            for(j in w){
                k = w[j];
                if(k === 'regra'){d = regra;}else if(k === 'terceira'){d = terceira;}else if(k === 'tecnico'){d = tecnico;}
                index = agenda[i][k];
                y = d[index];
                
                if(typeof index === 'undefined' || index === null){
                    z = x[k].x[x[k].x.length - 1];
                    if(agenda[i].visible){
                        if(agenda[i].val){
                            z.val += agenda[i].val;
                            x[k].val += agenda[i].val;
                        }
                        z.qtde++;
                        x[k].qtde++;
                    }
                    z.sum++;
                    x[k].sum++;
                }else if(typeof y !== 'undefined'){
                    z = x[k].x[y.i];
                    if(agenda[i].visible){
                        if(agenda[i].val){
                            y.total += agenda[i].val;
                            z.val += agenda[i].val;
                            x[k].val += agenda[i].val;
                        }
                        y.qtde++;
                        z.qtde++;
                        x[k].qtde++;
                    }
                    y.sum++;
                    z.sum++;
                    x[k].sum++;
                }
            }
        }
        mountResult(x);
    }
    function fRTB(t,x){
        t.find('table>tfoot').append(
            '<tr>'+
                "<th class='th-name'>Total de "+x.x.length+"</th>"+
                "<th class='th-qtde'>"+qtdG(x.qtde,x.sum)+"</th>"+
                "<th class='th-val'>R$ "+cas.formatMoney(x.val,2,'.',',')+"</th>"+
            '</tr>'
        );
    }
    function qtdG(a,b){
        return a+' / '+b;
        /*if(parseInt(a) !== parseInt(b))
            return a+' / '+b;
        else
            return a;*/
    }
    function mountResult(x){
        var i,t,e = $('#results-box').empty();
        for(i in x){
            e.append('<h3>'+x[i].name+'</h3>');
            t = nRTB();
            t.appendTo(e);
            x[i].x= x[i].x.filter(function(a){
                return a.filtered || parseInt(a.sum) > 0;
            });
            x[i].x.sort(function(a,b){
                if(a.val > b.val) 
                    return -1;
                if(a.val < b.val) 
                    return 1;
                
                if(a.sum > b.sum) 
                    return -1;
                if(a.sum < b.sum) 
                    return 1;
                return 0;
            });
            for(var j in x[i].x){
                aRTB(t,x[i].x[j],i);
            }
            
            if(t.find('table>tbody>.zero').length > 0)
                $("<tr>"+
                    "<td  class='zeroToggle' colspan=3></td>"+
                '</tr>').click(function(){
                    $(this).parent().find('.zero').toggle();
                }).appendTo(t.find('table>tbody'));
            
            fRTB(t,x[i]);
        }
    }
    function nRTB(){
        var t;
        t = $('<div>').append(
            "<table class='result-tb'>"+
                '<thead></thead>'+
                '<tbody></tbody>'+
                '<tfoot></tfoot>'+
            "</table>");
        t.find('table>thead').append(
            '<tr>'+
                "<th class='th-name'>Nome</th>"+
                "<th class='th-qtde'>Quantidade</th>"+
                "<th class='th-val'>Valor</th>"+
            '</tr>'
        );
        return t;
    }
    function inFilters(id,i){
        return filters[i] && filters[i].indexOf(parseInt(id)) > -1;
    }
    function aRTB(t,x,i){
        var classes = '';
        
        if(inFilters(x.id,i))
            classes += 'filtered';
        else if(x.val <= 0.0)
            classes += 'zero';
        
        $("<tr class='"+classes+"'>"+
            "<td class='td-name'>"+
                "<a class='inliner a-name' data-item='"+i+"' data-id='"+x.id+"'>"+
                    ((x.id === -1)?"<i class='undefined-nm'>"+x.name+'</i>':x.name)+
                "</a>"+
            "</td>"+
            "<td class='td-qtde'>"+qtdG(x.qtde,x.sum)+"</td>"+
            "<td class='td-val'>R$ "+cas.formatMoney(x.val,2,'.',',')+"</td>"+
        '</tr>').appendTo(t.find('table>tbody')).find('.a-name').click(clickFilter);
    }
    
    var filters = {};
    function clickFilter(){
        var me = $(this),
            father = me.closest('tr'),
            item = me.attr('data-item'),
            id = parseInt(me.attr('data-id'));
        
        if(!filters[item])
            filters[item] = [];
        
        if(filters[item].indexOf(id) > -1)
            cas.kill(id,filters[item]);
        else
            filters[item].push(id);
        
        father.toggleClass('filtered');
        filterAgenda();
        calcTotal();
    }
    
    //FILTRA AGENDA
    function filterAgenda(){
        
        var i,j;
        x_agCount = 0;
        x_osCount = 0;
        
        for(i in agenda){
            agenda[i].visible = true;
            
            for(j in filters){
                if(
                    filters[j].length
                    &&
                    !(
                        ( (typeof agenda[i][j] === 'undefined' || agenda[i][j] === null ) && filters[j].indexOf(-1) > -1)
                        || (agenda[i][j] && filters[j].indexOf(parseInt(agenda[i][j])) > -1)
                    )
                ){
                    agenda[i].visible = false;
                    break;
                }
            }
            if(agenda[i].visible){
                x_agCount++;
                x_osCount += agenda[i].os.length;
            }
        }
        
        $('#agCount').html(x_agCount);
        $('#osCount').html(x_osCount);
        $('#osLoaded').html(osLoaded);
        populateAgenda();
    }
    
    function makeProgress(){
        var progress = 0;
        if(osCount > 0)
            progress = Math.floor((osLoaded/osCount) * 100);
        if($('#progressbar').length > 0)
            $('#progressbar')
                .progressbar( "option", "value", progress );
        if(osZ.length > 0)
            loadAgOS();
        $('#osCount').html(osCount);
        $('#osLoaded').html(osLoaded);
    }
    function checkProgress(){
        if(osLoaded === osCount){
            finishedLoading();
        }else{
            setTimeout(checkProgress,300);
        }
        makeProgress();
    }
    function aCount(){
        return (x_agCount)
                    ?Math.min(x_agCount,agenda.length)
                    :agenda.length;
    }
    function populateAgenda(start){
        var i,capacity = aCapacity();
        if(typeof start === 'undefined'){
            var first = $('#agenda>.ag-li').first();
            if(first.length > 0)
                i = parseInt(first.attr("data-index"));
            else
                i = 0;
            if(i > agenda.length)
                i = 0;
        }else{
            i = start;
        }
        
        while(i > 0 && !checkCount(i,capacity)){
            i--;
        }
        
        slideUpdate(i);
        var count = 0;
        
        killEmAll();
        
        while(i < agenda.length && count < capacity ){
            if(agenda[i].visible){
                count++;
                putAgenda(i);
            }
            i++;
        }
    }
    function aCapacity(){
        return Math.floor($('#x-left').height()/40);
    }
    function checkCount(i,t){
        var count = 0;
        for(var j = i;j < agenda.length; j++){
            if(agenda[j].visible)
                count++;
            if(count === t)
                return true;
        }
        return false;
    }
    
    
    function putAgenda(i){
        //$('#agenda>.ag-li[data-index="'+i+'"]').remove();
        agenda[i].elem = 
            $("<li class='ag-li' data-index='"+i+"'>"+
                "<table class='ag-tb'>"+
                    "<thead data-id='"+agenda[i].id+"'>"+
                        "<tr>"+
                            "<th class='ag-index'>"+(i+1)+"</th>"+
                            "<th class='ag-agenda'>"+agenda[i].agD+"</th>"+
                            "<th class='ag-title' colspan=2>"+
                                "<span class='ag-wrap'>"+
                                    "<span class='ag-cod'>"+agenda[i].cod+"</span>"+
                                    "<span class='ag-nome'>"+
                                        ((agenda[i].assname)?agenda[i].assname:'')+
                                    "</span>"+
                                "</span>"+
                            "</th>"+
                            "<th class='ag-terceira'></th>"+
                            "<th class='ag-tecnico'></th>"+
                        "</tr>"+
                    "</thead>"+
                    "<tbody"+((openAgendas.indexOf(agenda[i].id) === -1)?" style='display:none;'":'')+"></tbody>"+
                "</table>"+
            "</li>");
        
        agenda[i].elem.appendTo('#agenda');
        agenda[i].elem.find('thead').click(agToggle);
        for(var j in agenda[i].os){
            if(typeof agenda[i].os[j].asscod !== 'undefined'){
                mountOS(i,j);
            }
        }
        updateAgendaElem(i);
        assocRule(i);
    }
    function mountOS(pos,ospos){
        if(!agenda[pos].elem)
            return false;
        var os = agenda[pos].os[ospos];
        var e = agenda[pos].elem.find('tbody');
        $("<tr>"+
            "<td class='ag-td1' title='Nº da OS' rowspan=2>"+
                "<a target='_blank' title='Clique para Mais Detalhes sobre a OS' href='"+'os#os='+os.os+'&per='+os.per+'&svc='+os.svc+"'>"+os.os+"</a>"+
            "</td>"+
            "<td class='ag-td1' title='Produto'>"+os.svc.toUpperCase()+"</td>"+
            "<td title='Tipo da OS'>"+defStr(os.os_tipo)+"</td>"+
            "<td class='ag-obs' title='Observação da Origem'>"+defStr(os.obs_origem)+"</td>"+
            "<td title='Falha'>"+defStr(os.falha)+"</td>"+
            "<td title='Código do Técnico'>"+defStr(os.tec_id)+"</td>"+
        "</tr>"+
        "<tr>"+
            "<td class='ag-td1' title='Permissora'>"+os.per+"</td>"+
            "<td title='Sub-tipo'>"+defStr(os.sub_tipo)+"</td>"+
            "<td class='ag-obs' title='Observação do Técnico'>"+defStr(os.obs_tec)+"</td>"+
            "<td title='Causa'>"+defStr(os.causa)+"</td>"+
            "<td title='Técnico'>"+defStr(os.tec)+"</td>"+
        "</tr>").appendTo(e);
    }
    function agToggle(){
        var me = $(this), id = me.attr('data-id'), visible = me.next('tbody').toggle().is(':visible');
        if(visible && openAgendas.indexOf(id) === -1)
            openAgendas.push(id);
        else if(!visible)
            cas.kill(id,openAgendas);
        
        $('#x-left').getNiceScroll().resize();
    }
    function loadAgOS(){
        if(cas.ajaxcount > 5)
            return false;
        var oss = osZ.splice(0,osPerRequest);
        for(var i in oss){
            var x = [oss[i].os,oss[i].per,oss[i].svc,oss[i].id];
            oss[i] = x.join('.');
        }
        oss = oss.join(',');
        cas.ajaxer({
            method:'GET',
            etc:{d: (new Date()).getTime(),u: oss},
            sendme:{oss:oss},
            sendto:'svc/agenda_oss',
            andthen:loadAgOS_
        });
    }
    function changeTec(){
        
        var v = $(this).val();
        var i = parseInt($(this).attr('data-index')),
            t = parseInt(v),tt = '---';
        delete agenda[i].terceira;
        delete agenda[i].tecnico;
        
        if(v !== ''){
            agenda[i].tecnico = t;
            if(tecnico[t] && tecnico[t].terceira){
                agenda[i].terceira = tecnico[t].terceira;
                tt = terceira[agenda[i].terceira].name;
            }
        }
        
        $(this).closest('.weird-dialog').remove();
        agenda[i].elem.find('.ag-terceira').html(tt);
        assocRule(i);
        checkRules(i);
        filterAgenda();
        calcTotal();
    }
    function mounTStec(){
        var l = JSON.parse(Base64.decode($(this).attr('data-list'))),
            index = parseInt($(this).attr('data-index'));
    
        var s = 
            $('<select class="tselect"><option value="">## VÁRIOS ##</option></select>')
                .attr('data-index',index)
                .click(function(){return false;}).change(changeTec);
        
        for(var i in l)
            s.append("<option value='"+l[i].id+"'>"+l[i].name+"</option>");
        
        if(agenda[index].tecnico)
            s.val(agenda[index].tecnico);
        
        cas.weirdDialogSpawn($(this).offset(),
            $("<div style='padding:10px 20px;width:auto;'>"+
                    "<h3 style='text-indent:10px;'>Selecione o técnico<h3></div>").append(s)
        );
        return false;
    }
    function updateAgendaElem(pos){
        if(!agenda[pos].elem)
            return false;
        if(agenda[pos].assname)
            agenda[pos].elem.find('.ag-nome').html(agenda[pos].assname);
        
        if( agenda[pos].tecnico )
            agenda[pos].elem.find('.ag-tecnico').html(tecnico[agenda[pos].tecnico].name);
        if( agenda[pos].tecnicos.length > 1 ){
            var z = agenda[pos].elem.find('.ag-tecnico').addClass('destac').empty();
            if(cas.checkPerms('d')){
                var tid, x = [];
                
                for(var i in agenda[pos].tecnicos){
                    tid = agenda[pos].tecnicos[i];
                    x.push({
                        id: tid,
                        name: tecnico[tid].name
                    });
                }
                
                $('<a style="color:white">'+
                            ((agenda[pos].tecnico)
                                ?tecnico[agenda[pos].tecnico].name
                                :'Selecionar')+
                '</a>')
                        .attr('data-list',Base64.encode(JSON.stringify(x)))
                        .attr('data-index',pos).appendTo(z).click(mounTStec);
            }else{
                z.html('VÁRIOS');
            }
        }
        
        
        if( agenda[pos].terceira )
            agenda[pos].elem.find('.ag-terceira').html(terceira[agenda[pos].terceira].name);
        else
            agenda[pos].elem.find('.ag-terceira').html('---');
    }
    function loadAgOS_(x){
        var diff = new Date() - x.etc.d,
            os,pos,i,this_tec,tpos,ospos;
    
        if(diff < 30 * 1000 && x.etc.u.length < 1500)
            osPerRequest += 2;
        
        for(i in x.data.os){
            os = x.data.os[i];
            
            pos = searchAgenda(os.id);
            if(pos !== null){
                
                osLoaded++;
                ospos = searchOS(os,agenda[pos]);
                
                if(ospos !== null)
                    agenda[pos].os[ospos] = os;
                
                if(!agenda[pos].assname && os.assname)
                    agenda[pos].assname = os.assname;
                
                if(os.tec){
                    
                    this_tec = {
                        sigaID: os.tec_id,
                        per: ((os.svc === 'tv')?os.grpper:os.per),
                        svc: os.svc
                    };
                    tpos = searchTec(this_tec);
                    
                    if(tecnico[tpos].equals_to)
                        tpos = searchTec({id:tecnico[tpos].equals_to});
                    
                    if(agenda[pos].tecnicos.indexOf(tpos) === -1)
                        agenda[pos].tecnicos.push(tpos);
                    
                }
                updateAgendaElem(pos);
                mountOS(pos,ospos);
            }
        }
    }
    function assocTecs(){
        var i,j,terc;
        for(i in agenda){
            agenda[i].tercs = [];
            agenda[i].visible = true;
            delete agenda[i].terceira;
            delete agenda[i].tecnico;
            
            for(j in agenda[i].tecnicos){
                terc = tecnico[agenda[i].tecnicos[j]].terceira;
                if(agenda[i].tercs.indexOf(terc) === -1)
                    agenda[i].tercs.push(terc);
            }
            
            if(agenda[i].tecnicos.length === 1)
                agenda[i].tecnico = agenda[i].tecnicos[0];
            
            if( agenda[i].tercs.length === 1 && parseInt(agenda[i].tercs[0]) )
                agenda[i].terceira = parseInt(agenda[i].tercs[0]);
            
            updateAgendaElem(i);
            assocRule(i);
            checkRules(i);
        }
    }
    
    function checkRules(index){
        var a = agenda[index];
        var i, j, val;
        
        delete a.regra;
        a.val = 0.0;
        a.regras = {};
        a.regraPlus = {};
        
        if(!a.terceira)
            return false;
        
        for(i in regra){
            if(typeof regraCheck[i] === 'function'){
                val = regraCheck[i].apply(regra[i],[a]);
                if(val > 0.0){
                    a.regras[i] = val;
                }
            }
            a.regraPlus[i] = [];
        }
        
        for(i in a.regras){
            if(regra[i].plus !== -1)
                for(j in a.regras){
                    if(
                        i !== j
                        && regra[j].plus !== null 
                        && typeof regra[j].plus === 'object'
                        && (regra[j].plus === 0 || regra[j].plus.indexOf(parseInt(i)) > -1)

                    ){
                        a.regras[i] += a.regras[j];
                        a.regraPlus[i].push(j);
                    }
                }
            
            if(
                a.regras[i] 
                    > a.val
                && !(regra[i].plus !== -1
                        && a.regra
                            && regra[a.regra].plus === -1)
            ){
                a.regra = i;
                a.val = a.regras[i];
            }
        }
    }
    function fVal(v){
        return ((v > 0.0)?'R$ '+cas.formatMoney(v,2,'.',','):'---');
    }
    function assocRule(index){
        var ag = agenda[index];
        if(!ag.elem)
            return false;
        ag.elem.find('.ag-price,.ag-rule').remove();
        var e = ag.elem, 
            val = fVal(ag.val);
        
        var l = $("<ul class='regra-list'>"),tmp,r;
        
        for(r in ag.regras){
            tmp = $('<li class=regra-list-l>'+
                    "<div class='regra-list-v'>"+
                        fVal(regra[r].val[ag.per][ag.terceira])+
                    "</div>"+
                    '<span class="inliner">'+regra[r].name+'</span></li>').appendTo(l);
            if(ag.regra === r)
                tmp.addClass('regra-list-r');
            if(ag.regraPlus[ag.regra].indexOf(r) > -1)
                tmp.addClass('regra-list-s');
                
        }
    
        e.find('thead>tr')
            .append("<th class='ag-price"+((val === '---')?' no-val':'')+"'>"+
                        val+
                    "</th>");
        
        var tr = e.find('tbody>tr');
        tr.first().append(
            $("<td class='ag-rule' rowspan="+tr.length+">").append(l));
    }
    function searchOS(x,a){
        for(var i in a.os)
            if(
                parseInt(a.os[i].os) === parseInt(x.os)
                && parseInt(a.os[i].per) === parseInt(x.per)
                && a.os[i].svc === x.svc
            )
                return i;
        return null;
    }
    
    $('#tec_to_terceira').click(function(){
        $(this).toggleClass('ok');
        $("#terceira_config").slideToggle(function(){
            if($("#terceira_config").is(':visible')){
                loadTecs();
                $("#terceira_config>div").height(
                    $("#terceira_config").height() - $("#terceira_config>h3").outerHeight()
                );
            }
        });
    });
    
    if(cas.get_pref('svc_ini'))
        $('#ini_selector').val(cas.get_pref('svc_ini'));
    if(cas.get_pref('svc_end'))
        $('#end_selector').val(cas.get_pref('svc_end'));
    
    $('#ini_selector').datepicker({ dateFormat: "yy-mm-dd" }).change(function(){
        cas.set_pref('svc_ini',$(this).val());
    });
    $('#end_selector').datepicker({ dateFormat: "yy-mm-dd" }).change(function(){
        cas.set_pref('svc_end',$(this).val());
    });
    $('#selectper').click(function(){
        $(this).toggleClass('ss');
        var me = $(this), p = $('#selectperlist');
        if(!p.is(':visible'))
            p.css('top',me.position().top + me.outerHeight() + 5)
                .css('left',me.position().left ).show();
        else
            p.hide();
    });
    var svpt;
    function clickPer(){
        $(this).toggleClass('selected');
        var me = parseInt($(this).attr('data-id'));
        if(selectedPers.indexOf(me) === -1)
            selectedPers.push(me);
        else
            cas.kill(me,selectedPers);
        
        selectedPers.sort();
        $('#selectper').button('option','label',pLabel(selectedPers));
        
        clearTimeout(svpt);
        svpt = setTimeout(svP,500);
        return false;
    }
    function svP(){
        cas.set_pref('svc_perlist',selectedPers);
    }
    function pLabel(s){
        var l = Object.keys(cas.permissora).length;
        var pp = "SIM TV";
        if(s 
            && s.length 
                && s.length < l
        ){
            pp = [];
            for(var i in s)
                pp.push(cas.permissora[s[i]].abbr);
            pp = pp.join(', ');
        }
        return pp;
    }
    function boot(){
        //LOAD PERLIST FROM PREFS
        var tmpp = cas.get_pref('svc_perlist');
        if(tmpp && tmpp.length){
            for(var i in tmpp){
                selectedPers.push(parseInt(tmpp[i]));
            }
        }
        selectedPers.sort();
        updatePs();
    }
    function updatePs(){
        $('#perlist').empty();
        for(var i in cas.permissora){
            var t = $("<li class='myper' data-id='"+cas.permissora[i].id+"'>"+cas.permissora[i].abbr+"</li>")
                .appendTo('#perlist')
                .click(clickPer);

            if(selectedPers.indexOf(cas.permissora[i].id) > -1)
                t.addClass('selected');
        }
        $('#selectper').button('option','label',pLabel(selectedPers));
    }
    $('#go').click(function(){
        $('#results-box').empty();
        $('.ui-button').button('disable');
        
        cas.ajaxer({
            method:'GET',
            sendme:_args(),
            sendto:'svc/auto_assign',
            andthen:function(){
                cas.ajaxer({
                    method:'GET',
                    sendme:_args(),
                    sendto:'svc/slave_all',
                    andthen:function(){
                        cas.ajaxer({
                            method:'GET',
                            sendme:_args(),
                            sendto:'svc/all_tecs',
                            andthen: function(x){
                                putTecs(x);
                                loadAgenda();
                            }
                        });
                    }
                });
            }
        });
    });
    $('#salvar_regras').click(salvarRegras);
    $('#left-toggle').click(function(){
        if(leftShow){
            $('#x-slider').hide();
            $('#x-right').animate({width:'100%'},500,function(){
                $('#x-left,#x-right').getNiceScroll().resize();
            });
            leftShow = false;
        }else{
            $('#x-slider').show();
            $('#x-right').animate({width:'30%'},500,function(){
                $('#x-left,#x-right').getNiceScroll().resize();
            });
            leftShow = true;
        }
        $('#x-left,#x-right').getNiceScroll().resize();
    });
    $('#x-left').niceScroll({
        cursoropacitymax:0
    });
    $('#x-right').niceScroll();
    cas.resizer.push(resizer);
    resizer();
    
    $('#x-abs').css('top',$('#x-left').offset().top);
    function killEmAll(){
        $('#agenda>.ag-li').each(function(){
            killAgenda($(this));
        });
    }
    function killAgenda(dead){
        if(dead.length > 0){
            var index = parseInt(dead.attr('data-index'));
            if(agenda[index])
                delete agenda[index].elem;
            dead.remove();
        }
    }
    var slideT,slideO;
    $('#x-slider-x').slider({
        orientation: 'vertical',
        min: 0,
        max: 0,
        slide: function( event, ui ) {
            clearTimeout(slideT);
            slideT = setTimeout(function(){
                populateAgenda(agenda.length - ui.value);
            },300);
        }
    });
    $('#x-slider').hover(function(){
        var me = $(this);
        clearTimeout(slideO);
        slideO = setTimeout(function(){
            me.stop(true,true).animate({
                opacity: 0.9
            },500);
        },300);
    },function(){
        var me = $(this);
        clearTimeout(slideO);
        slideO = setTimeout(function(){
            me.stop(true,true).animate({
                opacity: 0
            },500);
        },300);
    });
    var mstT;
    $('body').on('mouseenter','.inliner',function(){
        var me = $(this);
        clearTimeout(mstT);
        mstT = setTimeout(function(){
            if(me.is(':hover')){
                me.css('white-space','normal').css('display','block');
                me.one('mouseleave',function(){
                    me.css('white-space','').css('display','');
                });
            }
        },700);
    });
    function lockBTS(){
        
    }
    $('button').button().button('disable');
    loadRegras();
    boot();
};