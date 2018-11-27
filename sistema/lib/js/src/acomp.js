cas.controller = function(){
    var thread = null;
    var lsearched = '';
    function acs(){
        $('#the_auto_searcher').hide();
        cas.hidethis('body');
        cas.ajaxer({
            method:'GET',
            sendme:{
                area:$('#area_selector').val(),
                status:$('#status_selector').val(),
                stage:$('#stage_selector').val(),
                month:$('#month_selector').val()
            },
            sendto: 'acomp/l',
            andthen: acs_
        });
    }
    function acs_(x){
        var data = x.data;
        cas.showthis('body');
        $('#container').empty();
        for(var i=0;i<data.acomps.length;i++){
            if(data.acomps[i].stage === '1')
                var clr = '#C5EEC7';
            else if(data.acomps[i].stage === '2')
                var clr = '#D8E2E6';
            $('#container').append(
                "<div id='acmp_" + data.acomps[i].id + "' acomp=" + data.acomps[i].id +
                    " class='acmp' area='" + data.acomps[i].name_abbr +
                        "' stage='" + data.acomps[i].stage + "' status='" + data.acomps[i].status_n +
                        "' style='background-color:" + clr + "'>" +
                    "<span class='acmp-list-area'>" + data.acomps[i].name_abbr + "</span>" + 
                    "<span class='acmp-list-name'>" + data.acomps[i].assinante + "</span>" +
                    "<span class='acmp-list-status' style='font-size:7pt;font-style:normal'> [" + data.acomps[i].status_n.toLowerCase() + "]</span>" +
                    "<span class='acmp-list-date'>" + data.acomps[i].dmy + "</span>" +
                "</div>" +
                "<div class='acmp-tb' id='acmp-tb_"+data.acomps[i].id+"' acomp="+data.acomps[i].id+"></div>");
        }
        $('.acmp').click(function(){
            var acmp = $(this).attr('acomp');
            if($('#acmp-tb_'+acmp).is(':empty')){
                cas.hidethis('body');
                cas.ajaxer({
                    method:'GET',
                    sendme:{id:acmp},
                    sendto: 'dashboard/acomp_updates',
                    andthen:function(x){
                        cas.showthis('body');
                        makeAcompUpdateTable(acmp,x.data.list);
                    }
                });
            }else{
                $('#acmp-tb_'+acmp).empty();
            }
        });
        $('#the_auto_searcher').show();
    }
    function makeAcompUpdateTable(index,list){
        if(list && list.length){
            $('#acmp-tb_'+index).empty();
            var parthtm;
            parthtm ="<table class='acmpup'>" + 
                    "<tr>" +
                        "<td class='bgrey'>Data</td>" + 
                        "<td class='bgrey'>Etapa</td>" + 
                        "<td class='bgrey'>Usuário</td>" + 
                        "<td class='bgrey'>OS</td>" +
                        "<td class='bgrey'>Data Visita</td>" +
                        "<td class='bgrey'>Horário Visita</td>" + 
                        "<td class='bgrey'>Descrição</td>" +
                    "</tr>" +
                "</table>";
            
            $('#acmp-tb_'+index).append(parthtm);
            for(var u in list){
                parthtm ="<table id='acupdt_"+list[u].id+"' class='acmpup'><tr>";
                parthtm += "<td>";
                parthtm += list[u].date;
                parthtm += "</td>";
                parthtm += "<td>";
                parthtm += list[u].stage;
                parthtm += "</td>";
                parthtm += "<td>";
                parthtm += list[u].author;
                parthtm += "</td>";
                if(list[u].os){
                    parthtm += "<td>";
                    parthtm += list[u].os;
                    parthtm += "</td>";
                    parthtm += "<td>";
                    parthtm += list[u].schedule;
                    parthtm += "</td>";
                    parthtm += "<td>";
                    parthtm += list[u].window;
                    parthtm += "</td>";
                }else{
                    parthtm += "<td></td><td></td><td></td>";
                }

                parthtm += "<td><span class='acdescr' id='x_descr_"+list[u].id+"' fulltxt=\""+list[u].descr+"\">";
                if(list[u].descr.length > 19)
                    parthtm += "<span class='xpndme' updt="+list[u].id+">"+list[u].descr.substr(0, 20)+" [...]</span>";
                else
                    parthtm += list[u].descr;
                parthtm += "</span></td>";
                parthtm += "</tr></table>";
                parthtm = $(parthtm);
                parthtm.find('.xpndme').click(function(){
                    $('#fulldescr').html($(this).parent().attr('fulltxt'));
                    $('#acupdt_'+$(this).attr('updt')).addClass('slctdup');
                    $('#descr-dlg').dialog('open');
                });
                $('#acmp-tb_'+index).append(parthtm);
            }
        }
    }
    $('#the_apply_selector').click(function(){
        acs();
    });
    $('#descr-dlg').dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: true,
        width: 300,
        height:300,
        draggable:true,
        resizable: true,
        close: function(){
            $('.acmpup').removeClass('slctdup');
        }
    });

    $('#the_auto_searcher').keyup(function(){
        clearTimeout(thread);
        var $this = $(this);
        thread = setTimeout(function() {
            var txt = $.trim($this.val());
            if(!txt || !txt.length){
                $('.acmp').show();
                $('.acmp-tb').show();
            }else
                if(txt !== lsearched)
                    autoSearcher(txt);
            lsearched = txt;
        }, 100);
    });
    function autoSearcher(a){
        $('.acmp-list-name').each(function(i){
            if($(this).html().toLowerCase().indexOf(a.toLowerCase(),0) === -1){
                $(this).parent().hide();
                $(this).parent().next().hide();
            }else{
                $(this).parent().show();
                $(this).parent().next().show();
            }
        });
    }
};