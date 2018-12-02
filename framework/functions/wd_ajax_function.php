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
		ifind_save_business_statistics( $location_id, $business_id, $click_info );
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
		$message = ifind_sanitize_html_content($_REQUEST['message']);

		// message that will be displayed when everything is OK :)
		$okMessage = sprintf(__("Directions have been sent to your email: %s!", 'ifind'), $email_to);

		// If something goes wrong, we will display this message.
		$errorMessage = __("Error! An error occurred. Please try again later.:", 'ifind');

		//php mailer variables
		$from = get_option('admin_email');
		$subject = "[iFind] ".$title;
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=UTF-8',
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
		$location_id = $_REQUEST['location_id'];
		$business_id = $_REQUEST['business_id'];
		$datepicker_from = $_REQUEST['datepicker_from'];
		$datepicker_to = $_REQUEST['datepicker_to'];
		ifind_get_table_statistics( $location_id, $business_id, $datepicker_from, $datepicker_to, 'admin', true );
		die(); //stop "0" from being output
	}
}

add_action('wp_ajax_nopriv_refresh_business_by_location', 'ifind_refresh_business_by_location_ajax');
add_action('wp_ajax_refresh_business_by_location', 'ifind_refresh_business_by_location_ajax');
if( !function_exists('ifind_refresh_business_by_location_ajax') ){
	function ifind_refresh_business_by_location_ajax() { 
		$location_id = $_REQUEST['location_id'];
		$args = array(
			'post_type'			=> 'business',
			'post_status'		=> 'publish',
			'posts_per_page' 	=> -1,
		);
		if ($location_id && $location_id !== '-1') {
			$location_meta_data = ifind_get_post_custom_metadata($location_id, 'location');
			$args['post__in'] = $location_meta_data['list_business'];
		}
		$business_list = array();
		$data = new WP_Query($args);
		if( $data->have_posts() ){
			while( $data->have_posts() ){
				$data->the_post();
				global $post;
				$business_list[$post->ID] = html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' );
			}
		}
		wp_reset_postdata();
		if (count($business_list) > 0) {
			foreach ($business_list as $key => $value){ ?>
				<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
			<?php } 
		}
		die(); //stop "0" from being output
	}
}

add_action('wp_ajax_nopriv_remove_statistics_email_sender', 'ifind_remove_statistics_email_sender_ajax');
add_action('wp_ajax_remove_statistics_email_sender', 'ifind_remove_statistics_email_sender_ajax');
if( !function_exists('ifind_remove_statistics_email_sender_ajax') ){
	function ifind_remove_statistics_email_sender_ajax() { 
		$index = $_REQUEST['index'];
		$attachment_file = $_REQUEST['attachment_file'];
		ifind_remove_statistics_email_sender( $index, $attachment_file );

		die(); //stop "0" from being output
	}
}

add_action('wp_ajax_nopriv_add_pdf_attachment', 'ifind_add_pdf_attachment_ajax');
add_action('wp_ajax_add_pdf_attachment', 'ifind_add_pdf_attachment_ajax');
if( !function_exists('ifind_add_pdf_attachment_ajax') ){
	function ifind_add_pdf_attachment_ajax() { 
		$location_id = $_REQUEST['location_id'];
		$business_id = $_REQUEST['business_id'];
		$datepicker_from = $_REQUEST['datepicker_from'];
		$datepicker_to = $_REQUEST['datepicker_to'];
		$attachment_content = ifind_get_table_statistics($location_id, $business_id, $datepicker_from , $datepicker_to, 'email_content' );
		$attachment_file = ifind_save_pdf_file($attachment_content);
		wp_send_json_success(array(
			'attachment_file' => $attachment_file,
			'direct_link' => str_replace(ABSPATH, get_home_url(), $attachment_file)
		));

		die(); //stop "0" from being output
	}
}

add_action('wp_ajax_nopriv_send_mail_contact', 'ifind_send_mail_contact_ajax');
add_action('wp_ajax_send_mail_contact', 'ifind_send_mail_contact_ajax');
if( !function_exists('ifind_send_mail_contact_ajax') ){
	function ifind_send_mail_contact_ajax() { 
		$business_email = $_REQUEST['business_email'];
		$location_id = $_REQUEST['location_id'];
		$location_name = get_the_title($location_id);
		$business_id = $_REQUEST['business_id'];
		$business_name = get_the_title($business_id);
		$location_email = $_REQUEST['location_email'];
		$timezone = $_REQUEST['timezone'];
		$current_timestamp = ifind_get_current_timestamp_by_timezone($timezone);
		$current_time = ifind_convert_timestamp_to_time($current_timestamp);
		$ip_address = ifind_get_client_ip();

		if (!is_email($business_email)) return;

		$subject =  __("[iFind] Someone wants to contact you from our kiosk.", 'ifind');
		$message =  "
			Dear my customer,<br/>
			We have sent this email to inform you that there is a visitor just clicking on our kiosk contact button to find a way to contact you. Here are details on this:<br/>
			-Kiosk Location: $location_name <br/>
			-Business name: $business_name <br/>
			-IP Address: $ip_address <br/>
			-Time: $current_time <br/>
			<br/>Best Regards!
		";
		
		//php mailer variables
		$admin_email = get_option('admin_email');
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=UTF-8',
			'X-Priority: 1 (Higuest)',
			'X-MSMail-Priority: High',
			'Importance: High',
			'From: '. $admin_email,
			'Reply-To: ' . $admin_email,
			'Bcc: ' . $admin_email,
			
		);
		if (is_email($location_email)){
			$headers[] = 'Cc: ' . $location_email;
		}
		 
	  	//Here put your Validation and send mail
		$sent = wp_mail($business_email, $subject, $message, $headers, $attachment);
		if($sent) {
			$responseArray = array(
				'type' => 'success',
				'title' => __("Success!:", 'ifind'),
				'message' => $okMessage
			);
		} else  {
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

add_action('wp_ajax_nopriv_send_statistics_mail', 'ifind_send_statistics_mail_ajax');
add_action('wp_ajax_send_statistics_mail', 'ifind_send_statistics_mail_ajax');
if( !function_exists('ifind_send_statistics_mail_ajax') ){
	function ifind_send_statistics_mail_ajax() { 
		$email_to = sanitize_email($_REQUEST['email']);
		$attachment = $_REQUEST['attachment'];
		$attachment_file = $_REQUEST['attachment_file'];
		$direct_link = $_REQUEST['direct_link'];
		$title = $_REQUEST['title'];
		$location_id = $_REQUEST['location_id'];
		$business_id = $_REQUEST['business_id'];
		$datepicker_from = $_REQUEST['datepicker_from'];
		$datepicker_to = $_REQUEST['datepicker_to'];
		
		$message = ifind_get_table_statistics($location_id, $business_id, $datepicker_from , $datepicker_to, 'email_content' );
		if($attachment && $attachment_file){
			$attachment = $attachment_file;
			$message .= sprintf(__("<p>Note: Please see attachment file or click on the following link to view: <strong><a href='%s'>%s</a></strong></p>", 'ifind'), $direct_link, $direct_link);
		}

		// message that will be displayed when everything is OK :)
		$okMessage = sprintf(__("Statistics have been sent to your email: %s!", 'ifind'), $email_to);

		// If something goes wrong, we will display this message.
		$errorMessage = __("Error! An error occurred. Please try again later.:", 'ifind');

		//php mailer variables
		$from = get_option('admin_email');
		$subject = "[iFind] ".$title;
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=UTF-8',
			'X-Priority: 1 (Higuest)',
			'X-MSMail-Priority: High',
			'Importance: High',
			'From: '. $from,
			'Reply-To: ' . $from,
		);

		
		 
	  	//Here put your Validation and send mail
		$sent = wp_mail($email_to, $subject, $message, $headers, $attachment);
		if($sent) {
			$responseArray = array(
				'type' => 'success',
				'title' => __("Success!:", 'ifind'),
				'message' => $okMessage
			);
			
			$email_info = array(
				'email' => $email_to,
				'time' => ifind_get_current_timestamp_by_timezone(),
				'attachment_file' => $attachment_file,
				'direct_link' => $direct_link,
			);
			ifind_save_statistics_email_sender( $email_info );
		} else  {
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