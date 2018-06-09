(function(){

function onReady () {
    var map, 
        heatMap, 
        markers = {},
        cmInfoWindow,
        cmInfoWindowListener,
        lockedForUpdate = false,
        OMS;

    function resizer () {
        $('#container').height(
            $(window).height() 
                - $('#head').outerHeight()
                - $('#foot').outerHeight());
    }
    
    function submitSearch (e) {
        serializeForm();
        e.preventDefault();
        updateBounds(true);
    }
    
    function serializeForm () {
        var $form = $('#controls_container>form'),
            filter = [];

        $form.find('.filterRow').each(function () {
            var $this = $(this), 
                f = [$this.data('filter')];

            f.push(parseFloat($this.find('[data-value]').val()));
            var $op = $this.find('[data-operator]');
            if ($op.length) f.push($op.val());
            filter.push(f);
        });
        
        if (!filter.length && cas.args.filter) {
            delete cas.args.filter;
            return cas.pushArgs();
        }

        cas.args.filter = filter;
        cas.pushArgs();
    }

    var fXTimeout;
    function fetchCMs (ne, sw, FORCE) {
        
        clearTimeout(fXTimeout);

        function XHR () {
            lockedForUpdate = true;

            cas.ajaxer({
                method: 'GET',
                sendme: {
                    ne: [ne.lat(), ne.lng()].join(':'),
                    sw: [sw.lat(), sw.lng()].join(':'),
                    filter: cas.args.filter
                },
                sendto: 'noc/cm',
                andthen: updateMapCms
            });
        }

        if (lockedForUpdate && !FORCE) {
            fXTimeout = setTimeout(function () { fetchCMs(ne, sw); }, 100);
            return;
        }

        XHR();
    }

    function updateBounds (FORCE) {
        
        if (!cas.nocMapReady) return;

        var bounds = map.getBounds(), 
            ne = bounds.getNorthEast(),
            sw = bounds.getSouthWest(),
            c = map.getCenter(),
            viewport = [
                cas.roundNumber(c.lat(), 4), 
                cas.roundNumber(c.lng(), 4), 
                map.getZoom()
            ];
        
        if (!_.isEqual(viewport, cas.args.viewport)) {
            cas.args.viewport = viewport;
            cas.pushArgs();
        }

        fetchCMs(ne, sw, FORCE === true);
    }

    cas.nocMapUpdate = updateBounds;

    function killMarker (m) {
        OMS.removeMarker(m);
        m.setMap(null);
    }

    function removeOldMarkers (m) {
        var pos = this.ids.indexOf(m.__info.cm);
        
        if (this.currentZoom <= 13 || pos < 0) {
            killMarker(m);

            if (cmInfoWindow && cmInfoWindow.__cm === m.__info.cm) {

                closeCmInfoWindow();
                cmInfoWindow = null;

            }
            delete markers[m.__info.cm];
        }
    }
    
    function sameColor (a, b) {
        return a.color === b.color && a.weight === b.weight;
    }

    function makeMarker (cm, callback) {
                        
        var found, opts,
            val = parseInt(cm.num_val, 10),
            point = new google.maps.LatLng(cm.lat, cm.lng),
            marker, img, pointCfg = {color: cm.status[0], weight: 1};
        
        cm.pointCfg = pointCfg;
        this.currentZoom > 13 && (marker = markers[cm.cm]);

        this.data.push({
            location: point,
            weight: pointCfg.weight
        });

        opts = {};
            
        switch (pointCfg.color) {
            case 'gray':
                opts.zIndex = 2;
                
                break;
            case 'yellow':
                opts.zIndex = 3;
                pointCfg.weight = 5;
                break;
            case 'red':
                opts.zIndex = 4;
                pointCfg.weight = 10;
                break;
        }


        if (this.currentZoom > 13 && !(marker && sameColor(pointCfg, marker.__info.pointCfg))) {
            
            img = {url: '/lib/img/noc/' + pointCfg.color + '_marker.png',
                size: new google.maps.Size(12, 12),
                origin: new google.maps.Point(0,0),
                anchor: new google.maps.Point(6, 6)
            };

            _.merge(opts, {
                position: point,
                map: map,
                icon: img
            });

            marker = new google.maps.Marker(opts);

            marker.__info = cm;

            OMS.addMarker(marker);
            if (cas.args.cm === cm.cm && (!cmInfoWindow || cmInfoWindow.__cm !== cm.cm)) selectMarker(marker);

            markers[cm.cm] = marker;

            _.isFunction(callback) && setTimeout(callback, 1);
        } else {
            _.isFunction(callback) && callback();
        }

        if (marker) {
            marker.__info = cm;
            marker.setTitle('coletado há ' + marker.__info.when)
        }
        
    }

    var countDownT;
    function setPollCountDown () {
        clearTimeout(countDownT);
        countDownT = setTimeout(updateBounds, 1000 * 60);
    }

    function updateMapCms (response) {
        
        var cms = response.data.cms, 
            data = [], 
            currentZoom = map.getZoom();

        if (cms.length) {
            $('#cmCounter').text(cms.length + ' Cable Modem (s)');
        } else {
            $('#cmCounter').empty();
        }

        function drawHeatMap () {
            if (heatMap) heatMap.setMap(null);
            heatMap = new google.maps.visualization.HeatmapLayer({data: data});
            heatMap.setMap(map);
            lockedForUpdate = false;
            setPollCountDown();
        }
        
        _.forEach(markers, removeOldMarkers, {ids: _.map(cms, 'cm'), currentZoom: currentZoom});

        if (cms.length > 300 && currentZoom > 13) {
            async.eachSeries(cms,
                makeMarker.bind({data: data, currentZoom: currentZoom}),
                drawHeatMap
            );

        } else {
            _.forEach(cms, makeMarker, {data: data, currentZoom: currentZoom});
            drawHeatMap();
        }
        
        
    }

    function closeCmInfoWindow () {
        delete cas.args.cm;
           cas.pushArgs();

        if (cmInfoWindow) cmInfoWindow.close();
        cmInfoWindow = null;

        if (cmInfoWindowListener) google.maps.event.removeListener(cmInfoWindowListener);
        cmInfoWindowListener = null;
    }

    function mapPackage (pkg, key) {

        if (_.isArray(pkg)) return _.flatten(_.map(pkg, mapPackage));

        var info = [], modem = pkg.modem, t;
        
        if (pkg.packageDefinition) {
            info.push($('<tr><td>Pacote</td><td>' + pkg.packageDefinition.packageName + '</td></tr>'));
        }

        if (modem) {
            info.push($('<tr><td>Mac</td><td>' + modem.macAddress + '</td></tr>'));
        }

        return info;
    }
    function makeInfoWindow (response) {

        var info = response.data.info,
            statuses = response.data.last_status,
            marker = response.etc;

        cas.args.cm = marker.__info.cm;
        cas.pushArgs();

        var elem = $('<div class="cmInfoWindow">'), aux;

        aux = $("<h3 class='cmAddr'><span>" + info.address1 + " - " + info.city + "</span></h3>")
                .attr('title', info.address1 + " - " + info.city + 
                    (info.address2 ? ' [' + info.address2 + ']' : '')).appendTo(elem);

        
        elem.append("<h3>" + info.firstName + " " + info.lastName + "</h3>");
        
        aux = $('<table>').appendTo(elem);

        _.forEach(_.flatten(_.map(info.packageList, mapPackage)), 
            function ($this) {
                aux.append($this);
            });

        if (_.isArray(statuses) && statuses.length) {
            
            _.forEach(statuses, function (s) {
                var tr = $('<tr>');
                $('<td>').append('<label>' + s.timestamp + '</label>' + s.mib_name).appendTo(tr);
                var v = 
                    $('<td>')
                        .append(
                            s.status && s.status[1] ? s.status[1] : s.num_val || s.str_val
                        ).appendTo(tr);
                
                if (s.status && s.status[0]) v.addClass(s.status[0]);

                tr.appendTo(aux);
            });
        }
        

        if (cmInfoWindow) closeCmInfoWindow();

        cmInfoWindow = new google.maps.InfoWindow({
            content: elem[0],
        });

        cmInfoWindowListener = google.maps.event.addListener(cmInfoWindow, 'closeclick', closeCmInfoWindow);
        

        cmInfoWindow.open(map, marker);
        cmInfoWindow.__cm = marker.__info.cm;
    }

    function selectMarker (marker) {
        cas.hidethis('body');
        cas.ajaxer({
            method: 'GET',
            sendme: {
                id: marker.__info.cm
                //, real_time: event && event.ctrlKey ? 1 : 0
            },
            etc: marker,
            error: function (e) {/*cancel retry*/},
            complete: function () {
                cas.showthis('body');
            },
            sendto: 'noc/cm_info',
            andthen: makeInfoWindow
        });
    }

    function handleAutoSearchResult (event, result) {            
        if (!result.geometry || !result.geometry.location) return;
        
        map.panTo(result.geometry.location);
        map.setZoom(14);
        $('#autoSearch').val('');
    }

    function init() {
        
        var mapOptions;
        
        mapOptions = {
            zoom: 8,
            center: new google.maps.LatLng(-22.848658,-43.300933),
            panControl: false
        };

        map = new google.maps.Map(document.getElementById('map_container'), mapOptions);
        
        OMS = new OverlappingMarkerSpiderfier(map);

        OMS.addListener('click', selectMarker);

        if (cas.args.viewport) {
            
            var viewport = cas.args.viewport;
            var center = new google.maps.LatLng(viewport[0], viewport[1]);
            
            map.setCenter(center);
            map.setZoom(viewport[2]);
        }
        
        function loadFilterHandler () {
            map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push($('<div id=cmCounter>')[0]);

            var $search = $('<input id=autoSearch tabindex=1 placeholder="Buscar Localidade" type=text autocomplete=off />');
            
            map.controls[google.maps.ControlPosition.TOP_CENTER].push($search[0]);

            $search.geocomplete()
                .bind('geocode:result', handleAutoSearchResult);

            LazyLoad.js(['/lib/js/' + cas.src + '/noc.filter.js']);
            google.maps.event.addListener(map, 'idle', _.throttle(updateBounds, 300));
        }

        //wait till map is loaded
        google.maps.event.addListenerOnce(map, 'idle', loadFilterHandler);

        
        $('#controls_container>form').submit(submitSearch);

    }

    cas.resizer.push(resizer);
    resizer();
    init();}


cas.controller = function () { 
    LazyLoad.js([
            '/lib/js/ext/async.js',
            '//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js'
        ], onReady);
};

}());

