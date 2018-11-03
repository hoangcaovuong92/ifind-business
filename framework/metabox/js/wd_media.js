//****************************************************************//
/*							BACKEND MEDIA JS					  */
//****************************************************************//
jQuery(document).ready(function($) {
	"use strict";
//Media Screen Select
	wd_media_select();

	//Media Color Select
	wd_colorpicker_select();
});

//****************************************************************//
/*							FUNCTIONS							  */
//****************************************************************//

//Media Screen Select
if (typeof wd_media_select != 'function') { 
	function wd_media_select(){
		/* SELECT MEDIA BUTTON
		 * How to use:
		 * Set class wd_media_lib_select_btn to a button with:
		 * - data-image_value : (The id of the element will save the id of the image after selection) 
		 * - data-image_preview : (The id of the element will display image preview after selection)
		 */
		jQuery('.wd_media_lib_select_btn').on('click', function( e ){
	        e.preventDefault();
	        var file_frame_bridal;
	        var img_value_field     = jQuery(this).data('image_value');
	        var img_preview_field   = jQuery(this).data('image_preview'); 
	        var type   				= jQuery(this).data('type'); 

	        if ( file_frame_bridal ) {
	            file_frame_bridal.open();
	            return;
	        }

	        var _states = [new wp.media.controller.Library({
	            filterable: 'uploaded',
	            title: 'Select an Image',
	            multiple: false,
	            priority:  20
	        })];
				 
	        file_frame_bridal = wp.media.frames.file_frame_bridal = wp.media({
	            states: _states,
	            button: {
	                text: 'Insert URL'
	            }
	        });

	        file_frame_bridal.on( 'select', function() {
				var attachment = file_frame_bridal.state().get('selection').first().toJSON();
				if (type === 'image') {
					jQuery('#'+img_value_field).val(attachment.id);
					jQuery('#'+img_preview_field).attr("src",attachment.url); 
				}else if(type === 'video'){
					jQuery('#'+img_value_field).val(attachment.url);
				}

	        });
			 
	        file_frame_bridal.open();
	    });

		/* RESET MEDIA BUTTON
		 * How to use:
		 * Set class wd_media_lib_clear_btn to a button with:
		 * - data-image_value : (The id of the element will save the id of the image after selection) 
		 * - data-image_preview : (The id of the element will display image preview after selection)
		 * - data-image_default : (Default image url)
		 */
	    jQuery('.wd_media_lib_clear_btn').on('click', function( e ){
	        e.preventDefault();
	        var img_value_field     = jQuery(this).data('image_value');
	        var img_preview_field   = jQuery(this).data('image_preview'); 
	        var img_default   		= jQuery(this).data('image_default'); 
	        jQuery('#'+img_value_field).val('');
	        jQuery('#'+img_preview_field).attr("src", img_default); 
	    });
	}
}

//Media Color Select
if (typeof wd_colorpicker_select != 'function') { 
	function wd_colorpicker_select(){
		/* 
		 * How to use:
		 * Set class wd_colorpicker_select to a button You should embed the colorpicker css library into the following syntax:
		 * wp_enqueue_style( 'wp-color-picker' );
		 */
		if(jQuery('.wd_colorpicker_select').length > 0 ){
			jQuery('.wd_colorpicker_select').wpColorPicker();
		}
	}
}