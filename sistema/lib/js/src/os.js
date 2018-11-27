cas.controller = function() {
    var args = {}, os = {};

    function defStr(x) {
        return ((x && ('' + x).length > 0) ? x : '---');
    }

    function makePicSlide(tb, os) {

        if (!os.pics || !os.pics.length)
            return false;

        var td = $("<td class='picstd' colspan=4>")
            .appendTo($("<tr class='slidetr'>").appendTo(tb));
        var ul = $("<ul class='picsul'>").appendTo(td);
        for (var i in os.pics) {
            var pic = os.pics[i];

            $("<li>")
                .css('background',
                    "url(/media/tec_reg/72/" +
                    pic.file + ") center no-repeat white")
                .attr('data-x', Base64.encode(JSON.stringify(pic)))
                .click(openImgPreview)
                .appendTo(ul);
        }
    }

    function openImgPreview() {
        var elem = $("<div class='img-preview'>");
        var pos = $(this).offset();
        pos.top -= 200;
        pos.left -= 700;
        cas.weirdDialogSpawn(null, elem);
        var pic = JSON.parse(Base64.decode($(this).attr('data-x')));

        elem.append("<div class='preview-header'>Enviada por <i>" +
            ((pic.user_name) ? pic.user_name : pic.user_email) + "</i><sup>em " + pic.timestamp + "</sup></div>");
        $("<div class='img-box'><img src='/media/tec_reg/480/" + pic.file + "' /></div>")
            .appendTo(elem);

        var d = $("<div class='preview-menu'>").appendTo(elem);
        $("<a target='_blank'>Tamanho Real</a>").attr('href', "media/tec_reg/orig/" + pic.file).appendTo(d);

    }

    function fetchOS() {
        $('#os_container,#not_found').slideUp();
        cas.ajaxer({
            method: 'GET',
            sendto: 'os/get_os',
            sendme: args,
            andthen: function(x) {
                var htm, elem;
                if (x.data.os) {
                    x.data.os.svc = x.data.os.svc.toUpperCase();
                    for (var k in x.data.os) {
                        if (k === 'assname' || k === 'asscod') {
                            elem = $('#attr_' + k);
                            htm = "<a target='_blank' href='dashboard#" +
                                cas.hashbangify({
                                    dashboard: {
                                        dashboard: 'ri',
                                        view: 'assinante',
                                        ind: 'imb',
                                        item: x.data.os.per + ':' + x.data.os.asscod
                                    }
                                }) + "'>" + x.data.os[k] + "</a>";
                        } else if (k === 'os') {
                            elem = $('#attr_os');
                            htm = "<a target='_blank' title='Clique para abrir OS no Siga' href='" + cas.osSigaLink(x.data.os) + "'>" + x.data.os.os + "</a>";
                        } else if (k === 'ag' && x.data.os.ag && x.data.os.turno) {
                            elem = $('#attr_ag');
                            htm = x.data.os.ag + ' - ' + x.data.os.turno.name;
                        } else {
                            elem = $('#attr_' + k);
                            htm = defStr(x.data.os[k]);
                        }
                        elem.html(htm);
                    }
                    $('#attr_pac').html(
                        ((x.data.os.pacote) ? "<h3>" + x.data.os.pacote + "</h3>" : '') +
                        ((x.data.os.svc === 'TV') 
                            ? ((x.data.os.decoder) 
                                    ? "<p><b>Serial: </b><i>" + x.data.os.decoder + "</i></p>" +
                                        "<p><b>Decoder: </b><i>" + x.data.os.equipamento + "</i></p>" 
                                    : ''
                                ) 
                            : ((x.data.os.modem) 
                                    ? "<p><b>Modem: </b><i>" + x.data.os.modem + "</i></p>" 
                                    : ''
                                )
                        )
                    );

                    $('#attrs>h3, #attrs>.tt, #attrs>.tec_schedule-tb').remove();
                    if (x.data.tts.length)
                        $('#attrs').append("<h3 class='evtt'>Eventos Relacionados</h3>");
                    for (var i in x.data.tts) {
                        $('#attrs').append(
                            "<table class='tt' style='margin-top:5px;'><tbody>" +
                            "<tr>" +
                            "<td title='Código do Evento' class='ttid'><a href='eventos#" +
                            cas.hashbangify({
                                tt: x.data.tts[i].id
                            }) + "'>" + x.data.tts[i].id + "</a></td>" +
                            "<td title='Tipo de Evento' class='tttype'>" + x.data.tts[i].type + "</td>" +
                            "<td title='Descrição do Evento' class='ttdescr'>" + x.data.tts[i].descr + "</td>" +
                            "<td title='Horário de Início do Evento' class='ttini'>" + x.data.tts[i].ini_br + "</td>" +
                            "<td title='Status do Evento' class='ttstatus'>" + x.data.tts[i].status + "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td class='tdnoborder'></td>" +
                            "<td class='ttloc' title='Localidade do Evento' colspan=3>" +
                            x.data.tts[i].loc +
                            "</td>" +
                            "<td class='tdnoborder'></td>" +
                            "</tr>" +
                            ((typeof x.data.tts[i].obs === 'undefined') ?
                                '' :
                                "<tr>" +
                                "<td class='tdnoborder'></td>" +
                                "<td title='Horário de retorno da Rede Interna' class='tttimestamp'>" + x.data.tts[i].timestamp + "</td>" +
                                "<td title='Observação da Rede Interna' class='ttobs'>" + x.data.tts[i].obs + "</td>" +
                                "<td title='Usuário que retornou a Ordem' class='ttuser'>" + x.data.tts[i].user + "</td>" +
                                "<td class='tdnoborder'></td>" +
                                "</tr>"
                            ) +
                            "</tbody></table>"
                        );
                    }
                    var list = x.data.schedules;
                    if (list.length)
                        $('#attrs').append("<h3 class='evtt'>Histórico do Field Service</h3>");
                    for (var i in list) {
                        tb = $("<table class='tec_schedule-tb'>").appendTo('#attrs');

                        tb.append(
                            "<tr>" +
                            "<td title='Dia da visita' class='ts1td' rowspan=3>" +
                                "<a target='_blank' href='agenda#"+cas.hashbangify(
                                    {
                                        agenda_d: list[i].agenda_d,
                                        agenda_perlist: [parseInt(list[i].per)]
                                    })+"'>" + 
                                        list[i].day + 
                                "</a></td>" +
                            "<td class='ts2td invert'>Estimado</td>" +
                            "<td class='ts2td invert'>Real</td>" +
                            "<td title='Técnico' class='ts3td' rowspan=3>" + list[i].tec + "</td>" +
                            "</tr>");

                        tb.append(
                            "<tr>" +
                            "<td class='ts2td'>" + list[i].scheduled_ini + "</td>" +
                            "<td class='ts2td'>" + ((list[i].real_ini) ? list[i].real_ini : 'Não iniciado') + "</td>" +
                            "</tr>");
                        tb.append(
                            "<tr>" +
                            "<td class='ts2td'>" + list[i].scheduled_end + "</td>" +
                            "<td class='ts2td'>" + ((list[i].real_end) ? list[i].real_end : 'Não finalizado') + "</td>" +
                            "</tr>");
                        makePicSlide(tb, list[i]);

                        var resol = list[i].causa || list[i].motivo;
                        if (resol && resol.length)
                            tb.append(
                                "<tr>" +
                                "<td title='Causa/Motivo selecionado pelo técnico' colspan=4>" + resol + "</td>" +
                                "</tr>");

                        if (list[i].obs_o && list[i].obs_o.length)
                            tb.append(
                                "<tr>" +
                                "<td title='Observação do técnico sobre a Ordem' colspan=4>" + list[i].obs_o + "</td>" +
                                "</tr>");

                        if (list[i].obs_t && list[i].obs_t.length)
                            tb.append(
                                "<tr>" +
                                "<td title='Observação do técnico sobre a visita' colspan=4>" + list[i].obs_t + "</td>" +
                                "</tr>");

                        if (list[i].baixa)
                            tb.append(
                                "<tr>" +
                                "<td colspan=4>Baixado por <b>" + list[i].user + '</b> em <i>' + list[i].baixa + "</i></td>" +
                                "</tr>");

                        if (list[i].equipamento_in)
                            tb.append(
                                "<tr>" +
                                "<td colspan=4>Equipamento [ ENTRA ]:<br> <i>" + list[i].equipamento_in + "</td>" +
                                "</tr>");
                        if (list[i].equipamento_out)
                            tb.append(
                                "<tr>" +
                                "<td colspan=4>Equipamento [ SAI ]:<br> <i>" + list[i].equipamento_out + "</td>" +
                                "</tr>");

                    }

                    $('#os_container').slideDown();
                } else {
                    $('#not_found').slideDown();
                }
            }
        });
    }
    $(window).bind('hashchange', function(e) {
        var aa = $.deparam.fragment($(this).attr('href'));
        if (aa.per && aa.svc && aa.os) {
            $('#os_nro').val(aa.os);
            args.os = aa.os;
            $('#per_selector').val(aa.per);
            args.per = aa.per;
            $('#svc_selector').val(aa.svc);
            args.svc = aa.svc;
            fetchOS();
        } else {
            if (window.location.hash)
                $.bbq.pushState({}, 2);
        }
        return false;
    });
    $('#the_apply_selector').click(function() {
        $.bbq.pushState({
            os: $("#os_nro").val(),
            per: $("#per_selector").val(),
            svc: $("#svc_selector").val()
        });
    });
    $('input,select').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#the_apply_selector').trigger('click');
            return false;
        }
    });
    args = $.deparam.fragment($(this).attr('href'));
    $(window).trigger('hashchange');

    function updtTooltips() {
        $('.osattr').each(function() {
            var me = $(this),
                x = me.find('.inner-wrapper');
            if (x.length > 0 && typeof me.attr('title') !== 'undefined')
                $("<div class='inner-tooltip'>").append(me.attr('title')).appendTo(x);
        });
    }
};