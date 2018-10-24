<?php 
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */
 
if(!function_exists ('tvlgiao_wpdance_get_data_package')){
	function tvlgiao_wpdance_get_data_package( $template ) {
		/* DATA SETTING */ 
    	$wd_default_data    = tvlgiao_wpdance_get_theme_option_default_data();
		$data 	= array();
		$pre 	= 'ifind_';
		switch ($template) {
			case 'google_map':
				$data['api_key']	  		= wd_get_theme_option($pre.'google_map_api_key', $wd_default_data['google_map']['default']['api_key']);  
				$data['zoom']	  			= wd_get_theme_option($pre.'google_map_zoom', $wd_default_data['google_map']['default']['zoom']);  
				$data['time_set_map']	  			= wd_get_theme_option($pre.'slider_timerShowPopup', $wd_default_data['slider']['default']['timerShowPopup']);  
				break;

			case 'weather':
				$data['api_key']	  		= wd_get_theme_option($pre.'weather_api_key', $wd_default_data['weather']['default']['api_key']);  
				$data['update_time']	  	= wd_get_theme_option($pre.'weather_update_time', $wd_default_data['weather']['default']['update_time']);  
				break;

			case 'slider':
				$data['timerShowPopup']	  		= wd_get_theme_option($pre.'slider_timerShowPopup', $wd_default_data['slider']['default']['timerShowPopup']);  
				$data['timerShowPopupViewingInfo']	= wd_get_theme_option($pre.'slider_timerShowPopupViewingInfo', $wd_default_data['slider']['default']['timerShowPopupViewingInfo']);  
				$data['timerDelayPopup']	  	= wd_get_theme_option($pre.'slider_timerDelayPopup', $wd_default_data['slider']['default']['timerDelayPopup']);  
				$data['bigAutoplaySpeed']	  	= wd_get_theme_option($pre.'slider_bigAutoplaySpeed', $wd_default_data['slider']['default']['bigAutoplaySpeed']);  
				$data['smallAutoplaySpeed']	  	= wd_get_theme_option($pre.'slider_smallAutoplaySpeed', $wd_default_data['slider']['default']['smallAutoplaySpeed']);  
				$data['numSliderBreak']	  		= wd_get_theme_option($pre.'slider_numSliderBreak', $wd_default_data['slider']['default']['numSliderBreak']);  
				$data['numFooterSliderItems']	= wd_get_theme_option($pre.'slider_numFooterSliderItems', $wd_default_data['slider']['default']['numFooterSliderItems']);  
				break;

			default:
				break;
		}
		
		return $data;
	}
}


if(!function_exists ('wd_get_theme_option')){
	function wd_get_theme_option( $keyname, $default_value = '', $type = 'normal' ) {
		global $tvlgiao_wpdance_theme_options;
		$data = '';
		if (isset($tvlgiao_wpdance_theme_options[$keyname])) {
			if ($type == 'image') {
				$data = $tvlgiao_wpdance_theme_options[$keyname]['url'];
			}elseif ($type == 'font') {
				$data = $tvlgiao_wpdance_theme_options[$keyname]['font-family'];
			}elseif ($type == 'height') {
				$data = $tvlgiao_wpdance_theme_options[$keyname]['height'];
			}elseif ($type == 'width') {
				$data = $tvlgiao_wpdance_theme_options[$keyname]['width'];
			}else{
				$data = $tvlgiao_wpdance_theme_options[$keyname];
			}
		}else{
			$data = $default_value;
		}
		return $data;
	}
}
