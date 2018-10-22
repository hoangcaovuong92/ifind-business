//****************************************************************//
/*							Main JS								  */
//****************************************************************//
jQuery(document).ready(function ($) {
	"use strict";
	var window_width = jQuery(window).width();
	//Sidebar collapse
	wow_js_script();
	google_map_script();
	business_tab_script();
	image_popup_fancybox();
	ifind_ajax_display_weather_today_info();
	ifind_form_directions();
	ifind_add_class_to_body();
});

//****************************************************************//
/*							FUNCTIONS							  */
//****************************************************************//
if (typeof google_map_script != 'function') {
	function google_map_script() {
		var position2_lat = jQuery('#ifind-location-position').data('lat');
		var position2_lng = jQuery('#ifind-location-position').data('lng');
		var max_distance = jQuery('#ifind-location-position').data('max-distance');
		jQuery.each( jQuery('.business-distance'), function( i, item ) {
			var position1_lat = jQuery(item).parents('.business-direction-link').data('lat');
			var position1_lng = jQuery(item).parents('.business-direction-link').data('lng');
			var p1 = new google.maps.LatLng(position1_lat, position1_lng);
			var p2 = new google.maps.LatLng(position2_lat, position2_lng);
			var distance = google_map_calcDistance(p1, p2);
			if (max_distance && distance > max_distance){
				jQuery(item).parents('.ifind-business-item').remove();
			}else{
				jQuery(item).html('~'+distance+'km');
			}
		});
	}
}

//calculates distance between two points in km's
//var p1 = new google.maps.LatLng(45.463688, 9.18814);
//var p2 = new google.maps.LatLng(46.0438317, 9.75936230000002);
if (typeof google_map_calcDistance != 'function') {
	function google_map_calcDistance(p1, p2) {
		return (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000).toFixed(2);
	}
}

if (typeof business_tab_script != 'function') {
	function business_tab_script() {
		jQuery( document ).ready(function() {
			var category_banner_link = '.business-group-banner-link';
			var tab_wrap = '#ifind-business-tabs';
			var tab_title_item = '#ifind-business-tabs .nav-pills li';
			var tab_content_item = '#ifind-business-tabs .tab-content .tab-pane';
			var filter_item = '.ifind-business-filter-item';
			var business_item = '.ifind-business-item';

			jQuery(category_banner_link).on('click', function(e) {
				e.preventDefault();
				jQuery('body').addClass('showing-info');
				var category_id = jQuery(this).data('category-id');
				jQuery(tab_wrap).show();

				jQuery(category_banner_link).not(this).hide();
						
				jQuery(tab_title_item).removeClass('active');
				jQuery(tab_title_item+'[data-category-id='+category_id+']').addClass('active');
				jQuery(tab_content_item).removeClass('active');
				jQuery(tab_content_item+'[data-category-id='+category_id+']').addClass('active');
			});
			
			jQuery(tab_title_item).on('click', function(e) {
				e.preventDefault();
				var category_id = jQuery(this).data('category-id');
				jQuery(category_banner_link).hide();
				jQuery(category_banner_link+'[data-category-id='+category_id+']').show();
			});

			jQuery(filter_item).on('click', function(e) {
				e.preventDefault();
				var filter_key = jQuery(this).data('filter-by');
				var business_list = jQuery(this).parents('.ifind-business-filter').next('.ifind-business-list');
				jQuery(this).parents('.ifind-business-filter').find(filter_item).removeClass('active');
				jQuery(this).addClass('active');
				jQuery(business_list).find(business_item).hide();
				jQuery(business_list).find(business_item + '.' + filter_key).show();
			});

			jQuery('.business-back-link').on('click', function(e) {
				e.preventDefault();
				reset_business_tab();
			});

			jQuery('.directions-close').on('click', function(e) {
				e.preventDefault();
				jQuery('#map-directions').hide();
			});
		});
	}
}

if (typeof reset_business_tab != 'function') {
	function reset_business_tab() {
		var category_banner_link = '.business-group-banner-link';
		var tab_wrap = '#ifind-business-tabs';
		jQuery('#map-directions').hide();
		jQuery(tab_wrap).hide();
		jQuery(category_banner_link).show();
		jQuery('body').removeClass('showing-info');
		jQuery('.map-directions-email-form').removeClass('open');
		jQuery('.map-directions-email-form input[type="email"]').val('');
	}
}

if (typeof image_popup_fancybox != 'function') {
	function image_popup_fancybox() {
		jQuery(".ifind-fancybox-image").fancybox({
			openEffect  : 'fade',
			closeEffect : 'fade',
			margin      : [0, 0, 0, 0],
			padding 	: 7,
			width 		: 1038,
			height 		: 738,
			fitToView 	: true,
			autoSize 	: false,
			closeBtn    : true,
			arrows      : false,
			onComplete  : function() {
			},
			afterClose	: function() {
			},
			afterLoad	: function() { 
				setTimeout(() => {
					jQuery(".fancybox-overlay").fadeOut().remove(); 
				}, 10000);
			}
		});
	}
}

if (typeof wow_js_script != 'function') {
	function wow_js_script() {
		wow = new WOW({
			animateClass: 'animated',
			offset: 100,
			callback: function (box) {
				//console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
			}
		});
		wow.init();
	}
}

// Ajax Update weather
if (typeof ifind_ajax_display_weather_today_info != 'function') { 
	function ifind_ajax_display_weather_today_info() {
		setInterval(function () {
			jQuery.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: { 
					action: "display_weather_today_info",
					lat: jQuery('#ifind-location-position').data('lat'),
					lng: jQuery('#ifind-location-position').data('lng'),
				},
				beforeSend: function(){
				},
				success: function(data) {
					if(data){
						jQuery('.ifind-weather').html(data);
					}
				}
			});
		}, option_object.ifind_weather_update_time);
	}
}

if (typeof ifind_form_directions != 'function') { 
	function ifind_form_directions(){
		jQuery('#send-directions-form').validator();

		jQuery('.map-directions-email-send').on('click', function(e) {
			e.preventDefault();
			jQuery('.map-directions-email-form').toggleClass('open');
		});

		jQuery('.map-directions-header, .map-directions-content').on('click', function(e) {
			jQuery('.map-directions-email-form').removeClass('open');
		});

		jQuery('#send-directions-form').on('submit', function (e) {
			// if the validator does not prevent form submit
			var email = jQuery(this).find('input[name="email"]').val();
			var title = jQuery(this).find('input[name="title"]').val();
			var message = jQuery('#map-directions .map-directions-header').html();
			message += jQuery('#map-directions .map-directions-content').html();
			if (!e.isDefaultPrevented()) {
				// POST values in the background the the script URL
				jQuery.ajax({
					type: "POST",
					url: ajax_object.ajax_url,
					data: { 
						action: "send_directions_main",
						email: email,
						title: title,
						message: message,
					},
					beforeSend: function(){
						jQuery('.map-directions-email-form input[type="submit"]').addClass('disabled');
					},
					success: function (data){
						jQuery('.map-directions-email-form input[type="email"]').val('');
						if(data.type === 'success'){
							jQuery('.map-directions-email-form').removeClass('open');
						}
						swal({
							title: data.title,
							type: data.type,
							text: data.message,
							timer: 6000
						});
					}
				});
				return false;
			}
		})
	}
}

if (typeof ifind_add_class_to_body != 'function') { 
	function ifind_add_class_to_body(){
		jQuery('body').addClass('loaded');
	}
}