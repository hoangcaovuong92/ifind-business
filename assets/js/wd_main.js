//****************************************************************//
/*							Main JS								  */
//****************************************************************//
jQuery(document).ready(function ($) {
	"use strict";
	wow_js_script();
	google_map_script();
	business_tab_script();
	image_popup_fancybox();
	ifind_form_directions();
	ifind_add_class_to_body();
	ifind_virtual_keyboard();
	ifind_ajax_display_weather_today_info();
	ifind_ajax_auto_reload_browser();
	ifind_ajax_update_business_click_counter();
});

//****************************************************************//
/*							FUNCTIONS							  */
//****************************************************************//
if (typeof ifind_debug_mode != 'function') {
	function ifind_debug_mode() {
		return false;
	}
}

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

if (typeof ifind_virtual_keyboard != 'function') {
	function ifind_virtual_keyboard() {
		jQuery('.softkeys').softkeys({
			target : jQuery('.softkeys').data('target'),
			layout : [
				[
					['`','~'],
					['1','!'],
					['2','@'],
					['3','#'],
					['4','$'],
					['5','%'],
					['6','^'],
					['7','&amp;'],
					['8','*'],
					['9','('],
					['0',')'],
					['-', '_'],
					['=','+'],
					'delete'
				],
				[
					'q','w','e','r','t','y','u','i','o','p',
					['[','{'],
					[']','}']
				],
				[
					'capslock',
					'a','s','d','f','g','h','j','k','l',
					[';',':'],
					["'",'&quot;'],
					['\\','|']
				],
				[
					'shift',
					'z','x','c','v','b','n','m',
					[',','&lt;'],
					['.','&gt;'],
					['/','?'],
					['@']
				]
			]
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
				jQuery('body').addClass('working');
				if (ifind_debug_mode()) {
					console.log('body working');
				}
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
		if (!jQuery('body').hasClass('video-playing')) {
			var category_banner_link = '.business-group-banner-link';
			var tab_wrap = '#ifind-business-tabs';
			jQuery('#map-directions').hide();
			jQuery(tab_wrap).hide();
			jQuery(category_banner_link).show();
			jQuery('body').removeClass('working');
			if (ifind_debug_mode()) {
				console.log('body stoped work');
			}
			jQuery('.map-directions-email-form').removeClass('open');
			jQuery('.map-directions-email-form input[type="email"]').val('');
		}
	}
}

if (typeof ifind_fancybox_close != 'function') { 
	function ifind_fancybox_close(){
		jQuery(".fancybox-close").click();
		jQuery('.fancybox-overlay').click();
		jQuery.fancybox.close();
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
			type      	: 'image',
			helpers 	: {
				overlay : {
					css : {
						'background' : 'rgba(58, 42, 45, 0.5)'
					},
				}
			},
			onComplete  : function() {
			},
			beforeShow: function(){
				//jQuery("body").css({'overflow-y':'hidden'});
			},
			afterClose: function(){
				//jQuery("body").css({'overflow-y':'visible'});
			},
			afterLoad	: function() { 
				var timer;
				jQuery(document).on('mousemove touchstart click', function (event) {
					event.preventDefault();
					if (timer) clearTimeout(timer);
					timer = setTimeout(function () {
						ifind_fancybox_close();
						reset_business_tab();
					}, 10000);
				});
			}
		});

		jQuery('.map-directions-email-send').on('click', function(e) {
			e.preventDefault();
			jQuery.fancybox('#map-directions-email', {
				openEffect  : 'fade',
				closeEffect : 'fade',
				margin      : [0, 0, 0, 0],
				padding 	: 0,
				width 		: 1080,
				height 		: 1920,
				fitToView 	: true,
				autoSize 	: true,
				closeBtn    : false,
				arrows      : false,
				helpers : {
					overlay : {
						css : {
							'background' : 'rgba(58, 42, 45, 0.8)'
						},
					}
				},
				onComplete  : function() {
				},
				beforeShow: function(){
					//jQuery("body").css({'overflow-y':'hidden'});
				},
				afterLoad	: function() { 
					var timerShowPopupViewingInfo = option_object.ifind_slider_timerShowPopupViewingInfo; //time show large popup slider
					setTimeout(() => {
						jQuery(".fancybox-overlay").click();
					}, timerShowPopupViewingInfo);
				}
			});
			jQuery('.send-directions-form-close').on('click', function(e) {
				e.preventDefault();
				ifind_fancybox_close();
			});
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

// Ajax Update counter click
if (typeof ifind_ajax_update_business_click_counter != 'function') {
	function ifind_ajax_update_business_click_counter() {
		jQuery('.ifind-counter-item').on('click', function (e) {
			var business_id = jQuery(this).data('business-id');
			var location_id = jQuery(this).data('location-id');
			var counter_position = jQuery(this).data('counter-position');
			var timezone = jQuery('#ifind-location-position').data('timezone');
			jQuery.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: { 
					action: "update_business_click_counter",
					business_id: business_id,
					location_id: location_id,
					counter_position: counter_position,
					timezone: timezone,
				},
				beforeSend: function(){
				},
				success: function(data) {
					console.log(data);
				}
			});
		});
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
						action: "send_directions_mail",
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

if (typeof ifind_ajax_auto_reload_browser != 'function') {
	function ifind_ajax_auto_reload_browser() {
		setInterval(function () {
			var current_secret_key = jQuery('#ifind-secret-key').data('secret-key');
			jQuery.ajax({
				type: "POST",
				url: ajax_object.ajax_url,
				data: { 
					action: "get_curent_secret_key",
				},
				beforeSend: function(){
				},
				success: function (data){
					if (data && data !== current_secret_key) {
						location.reload(true);
					};
				}
			});
		}, 10000);
	}
}

if (typeof ifind_add_class_to_body != 'function') { 
	function ifind_add_class_to_body(){
		jQuery('body').addClass('loaded');
		setTimeout(() => {
			jQuery('.header-page-name').trigger('click');
		}, 3000);
	}
}