<?php 
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */
 
if(!function_exists ('tvlgiao_wpdance_get_theme_option_default_data')){
	function tvlgiao_wpdance_get_theme_option_default_data(){
		return array(
		    'google_map'       => array(
		        'default'       => array(
		            'api_key'     	=> 'AIzaSyAwJR7kylDCymhx59VKffi40Ez1qaU6aSo',
		            'zoom'    		=> 17,
		        ),
			),
			'weather'       => array(
		        'default'       => array(
					'api_key'     	=> 'b3e451be6f631c7934a9dccec4a6ab7d',
					'update_time'	=> 60000
		        ),
			),
			'slider'       => array(
		        'default'       => array(
					'timerShowPopup'    => 15000,
					'timerShowPopupViewingInfo'    => 60000,
					'timerDelayPopup'	=> 15000,
					'bigAutoplaySpeed'	=> 10000,
					'smallAutoplaySpeed' => 10000,
					'numSliderBreak' 	=> 3,
					'numFooterSliderItems'=> 1,
		        ),
		    ),
		);
	}
}