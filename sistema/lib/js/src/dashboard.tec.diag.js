(function(window){
    var cas = window.cas;
    var tecdiagok = false;
    $("#tec_mes-tabs").tabs();
    
    function fSVC(s) {
        if (s.length > 2) return s.substr(1, 2).toLowerCase();
        else return s.toLowerCase();
    }
    cas.charts.tecDiag = function(x) {
        var tot_vts = x.data.info.diag.total.ok + x.data.info.diag.total.not_ok;
        $('#diag-total-total').html(tot_vts);
        $('#diag-total-prod').html(cas.roundNumber(((tot_vts) / x.data.info.ndays), 2) + " por dia");
        $('#diag-total-rev').html(x.data.info.diag.total.not_ok);
        $('#diag-total-quali').html(cas.roundNumber((x.data.info.diag.total.ok / (tot_vts)) * 100, 2) + "%");
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$444
        $('#diag-cm-a-total').html(x.data.info.diag.cm.a.ok + x.data.info.diag.cm.a.not_ok);
        $('#diag-cm-a-not_ok').html(x.data.info.diag.cm.a.not_ok);
        $('#diag-cm-b-total').html(x.data.info.diag.cm.b);
        $('#diag-tv-a-total').html(x.data.info.diag.ptv.a.ok + x.data.info.diag.ptv.a.not_ok);
        $('#diag-tv-a-not_ok').html(x.data.info.diag.ptv.a.not_ok);
        $('#diag-tv-b-total').html(x.data.info.diag.ptv.b);
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $('#diag-cm-a-total-perc1').html(cas.roundNumber(((x.data.info.diag.cm.a.ok + x.data.info.diag.cm.a.not_ok) / tot_vts) * 100, 2) + "%");
        $('#diag-cm-a-not_ok-perc1').html(cas.roundNumber((x.data.info.diag.cm.a.not_ok / (x.data.info.diag.cm.a.ok + x.data.info.diag.cm.a.not_ok)) * 100, 2) + "%");
        $('#diag-cm-b-total-perc1').html(cas.roundNumber((x.data.info.diag.cm.b / (x.data.info.diag.total.not_ok)) * 100, 2) + "%");
        $('#diag-tv-a-total-perc1').html(cas.roundNumber(((x.data.info.diag.ptv.a.ok + x.data.info.diag.ptv.a.not_ok) / tot_vts) * 100, 2) + "%");
        $('#diag-tv-a-not_ok-perc1').html(cas.roundNumber((x.data.info.diag.ptv.a.not_ok / (x.data.info.diag.ptv.a.ok + x.data.info.diag.ptv.a.not_ok)) * 100, 2) + "%");
        $('#diag-tv-b-total-perc1').html(cas.roundNumber((x.data.info.diag.ptv.b / (x.data.info.diag.total.not_ok)) * 100, 2) + "%");
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $('#diag-cm-a-not_ok-perc2').html(cas.roundNumber((x.data.info.diag.cm.a.not_ok / tot_vts) * 100, 2) + "%");
        $('#diag-cm-b-total-perc2').html(cas.roundNumber((x.data.info.diag.cm.b / tot_vts) * 100, 2) + "%");
        $('#diag-tv-a-not_ok-perc2').html(cas.roundNumber((x.data.info.diag.ptv.a.not_ok / tot_vts) * 100, 2) + "%");
        $('#diag-tv-b-total-perc2').html(cas.roundNumber((x.data.info.diag.ptv.b / tot_vts) * 100, 2) + "%");
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $('#tec_mes-hist-list').empty();
        var phtm;
        for (var i in x.data.info.hist) {
            phtm = '';
            if (typeof x.data.info.hist[i].DT_INGR2 === 'undefined') {
                phtm = "<table class='tec-h-tb'>" + "<tr>" + "<th class='opihkas' style='width:10%;'>" +
                    "<a class='ashjw' target='_blank' style='color:whitesmoke' href='os#os=" +
                    x.data.info.hist[i].NRO_OS + "&per=" + x.data.info.hist[i].PER + "&svc=" +
                    fSVC(x.data.info.hist[i].SERVICO) + "'>" + x.data.info.hist[i].NRO_OS + "</a>" +
                    "</th>" + "<th class='opihkas ashjw' style='width:70%;'>" +
                    "<a style='color:whitesmoke' target='_blank' href='#" +
                    cas.hashbangify({
                        dashboard: {
                            dashboard: dashboard.dashboard,
                            view: 'assinante',
                            ind: 'imb',
                            item: x.data.info.hist[i].PER + ':' + x.data.info.hist[i].NUM_ASS
                        }
                    }) +
                    "'>" +
                    x.data.info.hist[i].NOME_ASSINANTE + "</a>" +
                    "</th>" +
                    "<th class='opihkas' style='width:15%;'>" +
                    x.data.info.hist[i].DT_INGR +
                    "</th>" + "</tr>";
                //~~~~~~~~~~CAUSA
                phtm += "<tr>" + "<td>" + "<b>Causa</b>" + "</td>" + "<td>" +
                    ((x.data.info.hist[i].CAUSA) ? x.data.info.hist[i].CAUSA : '---') + "</td>" + "<td rowspan='3'>" +
                    ((x.data.info.hist[i].OBS_TECNICO) ? x.data.info.hist[i].OBS_TECNICO : '---') + "</td>" + "</tr>";
                //~~~~~~~~~~FALHA
                phtm += "<tr>" + "<td>" + "<b>Falha</b>" + "</td>" + "<td>" +
                    ((x.data.info.hist[i].FALHA) ? x.data.info.hist[i].FALHA : '---') + "</td>" + "</tr>";
                //~~~~~~~~~~CAUSA
                phtm += "<tr>" + "<td>" + "<b>Motivo</b>" + "</td>" + "<td>" +
                    ((x.data.info.hist[i].MOTIVO) ? x.data.info.hist[i].MOTIVO : '---') + "</td>" + "</tr>";
                phtm += "</table>";
            } else {
                var tec = dashboard.item;
                tec = tec.split(':');
                tec = {
                    area: tec[0],
                    tec: tec[1]
                };

                phtm = "<table class='tec-h-tb'>" + "<tr>" + "<th class='opihkas' style='font-size:10pt;width:10%;'>" + "<a class='ashjw' target='_blank' style='color:whitesmoke' href='os#os=" + x.data.info.hist[i].NRO_OS + "&per=" + x.data.info.hist[i].PER + "&svc=" + fSVC(x.data.info.hist[i].SERVICO) + "'>" + x.data.info.hist[i].NRO_OS + "</a>" + "</th>" +
                    "<th class='opihkas ashjw' style='width:30%;'>" + "<a class='ashjw' target='_blank' style='color:whitesmoke' href='#" +
                    cas.hashbangify({
                        dashboard: {
                            dashboard: dashboard.dashboard,
                            view: 'assinante',
                            ind: 'imb',
                            item: x.data.info.hist[i].PER + ':' + x.data.info.hist[i].NUM_ASS
                        }
                    }) +
                    "'>" +
                    x.data.info.hist[i].NOME_ASSINANTE +
                    "</a>" +
                    "</th>" +
                    "<th class='opihkas' style=';width:15%;'>" + x.data.info.hist[i].DT_INGR + "</td>" + "<th style='width:5px;background-color:whitesmoke;' rowspan=4></td>" + "<th class='opihkas' style='font-size:10pt;width:10%;'>" + "<a class='ashjw' target='_blank' style='color:whitesmoke' href='os#os=" + x.data.info.hist[i].NRO_OS2 + "&per=" + x.data.info.hist[i].PER2 + "&svc=" + fSVC(x.data.info.hist[i].SERVICO2) + "'>" + x.data.info.hist[i].NRO_OS2 + "</a>" + "</th>" + "<th class='opihkas' style='width:20%;'>" + ((x.data.info.hist[i].NOME_TECNICO2) ? "<a style='color:whitesmoke' target='_blank' class='ashjw' href='#" +
                        cas.hashbangify({
                            dashboard: {
                                dashboard: dashboard.dashboard,
                                view: 'tecnico',
                                ind: 'irm',
                                item: tec.area + ':' + x.data.info.hist[i].TEC2
                            }
                        }) +
                        "'>" +
                        x.data.info.hist[i].NOME_TECNICO2 +
                        "</a>" : '---') + "</th>" + "<th class='opihkas' style='width:15%;'>" + x.data.info.hist[i].DT_INGR2 + "</th>" + "</tr>";
                //~~~~~~~~~~CAUSA
                phtm += "<tr>" + "<td>" + "<b>Causa</b>" + "</td>" + "<td>" +
                    ((x.data.info.hist[i].CAUSA) ? x.data.info.hist[i].CAUSA : '---') + "</td>" +
                    "<td rowspan='3'>" + ((x.data.info.hist[i].OBS_TECNICO) ? x.data.info.hist[i].OBS_TECNICO : '---') +
                    "</td>" + "<td  colspan=2 rowspan='3'>" + ((x.data.info.hist[i].OBS_TECNICO2) ? x.data.info.hist[i].OBS_TECNICO2 : '---') + "</td>" +
                    "<td>" + ((x.data.info.hist[i].CAUSA2) ? x.data.info.hist[i].CAUSA2 : '---') + "</td>" + "</tr>";
                //~~~~~~~~~~FALHA
                phtm += "<tr>" + "<td>" + "<b>Falha</b>" + "</td>" + "<td>" + ((x.data.info.hist[i].FALHA) ? x.data.info.hist[i].FALHA : '---') +
                    "</td>" + "<td>" + ((x.data.info.hist[i].FALHA2) ? x.data.info.hist[i].FALHA2 : '---') + "</td>" + "</tr>";
                //~~~~~~~~~~CAUSA
                phtm += "<tr>" + "<td>" + "<b>Motivo</b>" + "</td>" + "<td>" + ((x.data.info.hist[i].MOTIVO) ? x.data.info.hist[i].MOTIVO : '---') +
                    "</td>" + "<td>" + ((x.data.info.hist[i].MOTIVO2) ? x.data.info.hist[i].MOTIVO2 : '---') + "</td>" + "</tr>";
                phtm += "</table>";
            }
            $('#tec_mes-hist-list').append(phtm);
        }
        var jj = ['causas', 'falhas', 'motivos'];
        for (var j in jj) {
            var k = jj[j];
            $('#tec_mes-diag-' + k + '-a').empty();
            $('#tec_mes-diag-' + k + '-b').empty();
            for (var i in x.data.info.diag[k]['a']) {
                $('#tec_mes-diag-' + k + '-a').append("<div  style='width:100%;display:block;position:relative;'>" + "<table style='width:100%'>" + "<tr>" + "<th colspan=3 style='background-color: #E9ECEF;'>" + x.data.info.diag[k]['a'][i]['name'] + "</th>" + "</tr>" + "<tr>" + "<td><b>Conforme</b></td>" + "<td>" + x.data.info.diag[k]['a'][i]['total']['ok'] + "</td>" + "<td>" + cas.roundNumber((x.data.info.diag[k]['a'][i]['total']['ok'] / x.data.info.diag.total.ok) * 100, 2) + "%</td>" + "</tr>" + "<tr>" + "<td><b>Não Conforme</b></td>" + "<td>" + x.data.info.diag[k]['a'][i]['total']['not_ok'] + "</td>" + "<td>" + cas.roundNumber((x.data.info.diag[k]['a'][i]['total']['not_ok'] / x.data.info.diag.total.not_ok) * 100, 2) + "%</td>" + "</tr>" + "</table>" + "</div>");
            }
            for (var i in x.data.info.diag[k]['b']) {
                $('#tec_mes-diag-' + k + '-b').append("<div  style='width:100%;display:block;position:relative;'>" + "<table style='width:100%'>" + "<tr>" + "<th colspan=3 style='background-color: #E9ECEF;'>" + x.data.info.diag[k]['b'][i]['name'] + "</th>" + "</tr>" + "<tr>" + "<td><b>Revisitas</b></td>" + "<td>" + x.data.info.diag[k]['b'][i]['total'] + "</td>" + "<td>" + cas.roundNumber((x.data.info.diag[k]['b'][i]['total'] / x.data.info.diag.total.not_ok) * 100, 2) + "%</td>" + "</tr>" + "</table>" + "</div>");
            }
        }
        if (!tecdiagok) {
            $('.tyiaqw').niceScroll();
            tecdiagok = true;
        }
        var abc = $('<div id="w"></div>');
        abc.appendTo('body');
        abc.fullScreenDialog({
            title: dashTitle + " (" + x.etc.m + ")",
            content: $('#tec_mes-tabs'),
            /*after:function(){
                $('#tec_mes-tabs').tabs( "refresh" );
            },*/
            onClose: function() {
                $('#tec_mes-tabs').appendTo('#tec_mes');
            },
            buttons: [{
                title: "Imprimir",
                action: function() {
                    $(this).closest('.fullScreenDialog').find('.fSCDContainer').jqprint();
                }
            }]
        });
    };
}(window));