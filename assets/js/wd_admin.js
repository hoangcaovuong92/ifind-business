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
			var location_id = jQuery('#ifind-location-list-select').val();
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
					location_id: location_id,
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
			var title = jQuery('#ifind-business-statistics-result h2.ifind-statistics-result-title').text();
			var location_id = jQuery('#ifind-location-list-select').val();
			var business_id = jQuery('#ifind-business-list-select').val();
			var datepicker_from = jQuery('#datepicker_from').val();
			var datepicker_to = jQuery('#datepicker_to').val();
			if(jQuery('.ifind-table').length) {
				if (email) {
					if (attachment) {
						jQuery.ajax({
							type: "POST",
							url: ajax_object.ajax_url,
							data: {
								action: "add_pdf_attachment",
								location_id: location_id,
								business_id: business_id,
								datepicker_from: datepicker_from,
								datepicker_to: datepicker_to,
							},
							beforeSend: function () {
								jQuery('.send-statistics-mail-form input#submit').attr('disabled', 'disabled');
							},
							success: function (response, status, xhr) {
								var attachment_file = response.data.attachment_file;
								var direct_link = response.data.direct_link;
								if (direct_link) {
									jQuery("#attachment_link").html('<a target="_blank" href="'+direct_link+'">View & Download Attachment File</a>');
								}
								jQuery.ajax({
									type: "POST",
									url: ajax_object.ajax_url,
									data: {
										action: "send_statistics_mail",
										email: email,
										attachment: attachment,
										attachment_file: attachment_file,
										direct_link: direct_link,
										title: title,
										location_id: location_id,
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
										if(data.message){
											swal({
												title: 'iFind notifications',
												type: 'success',
												text: data.message,
												timer: 6000
											});
										}
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
								location_id: location_id,
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
								if(data.message){
									swal({
										title: 'iFind notifications',
										type: 'success',
										text: data.message,
										timer: 6000
									});
								}
							}
						});
					}
					return false;
				}else{
					jQuery('.send-statistics-mail-form input[type="email"]').focus();
					swal({
						title: 'iFind notifications',
						type: 'error',
						text: 'Enter an email first!',
						timer: 6000
					});
				}
			}else{
				swal({
					title: 'iFind notifications',
					type: 'error',
					text: 'No results to send. Please select another business!',
					timer: 6000
				});
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