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

add_action('wp_ajax_nopriv_send_directions_main', 'ifind_send_directions_main_ajax');
add_action('wp_ajax_send_directions_main', 'ifind_send_directions_main_ajax');
if( !function_exists('ifind_send_directions_main_ajax') ){
	function ifind_send_directions_main_ajax() { 
		$email_to = sanitize_email($_REQUEST['email']);
		$title = $_REQUEST['title'];
		$message = '';
		$message .= "<!DOCTYPE html>";
		$message .= '<html><head><meta http-equiv="Content-Type" content="text/html charset=UTF-8" /></head>';
		$message .= '<body>';
		$message .= $_REQUEST['message'];
		$message .= "</body>";
		$message .= "</html>";

		// message that will be displayed when everything is OK :)
		$okMessage = sprintf(__("Directions have been sent to your email: %s!", 'ifind'), $email_to);

		// If something goes wrong, we will display this message.
		$errorMessage = __("Error! An error occurred. Please try again later.:", 'ifind');

		//php mailer variables
		$from = get_option('admin_email');
		$subject = "[iFind] ".$title;
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=iso-8859-1',
			'X-Priority: 1 (Higuest)',
			'X-MSMail-Priority: High',
			'Importance: High',
			'From: '. $from,
			'Reply-To: ' . $from
		);
		 
	  	//Here put your Validation and send mail
		$sent = wp_mail($email_to, $subject, $message, $headers);
		if($sent) {
			$responseArray = array(
				'type' => 'success',
				'title' => __("Success!:", 'ifind'),
				'message' => $okMessage
			);
		}//message sent!
		else  {
			$responseArray = array(
				'type' => 'danger', 
				'title' => __("Error!", 'ifind'), 
				'message' => $errorMessage
			);
		}//message wasn't sent

		// if requested by AJAX request return JSON response
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$encoded = json_encode($responseArray);

			header('Content-Type: application/json');

			echo $encoded;
		}
		// else just display the message
		else {
			echo $responseArray['message'];
		}

		die(); //stop "0" from being output
	}
}

add_action('wp_ajax_nopriv_get_curent_secret_key', 'ifind_get_curent_secret_key_ajax');
add_action('wp_ajax_get_curent_secret_key', 'ifind_get_curent_secret_key_ajax');
if( !function_exists('ifind_get_curent_secret_key_ajax') ){
	function ifind_get_curent_secret_key_ajax() { 
		echo ifind_get_secret_key();
		die(); //stop "0" from being output
	}
}

// Ajax admin
add_action('wp_ajax_nopriv_reset_secret_key', 'ifind_reset_secret_key_ajax');
add_action('wp_ajax_reset_secret_key', 'ifind_reset_secret_key_ajax');
if( !function_exists('ifind_reset_secret_key_ajax') ){
	function ifind_reset_secret_key_ajax() { 
		$new_secret_key = 'ifind-secret-key-'.ifind_generateRandomString();
		ifind_update_secret_key($new_secret_key);
		die(); //stop "0" from being output
	}
}