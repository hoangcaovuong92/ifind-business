//****************************************************************//
/*                          METABOX ADMIN JS                      */
//****************************************************************//
jQuery(document).ready(function($) {
    "use strict";
    
    //Metabox custom script for form
    wd_metabox_form_script();

});

//****************************************************************//
/*                          FUNCTIONS                             */
//****************************************************************//
//Metabox custom script for form
if (typeof wd_metabox_form_script != 'function') { 
    function wd_metabox_form_script() {
        var _breadcrumb_style = jQuery('#wpdance_custom_breadcrumb_style');
        var _breadcrumb_image = jQuery('#wpdance_custom_breadcrumb_image_wrap');
        if (_breadcrumb_style.val() == 'breadcrumb_banner') {
            _breadcrumb_image.show();
        }else{
            _breadcrumb_image.hide();
        }

        _breadcrumb_style.change(function(){
            if (jQuery(this).val() == 'breadcrumb_banner') {
                _breadcrumb_image.show();
            }else{
                _breadcrumb_image.hide();
            }
        })

        var _layout         = jQuery('#wpdance_custom_layout');
        var _left_sidebar   = jQuery('#wpdance_custom_left_sidebar_wrap');
        var _right_sidebar  = jQuery('#wpdance_custom_right_sidebar_wrap');
        if (_layout.val() != '0' && _layout.val() != '0-0-0') {
            if (_layout.val() == '1-0-0') {
                 _left_sidebar.show();
                 _right_sidebar.hide();
            }else if(_layout.val() == '0-0-1'){
                _left_sidebar.hide();
                _right_sidebar.show();
            }else if(_layout.val() == '1-0-1'){
                _left_sidebar.show();
                _right_sidebar.show();
            }
        }else{
            _left_sidebar.hide();
            _right_sidebar.hide();
        }

        _layout.change(function(){
           if (jQuery(this).val() != '0' && jQuery(this).val() != '0-0-0') {
                if (_layout.val() == '1-0-0') {
                     _left_sidebar.show();
                     _right_sidebar.hide();
                }else if(_layout.val() == '0-0-1'){
                    _left_sidebar.hide();
                    _right_sidebar.show();
                }else if(_layout.val() == '1-0-1'){
                    _left_sidebar.show();
                    _right_sidebar.show();
                }
            }else{
                _left_sidebar.hide();
                _right_sidebar.hide();
            }
        })
    }
}
