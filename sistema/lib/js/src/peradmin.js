cas.controller = function(){
    var map,
        container = $('#container'),
        canedit = false,
        dragt = false,
        per = {},
        clickListener,
        zona = null,
        zonas = [],
        tec = null,
        pointsPlaced = 0,
        left = $('#left'),
        tec_container = $('#tec_container'),
        right = $('#right'),
        mover = $('#mover'),
        exTecID = null,
        workgrid = null,
        workgrids = [],
        workbatch = null,
        openTec = null,
        tmpPoints = [];
    var resizeme = function(){
        container.height(
                $(this).height()
                - ( $('#head-wrapper').outerHeight() + $('#foot').outerHeight() )
                - $('#upbar').outerHeight()
        );
        $('#map').height(
            right.height() - $('#zonas').outerHeight()
        );
        $('#zonas').width($('#zonasw').width() - 30);
        resizePans();
    }
    function resizePans(){
        left.width(mover.offset().left);
        right.width(
            container.width() -
            ( mover.offset().left + mover.outerWidth() )
        );
    }
    function resetMarkers(keepcentroids){
        if(!keepcentroids){
            map.removeMarkers();
        }else{
            for(var i in tmpPoints){
                tmpPoints[i].setMap(null);
            }
        }
        tmpPoints = [];
    }
    function extractID(me){
        var id0 = me.attr('id'), 
            id1 = id0.split('_');
        return parseInt(id1[1]);
    }
    function loadPer(){

        resetMarkers();
        per = {
            id: parseInt($('#per_selector').val()),
            name: $('#per_selector').children('option:selected').attr('name'),
            full: $('#per_selector').children('option:selected').attr('fullname')
        };

        cas.ajaxer({
            sendto:'peradmin/caneditper',
            method: 'GET',
            sendme:
                {per: per.id},
            andthen: function(x){
                canedit = x.data.canedit;
                if(canedit){
                    $('#per_settings').button('enable');
                }else{
                    $('#per_settings').button('disable');
                }
                fetchZonas();
            }
        });
        GMaps.geocode({
            address: per.full ,
            callback: function(results, status) {
                if(results.length > 0){
                    var latlng = results[0].geometry.location;
                    map.setCenter(latlng.lat(), latlng.lng());
                    map.map.fitBounds(results[0].geometry.viewport);
                }
            }
        });
    }
    function loadTecs(){
        tec_container.empty();
        cas.hidethis(tec_container);
        cas.ajaxer({
            sendto:'peradmin/getTecs',
            method: 'GET',
            sendme:
                {per: per.id},
            andthen: tecList
        });
    }
    function tecList(x){
        tec = null;
        tec_container.empty();
        for(var i in zonas){
            zonas[i].tecs = 0;
            if(zonas[i].elem)
                zonas[i].elem.find('.ztop>.ztxt').empty();
        }
        for(var i in x.data.tecs){
            var tec_tag = (x.data.tecs[i].deleted)?"del":'span';
            var aux = $(
                "<li class='tecw"+((x.data.tecs[i].workgrid)?' tecaloc':'')+"' id='tec_"+x.data.tecs[i].id+"'>"+
                    "<table class='tec' tecID='"+x.data.tecs[i].id+"' tecname='"+x.data.tecs[i].name+"' "+((x.data.tecs[i].zona)?"teczona="+x.data.tecs[i].zona:"")+">"+
                        "<tr>"+
                            "<td class='tecidw'>"+
                                ((canedit)?"<span title='Clique para configurações adicionais' class='tec_settings"+((!x.data.tecs[i].workgrid)?' tec_undefined':'')+"'>&zwnj;</span>":'')+
                            "</td>"+
                            "<td class='tecname'><"+tec_tag+" class='ttxt'>"+x.data.tecs[i].name+"</"+tec_tag+"></td>"+
                        "</tr>"+
                    "</table>"+
                "</li>"
            ).click(tecClick).appendTo(tec_container);
            aux.find('.tec_settings').click(tecConfClick);
            if(x.data.tecs[i].zona){
                aux.find('.tecidw').css('background-color',x.data.tecs[i].color);
                var tzona = parseInt(x.data.tecs[i].zona);
                if(!zonas[tzona].tecs)
                    zonas[tzona].tecs = 1;
                else
                    zonas[tzona].tecs++;
            }
        }
        for(var i in zonas){
            if(zonas[i].tecs && zonas[i].elem){
                zonas[i].elem.find('.ztop>.ztxt').html(zonas[i].tecs+" técnico"+((zonas[i].tecs > 1)?'s':''));
            }
        }
        if(exTecID){
            var extec = $('#tec_'+exTecID);
            if(extec.length > 0){
                cas.scrollToMe(extec,'y');
                extec.effect('highlight',5000);
            }
        }
        tec_container.find('del.ttxt').attr('title','Técnico removido do SIGA');
    }
    function mapLoader(){
        map = new GMaps({
            div: '#map',
            lat: 10,
            lng: 10,
            disableDefaultUI: true
        });
        google.maps.Polygon.prototype.my_getBounds=function(){
            var bounds = new google.maps.LatLngBounds();
            this.getPath().forEach(function(element,index){bounds.extend(element)});
            return bounds;
        }
    }
    function mapClick(e){
        tmpPoints.push(map.addMarker({
            lat: e.latLng.lat(),
            lng: e.latLng.lng(),
            draggable:true
        }));

        if(pointsPlaced == 0)
            cas.makeNotif('information','É possível mudar a posição dos pontos, basta apenas segurar com o mouse e arrastar.');

        pointsPlaced++;
        if(pointsPlaced > 2)
            $('#closezona').button('enable');
    }
    function boot(){
        var p = cas.get_pref('per');
        if(p)
            $('#per_selector').val(p);
        resizeme.call(window);
        cas.resizer.push(resizeme);
        mapLoader();
        loadPer();
    }
    function exportPath(){
        var mypath = {path:[],centroid:{}};
        //var bounds = new google.maps.LatLngBounds();
        //var polygonCoords = [];
        if(zonas[zona].polygon){
            var pls = zonas[zona].polygon.getPath();
            pls.forEach(function(x,i){
                mypath.path.push({
                    lat:x.lat(),
                    lng:x.lng()
                });

            });
            var x = zonas[zona].polygon.my_getBounds().getCenter();
            mypath.centroid = {lat:x.lat(),lng:x.lng()};
        }
        return mypath;
    }
    function centroid(){

        var i;

        // The Bermuda Triangle
        var polygonCoords = [
          new google.maps.LatLng(25.774252, -80.190262),
          new google.maps.LatLng(18.466465, -66.118292),
          new google.maps.LatLng(32.321384, -64.757370),
          new google.maps.LatLng(25.774252, -80.190262)
        ];

        for (i = 0; i < polygonCoords.length; i++) {
          bounds.extend(polygonCoords[i]);
        }

        // The Center of the Bermuda Triangle - (25.3939245, -72.473816)
        console.log(bounds.getCenter());
    }
    function zonaArea(z){
        if(typeof z === 'undefined')
            z = zona;
        if(zonas[z].polygon)
            return google.maps.geometry.spherical.computeArea(zonas[z].polygon.getPath());
        else
            return 0;
    }
    function fetchZonas(){
        cas.ajaxer({
            sendto:'peradmin/zonas',
            method: 'GET',
            sendme:
                {per: per.id},
            andthen: function(x){
                loadZonas(x.data.zonas);
            }
        });
    }
    function zonaListAndClick(x){
        loadZonas(x.data.zonas);
        if(typeof x.data.zona !== 'undefined' && x.data.zona)
            $('#zona_'+x.data.zona).trigger('click');
        else
            closePolygon();
    }

    function loadZonas(zs){
        $('#zonas').empty();
        for(var i in zonas){
            if(zonas[i].polygon)
                zonas[i].polygon.setMap(null);
        }
        resetMarkers();
        zonas = [];
        for(var i in zs){
            var zid = zs[i].id;

            if(zs[i].path){
                zs[i].polygon = map.drawPolygon({
                    paths:zs[i].path,
                    fillColor:zs[i].color,
                    fillOpacity:0.3,
                    strokeWeight:((parseInt(zs[i].selectable) > 0)?2:0)
                });
                map.addMarker({
                    lat: zs[i].lat,
                    lng: zs[i].lng,
                    icon:'lib/img/crosshair.png'
                })
            }else{
                zs[i].polygon = null;
            }
            delete zs[i].path;
            if(parseInt(zs[i].selectable) > 0){
                zs[i].elem = $(
                    "<li id='zona_"+zs[i].id+"' class='zona' zona='"+zs[i].id+"' zonacolor='"+zs[i].color+"'>"+
                        "<div class='ztop'><span class='ztxt'>&zwnj;</span></div>"+
                        "<span class='zonaname'>"+
                           "<span class='zn'>"+zs[i].name+"</span>"+
                        "</span>"+
                         "<div class='zbottom'><span class='ztxt'>"+
                            ((!zs[i].polygon)
                                ?"&zwnj;"
                                :cas.roundNumber(google.maps.geometry.spherical.computeArea(zs[i].polygon.getPath())/(1000 * 1000),2)+"km²"
                            )+"</span></div>"+
                    "</li>"
                ).css('background-color',zs[i].color).click(zonaClick).appendTo('#zonas');
            }else{
                zs[i].elem = null;
            }
            zonas[zid] = zs[i];
        }
        noZona();
        loadTecs();
    }
    function tecClick(){
        //unselect zona
        if(canedit){

            if(zona)
                $('#zona_'+zona).trigger('click');

            var me = $(this);
            var t = me.find('.tec');

            $('.tecwselected').removeClass("tecwselected");

            exTecID = extractID(me);

            if(!tec || tec.id !== exTecID){
                selectTec(t);
                me.addClass('tecwselected');
                cas.makeNotif('information','Agora clique na zona para a qual deseja alocar o técnico, ou novamente sobre ele para desalocar.');
            }else{
                saveTecZona(null);
                tec = null;
            }


        }
    }
    function tecConfClick(){
        var t = $(this).closest('.tec');
        t.parent().addClass('openTec');
        openTec = {
                name:t.attr('tecname'),
                id:parseInt(t.attr('tecID')),
                elem:t
            };
        $('#tec_settings').dialog("option", "title", openTec.name.toUpperCase()).dialog('open');
        return false;
    }
    function selectTec(t){
        tec = {
                elem:t,
                id:parseInt(t.attr('tecID')),
                zona:t.attr('zona')
            };
    }
    function saveTecZona(z){
        tec_container.empty();
        cas.hidethis(tec_container);
        cas.ajaxer({
            sendto:'peradmin/setteczona',
            sendme:
            {
                tec:tec.id,
                zona:z,
                per: per.id
            },
            andthen:tecList
        });
    }

    function zonaClick(){
        if(canedit){
            var
                me =$(this),
                id = parseInt(me.attr('zona'));
            if(tec){
                tec.elem.find('.tecidw').css('background-color',me.attr('zonacolor'));
                tec.zona = id;
                tec.elem.parent().removeClass('tecwselected');
                saveTecZona(id);
            }else{
                $('.editbts').button('disable');
                $('#savezona').button('enable');
                $('#removezona').button('enable');
                closePolygon();
                unselectZona();
                if(id === zona){
                    noZona();
                }else{
                    zona = id;
                    $(this).addClass('zonaselected');
                    if(!zonas[zona].polygon){
                        openPolygon();
                    }else{
                        zonas[zona].polygon.setOptions({
                            strokeColor:'red'
                        });
                        enableEdit();
                    }

                    cas.scrollToMe(me,'x');

                    $('#zonaname').val(zonas[zona].name);
                    $('#zonacolor').spectrum('set',zonas[zona].color);
                    $('#zonaedit').show();
                }
            }
        }
    }
    function noZona(){
        $('#zonaedit').hide();
        zona = null;
    }
    function unselectZona(){
        $('.zonaselected').removeClass('zonaselected');
        for(var i in zonas)
            if(zonas[i].polygon)
                zonas[i].polygon.setOptions({
                    strokeColor:'black'
                });
    }
    function openPolygon(){
        pointsPlaced = 0;
        $('#clicktoadd').show();
        clickListener = google.maps.event.addDomListener(map.map,'click',mapClick);
    }
    function closePolygon(){
        $('#clicktoadd').hide();
        google.maps.event.removeListener(clickListener);
        clickListener = false;
        resetMarkers(true);
    }
    function mountPolygon(){
        var path = [];
        for(var i in tmpPoints){
            path.push([
                tmpPoints[i].getPosition().lat(),
                tmpPoints[i].getPosition().lng()
            ]);
        }
        zonas[zona].polygon = map.drawPolygon({
            paths:path,
            fillColor:zonas[zona].color,
            fillOpacity:0.3
        });

    }
    function saveZona(){
        if($('#zonaname').val()){
            var mypath = exportPath();
            var myarea = zonaArea();
            cas.ajaxer({
                sendto:'peradmin/savezona',
                sendme:
                    {
                        per: per.id,
                        name:$('#zonaname').val(),
                        id:zonas[zona].id,
                        path:mypath.path,
                        centroid:mypath.centroid,
                        color:$('#zonacolor').val(),
                        area:myarea
                    },
                andthen: zonaListAndClick
            });
        }
    }
    function enableEdit(){
        $('#pleaseedit').button('enable');
        $('#clearzona').button('enable');
    }
    function onEdit(){
        $('#doneediting').button('enable');
        $('#pleaseedit').button('disable');
        $('#clearzona').button('disable');
    }
    function loadGrids(id, callback){
        cas.hidethis('body');
        cas.ajaxer({
            sendto:'peradmin/workgrids',
            method: 'GET',
            andthen:function(x){
                populateGrids(x,id);
                cas.showthis('body');
                if (callback) {
                    callback();
                }
            }
        });
    }
    function populateGrids(x,id){
        if(typeof id === 'undefined')
            id = 'grid_selector';
        var whereto = $('#'+id);
        var editing = (id === 'grid_selector');
        whereto.empty();
        workgrids = [];
        if(editing){
            whereto.html('<option value=0>### Nova ###</option>');
            workgrids[0] =
                {
                    id:0,
                    name:'### Nova ###',
                    editable:true,
                    lunch_ini:'12:00:00'
                };
        }else if(id === 'tec_grid'){
            whereto.html("<option value=''>--- Nenhum ---</option>");
        }
        for(var i in x.data.grids){
            whereto.append('<option value="'+x.data.grids[i].id+'">'+x.data.grids[i].name+'</option>');
            x.data.grids[i].id = parseInt(x.data.grids[i].id);
            workgrids[x.data.grids[i].id] = x.data.grids[i];
        }
        if(x.data.grid){
            whereto.val(x.data.grid);
        }
        if(id === 'grid_selector')
            whereto.trigger('change');
    }
    function loadGrid(){
        cas.ajaxer({
            sendto:'peradmin/timegrid',
            method: 'GET',
            sendme:{workgrid:workgrid},
            andthen:function(x){
                var
                    body = $('#grid_body').empty(),
                    head,subhead,tr,td,
                    d = x.data.grid[0].wd,
                    c = 0;
                head = $('<tr><th class="hourweek" rowspan=2>Dia</th></tr>');
                subhead = $('<tr>');
                for(var i in x.data.grid){
                    if(i == 0 || d != x.data.grid[i].wd){
                        tr = $('<tr><td class="weekday">'+x.data.grid[i].d+'</td></tr>');
                        tr.appendTo(body);
                        d = x.data.grid[i].wd;
                    }
                    if( x.data.grid[i].wd  === '1' ){
                        //subhead.append('<th class="hour"><div class="subhour">'+x.data.grid[i].ini+'</div></th>');
                        if((i%4) == 0)
                            head.append('<th class="hour" colspan=4>'+x.data.grid[i].ini+'</th>');
                    }
                    td = $("<td timeid="+x.data.grid[i].id+" time-d='"+x.data.grid[i].d+"' time-ini='"+x.data.grid[i].ini+"' time-end='"+x.data.grid[i].end+"' class='workslot"+(( parseInt(x.data.grid[i].working) > 0 )?' workentry':'')+"'></td>");
                    td.mousedown(function(e){
                        if(e.button === 0){
                            if($(this).is('.workentry')){
                                workbatch = true;
                            }else{
                                workbatch = false;
                            }
                            $(this).toggleClass('workentry');
                        }
                    }).hover(function(){
                        if(workbatch === false){
                            $(this).addClass('workentry');
                        }else if(workbatch === true){
                            $(this).removeClass('workentry');
                        }
                        $('#grid_tooltip').html($(this).attr('time-d')+': '+$(this).attr('time-ini')+' > '+$(this).attr('time-end'));
                    },function(){
                        $('#grid_tooltip').empty();
                    });
                    td.appendTo(tr);
                }
                $('#grid_head').html(head).append(subhead);
            }
        });
    }
    function saveGrid(){
        var workentries = [];
        $('.workentry').each(function(){
            workentries.push(parseInt($(this).attr('timeid')));
        });
        cas.hidethis('body');
        cas.ajaxer({
            sendto:'peradmin/saveworkgrid',
            sendme:
                {
                    workgrid:workgrid,
                    name:$('#grid_name').val(),
                    lunch_ini:$('#lunch_ini_h').val()+":"+$('#lunch_ini_m').val()+":00",
                    workentries:workentries
                },
            andthen:function(x){

                populateGrids(x);
                cas.showthis('body');
            }
        });
    }
    function removeGrid(){
        cas.hidethis('body');
        cas.ajaxer({
            sendto:'peradmin/removeworkgrid',
            sendme:
                {
                    workgrid:workgrid
                },
            andthen:function(x){
                populateGrids(x);
                cas.showthis('body');
            }
        });
    }
    function clearTimeoutClick(){
        cas.ajaxer({
            method:'GET',
            sendme:{
                tec:openTec.id,
                id:$(this).attr('timeout-id')
            },
            sendto:'agenda/clear_timeout',
            andthen:loadTimeouts
        });
    }
    function loadTimeouts(){
        cas.hidethis('body');
        cas.ajaxer({
            method:'GET',
            sendme:
                {tec:openTec.id,per: per.id},
            sendto:'agenda/tec_timeout',
            andthen:function(x){
                var t = $('#tec_timeouts>tbody').empty();

                for(var i in x.data.timeouts)
                    $("<tr>"+
                                "<td>"+x.data.timeouts[i].ini+"</td>"+
                                "<td>"+x.data.timeouts[i].end+"</td>"+
                                "<td><div class='clear_timeout' timeout-id='"+x.data.timeouts[i].id+"'>&zwnj;</div></td>"+
                            "</tr>").appendTo(t).find('.clear_timeout').click(clearTimeoutClick);
                cas.showthis('body');
            }
        });
    }
    $('#removegrid').click(function(){
        removeGrid();
    });
    $('#savegrid').click(function(){
        saveGrid();
    });
    $('#removezona').click(function(){
        cas.ajaxer({
            sendto:'peradmin/removezona',
            sendme:
                {
                    per: per.id,
                    id:zonas[zona].id
                },
            andthen: zonaListAndClick
        });
    });
    $('#grid_selector').change(function(){
        workgrid = parseInt($(this).val());
        $('#authorname').html(
                ((workgrids[workgrid].author)
                ?'Criado por '+workgrids[workgrid].author
                :'')
        );
        var tsize = ((workgrids[workgrid].tecs)?workgrids[workgrid].tecs.length:0);
        if(tsize)
            $('#hor_tec').show().html('Utilizado em '+tsize+' técnico'+((tsize > 1)?'s':''));
        else
            $('#hor_tec').hide();
        $('#hor_tec_list').empty();
        for(var i in workgrids[workgrid].tecs)
            $('#hor_tec_list').append('<li>'+workgrids[workgrid].tecs[i].name.toUpperCase()+'</li>');
        
        if(workgrids[workgrid].editable || workgrid === 0){
            $('#grid_name').prop('disabled',false);
            $('#savegrid').button('enable');
        }else{
            $('#grid_name').prop('disabled',true);
            $('#savegrid').button('disable');
        }
        if(workgrid > 0 && workgrids[workgrid].editable){
            $('#removegrid').button('enable');
        }else{
            $('#removegrid').button('disable');
        }
        $('#grid_name').val($(this).find(':selected').text());
        var lini = workgrids[workgrid].lunch_ini.split(':');
        $('#lunch_ini_h').val(lini[0]);
        $('#lunch_ini_m').val(lini[1]);
        loadGrid();
    });
    $('#per_selector').change(function(){
        cas.set_pref('per',parseInt($(this).val()));
        loadPer();
    });
    $('#hor_tec').hover(function(){
        if(!$('#hor_tec_list').is(':empty'))
            $('#hor_tec_list')
                .css('top',$(this).position().top + 30)
                .show();
            
    },function(){
        $('#hor_tec_list').hide();
    });
    $('#insertzona').click(function(){
        if(canedit){
            cas.ajaxer({
                sendto:'peradmin/insertzona',
                sendme:
                    {per: per.id},
                andthen: zonaListAndClick
            });
        }
    });
    $('#savezona').click(function(){
        saveZona();
    });
    $('.editbts,#removegrid,#savegrid,#saveargs').button();
    $("#zonacolor").spectrum({
        showInitial: true,
        showInput: true,
        chooseText: "Confirmar",
        cancelText: "Cancelar"
    });
    $('#closezona').click(function(){
        mountPolygon();
        closePolygon();
        saveZona();
    });
    $('#doneediting').click(function(){
        if(zonas[zona].polygon){
            zonas[zona].polygon.setEditable(false);
            zonas[zona].polygon.setDraggable(false);
            $('#savezona').trigger('click');
        }
    });
    $('#pleaseedit').click(function(){
        if(zonas[zona].polygon){
            zonas[zona].polygon.setEditable(true);
            zonas[zona].polygon.setDraggable(true);
            onEdit();
        }
    });
    $('#clearzona').click(function(){
        zonas[zona].polygon.setMap(null);
        zonas[zona].polygon = null;
        saveZona();
    });
    $('body').mouseup(function(){
        workbatch = null;
    });
    $('#tec_settings').dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: true,
        width: $(window).width() - 100,
        height: $(window).height() - 100,
        resizable: false,
        open: function(){
            
            var usr = $('#tec_email').empty().append("<option value=''>--- Nenhum ---</option>");
            var tp = $('#os_tipo_tec').empty();

            cas.hidethis('body');
            cas.ajaxer({
                sendme:
                    {tec:openTec.id},
                method: 'GET',
                sendto:'peradmin/tecfeed',
                andthen:function(x){

                    for(var i in x.data.users)
                        usr.append('<option value="'+x.data.users[i].login+'">'+x.data.users[i].login+'</option>');
                    for(var i in x.data.tipos)
                        tp.append('<option value="'+x.data.tipos[i].tipo+'" '+((parseInt(x.data.tipos[i].selected) > 0)?'selected':'')+' ">'+x.data.tipos[i].tipo+'</option>');
                    
                    
                    loadGrids('tec_grid', function(){
                        if (x.data.tec.workgrid) {
                            $('#tec_grid').val(x.data.tec.workgrid);
                        }
                        if (x.data.tec.user) {
                            usr.val(x.data.tec.user);
                        }
                    });
                    
                    cas.showthis('body');
                }
            });
        },
        close: function(){
            openTec.elem.parent().removeClass('openTec');
        },
        buttons:[
            {
                text:'Salvar',
                click:function(){
                    cas.hidethis('body');
                    cas.ajaxer({
                        sendme:{
                            tec:openTec.id,
                            tipos:$('#os_tipo_tec').val(),
                            email:$('#tec_email').val(),
                            workgrid:$('#tec_grid').val()
                        },
                        sendto:'peradmin/tecsettings',
                        andthen:function(x){
                            cas.showthis('body');
                            loadTecs();
                        }
                    });
                }
            }
        ]
    });
    $('#saveargs').click(function(){
        cas.hidethis('body');
        cas.ajaxer({
            sendme:{
                per:per.id,
                kmh:$('#kmh').spinner('value'),
                min_desloc_time:$('#min_desloc_time').spinner('value'),
                max_desloc_time:$('#max_desloc_time').spinner('value')
            },
            sendto:'peradmin/setdesloc',
            andthen:function(){
                cas.showthis('body');
            }
        });
        if(cas.checkPerms('x')){
            var ts = [];
            $('.tec_time').each(function(){
                ts.push({
                    name:$(this).attr('id'),
                    tec_time:$(this).spinner('value')
                });
            });
            cas.ajaxer({
                sendme:{tipos:ts,per: per.id},
                sendto:'peradmin/savetipos'
            });
        }
    });
    $('#grid_dialog').dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: true,
        width: $(window).width()-20,
        height:$(window).height()-20,
        dialogClass: 'noTitleDialog',
        resizable: false,
        open: function(){
            loadGrids('grid_selector');
            cas.ajaxer({
                sendme:{
                    per:per.id
                },sendto:'peradmin/getdesloc',
                method: 'GET',
                andthen:function(x){
                    $('#kmh').val(x.data.kmh);
                    $('#min_desloc_time').val(x.data.min_desloc_time);
                    $('#max_desloc_time').val(x.data.max_desloc_time);
                }
            });
            cas.ajaxer({
                method: 'GET',
                sendme:{per:per.id},
                sendto:'peradmin/tec_time',
                andthen:function(x){
                    var p = $('#grid_dialog').find('.grid_bar').first(),
                        t = $('#tec_times'),
                        k;

                    if(t.length < 1){
                        t = $('<fieldset id="tec_times"></fieldset>').prependTo(p);
                    }
                    t.empty();
                    for(var i in x.data.tipos){
                        k = x.data.tipos[i];
                        t.append(
                            "<label class='tec_time_label'>"+k.name+
                                ": <input readonly id='"+k.name+
                                    "' class='tec_time' type='text' value='"+k.tec_time+"'/>"+
                            "</label>"
                        );
                        $('.tec_time').spinner({min:1});
                    }
                }
            });
        },
        buttons:[
            {
                text:'Fechar',
                click:function(){
                    $(this).dialog('close');
                }
            }
        ]
    });
    $('#per_settings').button({
        text: false,
        icons: {
           primary: "ui-icon-clock"
        }
    }).click(function(){
        $('#grid_dialog').dialog('open');
    });
    $('#per_settings').button('disable');
    $('#kmh').spinner({min:1,step:0.1});
    $('#min_desloc_time,#max_desloc_time').spinner({min:1,step: 1});
    $('#timeout_ini,#timeout_end').datepicker({ dateFormat: "dd/mm/y" });
    $('#tec_timeout_dlg').dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: true,
        width: 700,
        height:400,
        resizable: false,
        open: function(){
            loadTimeouts();
        },
        close: function(){
            loadPer();
        }
    });
    
    $('#setTimeout').button().click(function(){
        var
            ini = $('#timeout_ini').datepicker('getDate'),
            end = $('#timeout_end').datepicker('getDate');
        cas.ajaxer({
            sendto:'agenda/set_timeout',
            sendme:{
                tec:openTec.id,
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
    });

    $('#tec_tm')
    .button({
        text: false,
        icons: {
           primary: "ui-icon-clock"
        }
    })
    .click(function(){
        $('#tec_timeout_dlg').dialog("option", "title", openTec.name.toUpperCase()).dialog('open');
    });
    $('#container,#grid_table').disableSelection();
    
    boot();
};