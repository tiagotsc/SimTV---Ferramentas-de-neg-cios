cas.controller=function(){var map,container=$("#container");function boot(){resizer();$("#s").geocomplete({map:"#map",location:[-22.9568306,-43.1826308],mapOptions:{scrollwheel:true}});var groundOverlay=new google.maps.GroundOverlay("lib/maps/files/3000.jpeg",new google.maps.LatLngBounds(new google.maps.LatLng(-23.45495,-44.22212777777778),new google.maps.LatLng(-22.43794444444444,-42.23421111111112)),{opacity:.5});map=$("#s").geocomplete("map");groundOverlay.setMap(map)}function resizer(){container.height($(this).height()-($("#head-wrapper").outerHeight()+$("#foot").outerHeight())-$("#topbar").outerHeight())}$("#legend").disableSelection();cas.resizer.push(resizer);boot()};