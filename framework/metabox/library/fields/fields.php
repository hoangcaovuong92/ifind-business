<?php 
if (!class_exists('iFind_Admin_Metabox_Fields')) {
	class iFind_Admin_Metabox_Fields{
		/**
		 * Refers to a single instance of this class.
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct(){}
        
        static function get_text_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "value" => "",
            );
            $data = wp_parse_args($data, $default); ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td><input type="text" class="wd-full-width" 
                    name="<?php echo $data['field_name']; ?>" 
                    value="<?php echo htmlspecialchars($data['value']); ?>" 
                    placeholder="<?php echo $data['placeholder']; ?>"/>
                </td>
            </tr>
            <?php
        }

        static function get_textarea_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "value" => "",
            );
            $data = wp_parse_args($data, $default); ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p> 
                </th>
                <td><textarea class="wd-full-width" 
                    name="<?php echo $data['field_name']; ?>" 
                    rows="10"
                    placeholder="<?php echo $data['placeholder']; ?>"><?php echo $data['value']; ?></textarea>
                </td>
            </tr>
            <?php
        }

        static function get_editor_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "value" => "",
            );
            $data = wp_parse_args($data, $default);
            $setting = array(
                'textarea_rows' => 4,
                'media_buttons' => false,
            );
            ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p> 
                </th>
                <td>
                    <!-- <textarea class="ifind-textarea-editor" name="<?php //echo $data['field_name']; ?>" rows="10"><?php //echo $data['value']; ?></textarea> -->
                    <?php wp_editor( $data['value'], $data['field_name'], $setting); ?>
                </td>
            </tr>
            <?php
        }

        static function get_select_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "options" => "",
                "value" => "",
            );
            $data = wp_parse_args($data, $default); ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td>
                    <select name="<?php echo $data['field_name']; ?>"">
                        <?php if ($data['options']) { ?>
                            <?php foreach ($data['options'] as $key => $value): ?>
                                <?php $selected = selected($data['value'], $key, false); ?>
                                <option value="<?php echo esc_html($key) ?>" <?php echo esc_attr($selected) ?>><?php echo esc_html($value) ?></option>
                            <?php endforeach; ?>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <?php
        }

        static function get_radio_field($data = array()){
            var_dump($data);
            $default = array(
                "title" => "",
                "desc" => "",
                "field_name" => "no_name_available",
                "options" => "",
                "value" => "",
            );
            $data = wp_parse_args($data, $default); ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td>
                    <?php if ($data['options']) { ?>
                        <div class="ifind-radio-list">
                            <?php foreach ($data['options'] as $key => $value): ?>
                                <?php 
                                $checked = (is_array($data['value']) && in_array($key, $data['value'])) ? "checked" : "";
                                $random_id 	= mt_rand();
                                ?>
                                <div class="ifind-radio-item">
                                    <input type="radio" id="ifind-radio-<?php echo $random_id; ?>" 
                                        name="<?php echo $data['field_name']; ?>" 
                                        value="<?php echo esc_html($key) ?>"
                                        <?php echo $checked; ?> />
                                    <label for="ifind-radio-<?php echo $random_id; ?>"><?php echo esc_html($value) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php } ?>
                </td>
            </tr>
            <?php
        }
        
        static function get_checkbox_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "field_name" => "no_name_available",
                "options" => "",
                "value" => "",
            );
            $data = wp_parse_args($data, $default); ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td>
                    <?php if ($data['options']) { ?>
                        <div class="ifind-checkbox-list">
                            <?php foreach ($data['options'] as $key => $value): ?>
                                <?php 
                                $checked = (is_array($data['value']) && in_array($key, $data['value'])) ? "checked" : "";
                                $random_id 	= mt_rand();
                                ?>
                                <div class="ifind-checkbox-item">
                                    <input type="checkbox" id="ifind-checkbox-<?php echo $random_id; ?>" 
                                        name="<?php echo $data['field_name']; ?>[]" 
                                        value="<?php echo esc_html($key) ?>"
                                        <?php echo $checked; ?> />
                                    <label for="ifind-checkbox-<?php echo $random_id; ?>"><?php echo $value ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php } ?>
                </td>
            </tr>
            <?php
        }

        static function get_qrcode_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "value" => array(
                    'qr_type' => 'url',
                    'qr_content' => ''
                ),
            );
            $random_id 	= mt_rand();
            $data = wp_parse_args($data, $default);
            $qr_type = $data['value']['qr_type'];
            $qr_content = $data['value']['qr_content'];
            ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td class="ifind-qrcode-wrap">
                    <div class="ifind-qrcode-col">
                        <label><?php esc_html_e('Type','ifind'); ?>:</label>
                        <select name="<?php echo $data['field_name']; ?>[qr_type]">
                            <option value="url" <?php echo ($qr_type === 'url') ? 'selected' : ''; ?>><?php esc_html_e('URL','ifind'); ?></option>
                            <!-- <option value="text" <?php echo ($qr_type === 'text') ? 'selected' : ''; ?>><?php esc_html_e('TEXT','ifind'); ?></option>
                            <option value="email" <?php echo ($qr_type === 'email') ? 'selected' : ''; ?>><?php esc_html_e('EMAIL','ifind'); ?></option>
                            <option value="sms" <?php echo ($qr_type === 'sms') ? 'selected' : ''; ?>><?php esc_html_e('SMS','ifind'); ?></option>
                            <option value="contact" <?php echo ($qr_type === 'contact') ? 'selected' : ''; ?>><?php esc_html_e('CONTACT','ifind'); ?></option>
                            <option value="content" <?php echo ($qr_type === 'content') ? 'selected' : ''; ?>><?php esc_html_e('CONTENT','ifind'); ?></option> -->
                        </select>
                    </div>
                    <div class="ifind-qrcode-col">
                        <label><?php esc_html_e('CONTENT','ifind'); ?>:</label>
                        <input type="text" class="wd-full-width" 
                                name="<?php echo $data['field_name']; ?>[qr_content]" 
                                value="<?php echo htmlspecialchars($qr_content); ?>" 
                                placeholder="<?php echo $data['placeholder']; ?>"/>
                    </div>
                </td>
            </tr>
            <?php
        }

        static function get_image_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "value" => "",
                "default" => TVLGIAO_WPDANCE_THEME_IMAGES."/default.jpg",
            );
            $random_id 	= mt_rand();
            $data = wp_parse_args($data, $default);
            ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td>
                    <img class="ifind-image-preview" id="image-preview-<?php echo $random_id; ?>" src="<?php echo ($data['value'] && is_numeric($data['value'])) ? esc_url(wp_get_attachment_url($data['value'])) : $data['default']; ?>"  width="100%" />
                    <input type="hidden" name="<?php echo $data['field_name']; ?>" id="<?php echo 'image-value-'.$random_id; ?>" value="<?php echo ($data['value'] && is_numeric($data['value'])) ? esc_attr($data['value'] ) : ""; ?>" />

                    <a 	class="wd_media_lib_select_btn button button-primary button-large" 
                        data-type="image"
                        data-image_value="<?php echo 'image-value-'.$random_id; ?>" 
                        data-image_preview="image-preview-<?php echo $random_id; ?>">
                        <?php esc_html_e('Select Image File','ifind'); ?>
                    </a>

                    <a 	class="wd_media_lib_clear_btn button" 
                        data-image_value="<?php echo 'image-value-'.$random_id; ?>" 
                        data-image_preview="image-preview-<?php echo $random_id; ?>" 
                        data-image_default=<?php echo $data['default']; ?>>
                        <?php esc_html_e('Reset','ifind'); ?>
                    </a>
                </td>
            </tr>
            <?php
        }

        static function get_video_file_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "value" => "",
                "default" => TVLGIAO_WPDANCE_THEME_IMAGES."/default.jpg",
            );
            $random_id 	= mt_rand();
            $data = wp_parse_args($data, $default);
            ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td>
                    <input readonly type="text" class="wd-full-width"
                            name="<?php echo $data['field_name']; ?>" 
                            id="<?php echo 'image-value-'.$random_id; ?>" 
                            value="<?php echo esc_url($data['value'] ) ?>" />

                    <a 	class="wd_media_lib_select_btn button button-primary button-large" 
                        data-type="video"
                        data-image_value="<?php echo 'image-value-'.$random_id; ?>" 
                        data-image_preview="image-preview-<?php echo $random_id; ?>">
                        <?php esc_html_e('Select Video File','ifind'); ?>
                    </a>

                    <a 	class="wd_media_lib_clear_btn button" 
                        data-image_value="<?php echo 'image-value-'.$random_id; ?>" 
                        data-image_preview="image-preview-<?php echo $random_id; ?>" 
                        data-image_default=<?php echo $data['default']; ?>>
                        <?php esc_html_e('Reset','ifind'); ?>
                    </a>
                </td>
            </tr>
            <?php
        }

        static function get_map_field($data = array()){
            $default = array(
                "title" => "",
                "desc" => "",
                "placeholder" => "",
                "field_name" => "no_name_available",
                "value" => array(
                    "lat" => 10.762622, 
                    "lng" => 106.660172, 
                    "address" => "" 
                ),
            );
            $data = wp_parse_args($data, $default);
            $lat = $data['value']['lat'];
            $lng = $data['value']['lng'];
            $address = $data['value']['address'];
            ?>
            <tr>
                <th scope="row">
                    <label><?php echo $data['title']; ?>:</label>
                    <p class="description"><?php echo $data['desc']; ?></p>
                </th>
                <td>
                    <div class="ifind-map-select-location">

                        <div class="ifind-location_data">
                            <input type="hidden" id="ifind-location_data-lat" name="<?php echo $data['field_name']; ?>[lat]" value="<?php echo $lat; ?>"/>
                            <input type="hidden" id="ifind-location_data-lng" name="<?php echo $data['field_name']; ?>[lng]" value="<?php echo $lng; ?>"/>
                            <input type="hidden" id="ifind-location_data-address" name="<?php echo $data['field_name']; ?>[address]" value="<?php echo htmlspecialchars($address); ?>"/>
                        </div>
                        
                        <div class="pac-card" id="pac-card">
                            <div>
                                <div id="title">
                                    <?php echo __( 'Location Search', 'ifind' ); ?>
                                </div>
                                <div id="type-selector" class="pac-controls" style="display: none;">
                                    <input type="radio" name="type" id="changetype-all" checked="checked">
                                    <label for="changetype-all"><?php echo __( 'All', 'ifind' ); ?></label>
                                    <input type="radio" name="type" id="changetype-establishment">
                                    <label for="changetype-establishment"><?php echo __( 'Establishments', 'ifind' ); ?></label>
                                    <input type="radio" name="type" id="changetype-address">
                                    <label for="changetype-address"><?php echo __( 'Addresses', 'ifind' ); ?></label>
                                    <input type="radio" name="type" id="changetype-geocode">
                                    <label for="changetype-geocode"><?php echo __( 'Geocodes', 'ifind' ); ?></label>
                                </div>
                                <div id="strict-bounds-selector" class="pac-controls" style="display: none;">
                                    <input type="checkbox" id="use-strict-bounds" value="">
                                    <label for="use-strict-bounds"><?php echo __( 'Strict Bounds', 'ifind' ); ?></label>
                                </div>
                            </div>
                            <div id="pac-container">
                                <input id="pac-input" type="text" value="<?php echo $address; ?>" placeholder="<?php echo $data['placeholder']; ?>">
                            </div>
                        </div>
                        <div id="select_location_map"></div>
                        <div id="infowindow-content">
                            <img src="" width="16" height="16" id="place-icon">
                            <span id="place-name" class="title"></span><br>
                            <span id="place-address"></span>
                        </div>
                    </div>
                </td>
            </tr>
            <?php 
                /**
                * package: google_map
                * var: api_key 		
                * var: zoom 	
                */
                extract(tvlgiao_wpdance_get_data_package( 'google_map' )); 
                $google_map_url = "//maps.googleapis.com/maps/api/js";
                $google_map_url = add_query_arg( array(
                    'key' => $api_key,
                    'libraries' => 'places',
                    'callback' => 'google_map_admin_script'
                ), $google_map_url );

            ?>
            
            <script>
                if (typeof google_map_admin_script != 'function') {
                    function google_map_admin_script() {
                        var lat = <?php echo $lat; ?>,
                            lng = <?php echo $lng; ?>;
                        // This example requires the Places library. Include the libraries=places
                        // parameter when you first load the API. For example:
                        // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
                        var latlng = new google.maps.LatLng(lat, lng);
                        var map = new google.maps.Map(document.getElementById('select_location_map'), {
                            center: latlng,
                            zoom: <?php echo $zoom; ?>,
                        });

                        var geocoder = new google.maps.Geocoder();


                        var card = document.getElementById('pac-card');
                        var input = document.getElementById('pac-input');
                        var types = document.getElementById('type-selector');
                        var strictBounds = document.getElementById('strict-bounds-selector');

                        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

                        var autocomplete = new google.maps.places.Autocomplete(input);

                        // Bind the map's bounds (viewport) property to the autocomplete object,
                        // so that the autocomplete requests use the current map bounds for the
                        // bounds option in the request.
                        autocomplete.bindTo('bounds', map);

                        // Set the data fields to return when the user selects a place.
                        autocomplete.setFields(
                            ['address_components', 'geometry', 'icon', 'name']);

                        var infowindow = new google.maps.InfoWindow();
                        var infowindowContent = document.getElementById('infowindow-content');
                        infowindow.setContent(infowindowContent);

                        var marker = new google.maps.Marker({
                            map: map,
                            anchorPoint: new google.maps.Point(0, -29),
                            position: latlng,
                        });
                        var place = autocomplete.getPlace();

                        autocomplete.addListener('place_changed', function() {
                            infowindow.close();
                            marker.setVisible(false);
                            var place = autocomplete.getPlace();
                            
                            // Set info to save
                            document.getElementById('ifind-location_data-lat').value = place.geometry.location.lat();
                            document.getElementById('ifind-location_data-lng').value = place.geometry.location.lng();
                            document.getElementById('ifind-location_data-address').value = converterSpecialCharacter(document.getElementById('pac-input').value);

                            if (!place.geometry) {
                                // User entered the name of a Place that was not suggested and
                                // pressed the Enter key, or the Place Details request failed.
                                window.alert("No details available for input: '" + place.name + "'");
                                return;
                            }

                            // If the place has a geometry, then present it on a map.
                            if (place.geometry.viewport) {
                                map.fitBounds(place.geometry.viewport);
                            } else {
                                map.setCenter(place.geometry.location);
                                map.setZoom(17); // Why 17? Because it looks good.
                            }
                            marker.setPosition(place.geometry.location);
                            marker.setVisible(true);

                            var address = '';
                            if (place.address_components) {
                                address = [
                                    (place.address_components[0] && place.address_components[0].short_name || ''),
                                    (place.address_components[1] && place.address_components[1].short_name || ''),
                                    (place.address_components[2] && place.address_components[2].short_name || '')
                                ].join(' ');
                            }

                            infowindowContent.children['place-icon'].src = place.icon;
                            infowindowContent.children['place-name'].textContent = place.name;
                            infowindowContent.children['place-address'].textContent = address;
                            infowindow.open(map, marker);
                        });

                        function converterSpecialCharacter(str) {
                            var i = str.length,
                                aRet = [];
                            while (i--) {
                                var iC = str[i].charCodeAt();
                                if (iC < 65 || iC > 127 || (iC>90 && iC<97)) {
                                    aRet[i] = '&#'+iC+';';
                                } else {
                                    aRet[i] = str[i];
                                }
                            }
                            return aRet.join('');
                        }

                        // Sets a listener on a radio button to change the filter type on Places
                        // Autocomplete.
                        function setupClickListener(id, types) {
                            var radioButton = document.getElementById(id);
                            radioButton.addEventListener('click', function() {
                                autocomplete.setTypes(types);
                            });
                        }

                        setupClickListener('changetype-all', []);
                        setupClickListener('changetype-address', ['address']);
                        setupClickListener('changetype-establishment', ['establishment']);
                        setupClickListener('changetype-geocode', ['geocode']);

                        document.getElementById('use-strict-bounds')
                            .addEventListener('click', function() {
                                console.log('Checkbox clicked! New state=' + this.checked);
                                autocomplete.setOptions({
                                    strictBounds: this.checked
                                });
                            });
                    }
                }
                </script>
            <script src="<?php echo $google_map_url; ?>"></script>
        <?php 
        }
	}
	iFind_Admin_Metabox_Fields::get_instance();
}