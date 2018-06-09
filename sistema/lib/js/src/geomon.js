cas.controller = function() {
    
    var ROLL_INTERVAL = 1000 * 40;
    var RELOAD_INTERVAL = 1000 * 60;

    var gMap = null, sPers = {}, geoT, os = {};
    var markerCluster = null;

    function dist(a,b){

        var dx = Math.pow((a.lat - b.lat), 2);
        var dy = Math.pow((a.lng - b.lng), 2);

        return Math.sqrt(dx+dy);
    }
    function geoloc(pos){

        pos = {lat: pos.coords.latitude, lng: pos.coords.longitude};
        var pers = cas.permissora;
        var closest = null;
        for(var i in pers){
            
            if( !closest || dist(pos,pers[closest]) > dist(pos,pers[i]) ){
                closest = i;
            }
        }
        sPers = {};
        sPers[closest] = true;
        gMap.setCenter(pers[closest].lat, pers[closest].lng);
        mount();
    }
    function geoerr(){
        sPers = {61:true, 62:true};
        mount();
    }

    function initialize(){
        cas.resizer.push(resizer);
        gMap = new GMaps({
            div: '#container',
            lat: -22.956831,
            lng: -43.182631,
            zoom: 13,
            streetViewControl: true,
            panControl: false,
            rotateControl: true,
            zoomControl: true,
            scaleControl: false,
            mapTypeControl: true
        });

        markerCluster = new MarkerClusterer(gMap.map, null, {gridSize: 50, maxZoom: 15});
        
        sPers = cas.get_pref('geo_pers');
        if(sPers){
            mount();
        }else{
            navigator.geolocation.getCurrentPosition(geoloc,geoerr,{
                enableHighAccuracy:false,
                timeout: 1000 * 60,
                maximumAge: 1000 * 60 * 5
            });
        }
    }
    function mount(){
        cas.set_pref('geo_pers', sPers);
        var pers = cas.permissora;
        var ul = $("<ul id='perlist'>").appendTo(topbar);
        for(var i in pers){
            var per = pers[i];

            var elem = 
                $("<li>"+per.abbr+"</li>").addClass('per'+per.grp)
                    .data('id',per.id)
                    .data('grp',per.grp)
                    .click(function(){

                        var per = $(this).data('id');
                        
                        if( !$(this).is('.selected') ){
                            sPers[per] = true;
                            $(this).addClass('selected');
                        }else if( Object.keys(sPers).length > 1 ){
                            delete sPers[per];
                            $(this).removeClass('selected');
                        }

                        clearTimeout(geoT);
                        geoT = setTimeout(reload, 1000);
                    }).appendTo(ul);

            if(sPers[per.id]){
                elem.addClass('selected');
            }
        }
        $('#filterCheck,#agCheck,#pCheck,#outrosCheck').change(function(){
            clearTimeout(geoT);
            geoT = setTimeout(reload, 1000);
        });

        reload();
        setInterval(reload, RELOAD_INTERVAL);
        roll();
    }
    var geoLock = false;
    function reload(){
        if(geoLock){
            return;
        }
        clearTimeout(geoT);
        geoLock = true;
        cas.set_pref('geo_pers', sPers);
        cas.ajaxer({
            method: 'GET',
            sendme: {
                pers: sPers,
                filter: $('#filterCheck').is(':checked'),
                agendada: $('#agCheck').is(':checked'),
                pendente: $('#pCheck').is(':checked'),
                outros: $('#outrosCheck').is(':checked')
            },
            sendto: 'geomon/recs',
            complete: function(){
                geoLock = false;
            },
            error: function(){
                geoLock = false;
                reload();
            },
            andthen: function(result){

                var ordens = result.data.ordens;
                geoQueue = [];

                for(var i in os){
                    if(!ordens[i]){
                        if(os[i].marker){
                            markerCluster.removeMarker(os[i].marker);
                            os[i].marker.setMap(null);
                            delete os[i].marker;
                        }
                        delete os[i];
                    }else{
                        ordens[i].status = os[i].status;
                    }
                }

                parseOrdens(ordens);
            }
        });
    }
    $('#agCheck').change(function(){
        if( !$(this).is(':checked') && !$('#pCheck').is(':checked') && !$('#outrosCheck').is(':checked') ){
            $('#pCheck').prop('checked',true);
        }
    });
    $('#pCheck').change(function(){
        if( !$(this).is(':checked') && !$('#agCheck').is(':checked') && !$('#outrosCheck').is(':checked') ){
            $('#agCheck').prop('checked',true);
        }
    });
    $('#outrosCheck').change(function(){
        if( !$(this).is(':checked') && !$('#agCheck').is(':checked') && !$('#pCheck').is(':checked') ){
            $('#agCheck,#pCheck').prop('checked',true);
        }
    });
    function parseOrdens(ordens){
        for(var i in ordens){
            if(os[i]){
                continue;
            }
            var ordem = ordens[i];
            
            if(ordem.lat){
                var latLng = new google.maps.LatLng(ordem.lat, ordem.lng);
                markOrdem(ordem,latLng);
            }else{
                geoQueue.push(ordem);
            }

            
            os[i] = ordem;
        }
    }
    function getRandom(min, max) {
        return min + Math.floor(Math.random() * (max - min + 1));
    }
    var geoQueue = [];
    function geoService(){
        var ordem = geoQueue.shift();
        if(!ordem){
            return;
        }
        GMaps.geocode({
              address: ordem.end+", "+ordem.bairro+" - "+ordem.cidade+", "+ordem.uf+", Brazil",
              callback: function(results, status) {
                
                if ( status === 'OK' ) {
                    var latLng = results[0].geometry.location;

                    cas.ajaxer({
                        sendme: {
                            lat: latLng.lat(), 
                            lng: latLng.lng(),
                            end: ordem.end, 
                            cep: ordem.cep
                        },sendto:'geomon/geo_cache'
                    });
                }else{
                    var pers = cas.permissora;
                    var latLng = new google.maps.LatLng(pers[ordem.per].lat, pers[ordem.per].lng);
                }
                markOrdem(ordem,latLng);
              }
            });
        console.log("Fila: "+geoQueue.length);
    }
    function markOrdem(ordem, latLng){
        var filter = {filter:{l: [ordem.node]}};
        var url = 'eventos#'+cas.hashbangify(filter);
        var marker = gMap.createMarker({
            lat: latLng.lat(),
            lng: latLng.lng(),
            details: ordem,
            infoWindow: {
                content:
                    "<div class='geoinfowindow'>"+
                        "<a target='_blank' href='os#os="+ordem.os+"&per="+ordem.per+"&svc="+ordem.svc+"'>"+
                            "<h3>"+ordem.assinante+"</h3>"+
                        "</a>"+
                        "<b>"+ordem.ingr+" - <i>"+ordem.falha+"</i></b><br>"+
                        ordem.end+", "+ordem.bairro+"<br>"+
                        "<b>"+ordem.status.toUpperCase()+"</b>, <a target='_blank' href='"+url+"'>"+ordem.node+"</a> - "+ordem.cep+
                    "</div>"
            }

          });
         markerCluster.addMarker(marker);
         ordem.marker = marker;
    }

    setInterval(geoService, 1000 * 2);
    
    setInterval(function(){
        if($('#autoRoll').is(':checked')){
            roll();
        }else{
            console.log('noroll');
        }
    }, ROLL_INTERVAL);
    
    
    
    function roll(){
        
        var selectedPers = [], ids = [];
        $('#perlist>li.selected').each(function(){
            var grp = $(this).data('grp'), id = $(this).data('id');
            if( selectedPers.indexOf(grp) < 0 ){
                selectedPers.push(grp);
                ids.push(id);
            }
        });
        
        if( selectedPers.length < 1 ){
            return;
        }
        
        var current = $('#perlist>li.selected.focus').data('grp');
        var next = 0;

        for( var i = 0; i< selectedPers.length; i++ ){
            if( selectedPers[i] === current && selectedPers[i+1] ){
                next = i+1;
                break;
            }
        }
        
        var per = cas.permissora[ids[next]];
        gMap.setCenter(per.lat, per.lng);

        $('#perlist>li.focus').removeClass('focus');
        $('#perlist>li.selected.per'+per.grp).addClass('focus');
    }

    $('#nextOne').click(function(){
        gMap.setZoom(12);
        roll();
    });
    function resizer(){
        $('#container').height(
            $(window).height() 
                - $('#head-wrapper').outerHeight() 
                - $('#foot').outerHeight() 
                - $('#topbar').outerHeight()
        );
    }
    resizer();
    initialize();
};