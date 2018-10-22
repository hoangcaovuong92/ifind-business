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
			var timerShowPopup = option_object.ifind_slider_timerShowPopup; //time show large popup slider
			var timerDelayPopup = option_object.ifind_slider_timerDelayPopup; //time delay after slider break
			var bigAutoplaySpeed = option_object.ifind_slider_bigAutoplaySpeed; //time autoplay large popup slider
			var smallAutoplaySpeed = option_object.ifind_slider_smallAutoplaySpeed; //time autoplay small slider
			var numSliderBreak = option_object.ifind_slider_numSliderBreak; //slider will break after number slide
			var numFooterSliderItems = option_object.ifind_slider_numFooterSliderItems; //slide number of footer slider

			var popSliderBigWrap = '.popupSlider';
			var popSliderListWrap = '.ifind-sliderPop-container';
			var logoSliderListWrap = '.ifind-logoSlider-container';
			var logoSliderItem = '.ifind-logoSlider-item';
			var smallSliderListWrap = '.ifind-smallSlider-container';
			var smallSliderItem = '.ifind-smallSlider-item';

			ifind_slider_call(smallSliderListWrap, {arrows: true, autoplaySpeed: smallAutoplaySpeed});
			ifind_slider_call(logoSliderListWrap, {arrows: true, autoplaySpeed: smallAutoplaySpeed, infinite: true, slidesToShow: numFooterSliderItems});

			function big_popup_open(options){
				var default_option = {
					autoplaySpeed: bigAutoplaySpeed,
				}
				options = jQuery.extend(default_option, options); 
				jQuery(popSliderBigWrap).addClass('open');
				ifind_slider_call(popSliderListWrap, options);
				jQuery(".fancybox-overlay").fadeOut().remove();
			}

			function big_popup_close(){
				if (jQuery(popSliderListWrap).hasClass('slick-initialized')) {
					jQuery(popSliderListWrap).slick('unslick');
				}
				jQuery(popSliderBigWrap).removeClass('open');
				reset_business_tab();
			}

			jQuery(document).on('mousemove touchstart click', function (event) {
				if(!jQuery(popSliderBigWrap).hasClass('open')){
					var time = (!jQuery('body').hasClass('showing-info')) ? timerShowPopup : timerShowPopup * 4;
					if (timer) clearTimeout(timer);
					timer = setTimeout(function () {
						var startSlider = 0;
						big_popup_open();
						
						// On before slide change
						jQuery(popSliderListWrap).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
							if ((currentSlide + 1) % numSliderBreak === 0 || nextSlide === 0 ) {
								startSlider = nextSlide;
								big_popup_close();

								setTimeout(function () {
									big_popup_open({initialSlide: startSlider});
								}, timerDelayPopup);
							}
						});

					}, time);
				}else{
					//big_popup_close();
				}
			});

			jQuery(smallSliderItem).on('click', function (e) {
				var slideIndex = jQuery(this).index();
				big_popup_open({initialSlide: slideIndex - 1});
			});

			jQuery(popSliderListWrap).on('click', function () {
				big_popup_close()
			});
		});
	}
}