<?php
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */

/*---------------------------------------------------------------------------*/
/*								MAIN FUNCTION 								 */
/*---------------------------------------------------------------------------*/
if( !function_exists('ifind_get_weather_today_api') ){
	function ifind_get_weather_today_api($lat = 35, $lng = 139) { 
		/**
		* package: weather
		* var: api_key 		
		*/
		extract(tvlgiao_wpdance_get_data_package( 'weather' )); 
		$response = wp_remote_get( "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lng&appid=$api_key&units=metric" );
		$body = '';
		if ( is_array( $response ) ) {
			$header = $response['headers']; // array of http header lines
			$body = $response['body']; // use the content
		}
		return json_decode($body);
	}
}

if( !function_exists('ifind_display_weather_today_info') ){
	function ifind_display_weather_today_info($lat, $lng) { 
		$weather_data = ifind_get_weather_today_api($lat, $lng);
		if ($weather_data) {
			$name = $weather_data->name;
			$icon = $weather_data->weather[0]->icon;
			$description = $weather_data->weather[0]->description;
			$humidity = $weather_data->main->humidity .'%'; //độ ẩm
			$temp = $weather_data->main->temp .'°c'; //nhiệt độ
			$wind_speed = $weather_data->wind->speed .'meter/sec';
			?>
			<div class="today day">
				<div class="ifind-weather-icon"><img class="wow pulse" data-wow-iteration="600" src="//openweathermap.org/img/w/<?php echo $icon; ?>.png"></div>
				<div class="ifind-weather-content">
					<span><strong><?php //echo $name; ?> <?php esc_html_e( 'Current weather:', 'ifind' ); ?></strong></span>
					<span><strong><?php echo $description; ?></strong></span>
					<span><?php esc_html_e( 'Temperature:', 'ifind' ); ?> <?php echo $temp; ?></span>
					<span><?php esc_html_e( 'Wind Speed:', 'ifind' ); ?> <?php echo $wind_speed; ?></span>
					<span><?php esc_html_e( 'Humidity:', 'ifind' ); ?> <?php echo $humidity; ?></span>
				</div>
			</div>
		<?php
		}
	}
}

// Converting Fahrenheit to Celsius (F => C)
if(!function_exists ('ifind_sanitize_email_content')){
	function ifind_sanitize_html_content($input){
		$message = '';
		$message .= "<!DOCTYPE html>";
		$message .= '<html><head><meta http-equiv="Content-Type" content="text/html charset=UTF-8" /></head>';
		$message .= '<body>';
		$message .= $input;
		$message .= "</body>";
		$message .= "</html>";
		return $message;
	}
}

// Converting Fahrenheit to Celsius (F => C)
if(!function_exists ('ifind_fahrenheit_to_elsius')){
	function ifind_fahrenheit_to_elsius($input){
		return ($input - 32) / 1.8;
	}
}

if(!function_exists ('ifind_removeSpecialCharacter')){
	function ifind_removeSpecialCharacter($value){
		$title = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), ' ', $value);
		return $title;
	}
}

if(!function_exists ('ifind_get_secret_key')){
	function ifind_get_secret_key(){
		return get_option('ifind_secret_key', '');
	}
}
if(!function_exists ('ifind_update_secret_key')){
	function ifind_update_secret_key($value){
		update_option('ifind_secret_key', $value);
	}
}

if(!function_exists ('ifind_generateRandomString')){
	function ifind_generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

if(!function_exists ('ifind_get_timestamp_by_timezone')){
	function ifind_get_current_timestamp_by_timezone($timezone = 'Australia/Brisbane') {
		$date = new DateTime(null, new DateTimeZone($timezone));
		return ($date->getTimestamp() + $date->getOffset());
	}
}

if(!function_exists ('ifind_convert_timestamp_to_time')){
	function ifind_convert_timestamp_to_time($timestamp, $datetimeFormat = 'Y-m-d H:i:s') {
		if ($timestamp) {
			$date = new \DateTime();
			// If you must have use time zones
			// $date = new \DateTime('now', new \DateTimeZone('Europe/Helsinki'));
			$date->setTimestamp($timestamp);
			$return = $date->format($datetimeFormat);
		} else {
			$return = __("Unlimited", 'ifind');
		}
		
		return $return;
	}
}

// timezone for one NY co-ordinate : echo ifind_get_nearest_timezone(40.772222,-74.164581);
// more faster and accurate if you can pass the country code : echo ifind_get_nearest_timezone(40.772222, -74.164581, 'US');
if(!function_exists ('ifind_get_nearest_timezone')){
	function ifind_get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
		$timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
										: DateTimeZone::listIdentifiers();

		if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

			$time_zone = '';
			$tz_distance = 0;

			//only one identifier?
			if (count($timezone_ids) == 1) {
				$time_zone = $timezone_ids[0];
			} else {

				foreach($timezone_ids as $timezone_id) {
					$timezone = new DateTimeZone($timezone_id);
					$location = $timezone->getLocation();
					$tz_lat   = $location['latitude'];
					$tz_long  = $location['longitude'];

					$theta    = $cur_long - $tz_long;
					$distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat))) 
					+ (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
					$distance = acos($distance);
					$distance = abs(rad2deg($distance));
					// echo '<br />'.$timezone_id.' '.$distance; 

					if (!$time_zone || $tz_distance > $distance) {
						$time_zone   = $timezone_id;
						$tz_distance = $distance;
					} 

				}
			}
			return  $time_zone;
		}
		return 'unknown';
	}
}


// Function to get the client IP address
function ifind_get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Get List terms of taxonomy
if(!function_exists ('ifind_get_list_category')){
	function ifind_get_list_category($taxonomy = 'category', $options = array()){
		$list_categories = array();
		$args = array(
			'hide_empty' 	=> 0
		);
		$args = wp_parse_args( $options, $args );
		$categories = get_terms( $taxonomy, $args );
		if (!is_wp_error($categories) && count($categories) > 0) {
			foreach ($categories as $category ) {
				$list_categories[$category->term_id]['name'] = html_entity_decode( $category->name, ENT_QUOTES, 'UTF-8' ).' (' . $category->count . ' items)';
				$list_categories[$category->term_id]['name'] = html_entity_decode( $category->name, ENT_QUOTES, 'UTF-8' ).' (' . $category->count . ' items)';
			}
		}
		wp_reset_postdata();
		return $list_categories;
	}
}

// Get list video file to header
//add_action('wp_head','ifind_include_list_video_id_to_header',5); 
if(!function_exists ('ifind_include_list_video_id_to_header')){
	function ifind_include_list_video_id_to_header(){
		$location_metadata = ifind_get_list_business_location(get_the_ID());
		$list_business = $location_metadata['list_business'];
		$list_video_link = array();
		if (is_array($list_business)) {
			foreach ($list_business as $business_id) {
				$info_banner_type = ifind_get_post_custom_metadata($business_id, 'business', 'info_banner_type');
				$video_file = ifind_get_post_custom_metadata($business_id, 'business', 'video_file');
				if (($info_banner_type === 'video-file' || $info_banner_type ===  'content-file-and-video') && $video_file) { 
					$list_video_link[] = $video_file;
				}
			}
		}
		return $list_video_link;
	}
}


// Get List terms of taxonomy
if(!function_exists ('ifind_get_list_business_location')){
	function ifind_get_list_business_location($location_id){
		$list_business_location = ifind_get_post_custom_metadata($location_id, 'location');
		return $list_business_location;
	}
}

// Get List terms of taxonomy
if(!function_exists ('ifind_get_list_posts')){
	function ifind_get_list_posts($post_type, $list_post_id, $taxonomy, $term_id, $posts_per_page = -1){
		global $post;
		$args = array(
			'post_type'			=> $post_type,
			'post_status'		=> 'publish',
			'post__in' 			=> $list_post_id,
			'posts_per_page' 	=> $posts_per_page,
		);
		if ($taxonomy && $term_id) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'id', //get by slug or term_id
					'terms' => $term_id,
					'include_children' => true,
			  		'operator' => 'IN'
				)
			);
		}
		$data_array = array();
		$data = new WP_Query($args);
		if( $data->have_posts() ){
			while( $data->have_posts() ){
				$data->the_post();
				$link = sprintf(' <a target="_blank" href="%s">'.__('Edit', 'ifind').'</a>', get_edit_post_link( $post->ID ));
				$data_array[$post->ID] = html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' ).$link;
			}
		}else{
			//$data_array[] = sprintf(__( "Please add data for \"%s\" before", 'ifind' ), $post_type);
		}
		wp_reset_postdata();
		return $data_array;
	}
}

// Get List terms of taxonomy
if(!function_exists ('ifind_get_metadata')){
	function ifind_get_post_custom_metadata($post_id, $post_type, $key = ''){
		$meta_data = unserialize(get_post_meta( $post_id, $post_type.'_meta_data', true ));
		if($key){
			$return_data = (!empty($meta_data[$post_type][$key])) ? $meta_data[$post_type][$key] : '';
		}else{
			$return_data = $meta_data[$post_type];
		}
		return $return_data;
	}
}

// Get List terms of taxonomy
if(!function_exists ('ifind_get_metadata')){
	function ifind_business_custom_metadata($location_id){
		$list_business = ifind_get_post_custom_metadata($location_id, 'location', 'list_business');
		$list_media = array();
		if (count($list_business) > 0) {
			foreach ($list_business as $business_id) {
				if (get_post_status( $business_id ) === 'publish') {
					$business_meta_data = ifind_get_post_custom_metadata($business_id, 'business');
					$logo = $business_meta_data['logo'];
					$small_banner = $business_meta_data['small_banner'];
					$large_banner = $business_meta_data['large_banner'];
					$info_banner_type = $business_meta_data['info_banner_type'];
					$info_banner = $business_meta_data['info_banner'];
					$info_banner_file = $business_meta_data['info_banner_file'];
					$youtube_video_id = $business_meta_data['youtube_video_id'];
					$list_media[$business_id] = array(
						'logo' => $logo,
						'small_banner' => $small_banner,
						'large_banner' => $large_banner,
						'info_banner_type' => $info_banner_type,
						'info_banner' => $info_banner,
						'info_banner_file' => $info_banner_file,
						'youtube_video_id' => $youtube_video_id,
						'video_type' => $youtube_video_id ? true : false,
					);
				}
			}
		}
		return $list_media;
	}
}

// HTML before main content
add_action('tvlgiao_wpdance_before_main_content','tvlgiao_wpdance_content_before_main_content',10);
if(!function_exists ('tvlgiao_wpdance_content_before_main_content')){
	function tvlgiao_wpdance_content_before_main_content(){ ?>
		<div id="main-content" class="main-content">
	<?php 
	}
}

// HTML after main content
add_action('tvlgiao_wpdance_after_main_content','tvlgiao_wpdance_content_after_main_content',10);
if(!function_exists ('tvlgiao_wpdance_content_after_main_content')){
	function tvlgiao_wpdance_content_after_main_content(){ ?>
		</div><!-- End main-content -->
	<?php 
	}
}

// Get global data
if(!function_exists ('tvlgiao_wpdance_get_post_by_global')){
	function tvlgiao_wpdance_get_post_by_global(){
		global $post;
		if ($post) {
			return $post->ID;
		}
	}
}


// Tablet and mobile device detection
// Source : https://mobiforge.com/design-development/tablet-and-mobile-device-detection-php
if(!function_exists ('tvlgiao_wpdance_is_mobile_or_tablet')){
	function tvlgiao_wpdance_is_mobile_or_tablet() {
		$tablet_browser = 0;
		$mobile_browser = 0;
		
		if (wp_is_mobile()) {
			$mobile_browser++;
		}

		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		    $tablet_browser++;
		}
		 
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		    $mobile_browser++;
		}
		 
		if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		    $mobile_browser++;
		}
		 
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
		    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		    'newt','noki','palm','pana','pant','phil','play','port','prox',
		    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		    'wapr','webc','winw','winw','xda ','xda-');
		 
		if (in_array($mobile_ua,$mobile_agents)) {
		    $mobile_browser++;
		}
		 
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
		    $mobile_browser++;
		    //Check for tablets on opera mini alternative headers
		    $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
		    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
		      	$tablet_browser++;
		    }
		}

		if ($tablet_browser > 0 || $mobile_browser > 0) {
		   return true;
		}else {
		   return false;
		}  
	}
}

// Minify CSS
if ( ! function_exists( 'tvlgiao_wpdance_minify_css' ) ) {
	function tvlgiao_wpdance_minify_css( $content ) {
	    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
	    $content = str_replace(["\r\n","\r","\n","\t",'  ','    ','     '], '', $content);
	    $content = preg_replace(['(( )+{)','({( )+)'], '{', $content);
	    $content = preg_replace(['(( )+})','(}( )+)','(;( )*})'], '}', $content);
	    $content = preg_replace(['(;( )+)','(( )+;)'], ';', $content);
	    return $content;
	}
}

// Minify JS
if ( ! function_exists( 'tvlgiao_wpdance_minify_js' ) ) {
	function tvlgiao_wpdance_minify_js( $content ) {
	    $content = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $content);
	    $content = str_replace(["\r\n","\r","\t","\n",'  ','    ','     '], '', $content);
	    $content = preg_replace(['(( )+\))','(\)( )+)'], ')', $content);
	    return $content;
	}
}

/* Get array post name (autocomplete search) */
if(!function_exists ('tvlgiao_wpdance_get_array_post_name')){
	function tvlgiao_wpdance_get_array_post_name($post_type = 'post', $json = true, $ppp = -1){ 
		$args 			= array(
			'post_type'			=> $post_type,
			'posts_per_page'	=> $ppp,
		);
		$post_name 		= array();
		$posts_array 	= get_posts( $args );
		if (count($posts_array) > 0) {
			foreach ($posts_array as $post) {
				$post_name[] = addslashes($post->post_title);
			}
		}
		return ($json) ? json_encode($post_name) : $post_name;
	}
}  

/* Get current URL */
if(!function_exists ('tvlgiao_wpdance_get_current_url')){
	function tvlgiao_wpdance_get_current_url(){ 
		$current_url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		$current_url = htmlspecialchars( $current_url, ENT_QUOTES, 'UTF-8' );
		$current_url = explode('?', $current_url);
		return $current_url[0];
	}
} 

?>