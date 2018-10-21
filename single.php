<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Wordpress
 */

get_header(); 	
$post_ID		= tvlgiao_wpdance_get_post_by_global();

/**
 * tvlgiao_wpdance_before_main_content hook.
 *
 * @hooked tvlgiao_wpdance_content_before_main_content
 */
do_action('tvlgiao_wpdance_before_main_content'); ?>
	<div class="row">
	</div>

<?php 
/**
 * tvlgiao_wpdance_after_main_content hook.
 *
 * @hooked tvlgiao_wpdance_content_after_main_content
 */
do_action('tvlgiao_wpdance_after_main_content'); ?>

<?php get_footer(); ?>