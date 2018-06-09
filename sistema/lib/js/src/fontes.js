cas.controller = function(){
    var gMap, map, container = $('#container'), markerCluster, fontes = {}, searchMarker;
    function resizer(){
        container.height(
            $(this).height()
            - ($('#head-wrapper').outerHeight() + $('#foot').outerHeight())
            - $('#topbar').outerHeight()
        );
    }
    function boot(){
        resizer();
        gMap = new GMaps({
            div: '#map',
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
        map = gMap.map;
        
        $('#s').geocomplete()
                .bind('geocode:result', function(event, result){
                    
                    if(!result.geometry || !result.geometry.location){
                        return;
                    }
                    
                    killSearchMarker();
                    
                    searchMarker = gMap.createMarker({
                        lat: result.geometry.location.lat(), 
                        lng: result.geometry.location.lng(),
                        infoWindow: {
                            content: 
                                "<p class='infoWindowAddr'>" +
                                    ( (result.adr_address)?result.adr_address:result.formatted_address ) +
                                "</p>"
                        }
                    });
                    markerCluster.addMarker(searchMarker);
                    google.maps.event.addListener(searchMarker.infoWindow, 'closeclick', killSearchMarker);
                    
                    map.panTo(result.geometry.location);
                    map.setZoom(13);
                });
        
        zoomBind();
        geoCodeFonte();
    }
    function killSearchMarker () {
        if(!searchMarker){
            return;
        }
        markerCluster.removeMarker(searchMarker);
        searchMarker.setMap(null);
        searchMarker = null;
    }
    var boundT;
    function updateBounds(){
        var bounds = map.getBounds(), ne = bounds.getNorthEast(), sw = bounds.getSouthWest(), c = map.getCenter();
        cas.args.viewport = [
            cas.roundNumber(c.lat(), 4), 
            cas.roundNumber(c.lng(), 4), 
            map.getZoom()
        ];
        cas.pushArgs();
        cas.ajaxer({
            method: 'GET',
            sendme: {
                ne: [ne.lat(), ne.lng()].join(':'),
                sw: [sw.lat(), sw.lng()].join(':')
            },
            sendto: 'fontes/f',
            andthen: plotFontes
        });
    }
    function zoomBind(){
        $('#updatenow').click(function(){
            updateBounds();
        });
        
        if(cas.args.viewport){
            
            var viewport = cas.args.viewport;
            var center = new google.maps.LatLng(viewport[0], viewport[1]);
            
            map.setCenter(center);
            map.setZoom(viewport[2]);
        }
        
        markerCluster = new MarkerClusterer(map, null, {gridSize: 50, maxZoom: 15});
        
        google.maps.event.addListener(map, 'idle', function() {
            clearTimeout(boundT);
            boundT = setTimeout(updateBounds, 1000);
        });
    }
    function redrawFonte(f){

        if(f.marker){
            return;
        }
        
        var addr = 
            "<div class='geoinfowindow'>"+
                "<h3>Fonte "+f.nome+"</h3>"+
                "<p>"+f.end+", "+f.cidade+" - <b>"+f.node+"</b></p>"+
                "<p><strong>Latitude: </strong> "+f.lat+"<strong>, Longitude: </strong>"+f.lng+"</p>"+
            "</div>";
    
        var marker = gMap.createMarker({
            lat: f.lat, lng: f.lng,
            details: f,
            icon: '/lib/img/1397088466_status.png',
            infoWindow: {
                content: 
                    "<div class='geoinfowindow'>"+
                        "<h3>Fonte "+f.nome+"</h3>"+
                        "<p>"+f.end+", "+f.cidade+" - <b>"+f.node+"</b></p>"+
                        "<p><strong>Latitude: </strong> "+f.lat+"<strong>, Longitude: </strong>"+f.lng+"</p>"+
                    "</div>"
            }
        });
        
        markerCluster.addMarker(marker);
        f.marker = marker;

        google.maps.event.addListener(marker, 'click', function () {

            var fonte = this.details;
            if(fonte.realMarker){
                
                markerCluster.removeMarker(fonte.realMarker);
                fonte.realMarker.setMap(null);
                delete fonte.realMarker;
                
                fonte.line.setMap(null);
                delete fonte.line;
                
                return;
            }
            if(fonte.lat2){
                
                fonte.line = 
                    gMap.drawPolyline({
                        path: [[fonte.lat, fonte.lng], [fonte.lat2, fonte.lng2]],
                        strokeColor: '#30432F',
                        strokeOpacity: 0.5,
                        strokeWeight: 2
                    });
                var lengthInMeters = google.maps.geometry.spherical.computeLength(fonte.line.getPath());
                marker = gMap.createMarker({
                    lat: fonte.lat2, lng: fonte.lng2,
                    icon: '/lib/img/1397094917_Cursor_drag_arrow.png',
                    infoWindow: {
                        content: 
                            "<div class='geoinfowindow'>"+
                                "<h3>Fonte "+f.nome+"</h3>"+
                                "<p>"+f.end+", "+f.cidade+" - <b>"+f.node+"</b></p>"+
                                "<p><b style='color: red'>Distância de " + 
                                    ( (lengthInMeters > 1000)
                                        ? (lengthInMeters/1000).toFixed(2) + ' Km'
                                        : lengthInMeters.toFixed(2) + ' metros'
                                    ) + 
                                "</b></p>"+
                                "<p><strong>Latitude: </strong> "+f.lat2+"<strong>, Longitude: </strong>"+f.lng2+"</p>"+
                            "</div>"
                    }
                });

                markerCluster.addMarker(marker);
                fonte.realMarker = marker;
            }
            
        });
    }

    function killFonteMarkers(fonte){
        if (fonte.marker) {
            markerCluster.removeMarker(fonte.marker);
            fonte.marker.setMap(null);
            delete fonte.marker;
        }
        
        if (fonte.realMarker) {
            markerCluster.removeMarker(fonte.realMarker);
            fonte.realMarker.setMap(null);
            delete fonte.realMarker;
        }
        
        if (fonte.line) {
            fonte.line.setMap(null);
            delete fonte.line;
        }
    }
    function plotFontes(x){
        var n_fontes = x.data.fontes;
        var t = (new Date()).getTime();
        for (var i in n_fontes) {
            var f = n_fontes[i];
            if(!fontes[f.nome]){
                fontes[f.nome] = f;
            }
            
            f = fontes[f.nome];
            redrawFonte(f);
            f.updated = t || (new Date()).getTime();
        }
        for(var i in fontes){
            if(fontes[i].marker && fontes[i].updated < t){
                killFonteMarkers(fontes[i]);
                delete fontes[i];
            }
        }
        
        //console.log(markerCluster.getTotalMarkers() + ' markers');
    }
    var geoLoop;
    function geoCodeFonte(){
        
        if(!geoLoop){
            geoLoop = setInterval(geoCodeFonte, 1000 * 5);
        }
        
        var fonte = _.find(fontes, function(fonte){
            if(!fonte.lat2 && !fonte.geofail){
                return fonte;
            }
            return false;
        });
        if(!fonte){
            return;
        }
        
        GMaps.geocode({
            address: fonte.end + ' - '+fonte.cidade,
            callback: function(results, status) {
                if (status === 'OK') {
                    console.log('found '+fonte.nome+ ' [ '+ fonte.end + ' - '+fonte.cidade+' ] ');
                    
                    var latlng = results[0].geometry.location;
                    fonte.lat2 = latlng.lat();
                    fonte.lng2 = latlng.lng();
                    
                    cas.ajaxer({
                        sendme: {
                            nome: fonte.nome,
                            lat: latlng.lat(),
                            lng: latlng.lng()
                        },
                        sendto: 'fontes/sv_fonte'
                    });
                }else{
                    fonte.geofail = true;
                }
            }
        });
    }
    cas.resizer.push(resizer);
    //boot();
    LazyLoad.js('//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js', boot);
};