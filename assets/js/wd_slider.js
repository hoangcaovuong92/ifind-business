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
				console.log('popup opened');
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
					closeBtn    : true,
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
				console.log('popup closed');
				if (jQuery(popSliderListWrap).hasClass('slick-initialized')) {
					jQuery(popSliderListWrap).slick('unslick');
				}
				ifind_fancybox_close();
				jQuery(popSliderBigWrap).removeClass('open');
				reset_business_tab();
			}

			jQuery(document).on('mousemove touchstart click', function (event) {
				console.log('popup opening: ' + jQuery(popSliderBigWrap).hasClass('open'));
				var time = (!jQuery('body').hasClass('showing-info')) ? timerShowPopup : timerShowPopupViewingInfo;
				if (timer) clearTimeout(timer);
				timer = setTimeout(function () {
					if(!jQuery(popSliderBigWrap).hasClass('open')){
					
						var startSlider = 0;
						console.log('popup after waiting opened');
						big_popup_open();
					}else{
						// On before slide change
						jQuery(popSliderListWrap).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
							if ((currentSlide + 1) % numSliderBreak === 0 || nextSlide === 0 ) {
								console.log('popup breaked');
								startSlider = nextSlide;
								big_popup_close();

								setTimeout(function () {
									console.log('popup after break opened');
									big_popup_open({initialSlide: startSlider});
								}, timerDelayPopup);
							}
						});
					}

				}, time);
			});

			jQuery(smallSliderItem).on('click', function (e) {
				console.log('popup after click small slider opened');
				var slideIndex = jQuery(this).index();
				big_popup_open({initialSlide: slideIndex - 1});
			});

			jQuery(popSliderListWrap).on('click', function () {
				console.log('popup clicked');
				big_popup_close();
			});
		});
	}
}