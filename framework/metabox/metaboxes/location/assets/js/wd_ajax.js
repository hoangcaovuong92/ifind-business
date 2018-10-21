//****************************************************************//
/*							Ajax Loadmore JS					  */
//****************************************************************//
jQuery( document ).ready( function($) {
	//hide loading button
	$(".show_image_loading").hide(); 

	//Ajax Load Feature Content With Modal. 
	wd_ajax_load_feature_with_modal();
});

//****************************************************************//
/*                          FUNCTIONS                             */
//****************************************************************//
//Ajax Load Feature Content With Modal. 
if (typeof wd_ajax_load_feature_with_modal != 'function') { 
	function wd_ajax_load_feature_with_modal(){
		jQuery('.wd-modal-bootstrap-ajax').click(function(e) {
		  	e.preventDefault();
		 	var feature_id 	= jQuery(this).data('feature_id');
		 	if (feature_id && !jQuery("#wd-modal-container-"+feature_id).hasClass('loaded')) {
			 	jQuery.ajax({
					url: ajax_object.ajax_url,
					type: 'post',
					data: {
						action			: 'load_feature_content_modal',
						query_vars		: ajax_object.query_vars,
						feature_id		: feature_id,
					},
					beforeSend: function(data) {
						jQuery("#wd-feature-loading-"+feature_id).removeClass('hidden');
					},
					success: function( data ) {
						jQuery("#wd-modal-container-"+feature_id).addClass('loaded').html(data);
						jQuery("#wd-feature-loading-"+feature_id).addClass('hidden');
						jQuery("#wd-modal-container-"+feature_id).find('.wd-modal-content').modal();

					}, 
					error: function(req, err){ 
						//console.log('my message' + err); 
					}
				});
			}
			jQuery("#wd-modal-container-"+feature_id).find('.wd-modal-content').modal();
		});

	}	
}