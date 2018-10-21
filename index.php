<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @package Wordpress
 * @since wpdance
 */
?>
<?php get_header(); ?>
<?php 
/**
 * tvlgiao_wpdance_before_main_content hook.
 *
 * @hooked tvlgiao_wpdance_content_before_main_content
 */
do_action('tvlgiao_wpdance_before_main_content'); ?>
    content
<?php 
/**
 * tvlgiao_wpdance_after_main_content hook.
 *
 * @hooked tvlgiao_wpdance_content_after_main_content
 */
do_action('tvlgiao_wpdance_after_main_content'); ?>

<?php get_footer(); ?>