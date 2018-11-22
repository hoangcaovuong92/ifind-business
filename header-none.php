<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Wordpress
 * @since wpdance
 */
?><!DOCTYPE html>
<html itemscope <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php 
	/**
	 * tvlgiao_wpdance_header_meta_data hook.
	 *
	 * @hooked tvlgiao_wpdance_accessibility_display_favicon - 5
	 * @hooked tvlgiao_wpdance_accessibility_facebook_comment_setting_meta - 10
	 */
	do_action('tvlgiao_wpdance_header_meta_data'); ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php 
/**
 * tvlgiao_wpdance_after_opening_body_tag hook.
 *
 * @hooked tvlgiao_wpdance_accessibility_facebook_comment_api - 5
 * @hooked tvlgiao_wpdance_accessibility_loading_page_effect - 10
 * @hooked tvlgiao_wpdance_content_pushmenu_mobile - 15
 */ 
do_action('tvlgiao_wpdance_after_opening_body_tag'); ?>
<div class="body-wrapper">
<header id="header" class="header">
	<div class="wd-header-content wd-header-mobile visible-xs visible-sm">
		<?php
		/**
		 * tvlgiao_wpdance_header_mobile hook.
		 *
		 * @hooked tvlgiao_wpdance_content_header_mobile - 5
		 */ 
		do_action('tvlgiao_wpdance_header_mobile'); ?>
	</div>
	<div id="ifind-secret-key" data-secret-key="<?php echo ifind_get_secret_key(); ?>"></div>
</header> <!-- END HEADER  -->