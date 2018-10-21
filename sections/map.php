<?php
$location_meta_data = ifind_get_post_custom_metadata(get_the_ID(), 'location');
$lat = $location_meta_data['location_data']['lat'];
$lng = $location_meta_data['location_data']['lng'];
$address = $location_meta_data['location_data']['address'];

$list_business_location = ifind_get_list_business_location(get_the_ID());
//var_dump($list_business_location);
?>

<div class="ifind-map-select-location">
    <div id="select_location_map"></div>
    <div id="infowindow-content">
        <img src="" width="16" height="16" id="place-icon">
        <span id="place-name" class="title"></span><br>
        <span id="place-address"></span>
    </div>
</div>
<?php 
    /**
    * package: google_map
    * var: api_key 		
    * var: zoom 	
    * var: time_set_map 	
    */
    extract(tvlgiao_wpdance_get_data_package( 'google_map' )); 
    $google_map_url = "//maps.googleapis.com/maps/api/js";
    $google_map_url = add_query_arg( array(
        'key' => $api_key,
        'libraries' => 'places,geometry',
        'callback' => 'google_map_admin_script',
        'sensor' => 'false',
    ), $google_map_url );
?>
<script>
    if (typeof google_map_admin_script != 'function') {
        function google_map_admin_script() {
            jQuery(document).ready(function ($) {
                var map;
                var markers = []; //list position markers (change by business group)
                var positions = []; //list all positon of business
                var directionsDisplay;
                var directionsService;
                var infowindow;
                var stepDisplay;
                var timer;

                jQuery.each( jQuery('.business-geocoding-link'), function( i, el ) {
                    positions.push({
                        lat: jQuery(el).data('lat'), 
                        lng: jQuery(el).data('lng'), 
                        icon: jQuery(el).data('icon'),
                        info: jQuery(el).parents('.ifind-business-item').find('.ifind-business-desc').html(),
                    });
                });
                //console.log(positions);
                
                // This example requires the Places library. Include the libraries=places
                // parameter when you first load the API. For example:
                // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
                var lat = <?php echo $lat; ?>,
                    lng = <?php echo $lng; ?>;

                var locationLatlng = new google.maps.LatLng(lat, lng);
                
                map = new google.maps.Map(document.getElementById('select_location_map'), {
                    center: locationLatlng,
                    zoom: 12,
                    fullscreenControl: false,
                });

                directionsService = new google.maps.DirectionsService();    
                directionsDisplay = new google.maps.DirectionsRenderer({map: map});
                directionsDisplay.setOptions( { suppressMarkers: true } );
                infowindow = new google.maps.InfoWindow();

                //If default position changed
                map.addListener('center_changed', function() {
                    // a bit seconds after the center of the map has changed, pan back to the marker.
                    jQuery(document).on('mousemove', function (event) {
                        if (timer) clearTimeout(timer);
                        timer = setTimeout(function () {
                            resetMapStatus();
                        }, <?php echo $time_set_map; ?>);
                    });
                });

                //var infowindow = new google.maps.InfoWindow();
                //var infowindowContent = document.getElementById('infowindow-content');
                //infowindow.setContent(infowindowContent);

                var locationCenterMarker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29),
                    position: locationLatlng,
                    icon: '<?php echo TVLGIAO_WPDANCE_THEME_IMAGES; ?>/marker.png',
                });

                locationCenterMarker.addListener('click', function() {
                    var content =   '<div class="heading"><?php the_title(); ?></div>'+
                                    '<div class="ifind-business-desc-content"><?php echo $address; ?></div>';
                    markerClick(locationCenterMarker, content, infowindow);
                });

                for (var i = 0; i < positions.length; ++i) {
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(positions[i]['lat'], positions[i]['lng']),
                        map: map
                    });
                    
                    var content = positions[i]['info'];
                    markerClick(marker, content, infowindow);
                }

                // Directions
                jQuery('.business-direction-link').on('click', function(e) {
                    e.preventDefault();
                    
                    var lat = jQuery(this).data('lat');
                    var lng = jQuery(this).data('lng');
                    var latlng = new google.maps.LatLng(lat, lng);
                    calculateAndDisplayRoute(latlng, directionsService, directionsDisplay);
                });
                
                //Close directions, back
                jQuery('.directions-close, .business-back-link').on('click', function(e) {
                    resetMapStatus();
                });

                function markerClick(marker, content, infowindow) {
                    var contentString = '<div id="ifind-infoWindow-content">'+content+'</div>';
                    displayInfoWindow(marker, contentString, infowindow);
                }
                
                function displayInfoWindow(marker, content, infowindow){
                    google.maps.event.addListener(marker,'click', (function(marker, content, infowindow){ 
                        return function() {
                            map.setZoom(17);
                            map.setCenter(marker.getPosition());
                            removeDirections(directionsDisplay);
                            infowindow.setContent(content);
                            infowindow.open(map, marker);
                            setTimeout(() => {
                                infowindow.close();
                                resetMapStatus();
                            }, 5000);
                        };
                    })(marker, content, infowindow)); 
                }

                function resetMapStatus() {    
                    map.setZoom(12);
                    map.setCenter(locationCenterMarker.getPosition());
                    removeDirections(directionsDisplay);
                }

                function removeDirections(directionsDisplay) {    
                    directionsDisplay.setDirections({routes: []});
                }
                
                function calculateAndDisplayRoute(latlng, directionsService, directionsDisplay) {    
                    directionsService.route({    
                        origin: latlng,    
                        destination: locationLatlng,    
                        travelMode: 'DRIVING',     
                    }, function(response, status) {    
                        if (status === google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);
                            showSteps(response);
                        } else {    
                            window.alert('Request for getting direction is failed due to ' + status);    
                        }    
                    });    
                }    

                function showSteps(directionResult) {
                    var myRoute = directionResult.routes[0].legs[0];
                    var distance = myRoute.distance.text;
                    var duration = myRoute.duration.text;
                    var start_address = myRoute.start_address;
                    var end_address = myRoute.end_address;
                    instructions = '<ol>';
                    var steps = '';

                    for (var i = 0; i < myRoute.steps.length; i++) {
                        steps += '<li>' + myRoute.steps[i].instructions + '</li>';
                    }
                    instructions += '<p class="steps">'+ steps +'</p>';
                    instructions += '</ol>';

                    jQuery('#map-directions').show();
                    jQuery('#map-directions').find('.map-directions-distance .content').html(distance);
                    jQuery('#map-directions').find('.map-directions-duration .content').html(duration);
                    jQuery('#map-directions').find('.map-directions-start-address .content').html(start_address);
                    jQuery('#map-directions').find('.map-directions-end-address .content').html(end_address);
                    jQuery('#map-directions').find('.map-directions-content').html(instructions);
                }

                function attachInstructionText(marker, text) {
                    google.maps.event.addListener(marker, 'click', function() {
                        stepDisplay.setContent(text);
                        stepDisplay.open(map, marker);
                    });
                }
           
                //Geocoding
                jQuery('.business-geocoding-link').on('click', function(e) {
                    e.preventDefault();
                    var lat = jQuery(this).data('lat');
                    var lng = jQuery(this).data('lng');
                    var icon = jQuery(this).data('icon');
                    var address = jQuery(this).data('address');
                    var latlng = new google.maps.LatLng(lat, lng);
                    removeDirections(directionsDisplay);
                    map.setZoom(17);
                    map.setCenter(latlng);
                });


                // Sets the map on all markers in the array.
                function setMapOnAll(map) {
                    for (var i = 0; i < markers.length; i++) {
                        markers[i].setMap(map);
                    }
                }

                // Removes the markers from the map, but keeps them in the array.
                function clearMarkers() {
                    setMapOnAll(null);
                }

                // Shows any markers currently in the array.
                function showMarkers() {
                    setMapOnAll(map);
                }

                // Deletes all markers in the array by removing references to them.
                function deleteMarkers() {
                    clearMarkers();
                    markers = [];
                }
            });
        }
    }
</script>
<script src="<?php echo $google_map_url; ?>"></script>