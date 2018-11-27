(function ($, cas) {
    'use strict';
    cas.controller = function () {
        var map = null, schedule = null, cs, cselector = {}, markedS,
            geoOpts = {
                enableHighAccuracy: true,
                timeout: 1000 * 120,
                maximumAge: 1000 * 60 * 2
            };

        function defStr(x) {
            return x || '----';
        }

        function agendaTurno(ag, t) {
            return (ag ? ag + '<br>' + (t ? t.name : '') : 'FORA DA AGENDA');
        }

        function zeroView() {
            $('#up_status').hide();
            $('#container').empty();

            if (map) {
                map.removeMarkers();
            }
        }

        function checkSession() {
            zeroView();
            nextSchedule();
        }

        function scheduleMark() {
            if (!map) {
                return;
            }
            if (schedule.lat) {
                markedS = map.addMarker({
                    lat: schedule.lat,
                    lng: schedule.lng,
                    icon: 'lib/img/home-marker.png',
                    title: schedule.assname
                });
                map.setCenter(schedule.lat, schedule.lng);
                map.setZoom(15);
            }
        }

        function okString(x, y) {
            if (!y)
                y = '---';
            return ((x) ? x : y);
        }

        function nextSchedule() {

            xhrTry({
                method: 'GET',
                sendto: 'tec/current_schedule',
                andthen: nextSchedule_
            });
        }

        function loadAssInfo() {
            var me = $(this), container = me.find('.extraInfo');
            if (container.length) {
                return container.slideToggle();
            }
            container = $('<ul>').addClass('extraInfo').appendTo(me);
            container.click(function(){
                return false;
            });

            xhrTry({
                method: 'GET',
                sendme: {cod: me.data('cod'), per: me.data('per')},
                etc: {container: container},
                sendto: 'tec/ass_info',
                andthen: assInfo
            });
        }

        function assInfo (response) {
            var container = response.etc.container,
                ponto = response.data.ponto,
                info = response.data.info, pontoElem;

            if (info) {
                $('<li>')
                    .append('<p>Assinante <b>' + info.grupo + '</b> desde ' +  info.since + '</p>')
                    .appendTo(container);
            }

            for (var i in ponto) {
                pontoElem = $('<li>').appendTo(container);
                pontoElem.append('<p>' + ponto[i].pacote + ( ponto[i].pacote_tipo ? ' - ' + ponto[i].pacote_tipo : '' )+'</p>');
                if (ponto[i].serial) {
                    var eq = [];
                    if (ponto[i].equipamento) {
                        eq.push(ponto[i].equipamento);
                    }
                    if (ponto[i].tipo) {
                        eq.push(ponto[i].tipo);
                    }
                    eq.push('<b>'+ponto[i].serial+'</b>');
                    pontoElem.append('<p><span>Equipamento</span>' + eq.join(' - ') + '</p>');
                }
            }
        }
        function loadOsInfo() {
            var me = $(this), container = me.find('.extraInfo');
            if (container.length) {
                return container.slideToggle();
            }
            container = $('<ul>').addClass('extraInfo').appendTo(me);
            container.click(function(){
                return false;
            });

            xhrTry({
                method: 'GET',
                sendme: {id: me.data('id')},
                etc: {container: container},
                sendto: 'tec/schedule_info',
                andthen: osInfo
            });
        }

        function osInfo(response) {
            var container = response.etc.container, os = response.data.os, osElem;
            for(var i in os){
                osElem =
                    $('<li>').appendTo(container);
                osElem.append('<p>' + os[i].svc + ' - ' + os[i].os + ', ' + os[i].tipo + '</p>');
                osElem.append('<p><span>Ingresso:</span><b>'+os[i].ingr+'</b></p>');
                if(os[i].ag){
                    osElem.append('<p><span>Agenda:</span>' +
                        os[i].ag + (os[i].turno ? ' - ' + os[i].turno : '') + '</p>');
                }
                osElem.append(
                    $('<p><span>Observação:</span></p>').append('<smaller>' + os[i].obs_origem || '---' + '</smaller>')
                );
            }
        }

        function makeScheduleForm(scheduleBox) {
            var aux;
            if (schedule.s_ini && schedule.s_ini !== '---') {
                scheduleBox.append("<h3 class='scheduleinfobox s_start_at'>Início estimado " +
                    schedule.s_ini.substr(0, 5) + "</h3>");
            }

            var desireToKnowMore =
                $("<ul>").addClass('toggleExtraInfo').append(
                    $('<li>').append('<a>Assinante</a>')
                        .data('cod', schedule.asscod)
                        .data('per', schedule.per)
                        .click(loadAssInfo)
                );


            aux = $("<h3 class='scheduleinfobox scheduledescr'>").appendTo(scheduleBox);

            if (schedule.activity === 'desloc') {
                desireToKnowMore.append(
                    $('<li>').append('<a>Ordens</a>')
                        .data('id', schedule.id)
                        .click(loadOsInfo)
                );
                aux.append('Deslocamento para ');
            }

            aux.append((schedule.assname) ? schedule.assname + " [Cód: " + schedule.asscod + "]" : schedule.descr);

            desireToKnowMore.appendTo(scheduleBox);

            if (schedule.addr) {
                scheduleBox.append("<h4 class='scheduleinfobox scheduleaddr'>" + schedule.addr + "</h4>");
            }
            /*
             if (x.data.lunch_ini) {
             aux.append("<h4 class='scheduleinfobox extrainfo'>Refeição estimada para " + x.data.lunch_ini + "</h4>");
             }
             */
            aux = $("<div class='scheduleinfobox schedule_action'>").appendTo(scheduleBox);
            aux.append("<div><textarea autocomplete='off' placeholder='Observação' id='schedule_obs'>" + schedule.obs + "</textarea></div>");
            aux = $('<div>').appendTo(aux);

            if (schedule.real_ini) {
                aux.append("<button disabled>Iniciado às " + schedule.real_ini + "</button>");
            } else {
                aux.append("<button class='set_time' data-step='0' data-schedule='" + schedule.id + "'>Iniciar</button>");
            }
            if (schedule.real_end) {
                aux.append("<button disabled>Finalizado às " + schedule.real_end + "</button>");
            } else if (schedule.real_ini) {

                if (schedule.okToClose) {
                    aux.append("<button class='set_time' data-step='1' data-schedule='" + schedule.id + "'>Finalizar</button>");
                } else {
                    aux.append("<button disabled>Ainda restam " + Math.floor(schedule.wait / 60) + " minutos</button>");
                }

            }
            aux.append("<button data-id='" + schedule.id + "' class='obs_save'>Salvar</button>");
            scheduleBox.append("<div class='oscontainer'>");
            aux = $("<table class='ass_history'><thead></thead><tbody></tbody></table>");
            if (schedule.history.length > 0) {
                aux.find('thead')
                    .append(
                        "<tr>" +
                        "<th class='histtd1'></th>" +
                        "<th class='histtd2'></th>" +
                        "</tr>" +
                        "<tr>" +
                        "<th colspan=2>Reclamações recentes</th>" +
                        "</tr>");
            }
        }

        function nextSchedule_(x) {
            $('#container').empty();
            mapKill();
            schedule = x.data.schedule;
            if (!schedule) {
                $('#container').html("<div id='emptyschedule'>Escaninho vazio.</div>");
                $('#up_status').show();
                l_days();
                return;
            }
            schedule.history = x.data.history || [];

            if (schedule.activity !== 'tec_status') {
                $('#up_status').show();
            }

            if (!schedule.obs)
                schedule.obs = '';

            var s = $("<div id='s_" + schedule.id + "' class='schedulebox'>").appendTo('#container');
            makeScheduleForm(s);

            if (schedule.activity === 'desloc') {
                var obsLast = $('#schedule_obs').parent();

                var gas =
                    $("<select id='gas_level'>")
                        .append("<option>### Selecionar ###</option>")
                        .append("<option value='0'> [E]mpty (vazio) </option>")
                        .append("<option value='0.25'> 1/4 </option>")
                        .append("<option value='0.5'> 1/2 </option>")
                        .append("<option value='0.75'> 3/4 </option>")
                        .append("<option value='1'> [F]ull (cheio) </option>")
                        .prop('disabled', ( schedule.real_ini ))
                        .appendTo($('<div>').insertAfter(obsLast));
                if (schedule.gas !== null)
                    gas.val(schedule.gas);

                $("<input type='number' min='0' step='5' autocomplete='off' placeholder='Kilometragem' id='current_km' />")
                    .prop('disabled', ( schedule.real_ini ))
                    .val(((schedule.km) ? schedule.km : ''))
                    .appendTo($('<div>').insertAfter(obsLast));

                $("<input type='text' autocomplete='off' placeholder='Placa' id='tec_placa' />")
                    .prop('disabled', ( schedule.real_ini ))
                    .val(((schedule.placa) ? schedule.placa : ''))
                    .appendTo($('<div>').insertAfter(obsLast));

            }

            var ntbody = s.find('.ass_history').children('tbody');
            for (var xt in schedule.history) {
                if (ntbody.length) {
                    ntbody.append(
                            "<tr class='histr1'>" +
                            "<td colspan=2>" + schedule.history[xt].ingr + "</td>" + +"</tr>")
                        .append("<tr>" +
                            "<td class='histtd1'>Status</td>" +
                            "<td class='histtd2'>" + okString(schedule.history[xt].status).toUpperCase() + "</td>" + +"</tr>")
                        .append("<tr>" +
                            "<td class='histtd1'>Falha</td>" +
                            "<td class='histtd2'>" + okString(schedule.history[xt].falha) + "</td>" + +"</tr>")
                        .append("<tr>" +
                            "<td class='histtd1'>Obs. Origem</td>" +
                            "<td class='histtd2'>" + okString(schedule.history[xt].obs_origem) + "</td>" + +"</tr>")
                        .append("<tr>" +
                            "<td class='histtd1'>Obs. Técnico</td>" +
                            "<td class='histtd2'>" + okString(schedule.history[xt].obs_tec) + "</td>" + +"</tr>"
                    );
                    if (schedule.history[xt].tecnico)
                        ntbody.append("<tr>" +
                            "<td class='histtd1'>Técnico</td>" +
                            "<td class='histtd2'>" + schedule.history[xt].tecnico + "</td>" + +"</tr>");
                }
            }
            s.find('.set_time').click(setTimeClick);
            s.find('.obs_save').click(sv_obs);
            s.find('.scheduleaddr').click(function () {
                if (map)
                    mapKill();
                else
                    mapLoad();
            });

            if (schedule.pack) {
                fetchOSS(schedule);
            }

            l_days();
        }

        function checkForm() {
            var ok = true;

            $('.ospaneltb').each(function () {
                if (ok) {
                    var e = $(this).find('.cselector').filter(function () {
                        return !validateCausaVal($(this).val());
                    });
                    ok = e.length < 1;
                    if (!ok) {
                        $(document.documentElement).scrollTop(e.first().offset().top);
                        alert('Selecione uma opção.');
                        return false;
                    }
                }
            });
            return ok;
        }

        function deslocFields(ignore) {
            var vals = {km: 0, placa: null, gas: 0};
            var km = $('#current_km');
            var placa = $('#tec_placa');
            var gas = $('#gas_level');

            if (placa.length) {
                vals.placa = placa.val();
                if (!( vals.placa && vals.placa.length && validaPlaca(vals.placa.replace(/\W/g, '')) )) {
                    if (!ignore)
                        alert('É obrigatório informar a placa do veículo pelo menos uma vez ao dia, o valor preenchido não é uma placa válida.');
                    return false;
                }
            }

            if (km.length) {
                var kmRaw = km.val(), kmConverted = parseInt(kmRaw);
                vals.km =
                    (( cas.isNumber(kmRaw) && kmConverted > 0 )
                        ? kmConverted
                        : 0
                        );
                if (!vals.km) {
                    if (!ignore)
                        alert('É obrigatório informar a Kilometragem do carro no início de cada deslocamento, ' +
                            'e o valor deve ser um número maior que zero.');
                    return false;
                }
            }

            if (gas.length) {
                var gasRaw = gas.val(), gasConverted = parseFloat(gasRaw);
                vals.gas =
                    (( gasRaw.length > 0 && cas.isNumber(gasRaw) )
                        ? gasConverted
                        : null
                        );
                if (vals.gas === null) {
                    if (!ignore)
                        alert('É obrigatório informar o nível do combustível do carro no início de cada deslocamento.');
                    return false;
                }
            }


            return vals;
        }

        function validaPlaca(placa) {
            var er = /[a-z]{3}-?\d{4}/gim;
            er.lastIndex = 0;
            return er.test(placa);
        }

        function setTimeClick() {
            var me = $(this), oldstr = me.html(), isFinal = (parseInt(me.attr('data-step')) === 1);

            if (( isFinal && !checkForm() ) || (!isFinal && !deslocFields()))
                return false;

            if (!confirm("Deseja realmente " +
                ((isFinal) ? 'finalizar' : 'iniciar') + " esta atividade?"))
                return false;
            me.html('Buscando posição...').prop('disabled', true);

            navigator.geolocation.getCurrentPosition(
                function (x) {
                    me.html(oldstr).prop('disabled', false);
                    setTime({
                        isFinal: ((isFinal) ? 1 : 0),
                        elem: me,
                        lat: x.coords.latitude,
                        lng: x.coords.longitude
                    });
                }, function () {

                    me.html(oldstr).prop('disabled', false);
                    alert('Não foi possível determinar sua localização, esta informação é necessária para atualizar a ordem. Por favor habilite a localização do seu aparelho.');
                    return;

                    /*
                        me.html(oldstr).prop('disabled', false);
                        setTime({
                            isFinal: ((isFinal) ? 1 : 0),
                            elem: me,
                            lat: null,
                            lng: null
                        });
                    */
                }, geoOpts
            );
        }

        function validateCausaVal(v) {
            return (v !== 'nope' && v && v.length);
        }

        function sv_obs(callback) {
            var oz = [];
            $('.ospaneltb').each(function () {
                var z = {
                    id: $(this).attr('data-id'),
                    obs: $(this).find('.os_obs').val(),
                    equipamento_in: $(this).data('eqIn') ? $(this).find('.os_eqpt_in').val() : null,
                    equipamento_out: $(this).data('eqOut') ? $(this).find('.os_eqpt_out').val() : null,
                    tx: $(this).find('.os_tx').val(),
                    rx: $(this).find('.os_rx').val(),
                    ch_baixo: $(this).find('.os_ch_baixo').val(),
                    ch_alto: $(this).find('.os_ch_alto').val()
                };
                $(this).find('.cselector').each(function () {
                    z[$(this).attr('data-item')] = (
                        validateCausaVal($(this).val())
                            ? $(this).val()
                            : null
                        );
                });
                oz.push(z);
            });
            var vals = {
                id: schedule.id,
                obs: $('#schedule_obs').val(),
                oss: oz
            };
            var dF = deslocFields(true);
            if (dF)
                vals = $.extend(vals, dF);

            xhrTry({
                sendto: 'tec/save_oss',
                sendme: vals,
                etc: {
                    themcallback: callback
                }, andthen: sv_obs_
            });
        }

        function sv_obs_(x) {
            if (typeof x.etc.themcallback === 'function') {
                x.etc.themcallback();
            }
        }

        function setTime(x) {
            sv_obs(
                function () {
                    xhrTry({
                        sendme: {
                            is_final: x.isFinal,
                            id: schedule.id,
                            lat: x.lat,
                            lng: x.lng
                        },
                        sendto: 'tec/set_time',
                        andthen: function (z) {
                            checkSession();
                        }
                    });
                }
            );

        }

        function fetchOSS(m) {
            for (var i in m.pack) {
                xhrTry({
                    method: 'GET',
                    etc: {
                        i: i,
                        m: m
                    },
                    sendme: {
                        os: m.pack[i].os,
                        per: m.pack[i].per,
                        svc: m.pack[i].svc
                    },
                    sendto: 'tec/osser',
                    andthen: fetchOSS_

                });
            }
        }

        function fetchOSS_(x) {
            var m = x.etc.m,
                $os = x.data.os,
                p = $('#s_' + m.id + ">.oscontainer"),
                eqIn = true, eqOut = true;

            if ($os) {
                $os.os_tipo = $os.os_tipo.toLowerCase();

                if ($os.os_tipo === 'desconexão') {
                    eqIn = false;
                }

                if ($os.os_tipo === 'instalação') {
                    eqOut = false;
                }

                if (!m.pack[x.etc.i].obs)
                    m.pack[x.etc.i].obs = '';
                var eqpt_descr = ((m.pack[x.etc.i].svc === 'tv') ? 'Decoder' : 'Modem');
                var tb =
                    $("<table class='ospaneltb' data-id='" + m.pack[x.etc.i].id + "'>" +
                            "<tbody>" +
                            "<tr>" +
                            "<td class='ostd osp_tipo' colspan='4'>" + $os.os_tipo.toUpperCase() + "</td>" +
                            "</tr>" +
                            (($os.turno)
                                ? "<tr>" +
                                "<td class='ostd osturno' colspan='4'>Turno " + $os.turno.name + "</td>" +
                                "</tr>"
                                : '') +
                            "<tr>" +
                            "<td class='ostd ossvc osp_svc'>" + $os.svc.toUpperCase() + "</td>" +
                            "<td class='ostd osnro osp_os' colspan='2'>" + $os.os + "</td>" +
                            "<td class='ostd osnode osp_node'>" + $os.node + "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td class='ostd ospac' colspan=2><p><b>Nº do Assinante: </b><i>" + $os.asscod + "</i></p></td>" +
                            "<td class='ostd ospac' colspan=2>" +
                            "<p>" +
                            "<b>Contrato: </b>" +
                            "<i>" + okString($os.contrato) + "</i>" +
                            "</p>" +
                            "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td class='ostd' colspan='2'>" + (($os.pacote) ? $os.pacote : 'Nenhum Pacote Associado') + "</td>" +
                            "<td class='ostd ospac' colspan='2'>" +
                            (($os.svc === 'tv')
                                ? "<p><b>Serial: </b><i>" + okString($os.serial) + "</i></p>" +
                                "<p><b>Decoder: </b><i>" + okString($os.decoder) + "</i></p>"
                                : "<p><b>Modem: </b><i>" + okString($os.modem) + "</i></p>"

                                )
                            + "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td class='ostd osp_end' colspan='2'><strong>Falha:</strong></br>" + okString($os.falha, 'FALHA NÃO INFORMADA') + "</td>" +
                            "<td class='ostd osp_end' colspan='2'><strong>Observação:</strong></br>" + okString($os.obs_origem, 'SEM OBSERVAÇÃO') + "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td class='ostd osp_end' colspan='4'>" + $os.end + "<br>" + $os.bairro + " - " + $os.cid + "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td class='ostd osp_end2' colspan='4'>" + $os.uf + ", " + $os.cep + " - Brazil" + "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td class='ostd osp_ingr' colspan='2'>" + $os.ingr + "</td>" +
                            "<td class='ostd osp_ag' colspan='2'>" + agendaTurno($os.ag, $os.turno) + "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td colspan=4 class='ostd osobs'>" +
                            "<textarea autocomplete='off' placeholder='Observação' class='os_obs' data-id='" + m.pack[x.etc.i].id + "'>" + m.pack[x.etc.i].obs + "</textarea>" +
                            "</td>" +
                            "</tr>" +
                            "<tr class='eqIn'>" +
                            "<td colspan=1 class='ostd osl'>" + eqpt_descr + " <br>[ ENTRA ]: </td>" +
                            "<td colspan=3 class='ostd osr'>" +
                            "<input autocomplete='off' type='text' placeholder='Código do equipamento' " +
                            "class='os_eqpt_in' data-id='"
                            + m.pack[x.etc.i].id + "' value='" +
                            ((m.pack[x.etc.i].equipamento_in) ? m.pack[x.etc.i].equipamento_in : '') +
                            "'/>" +
                            "</td>" +
                            "</tr>" +
                            "<tr class='eqOut'>" +
                            "<td colspan=1 class='ostd osl'>" + eqpt_descr + " <br>[ SAI ]: </td>" +
                            "<td colspan=3 class='ostd osr'>" +
                            "<input autocomplete='off' type='text' placeholder='Código do equipamento' " +
                            "class='os_eqpt_out' data-id='"
                            + m.pack[x.etc.i].id + "' value='" +
                            ((m.pack[x.etc.i].equipamento_out) ? m.pack[x.etc.i].equipamento_out : '') +
                            "'/>" +
                            "</td>" +
                            "</tr>" +
                            "<tr>" +
                            "<td colspan=1 class='ostd osl'>TX:</td>" +
                            "<td colspan=1 class='ostd osr'>" +
                            "<input autocomplete='off' type='number' placeholder='TX' class='os_tx' data-id='" + m.pack[x.etc.i].id + "' value='" + ((m.pack[x.etc.i].tx) ? m.pack[x.etc.i].tx : 0) + "'/>" +
                            "</td>" +
                            "<td colspan=1 class='ostd osl'>RX:</td>" +
                            "<td colspan=1 class='ostd osr'>" +
                            "<input autocomplete='off' type='number' placeholder='RX' class='os_rx' data-id='" + m.pack[x.etc.i].id + "' value='" + ((m.pack[x.etc.i].rx) ? m.pack[x.etc.i].rx : 0) + "'/>" +
                            "</td>" +
                            "</tr>" +
                            "<tr><td colspan=4>CH</td></tr>" +
                            "<tr>" +
                            "<td colspan=1 class='ostd osl'>Baixo:</td>" +
                            "<td colspan=1 class='ostd osr'>" +
                            "<input autocomplete='off' type='number' placeholder='Baixo' class='os_ch_baixo' data-id='" + m.pack[x.etc.i].id + "' value='" + ((m.pack[x.etc.i].ch_baixo) ? m.pack[x.etc.i].ch_baixo : 0) + "'/>" +
                            "</td>" +
                            "<td colspan=1 class='ostd osl'>Alto:</td>" +
                            "<td colspan=1 class='ostd osr'>" +
                            "<input autocomplete='off' type='number' placeholder='Alto' class='os_ch_alto' data-id='" + m.pack[x.etc.i].id + "' value='" + ((m.pack[x.etc.i].ch_alto) ? m.pack[x.etc.i].ch_alto : 0) + "'/>" +
                            "</td>" +
                            "</tr>" +
                            "</tbody>" +
                            "</table>"
                    ).appendTo(p);

                if (!eqOut) {
                    tb.find('.eqOut').remove();
                }
                if (!eqIn) {
                    tb.find('.eqIn').remove();
                }
                tb.data('eqIn', eqIn).data('eqOut', eqOut);

                tb.find('thead').click(function () {
                    $(this).next().slideToggle();
                });

                var auz = tb.find("tbody"),
                    zvc = $os.svc.toLowerCase(),

                    tr = $("<tr>").appendTo(auz),
                    opt = 'causa';

                if ($os.os_tipo !== 'reclamação') {
                    opt = 'motivo';
                }

                tr.prepend("<td colspan=1 class='ostd osl'>" +
                    ( ($os.os_tipo === 'reclamação') ? 'Causa' : 'Motivo' ) + ": </td>");
                var cc = cselector[opt][zvc].clone();

                if (m.pack[x.etc.i][opt]) {
                    cc.val(m.pack[x.etc.i][opt]);
                }

                $("<td colspan=3 class='ostd osr'>").append(cc).appendTo(tr);
            }
        }

        function resizer() {
            $('#map').height($(window).height() - 60);
        }

        function mapKill() {
            $('#map').remove();
            map = null;
        }

        function mapLoad() {
            $("<div id='map'>").prependTo('#container');
            resizer();
            map = new GMaps({
                div: '#map',
                lat: 10,
                lng: 10,
                zoom: 2,
                streetViewControl: false,
                panControl: false,
                rotateControl: false,
                zoomControl: true,
                scaleControl: false,
                mapTypeControl: false
            });
            $(window).scrollTop($('#container').offset().top - 40);
            scheduleMark();
            navigator.geolocation.getCurrentPosition(markCurrPos, doNothing, geoOpts);

        }

        function markCurrPos(pos) {
            if (!map)
                return;
            map.cleanRoute();
            map.addMarker({
                lat: pos.coords.latitude,
                lng: pos.coords.longitude,
                title: 'Posição Atual'
            });
            var r = {
                origin: [pos.coords.latitude, pos.coords.longitude],
                destination: [schedule.lat, schedule.lng],
                travelMode: 'driving',
                strokeColor: '#3A3D75',
                strokeOpacity: 0.7,
                strokeWeight: 6
            };
            map.drawRoute(r);
            map.fitZoom();
        }

        function doNothing(err) {
            alert('É necessário habilitar a localização do seu aparelho.');
        }

        function beforeSubmit() {
            alert('Não foi possível determinar sua localização, esta informação é necessária para atualizar a ordem. Por favor habilite a localização do seu aparelho.');
            //submitStatus(null);
        }

        function submitStatus(x) {
            var y = {
                lat: null,
                lng: null,
                descr: $('#status_descr').val()
            };
            if (x && x.coords) {
                y.lat = x.coords.latitude;
                y.lng = x.coords.longitude;
            }
            xhrTry({
                sendto: 'tec/tec_status',
                sendme: y,
                andthen: function (x) {
                    statusDefault();
                    checkSession();
                }
            });
        }

        function boot() {
            xhrTry({
                sendto: 'tec/opts',
                andthen: function (x) {
                    cs = x.data.cs;
                    for (var z in cs) {
                        cselector[z] = {};
                        for (var svc in cs[z]) {
                            cselector[z][svc] = $("<select class='cselector " + z + "' data-item='" + z + "'><option value='nope'>### SELECIONAR ###</option></select>");
                            for (var i in cs[z][svc]) {
                                if (cs[z][svc][i].name && cs[z][svc][i].name.length) {
                                    cselector[z][svc].append("<option value='" + cs[z][svc][i].name + "'>" + cs[z][svc][i].name + "</option>");
                                }

                            }
                        }
                    }
                    checkSession();
                }
            });
        }

        var rFilter =
            /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

        var file = null, reader;

        function selectImg() {
            var root = $(this).closest('.tec-visita');
            var id = parseInt(root.attr('data-id'));
            var me = $(this)[0];
            if (
                typeof me.files !== 'undefined'
                && me.files.length >= 1
                ) {
                file = me.files[0];
                if (!rFilter.test(file.type)) {
                    alert('Selecione um formato válido de imagem.');
                    return false;
                }

                var w = $(this).parent();
                var prg = $("<div class='img-upload-progress'>")
                    .append("<div class='thick'>" + file.name + "</div>").prependTo(w);

                var xhr = new XMLHttpRequest();

                xhr.upload.addEventListener("progress", function (e) {
                    var pc = (e.loaded / e.total) * 100;
                    prg.children('.thick').width(pc + '%');
                }, false);

                xhr.onreadystatechange = function (e) {
//                if (xhr.readyState === 4) {
//                    progress.className = (xhr.status === 200 ? "success" : "failure");
//                }
                    if (xhr.readyState === 4) {
                        loadImgs(root);
                        prg.remove();
                    }
                };

                xhr.open("POST", 'tec/upload_img', true);
                xhr.setRequestHeader("tec-schedule", id);
                xhr.setRequestHeader("img-filename", file.name);
                xhr.send(file);

//            reader = new FileReader();
//            reader.readAsDataURL(file);
//            reader.onload = function(e){
//                if(typeof e.target.result !== 'undefined'){
//                    var f = e.target.result;
//                    
//                }
//            };
            }
            return false;
        }

        function visitas_dia(dia) {
            $('#os_control>ul').empty();
            xhrTry({
                method: 'GET',
                sendme: { dia: dia },
                sendto: 'tec/visitas',
                andthen: visitas
            });
        }

        function visitas(x) {
            var ul = $('#os_control>ul').empty(), li, header;
            var visitas = x.data.visitas;
            for (var i in visitas) {

                li = $("<li class='tec-visita'>")
                    .attr('data-id', visitas[i].id).appendTo(ul);
                header = $("<h4>" + visitas[i].nome + "</h4>").appendTo(li);
                li.append("<sub><b>" + visitas[i].bairro + '</b> ~ ' + visitas[i].endereco + "</sub>");

                li.append("<ul class='img-list' style='display:none'></ul>");

                header = $("<div class='upform' style='display:none'>").appendTo(li);
                $("<input class='up_img' type='file' />").change(selectImg).appendTo(header);

                li.children('h4,sub').click(function () {
                    var root = $(this).parent(), x = root.find('.img-list,.upform').toggle();
                    root.find('.img-preview').remove();
                    x = x.filter('.img-list');
                    if (x.is(':visible')) {
                        loadImgs(root);
                    }
                });
            }
        }

        function loadImgs(visita) {
            var id = visita.attr('data-id');
            xhrTry({
                method: 'GET',
                sendto: 'tec/visita_pics',
                sendme: {id: id},
                etc: { root: visita },
                andthen: function (x) {
                    var ul = x.etc.root.find('.img-list').empty();
                    var pics = x.data.pics;
                    for (var i in pics) {
                        var file = "/media/tec_reg/72/" + pics[i].file;
                        $("<li class='single-pic'>&zwnj;</li>")
                            .attr('data-picid', pics[i].id)
                            .attr('data-file', pics[i].file)
                            .css('background', "url(" + file + ") center no-repeat white")
                            .click(togglePreview)
                            .appendTo(ul);
                    }
                }
            });
        }

        function togglePreview() {
            var me = $(this);
            var id = me.attr('data-picid');
            var file = me.attr('data-file');
            var picList = me.parent();
            var preview = picList.next('.img-preview');

            me.parent().children().removeClass('selected');

            if (!preview.length) {
                preview =
                    $("<div class='img-preview'>" +
                        "<a class='img-box' target='_blank'>" +
                        "<img src='" + file + "'/>" +
                        "</a>" +
                        "<div class='preview-menu'>" +
                        "<a class='preview-delete'>Excluir</a>" +
                        "</div>" +
                        "</div>").attr('data-picid', id).insertAfter(picList);
                preview.find('.preview-delete').click(killPic);
            } else if (preview.attr('data-picid') === id) {
                preview.remove();
                return null;
            }
            me.addClass('selected');
            preview.attr('data-picid', id);
            preview.find('.img-box').attr('href', "/media/tec_reg/orig/" + file);
            preview.find('img').attr('src', "/media/tec_reg/480/" + file);
        }

        function killPic() {
            if (!confirm('Deseja realmente remover esta imagem?'))
                return null;
            var me = $(this);
            var p = me.closest('.img-preview');
            xhrTry({
                method: 'GET',
                sendme: {id: p.attr('data-picid')},
                sendto: 'tec/kill_pic',
                andthen: function (x) {
                    loadImgs(me.closest('.tec-visita'));
                    p.remove();

                }
            });
        }

        function l_days() {
            var bt = $('#os_control>div>a');
            bt.click(function () {
                visitas_dia($('#dia_select').val());
            });
            xhrTry({
                method: 'GET',
                sendto: 'tec/dias',
                andthen: function (x) {
                    var s = $('#dia_select').empty();
                    var ds = x.data.ds;
                    for (var i in ds)
                        s.append("<option value='" + ds[i].d + "'>" + ds[i].dd + "</option>");
                }
            });
        }

        function statusDefault() {
            $('#up_status').show();
            $('#status_descr').val("");
            $('#new_status').hide();
        }

        function doPgLock() {
            $('button').not(':disabled').addClass('preDisabled').prop('disabled', true);
            cas.hidethis('body', 'Conectando...');
        }

        function pgUnlock() {
            cas.showthis('body');
            $('.preDisabled').removeClass('preDisabled').prop('disabled', false);
        }

        function xhrTry(r) {
            if (r) {
                doPgLock();
                r.second = r.andthen;
                if (!r.c)
                    r.c = 0;
                r.andthen = function (x) {
                    pgUnlock();
                    if (typeof r.second === 'function')
                        r.second(x);
                };
                var err = function () {
                    r.c++;
                    if (r.c < 5) {
                        setTimeout(
                            function () {
                                cas.ajaxer(r);
                            },
                                2 * 1000
                        );
                    } else {
                        alert('Não foi possível se conectar com o servidor neste momento,' +
                            ' você pode tentar novamente.');
                        pgUnlock();
                    }
                };
                r.error = err;
                cas.ajaxer(r);
            }
        }

        function itemToggle() {
            $('#warehouse').toggle();
            if ($('#warehouse').is(':visible')) {
                loadItems();
            }
        }

        function loadItems() {
            xhrTry({
                sendto: 'tec/tec_item',
                andthen: tecItem
            });
        }

        function tecItem(x) {
            $('#item_list').empty();
            var item = x.data.item, t = null, lt = null, i;
            $('#item_status').empty();
            for (i in x.data.sts)
                $('#item_status').append("<option value='" + x.data.sts[i].id + "'>" + x.data.sts[i].name + "</option>");
            if (!item || !item.length)
                $('#item_list').html('<h4>Nenhum item alocado</h4>');
            for (i in item) {
                if (t === null || lt !== item[i].almox_type) {
                    if (t !== null)
                        t.find('.almox_type_count').html(t.find('tbody>tr').length);
                    t =
                        $("<table class='tec_item_type' type-id='" + item[i].almox_type + "'>" +
                            "<thead>" +
                            "<tr>" +
                            "<th class='almox_type_count'>" + item[i].almox_type + "</th>" +
                            "<th class='almox_type_name' colspan=2>" + defStr(item[i].almox_type_name) + "</th>" +
                            "</tr>" +
                            "</thead>" +
                            "<tbody style='display:none'></tbody>" +
                            "</table>")
                            .appendTo('#item_list');
                    t.find('thead').click(function () {
                        $(this).parent().find('tbody').toggle();
                    });
                    t.find('.almox_type_count').click(function () {
                        var tbody = $(this).closest('.tec_item_type').find('tbody');
                        if (tbody.is(':visible')) {
                            tbody.find('.tec_item_id').trigger('click');
                            return false;
                        }
                    });
                    lt = item[i].almox_type;
                }
                $('<tr>' +
                    "<td class='tec_item_id' lease-id='" + item[i].lease_id +
                    "' item-id='" + item[i].almox_item + "'>" + item[i].almox_item + '</td>' +
                    '<td class=tec_item_name>' + defStr(item[i].almox_item_name) + '</td>' +
                    '<td class=tec_item_status_name>' + item[i].almox_item_status_name + '</td>' +
                    '</tr>').appendTo(t.find('tbody')).find('.tec_item_id').click(function () {
                    $(this).toggleClass('item_selected');
                });

            }
            if (t)
                t.find('.almox_type_count').html(t.find('tbody>tr').length);
        }

        function AIDS() {
            var ids = [];
            $('.tec_item_id.item_selected').each(function () {
                ids.push(
                    {
                        id: parseInt($(this).attr('lease-id')),
                        aid: parseInt($(this).attr('item-id'))
                    });
            });
            return ids;
        }

        function returnItem() {
            if (confirm('Deseja realmente retornar os itens para o Almoxarifado?'))
                xhrTry({
                    sendme: {
                        id: AIDS(), schedule: ((schedule) ? schedule.id : null)
                    }, sendto: 'tec/tec_item_return',
                    andthen: loadItems
                });
        }

        function saveItemStatus() {
            var st = $('#item_status').val();
            if (st && confirm('Confirma a mudança de status?'))
                xhrTry({
                    sendme: {
                        id: AIDS(),
                        status: st,
                        obs: prompt("Escreva alguma observação caso desejar..."),
                        schedule: ((schedule) ? schedule.id : null)
                    }, sendto: 'tec/tec_item_status',
                    andthen: loadItems
                });
        }

        $('#load_items').click(itemToggle);
        $('#force_auto').click(checkSession);
        $('#up_status').click(function () {
            $(this).hide();
            $('#new_status').show();
        });
        $('#save_item_status').click(saveItemStatus);
        $('#unlink_item').click(returnItem);
        $('#cupdt').click(statusDefault);
        $('#svupdt').click(function () {
            navigator.geolocation.getCurrentPosition(submitStatus, beforeSubmit, geoOpts);
        });
        cas.resizer.push(resizer);
        resizer();
        boot();

    };
}(jQuery, window.cas));