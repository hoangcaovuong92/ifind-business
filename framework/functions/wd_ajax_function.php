<?php 
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */

add_action('wp_ajax_nopriv_update_business_click_counter', 'ifind_update_business_click_counter_ajax');
add_action('wp_ajax_update_business_click_counter', 'ifind_update_business_click_counter_ajax');
if( !function_exists('ifind_update_business_click_counter_ajax') ){
	function ifind_update_business_click_counter_ajax() { 
		$business_id = $_REQUEST['business_id'];  
		$location_id = $_REQUEST['location_id'];
		$counter_position = $_REQUEST['counter_position'];
		$timezone = $_REQUEST['timezone'];
		$current_timestamp = ifind_get_current_timestamp_by_timezone($timezone);
		$ip_address = ifind_get_client_ip();
		$click_info = array(
			'position' => $counter_position,
			'timestamp' => $current_timestamp,
			'location_id' => $location_id,
			'ip_address' => $ip_address,
		);
		ifind_set_click_counter( $location_id, $business_id, $click_info );
		//echo $timezone.': '.$current_timestamp.PHP_EOL;
		//echo ifind_convert_timestamp_to_time($current_timestamp);
		die(); //stop "0" from being output
	}
}

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

add_action('wp_ajax_nopriv_send_directions_mail', 'ifind_send_directions_mail_ajax');
add_action('wp_ajax_send_directions_mail', 'ifind_send_directions_mail_ajax');
if( !function_exists('ifind_send_directions_mail_ajax') ){
	function ifind_send_directions_mail_ajax() { 
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

add_action('wp_ajax_nopriv_load_table_bussiness_statistics', 'ifind_load_table_bussiness_statistics_ajax');
add_action('wp_ajax_load_table_bussiness_statistics', 'ifind_load_table_bussiness_statistics_ajax');
if( !function_exists('ifind_load_table_bussiness_statistics_ajax') ){
	function ifind_load_table_bussiness_statistics_ajax() { 
		$business_id = $_REQUEST['business_id'];
		$datepicker_from = $_REQUEST['datepicker_from'];
		$datepicker_to = $_REQUEST['datepicker_to'];
		ifind_get_click_counter( $business_id, $datepicker_from, $datepicker_to, 'table', true );
		die(); //stop "0" from being output
	}
}


add_action('wp_ajax_nopriv_send_statistics_mail', 'ifind_send_statistics_mail_ajax');
add_action('wp_ajax_send_statistics_mail', 'ifind_send_statistics_mail_ajax');
if( !function_exists('ifind_send_statistics_mail_ajax') ){
	function ifind_send_statistics_mail_ajax() { 
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
		$okMessage = sprintf(__("Statistics have been sent to your email: %s!", 'ifind'), $email_to);

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


add_action('wp_ajax_nopriv_get_contact_form', 'ifind_get_contact_form_ajax');
add_action('wp_ajax_get_contact_form', 'ifind_get_contact_form_ajax');
if( !function_exists('ifind_get_contact_form_ajax') ){
	function ifind_get_contact_form_ajax() { 
		$email_to = $_REQUEST['email_to'];
		$cc_to = $_REQUEST['cc_to'];
		$qr_link = ($_REQUEST['qr_link']); ?>

		<div id="ifind-contact-form" class="ifind-contact-form">
			<?php if ($email_to) { ?>
				<form id="ifind-contact-form-form" class="ifind-validator-form" method="post" action="" role="form">
					<div class="form-group">
						<input type="text" name="fullname" class="form-control input-lg" 
								placeholder="<?php _e("Please enter your fullname *", 'ifind'); ?>" autocomplete="off" 
								required="required" 
								data-error="<?php _e("Fullname is required.", 'ifind'); ?>">
						<input type="email" name="email" class="form-control input-lg" 
								placeholder="<?php _e("Please enter your email *", 'ifind'); ?>" autocomplete="off" 
								required="required" 
								data-error="<?php _e("Valid email is required.", 'ifind'); ?>">
						<input type="text" name="phone" class="form-control input-lg" 
								placeholder="<?php _e("Please enter your phone number *", 'ifind'); ?>" autocomplete="off" 
								required="required" 
								data-error="<?php _e("Valid phone number is required.", 'ifind'); ?>">
						<div class="help-block with-errors"></div>
						<div class="softkeys" data-target="input[name='email']"></div>
						<input type="hidden" name="email_to" value="<?php echo $email_to; ?>">
						<input type="hidden" name="cc_to" value="<?php echo $cc_to; ?>">
					</div>
					<div class="form-group">
						<input type="submit" class="btn btn-success btn-lg" value="<?php _e("Send", 'ifind'); ?>">
					</div>
				</form>
			<?php } ?>
			<?php if ($qr_link) { ?>
				<div class="ifind-qr-code">
					<img src="<?php echo $qr_link; ?>" alt="">
				</div>
			<?php } ?>
			<div class="ifind-fancybox-close ifind-contact-form-close"><?php _e('x','ifind'); ?></div>
		</div>
		<?php
		die(); //stop "0" from being output
	}
}