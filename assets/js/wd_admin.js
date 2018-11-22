//****************************************************************//
/*							ADMIN JS							  */
//****************************************************************//
jQuery(document).ready(function ($) {
	"use strict";
	ifind_ajax_reset_secret_key();
	ifind_ajax_statistics();
	ifind_datepicker();
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
				beforeSend: function () {},
				success: function (data) {
					alert('success!');
				}
			});
		})
	}
}

if (typeof ifind_ajax_statistics != 'function') {
	function ifind_ajax_statistics() {
		//Statistics filter select
		jQuery('#ifind-location-list-select').on('change', function (e) {
			e.preventDefault();
			var location_id = jQuery(this).val();
			jQuery.ajax({
				type: "POST",
				url: ajax_object.ajax_url,
				data: {
					action: "refresh_business_by_location",
					location_id: location_id,
				},
				beforeSend: function () {},
				success: function (data) {
					jQuery('#ifind-business-list-select').html(data).trigger('change');
				}
			});
		})

		jQuery('#ifind-business-list-select').on('change', function (e) {
			e.preventDefault();
			var business_id = jQuery(this).val();
			var datepicker_from = jQuery('#datepicker_from').val();
			var datepicker_to = jQuery('#datepicker_to').val();
			jQuery("#attachment_link").html('');
			jQuery('.send-statistics-mail-form input[type="email"]').val('');
			jQuery.ajax({
				type: "POST",
				url: ajax_object.ajax_url,
				data: {
					action: "load_table_bussiness_statistics",
					business_id: business_id,
					datepicker_from: datepicker_from,
					datepicker_to: datepicker_to,
				},
				beforeSend: function () {},
				success: function (data) {
					jQuery('#ifind-business-statistics-result').html(data);
				}
			});
		})
		jQuery('#ifind-business-list-select').trigger('change');

		//Send email
		jQuery('.send-statistics-mail-form').on('submit', function (e) {
			e.preventDefault();
			// if the validator does not prevent form submit
			var email = jQuery(this).find('input[name="email"]').val();
			var attachment = jQuery(this).find('input[name="attachment"]').prop('checked') ? 1 : 0;
			var attachment_file = '';
			var title = jQuery('#ifind-business-statistics-result h2').text();
			var business_id = jQuery('#ifind-business-list-select').val();
			var datepicker_from = jQuery('#datepicker_from').val();
			var datepicker_to = jQuery('#datepicker_to').val();
			if (email) {
				if (attachment) {
					jQuery.ajax({
						type: "POST",
						url: ajax_object.ajax_url,
						data: {
							action: "add_pdf_attachment",
							business_id: business_id,
							datepicker_from: datepicker_from,
							datepicker_to: datepicker_to,
						},
						beforeSend: function () {
							jQuery('.send-statistics-mail-form input#submit').attr('disabled', 'disabled');
						},
						success: function (response, status, xhr) {
							var status = response.data.success;
							var attachment_file = response.data.attachment_content;
							jQuery("#attachment_link").html('<a target="_blank" href="'+attachment_file+'">View & Download</a>');
							jQuery.ajax({
								type: "POST",
								url: ajax_object.ajax_url,
								data: {
									action: "send_statistics_mail",
									email: email,
									attachment: attachment,
									attachment_file: attachment_file,
									title: title,
									business_id: business_id,
									datepicker_from: datepicker_from,
									datepicker_to: datepicker_to,
								},
								beforeSend: function () {
									jQuery('.send-statistics-mail-form input#submit').attr('disabled', 'disabled');
								},
								success: function (data) {
									jQuery('.send-statistics-mail-form input[type="email"]').val('');
									jQuery('.send-statistics-mail-form input#submit').removeAttr('disabled');
									alert(data.message);
								}
							});
						}
					});
				} else {
					jQuery.ajax({
						type: "POST",
						url: ajax_object.ajax_url,
						data: {
							action: "send_statistics_mail",
							email: email,
							attachment: attachment,
							attachment_file: attachment_file,
							title: title,
							business_id: business_id,
							datepicker_from: datepicker_from,
							datepicker_to: datepicker_to,
						},
						beforeSend: function () {
							jQuery('.send-statistics-mail-form input#submit').attr('disabled', 'disabled');
						},
						success: function (data) {
							jQuery('.send-statistics-mail-form input[type="email"]').val('');
							jQuery('.send-statistics-mail-form input#submit').removeAttr('disabled');
							alert(data.message);
						}
					});
				}
				return false;
			}else{
				jQuery('.send-statistics-mail-form input[type="email"]').focus();
				alert('Enter an email first!');
			}
		});
	}
}

if (typeof ifind_datepicker != 'function') {
	function ifind_datepicker() {
		//jQuery(".datepicker").datepicker();
		var dateFormat = "yy-mm-dd";
		var from = jQuery("#datepicker_from")
			.datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 2,
				dateFormat: dateFormat
			})
			.on("change", function () {
				to.datepicker("option", "minDate", getDate(this));
				if (to.val() === '') {
					to.val(jQuery(this).val());
				}
				jQuery('#ifind-business-list-select').trigger('change');
			});

		var to = jQuery("#datepicker_to").datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 2,
				dateFormat: dateFormat
			})
			.on("change", function () {
				from.datepicker("option", "maxDate", getDate(this));
				if (from.val() === '') {
					from.val(jQuery(this).val());
				}
				jQuery('#ifind-business-list-select').trigger('change');
			});

		function getDate(element) {
			var date;
			try {
				date = jQuery.datepicker.parseDate(dateFormat, element.value);
			} catch (error) {
				date = null;
			}

			return date;
		}
	};
}