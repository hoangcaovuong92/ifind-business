//****************************************************************//
/*							ADMIN JS							  */
//****************************************************************//
jQuery(document).ready(function ($) {
	"use strict";
	ifind_froala_editor();
});

//****************************************************************//
/*							FUNCTIONS							  */
//****************************************************************//
// Add editor tool to textarea field
if (typeof ifind_froala_editor != 'function') {
	function ifind_froala_editor() {
		$('textarea.ifind-textarea-editor').froalaEditor();
	}
}