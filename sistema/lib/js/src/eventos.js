(function (window, cas, $, LazyLoad) {
    'use strict';
    cas.controller = function () {
        var locT, ediTT, ttElem, filterMaker, filterLoader,
            isFilterFormVisible = false,
            filter = {}, filterT = null,
            savedFilter = null, dateTypeInput,
            itWasMe = false, itWasMeT,
            loaded = false;

        $('#filterdate0,#filterdate1').val(window.svrtime.toYMD());

        function rsz() {
            $('#container').css('min-height',
                $(window).height() - $('#head-wrapper').outerHeight()
                    - $('#foot').outerHeight());
        }

        function showFilterForm() {
            isFilterFormVisible = true;
            $('#nofilter').removeClass('closed').html('Desativar Filtro');
            $('#yesfilter').slideDown();
        }

        function hideFilterForm() {
            isFilterFormVisible = false;
            $('#nofilter').addClass('closed').html('>>');
            $('#yesfilter').slideUp();
            $('.onefilter').removeClass('activefilter');
            $('#genericsearch,#locsearch,#yesfilter select').val('');
            $('#locfilter').empty();
        }

        function activateFilter(f) {
            $(".onefilter[data-filter='" + f + "']").addClass('activefilter');
        }

        function okPushArgs() {
            clearTimeout(itWasMeT);

            itWasMe = true;
            cas.pushArgs();

            itWasMeT = setTimeout(function () {
                itWasMe = false;
            }, 500);
        }

        filterLoader = {
            g: function () {
                if (filter.g) {
                    $("#genericsearch").val(filter.g);
                    activateFilter('g');
                }
            },
            s: function () {
                var s = $("#stageselect");
                if (filter.s) {
                    s.val(filter.s);
                    activateFilter('s');
                }
            },
            t: function () {
                var t = $("#typeselect");
                if (filter.t) {
                    t.val(filter.t);
                    activateFilter('t');
                }
            },
            d: function () {
                if (filter.d) {
                    $("#filterdate0").val(filter.d.from);
                    $("#filterdate1").val(filter.d.to);
                    activateFilter('d');
                }
            },
            l: function () {
                var i;
                if (filter.l) {
                    for (i in filter.l) {
                        if (filter.l.hasOwnProperty(i)) {
                            locLi(filter.l[i]);
                        }
                    }
                    activateFilter('l');
                }
            }
        };

        filterMaker = {
            g: function () {
                return $("#genericsearch").val();
            },
            s: function () {
                return $("#stageselect").val();
            },
            t: function () {
                return $("#typeselect").val();
            },
            d: function () {
                return {
                    from: $('#filterdate0').val(),
                    to: $('#filterdate1').val()
                };
            },
            l: function () {
                var x = [];
                $('#locfilter>li').each(function () {
                    x.push($(this).text());
                });
                return x;
            }
        };

        function parseFilterToDOM() {
            var i;
            for (i in filter) {
                if (filter.hasOwnProperty(i)) {
                    filterLoader[i]();
                }
            }
        }

        function parseFilterFromDOM() {
            filter = {};
            $('.onefilter').each(function () {
                if (!$(this).is('.activefilter')) {
                    return true;
                }

                var f = $(this).attr('data-filter'), tmp = filterMaker[f]();

                if (tmp) {
                    filter[f] = tmp;
                } else {
                    delete filter[f];
                }
            });
        }
        function loadTTs() {
            $('.weird-dialog').fadeOut(function () {
                $(this).remove();
            });
            cas.ajaxer({
                method: 'GET',
                sendme: {
                    filter: filter,
                    tt: cas.args.tt
                },
                sendto: 'eventos/l',
                error: function () {
                    cas.makeNotif('error', 'Ops... ocorreu um erro ao carregar os eventos.', function () {
                        toggleFilterForm();
                    });
                },
                andthen: loadTTs_
            });
        }
        function downloadXls () {
            cas.ajaxer({
                method: 'GET',
                sendme: {
                    filter: filter,
                    tt: cas.args.tt
                },
                sendto: 'eventos/l_xls',
                error: function () {
                    cas.makeNotif('error', 'Ops... ocorreu um erro ao carregar os eventos.');
                },
                complete: function(){
                    $('#downloadxls').prop('disabled', false);
                },
                andthen: function (x) {
                    window.location.href = x.data.path;
                }
            });
        }
        function applyFilter(arg) {
            $('#ttid').val('');
            delete cas.args.tt;
            parseFilterFromDOM();

            if (filter && Object.keys(filter).length) {
                savedFilter = null;
                cas.args.filter = $.extend({}, filter);
                if (!isFilterFormVisible && arg !== 'noreload') {
                    showFilterForm();
                }
            } else {
                delete cas.args.filter;
                if (isFilterFormVisible && arg !== 'noreload') {
                    hideFilterForm();
                }
            }
            okPushArgs();
            if(arg !== 'noreload') {
                loadTTs();
            }
        }

        function toggleFilterForm() {
            if (!isFilterFormVisible) {
                showFilterForm();
            } else {
                hideFilterForm();
                if (!cas.args.tt) {
                    applyFilter();
                }
            }
        }
        function getTTById() {
            saveFilter();
            hideFilterForm();
            okPushArgs();
            loadTTs();
        }
        function firstParseID() {
            hideFilterForm();
            $('#ttid').val(cas.args.tt);
            getTTById();
        }

        function firstParseFilter() {
            filter = $.extend({}, cas.args.filter);

            if (!Object.keys(filter).length) {
                filter = {
                    t: ["emergencia", "canal", "geral", "mdu", "node"]
                };
            }

            parseFilterToDOM();
            applyFilter();
        }

        function parseArgs() {
            if (cas.args.tt !== undefined && cas.args.tt) {
                return firstParseID();
            }
            firstParseFilter();
        }

        function loadTypes() {
            cas.ajaxer({
                method: 'GET',
                sendto: 'eventos/types',
                andthen: function (x) {
                    var s = x.data.stage, e = $('#typeselect').empty(), i;
                    for (i in s) {
                        if (s.hasOwnProperty(i)) {
                            e.append("<option value='" + s[i].id + "'>" + s[i].name + "</option>");
                        }
                    }
                    parseArgs();
                }
            });
        }

        function loadStages() {
            cas.ajaxer({
                method: 'GET',
                sendto: 'eventos/stages',
                andthen: function (x) {
                    var s = x.data.stage, e = $('#stageselect').empty(), i;
                    for (i in s) {
                        if (s.hasOwnProperty(i)) {
                            e.append("<option value='" + s[i].id + "'>" + s[i].name + "</option>");
                        }
                    }
                    loadTypes();
                }
            });
        }

        function newOS() {
            var me = $(this).prop('disabled', true), oldLFilter = null;

            if (filter.l) {
                oldLFilter = filter.l;
            }

            delete cas.args.tt;
            $('#ttid').val('');

            saveFilter();
            okPushArgs();

            hideFilterForm();
            cas.ajaxer({
                method: 'GET',
                etc: {
                    me: me
                },
                sendto: 'eventos/nova',
                andthen: function (x) {
                    x.etc.me.prop('disabled', false);
                    loadTTs_(x);
                    if (oldLFilter) {
                        insertLoc(oldLFilter.join('; '));
                    }
                }
            });
        }
        function boot() {
            var h, m;
            cas.ajaxer({
                method: 'GET',
                sendto: 'eventos/can_create',
                andthen: function (x) {
                    bindEvents();
                    if (x.data.ok) {
                        $('<button>Novo evento</button>').click(newOS).appendTo('#abovelist');
                    }
                }
            });

            for (h = 0; h < 24; h += 1) {
                for (m = 0; m < 60; m += 1) {
                    $('#timelist').append("<option value='" + cas.strpad(h, '0', 2, true) + ':' + cas.strpad(m, '0', 2, true) + "'>");
                }
            }

            dateTypeInput = checkInput('date');
            if (!dateTypeInput) {
                LazyLoad.js(["/lib/js/ext/jquery-ui-1.10.3.custom.min.js"], function () {
                    $('#filterdate0,#filterdate1').datepicker({
                        dateFormat: 'yy-mm-dd'
                    });
                });
                LazyLoad.css('/lib/css/custom-theme/jquery-ui-1.10.3.custom.min.css');
            }

            rsz();
            loadStages();
        }

        function locationSearch(nval) {
            if (!nval || !nval.length) {
                $('#loclist').empty();
                return true;
            }

            cas.ajaxer({
                method: 'GET',
                sendme: {term: nval},
                sendto: 'eventos/lsearch',
                andthen: function (x) {
                    var i, opts = x.data.guess;
                    $('#loclist').empty();
                    for (i in opts) {
                        if (opts.hasOwnProperty(i)) {
                            $("<option>").attr('value', opts[i].name.toUpperCase()).appendTo('#loclist');
                        }
                    }
                }
            });
        }

        function locLi(v) {
            $("<li title='Clique para remover'>").click(function () {
                $(this).remove();
                checkLocFilter();
            }).html(v).attr('data-value', v).appendTo('#locfilter');
        }

        function checkCompletion(v) {
            v = v.toUpperCase();
            if (!$('#locfilter>li[data-value="' + v + '"]').length) {
                locLi(v);
                $('#locsearch').val('');
                $('#loclist').empty();
                checkLocFilter();
            }
        }

        function checkLocFilter() {
            if ($('#locfilter>li').length) {
                $('#locfilter').closest('.onefilter').addClass('activefilter');
            } else {
                $('#locfilter').closest('.onefilter').removeClass('activefilter');
            }
            scheduleFilter();
        }

        function scheduleFilter(x) {
            clearTimeout(filterT);
            filterT = setTimeout(function(){
                applyFilter();
            },2000);
        }

        function saveFilter () {
            delete cas.args.filter;
            if(filter && !savedFilter)
                savedFilter = $.extend({},filter);
            filter = {};
        }

        function loadTTs_(x){
            var n = x.data.tt.length;
            $('#countlist').html( 
                (n?
                    n+" evento"+(n>1?'s':'')
                    :'nenhum evento'
                ));
            if(n !== 1){
                listTTs(x.data.tt);
            }else{
                openTT(x.data.tt[0]);
            }
            if(!loaded){
                loaded = true;
                $( document ).idleTimer( 30 * 1000 );
                $( document ).on( "idle.idleTimer", function(){
                    if(!$('#ttlist>.tt-one').length)
                        loadTTs();
                });
            }
        }
        function searchID(){
            var id = $('#ttid').val();

            if(id){
                cas.args.tt = id;
                getTTById();
            }else{
                delete cas.args.tt;
                if(savedFilter){
                    filter = $.extend({},savedFilter);
                    savedFilter = null;
                    parseFilterToDOM();
                }
                applyFilter();
            }
            
        }
        function killID(){
            $('#ttid').val('');
            searchID();
        }
        
        function listTTs(tts){
            var ul = $('#ttlist').empty();

            for(var i in tts){
                var tt = tts[i];
                var elem = $("<li class='tt-item'>").appendTo(ul);
                var x = $("<div class='tt-item-header'>").appendTo(elem);
                if(tt.os_count)
                    x.append("<span class='os'>"+tt.os_count+' OS'+((tt.os_count>1)?'s':'')+"</span>");
                x.append("<span>"+tt.type_abbr.toUpperCase()+"</span>");
                $("<a class='cod' href='eventos#"+cas.hashbangify({tt:tt.id})+
                    "' title='Clique para abrir'>"+tt.id+"</a>")
                        .attr('data-id',tt.id).click(function(e){
                            if(!e.ctrlKey)
                                saveFilter();
                        }).appendTo(x);

                var priorityS = ' priority'+tt.priority;
                var tb = $("<table class='tt-item-table'>").appendTo(elem);
                tb.append("<tr><td colspan=3 class='tt-item-descr'>"+tt.descr.toUpperCase()+"</td></tr>");
                tb.append("<tr>"+
                    "<td class='tt-item-cidade"+priorityS+"'>"+tt.cidade+"</td>"+
                    "<td class='tt-item-location' colspan=2><div class='limited-txt'>"+tt.location+"</div></td>"+
                "</tr>");
                tb.append("<tr>"+
                    "<td class='tt-item-status"+priorityS+"'>"+tt.tt_status.toUpperCase()+"</td>"+
                    "<td class='tt-item-obs' colspan=2>"+
                        "<div class='limited-txt'>"+
                            ((tt.u.obs)?tt.u.obs:'---')+
                        "</div>"+
                        "<div class='fancy-sub-line'>" +
                            tt.last_seen +
                        " atrás</div>"+
                    "</td>"+
                "</tr>");
                tb.append("<tr>"+
                    "<td class='tt-item-ini"+priorityS+"'><sub>Início:</sub><div>"+tt.since+"</div></td>"+
                    "<td class='tt-item-deadline' colspan=2>"+
                        ((tt.type !== 'backlog')
                            ?"Previsão de normalização <br><b>"+tt.deadline+"</b>"
                            :'----'
                        )+
                    "</td>"+
                "</tr>");
            }
        }
        
        
        function insertLoc(v){
            var notIn = [];
            $(ediTT.locations).each(function(){
                notIn.push(this.location.trim().toLowerCase());
            });
            cas.ajaxer({
                sendme:{txt:v,id:ediTT.id,filter:notIn},
                sendto:'eventos/nlocinputparse',
                andthen:function(x){
                    ediTT.locations = ediTT.locations.concat(x.data.locations);
                    oneTTLocList(ediTT);
                }
            });
        }
        function newLocInput(event){
            var v = $(this).val();
            if(event.which === 13 && !event.shiftKey && !event.ctrlKey && v.length){
                $(this).prop('disabled',true);
                insertLoc(v);
            }
        }
        function osCountLabel(){
            return ((ediTT.os_count)
                        ?ediTT.os_count+' OS'+
                            ((ediTT.os_count>1)?'s':'')
                        :((ediTT.id)
                            ?'Nenhuma OS anexa'
                            :'Evento pendente'
                        )
                    );
        }
        function openTT(tt){
            ediTT = tt;
            var ttElem = $("<div class='tt-one'>").appendTo($('#ttlist').empty()), aux;
            ediTT.form = ttElem;
            if(tt.mine){
                aux = $('<h3>').appendTo(ttElem);
                $("<input class='tt-one-descr' placeholder='Descrição do problema' type='text'>").val(tt.descr).appendTo(aux);
                aux = $("<div class='tt-one-svline tt-one-txt-type'>").html("Evento do tipo: ").appendTo(aux);
                if(tt.mod){
                    aux = $("<select class='tt-one-type'>").appendTo(aux);
                    for(var i in tt.typeList)
                        $("<option>").attr('value',tt.typeList[i].id).html(tt.typeList[i].name).appendTo(aux);
                    aux.val(tt.type);
                }else{
                    aux.append('<b>'+tt.tt_type.toUpperCase()+'</b>');
                }
            }else{
                $('<h3>'+tt.descr+'</h3>')
                    .append("<div class='tt-one-svline tt-one-txt-type'>Evento do tipo: <b>"+tt.tt_type.toUpperCase()+"</b></div>")
                        .appendTo(ttElem);
            }
                        

            aux = $("<h3 style='text-align:center'>"+
                        "<span class='tt-one-ini'>"+tt.since+"<sub>Início do problema</sub>"+
                    "</span></h3>").appendTo(ttElem);

            var osBT =
                $("<a class='tt-one-oss'>"+osCountLabel()+"</a>").appendTo(aux);
            if(tt.id)
                osBT.click(openTTOSs);

            aux = $("<span class='tt-one-deadline'>").appendTo(aux);
            if(tt.mine){
                var d = $("<input class='tt-one-deadline-d' type=date />").appendTo(aux).val(tt.deadline_d);
                if(!dateTypeInput)
                    d.datepicker({dateFormat:'yy-mm-dd'});
                $("<input class='tt-one-deadline-t' type=text list='timelist'/>").val(tt.deadline_t).appendTo(aux);
            }else{
                aux.append(tt.deadline_d+" - "+tt.deadline_t);
            }
            aux.append("<sub style='color:red'>Previsão de normalização</sub>");
            
            aux = $("<div class='svh3'>").appendTo(ttElem);

            if(tt.mine){
                aux.append("<textarea rows=3 class='tt-one-obs' placeholder='Nova observação'></textarea>");
                aux = 
                $("<div class='tt-one-svline'><i>Status, mudar de "+
                    "<b>"+ediTT.tt_status.toUpperCase()+"</b> para:</i></div>")
                        .appendTo(aux);

                var selectS = $("<select class='tt-one-new-status'></select>").appendTo(aux)
                var statusList = tt.statusList;
                for(var i in statusList)
                    $("<option>").attr('value',statusList[i].id).html(statusList[i].name).appendTo(selectS);
                selectS.val(ediTT.status);
                $("<button class='tt-one-save'>Salvar</button>").appendTo(aux).click(saveNewUpdate);
            }else{
                $("<div class='tt-one-svline'><i>Status: <b>"+ediTT.tt_status.toUpperCase()+"</b></i></div>")
                    .appendTo(aux);
            }

            oneTTLocList(tt);
            
        }
        function saveNewUpdate(){
            var tt = {};
            
            tt.id = ediTT.id;
            tt.locations = ediTT.locations;
            
            tt.descr = ediTT.form.find('.tt-one-descr').val();
            tt.status = ediTT.form.find('.tt-one-new-status').val();
            tt.type = ediTT.form.find('.tt-one-type').val();
            tt.obs = ediTT.form.find('.tt-one-obs').val();
            tt.deadline_d = ediTT.form.find('.tt-one-deadline-d').val();
            tt.deadline_t = ediTT.form.find('.tt-one-deadline-t').val();
            
            if(!tt.descr || !tt.descr.trim().length)
                return alert('O campo descrição é obrigatório');

            if(!tt.obs || !tt.obs.trim().length)
                return alert('O campo observação é obrigatório');

            $(this).prop('disabled',true);
            cas.ajaxer({
                etc: {me:$(this),id:tt.id},
                sendme: {tt:tt},
                sendto: 'eventos/sv',
                complete: function(x){
                    x.etc.me.prop('disabled',false);
                },error: function(x){
                    cas.makeNotif('error','Não foi possível salvar sua atualização. Por favor, tente novamente mais tarde.');
                },
                andthen: saveNewUpdate_
            });
        }
        function saveNewUpdate_(x){
            x.etc.me.prop('disabled',false);
            $('#ttid').val(x.data.id);
            searchID();
        }
        function stdStr(s){
            return ((s && s.length)?s:'---');
        }
        function ttHist(ttElem){
            var elem = $("<table class='tt-one-updates'>").appendTo(ttElem.find('.tt-one-hist'));
            cas.ajaxer({
                method:'GET',
                etc:{elem:elem},
                sendme:{tt:ediTT.id},
                sendto:'eventos/tt_updates',
                andthen: function(x){
                    var table = x.etc.elem,updates = x.data.updates;
                    for(var i in updates)
                        table.append(
                            "<tr>"+
                                "<td rowspan=2>"+
                                    "<div class='tt-one-updates-deadline'>"+updates[i].deadline+"</div>"+
                                    "<div class='fancy-sub-line'>nova previsão</div>"+
                                "</td>"+
                                "<td class='tt-one-updates-obs' rowspan=2>"+
                                    stdStr(updates[i].obs)+
                                    "<div class='fancy-sub-line'>"+
                                        "<span>"+updates[i].exactly+" - </span>"+
                                        updates[i].timestamp+" atrás</div>"+
                                "</td>"+
                                "<td class='tt-one-updates-author'>"+updates[i].author+"</td>"+
                            "</tr>"
                        ).append(
                            "<tr>"+
                                "<td class='tt-one-updates-status'>novo status: "+
                                    "<b>"+updates[i].tt_status.toUpperCase()+"</b></td>"+
                            "</tr>"
                        );
                }
            });
        }
        function oneTTLocList(tt){
            ttElem = ediTT.form;
            var aux = ttElem.find('.tt-one-cols');
            if(!aux.length) aux = $("<div class='tt-one-cols'>");
            else aux.empty();

            aux.append("<div class='tt-one-hist'></div>").appendTo(ttElem);
            ttHist(ttElem);
            var ls = $("<div class='tt-one-locs'></div>").prependTo(aux);
            if(tt.mine)
                $("<textarea class='tt-one-new-loc' rows=3 type='text' placeholder='Nova localidade, em caso de múltiplas separe-as por ponto e vírgula.'>")
                    .keydown(newLocInput).appendTo(ls);
            var locs = $("<ul class='tt-one-loclist'>").appendTo(ls);
            for(var i in tt.locations){
                var title = ((tt.locations[i].ok)
                                ?"<del>"+tt.locations[i].location+"</del>"
                                :tt.locations[i].location);
                var li = 
                    $("<li>"+
                        "<span class='tol tt-one-location-"+tt.locations[i].location_type+"'>&zwnj;</span>"+
                    "</li>").append(
                        $("<span class='tt-one-location-title'>").html(title).attr('title',title)
                    ).attr('data-index',i).appendTo(locs);
                var tol = li.find('.tol');
                if(tt.mine){
                    tol.click(function(){
                        $(this).children('.tt-one-location-opts').toggle();
                    });
                    var tmp = 
                        $("<span class='tt-one-location-opts' style='display:none'>"+
                            "<a class='tt-one-location-solve'>&#10003;</a>"+
                            ((tt.mod)?"    |    <a class='tt-one-location-delete'>Excluir</a>":"")+
                        "</span>").appendTo(tol);

                    tmp.find('.tt-one-location-solve').click(solveLocation);
                    if(tt.mod)
                        tmp.find('.tt-one-location-delete').click(deleteLocation);
                }
            }
        }
        function makeMeAnOSDialog(){
            var pos = 
                {
                    top: $('#abovelist').offset().top + $('#abovelist').outerHeight(), 
                    left: 20
                };

            var d = $('<div>').addClass('tt-one-os-dialog').width($(window).width()-60).attr('data-tt',ediTT.id);
            ediTT.dialog = d;
            var a =
                $("<div class='tt-one-os-att'>")
                    .append('<h3><span class="os-att-count">[ 0 ]</span> Anexas ao evento</h3>')
                    .append("<ul class='tt-one-os-list'></ul>")
                    .appendTo(d);

            var b =
                $("<div class='tt-one-os-free'>")
                    .append('<h3><span class="os-free-count">[ 0 ]</span> Ordens Soltas</h3>')
                    .append("<ul class='tt-one-os-list'></ul>")
                    .appendTo(d);
            if(ediTT.mine){
                var invertC = function(){$(this).prop('checked',!$(this).prop('checked'));};

                $("<div class='tt-one-svline tt-os-lsv'>")
                    .append($("<button>Inverter</button></div>").click(function(){
                        ediTT.dialog.find('.tt-one-os-att>.tt-one-os-list').find('.checkOS').each(invertC);
                    }))
                    .append($("<button>Remover</button></div>").click(removeOS))
                    .appendTo(a);

                var dt0 = new Date(), dt1 = new Date();
                dt0.setTime(ediTT.t_ini - (24 * 60 * 60 * 1000) );
                dt1.setTime(ediTT.t_ini);
                
                $("<div class='tt-one-svline tt-os-lsv'>")
                    .append("<input class='tt-os-term' type='text' placeholder='Busca livre' />")
                    .append(" de <input class='tt-os-from' type='date'  value='"+dt0.toYMD()+"' />")
                    .append(" à <input class='tt-os-to' type='date' value='"+dt1.toYMD()+"' />")
                    .append($("<button>Atualizar</button></div>").click(function(){
                        freeOSList();
                    }))
                    .append($("<button>Inverter</button></div>").click(function(){
                        ediTT.dialog.find('.tt-one-os-free>.tt-one-os-list').find('.checkOS').each(invertC);
                    }))
                    .append($("<button>Anexar</button></div>").click(attOS))
                    .appendTo(b);

                if(!dateTypeInput){
                    b.find("input[type='date']").datepicker({dateFormat:'yy-mm-dd'});
                }
                freeOSList();
            }
            cas.weirdDialogSpawn(pos,d);

            return d;
        }
        function freeOSList(){

            var s = {
                locations:ediTT.locations,
                from: ediTT.dialog.find('.tt-os-from').val(),
                to: ediTT.dialog.find('.tt-os-to').val(),
                q: ediTT.dialog.find('.tt-os-term').val()
            };
            cas.ajaxer({
                method:'GET',
                sendme: s,
                sendto:'eventos/avaible_os',
                andthen:function(x){
                    var os = x.data.os;
                    var ul = ediTT.dialog.find('.tt-one-os-free>.tt-one-os-list').empty();
                    ediTT.dialog.find('.os-free-count').html("[ "+os.length+" ]");
                    for(var i in os)
                        appendOS( os[i], ul, false );
                }
            });

        }
        function myOSList(r){
            var x = [];
            ediTT.dialog.find('.tt-one-os-'+r+'>.tt-one-os-list').find('.checkOS:checked').each(function(){
                var val = $(this).val();
                if($(this).attr('data-json')){
                    val = JSON.parse(Base64.decode(val));
                }
                x.push(val);
            });
            return x;
        }
        function attOS(){
            cas.ajaxer({
                sendme:{oss:myOSList('free'),tt:ediTT.id},
                sendto:'eventos/attos',
                andthen:openTTOSs
            });
        }
        function removeOS(){
            cas.ajaxer({
                sendme:{ids:myOSList('att')},
                sendto:'eventos/killosatt',
                andthen:openTTOSs
            });
        }
        function openTTOSs(){
            $('.weird-dialog').remove();
            makeMeAnOSDialog();
            cas.ajaxer({
                method:'GET',
                sendme:{tt:ediTT.id},
                sendto:'eventos/evento_os',
                andthen:function(x){
                    var osList = x.data.os;
                    ediTT.os_count = osList.length;
                    ediTT.form.find('.tt-one-oss').html(osCountLabel());
                    ediTT.dialog.find('.tt-one-os-att>.tt-one-os-list').empty();
                    ediTT.dialog.find('.os-att-count').html("[ "+osList.length+" ]");
                    for(var i = 0;i < osList.length;i+=10)
                        osFetch(osList.slice(i,i+10),'att');
                }
            });

        }
        function osFetch(osList,repo){
            cas.ajaxer({
                method:'GET',
                sendme:{os:osList},
                sendto:'eventos/osfetch',
                andthen:function(x){
                    var os = x.data.os;
                    var ul = ediTT.dialog.find('.tt-one-os-'+repo+'>.tt-one-os-list');
                    for(var i in os){
                        appendOS( os[i], ul, (repo === 'att') );
                    }
                }
            });
        }
        function appendOS(os,ul,id){
            $("<li>").append(
                "<table class='tt-os-table'>"+
                    "<tr>"+
                        ((ediTT.mine)?
                            "<td class='tot-checker' rowspan=2>"+
                                "<input class='checkOS' type='checkbox' value="+
                                    ((os.id)?os.id:
                                        "'"+Base64.encode(JSON.stringify({
                                            os:os.os,
                                            per:os.per,
                                            svc:os.svc
                                        }))+"' data-json=true"
                                    )+" />"+
                            "</td>"
                            :'')+
                        "<td class='tot-id' rowspan=2>"+
                            "<div>"+os.cid+"</div>"+
                            "<a target='_blank' href='os#os="+os.os+"&per="+os.per+
                                    "&svc="+os.svc.toLowerCase()+"'>"+
                                os.svc.toUpperCase()+" - "+os.os +"</a>"+
                            "<br><i>"+os.os_status.toUpperCase()+"</i>"+
                        "</td>"+
                        "<td class='tot-name'>"+
                            "<b>"+os.asscod+"</b>: "+os.assname+
                        "</td>"+
                        "<td class='tot-falha'>"+
                            os.falha+"<div class='fancy-sub-line'>Ingresso em "+os.ingr+"</div>"+
                        "</td>"+
                    "</tr>"+
                    "<tr>"+
                        "<td class='tot-end'>"+
                            os.end+"<br>"+
                            os.bairro+" - "+"<b>"+os.node+"</b>"+
                        "</td>"+
                        "<td class='tot-obs'>"+
                            os.obs_origem+
                        "</td>"+
                    "</tr>"+
                "</table>"
            ).appendTo(ul);
        }
        function solveLocation(){
            var me = $(this),
                li = me.closest('li'),
                index = parseInt(li.attr('data-index'));

            var location = ediTT.locations[index];
            location.ok = !location.ok;
            var title = ((location.ok)
                            ?"<del>"+location.location+"</del>"
                            :location.location);
            li.find('.tt-one-location-title').html(title);
        }
        function deleteLocation(){
            var me = $(this),
                li = me.closest('li'),
                index = parseInt(li.attr('data-index'));
            ediTT.locations.splice(index,1);
            oneTTLocList(ediTT);
        }
        $(window).on('hashchange',function(){
            if(!itWasMe){
                parseArgs();
            }
        });
        function bindEvents() {
            $('.filtertitle').click(function () {
                $(this).parent().toggleClass('activefilter');
                scheduleFilter();
            });

            $('nav select').change(function () {
                var x = $(this).val();
                if (x && x.length) {
                    $(this).closest('.onefilter').addClass('activefilter');
                } else {
                    $(this).closest('.onefilter').removeClass('activefilter');
                }
                scheduleFilter();
            });
            $('#filterdate1,#filterdate0').change(function () {
                $(this).closest('.onefilter').addClass('activefilter');
                scheduleFilter();
            });

            $('#genericsearch').on('input', function () {
                var me = $(this);
                if(me.val().length)
                    me.closest('.onefilter').addClass('activefilter');
                else
                    me.closest('.onefilter').removeClass('activefilter');
                scheduleFilter();
            });
            
            $('#locsearch').keydown(function(event){
                clearTimeout(locT);
                if(event.which === 13){
                    checkCompletion($(this).val());
                }else{
                    var me = $(this);
                    locT = 
                        setTimeout(function(){
                            locationSearch(me.val());
                            
                        },300);
                }
            });
            $('#equaldates').click(function(){
                $('#filterdate1').val($('#filterdate0').val());
            });
            $(window).resize(rsz);
            $('#killid').click(killID);
            $('#ttid').keydown(function(event){
                if(event.which === 13)
                    searchID();
            });
            $('#nofilter').click(toggleFilterForm);
            $('#dofilter').click(applyFilter);
            $('#downloadxls').click(function(){
                $(this).prop('disabled', true);
               //applyFilter('noreload');
                downloadXls();
            });
        }
        boot();
    };
}(window, window.cas, jQuery, window.LazyLoad));