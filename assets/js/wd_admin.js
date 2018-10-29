//****************************************************************//
/*							ADMIN JS							  */
//****************************************************************//
jQuery(document).ready(function ($) {
	"use strict";
	ifind_ajax_reset_secret_key();
	ifind_ajax_load_table_bussiness_statistics();
	ifind_ajax_send_statistics_mail();
});

//****************************************************************//
/*							FUNCTIONS							  */
//****************************************************************//
if (typeof ifind_ajax_reset_secret_key != 'function') {
	function ifind_ajax_reset_secret_key() {
		jQuery('.ifind-reload-browser').on('click', function (e) {
			e.preventDefault();
			jQuery.ajax({
				type: "POST",
				url: ajax_object.ajax_url,
				data: { 
					action: "reset_secret_key",
				},
				beforeSend: function(){
				},
				success: function (data){
					alert('success!');
				}
			});
		})
	}
}

if (typeof ifind_ajax_load_table_bussiness_statistics != 'function') {
	function ifind_ajax_load_table_bussiness_statistics() {
		jQuery('#ifind-business-list-select').on('change', function (e) {
			e.preventDefault();
			var business_id = jQuery(this).val();
			jQuery.ajax({
				type: "POST",
				url: ajax_object.ajax_url,
				data: { 
					action: "load_table_bussiness_statistics",
					business_id: business_id,
				},
				beforeSend: function(){
				},
				success: function (data){
					jQuery('#ifind-business-statistics').html(data);
				}
			});
		})
		jQuery('#ifind-business-list-select').trigger('change');
	}
}


if (typeof ifind_ajax_send_statistics_mail != 'function') { 
	function ifind_ajax_send_statistics_mail(){
		jQuery('.send-statistics-mail-form').on('submit', function (e) {
			// if the validator does not prevent form submit
			var email = jQuery(this).find('input[name="email"]').val();
			var title = jQuery('#ifind-business-statistics h2').text();
			var message = jQuery('#ifind-business-statistics .ifind-statistics-result').html();
			if (!e.isDefaultPrevented()) {
				// POST values in the background the the script URL
				jQuery.ajax({
					type: "POST",
					url: ajax_object.ajax_url,
					data: { 
						action: "send_statistics_mail",
						email: email,
						title: title,
						message: message,
					},
					beforeSend: function(){
						jQuery('.send-statistics-mail-form input[type="submit"]').attr('disabled', 'disabled');
					},
					success: function (data){
						jQuery('.send-statistics-mail-form input[type="email"]').val('');
						jQuery('.send-statistics-mail-form input[type="submit"]').removeAttr('disabled');
						alert(data.message);
					}
				});
				return false;
			}
		})
	}
}