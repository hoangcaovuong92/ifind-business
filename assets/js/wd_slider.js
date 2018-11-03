//****************************************************************//
/*							SLIDER JS							  */
//****************************************************************//
jQuery(window).ready(function ($) {
	"use strict";
	//Blog related slider (template-parts/related.php)
	ifind_big_popup_slider();
});

//****************************************************************//
/*							FUNCTIONS							  */
//****************************************************************//
if (typeof ifind_slider_call != 'function') {
	function ifind_slider_call(sliderWrap, options) {
		if (jQuery(sliderWrap).hasClass('slick-initialized')) {
			jQuery(sliderWrap).slick('unslick');
		}

		var default_option = {
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			autoplay: true,
			speed: 900,
			infinite: true,
			touchThreshold: 100,
			pauseOnHover:false
		}

		options = jQuery.extend(default_option, options);
		slider = jQuery(sliderWrap).slick(options);
		return slider;
	}
}

if (typeof ifind_big_popup_slider != 'function') {
	function ifind_big_popup_slider() {
		jQuery(document).ready(function () {
			var timer;
			//var timerShowPopup = 9999999; //time show large popup slider
			var timerShowPopup = option_object.ifind_slider_timerShowPopup; //time show large popup slider
			var timerShowPopupViewingInfo = option_object.ifind_slider_timerShowPopupViewingInfo; //time show large popup slider
			var timerDelayPopup = option_object.ifind_slider_timerDelayPopup; //time delay after slider break
			var bigAutoplaySpeed = option_object.ifind_slider_bigAutoplaySpeed; //time autoplay large popup slider
			var smallAutoplaySpeed = option_object.ifind_slider_smallAutoplaySpeed; //time autoplay small slider
			var numSliderBreak = option_object.ifind_slider_numSliderBreak; //slider will break after number slide
			var numFooterSliderItems = option_object.ifind_slider_numFooterSliderItems; //slide number of footer slider

			var popSliderBigWrap = '#popupSlider';
			var popSliderListWrap = '.ifind-sliderPop-container';
			var popSliderItem = '.ifind-sliderPop-item';
			var footerSliderListWrap = '.ifind-footerSlider-container';
			var footerSliderItem = '.ifind-footerSlider-item';
			var smallSliderListWrap = '.ifind-smallSlider-container';
			var smallSliderItem = '.ifind-smallSlider-item';

			ifind_slider_call(smallSliderListWrap, {
				arrows: true, 
				autoplaySpeed: smallAutoplaySpeed,
				swipe: 'false',
				swipeToSlide: 'false',
				touchMove: 'false',
				draggable: 'false',
				accessibility: 'false',
			});
			ifind_slider_call(footerSliderListWrap, {
				arrows: true, 
				autoplaySpeed: smallAutoplaySpeed, 
				infinite: true, 
				slidesToShow: numFooterSliderItems
			});

			function big_popup_open(options){
				ifind_debug_mode('popup opened');
				var default_option = {
					autoplaySpeed: bigAutoplaySpeed,
				}
				options = jQuery.extend(default_option, options); 
				jQuery(popSliderBigWrap).addClass('open');
				jQuery.fancybox(popSliderBigWrap, {
					openEffect  : 'fade',
					closeEffect : 'fade',
					margin      : [0, 0, 0, 0],
					padding 	: 0,
					width 		: 1080,
					height 		: 1920,
					fitToView 	: true,
					autoSize 	: false,
					closeBtn    : false,
					arrows      : false,
					scrolling	: false,
					helpers 	: {
						overlay : {
							css : {
								'background' : 'rgba(58, 42, 45, 0)'
							}
						}
					},
					onComplete  : function() {
					},
					beforeShow: function(){
						//jQuery("body").css({'overflow-y':'hidden'});
					},
					afterClose: function(){
						//jQuery("body").css({'overflow-y':'visible'});
						jQuery(popSliderBigWrap).removeClass('open');
					},
					afterLoad	: function() {
					}
				});
				ifind_slider_call(popSliderListWrap, options);
			}

			function big_popup_close(){
				ifind_debug_mode('popup closed');
				if (jQuery(popSliderListWrap).hasClass('slick-initialized')) {
					jQuery(popSliderListWrap).slick('unslick');
				}
				ifind_fancybox_close();
				jQuery(popSliderBigWrap).removeClass('open popup-breaking');
			}

			jQuery(document).on('mousemove touchstart click', function (event) {
				ifind_debug_mode('popup opening: ' + jQuery(popSliderBigWrap).hasClass('open'));
				var time = (!jQuery('body').hasClass('working')) ? timerShowPopup : timerShowPopupViewingInfo;
				if (timer) clearTimeout(timer);
				timer = setTimeout(function () {
					if (!jQuery('body').hasClass('video-playing')) {
						ifind_debug_mode('popup break status:'+jQuery(popSliderBigWrap).hasClass('popup-breaking'));
						if(!jQuery(popSliderBigWrap).hasClass('popup-breaking') && !jQuery(popSliderBigWrap).hasClass('open')){
							var startSlider = 0;
							ifind_debug_mode('popup opened after waiting '+(time/1000)+' seconds! Open at slider index: '+startSlider);
							big_popup_open();
						}
						// On before slide change
						jQuery(popSliderListWrap).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
							if ((currentSlide + 1) % numSliderBreak === 0 || nextSlide === 0 ) {
								ifind_debug_mode('popup breaked');
								startSlider = nextSlide;
								big_popup_close();
								ifind_reset_system();
								jQuery(popSliderBigWrap).addClass('popup-breaking');
								ifind_debug_mode('popup break status:'+jQuery(popSliderBigWrap).hasClass('popup-breaking'));

								setTimeout(function () {
									if(jQuery(popSliderBigWrap).hasClass('popup-breaking')){
										ifind_debug_mode('popup after break opened at slider index: '+startSlider);
										big_popup_open({initialSlide: startSlider});
									}
								}, timerDelayPopup);
							}
						}); 
					}else{
						ifind_debug_mode('Video playing status:'+jQuery('body').hasClass('video-playing'));
					}
				}, time);
			});

			jQuery(smallSliderItem).on('click', function (e) {
				ifind_debug_mode('popup after click small slider opened');
				var slideIndex = jQuery(this).index();
				big_popup_open({initialSlide: slideIndex - 1});
			});

			jQuery(popSliderItem).on('click', function () {
				ifind_debug_mode('popup clicked');
				big_popup_close();
				ifind_reset_system();
			});

			jQuery('body').on('click', function () {
				if (jQuery(popSliderBigWrap).hasClass('popup-breaking')) {
					ifind_debug_mode('body clicked!');
					big_popup_close();
				}
			});
		});
	}
}