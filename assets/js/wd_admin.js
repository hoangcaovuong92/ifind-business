//****************************************************************//
/*							ADMIN JS							  */
//****************************************************************//
jQuery(document).ready(function ($) {
	"use strict";
	ifind_ajax_reset_secret_key();
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