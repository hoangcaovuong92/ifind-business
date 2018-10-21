<?php 
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */
add_action('wp_ajax_nopriv_display_weather_today_info', 'ifind_display_weather_today_info_ajax');
add_action('wp_ajax_display_weather_today_info', 'ifind_display_weather_today_info_ajax');
if( !function_exists('ifind_display_weather_today_info_ajax') ){
	function ifind_display_weather_today_info_ajax() { 
		$lat = $_REQUEST['lat'];  
		$lng = $_REQUEST['lng'];  
		ifind_display_weather_today_info($lat, $lng);
		die(); //stop "0" from being output
	}
}