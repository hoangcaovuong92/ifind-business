<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Wordpress
 * @since wpdance
 */
?>
<?php 
/**
 * package: 404
 * var: select_style			
 * var: bg_404_url			
 * var: bg_404_color 			
 * var: show_search_form		
 * var: show_back_to_home_btn	
 * var: back_to_home_btn_text	
 * var: back_to_home_btn_class
 * var: show_header_footer	
 * var: content_shortcode		
 */
extract(tvlgiao_wpdance_get_data_package( '404' )); ?>
<?php if ($show_header_footer): ?>
	<?php get_header(); ?>
<?php else: ?>
	<?php get_header('none'); ?>
<?php endif ?>

<?php
	$class_style_select = 'wd-bg-color-error';
	if($select_style == 'bg_image'){
		$class_style_select = 'wd-bg-image-error';
	}
?>
<div id="main-content" class="main-content wd-404-error <?php echo esc_attr($class_style_select); ?>">
	<section class="wd-error-404 wd-error-404-page-content">
		<div class="wd-page-header">
			<span class="wd-text-title"><?php esc_html_e( 'Sorry, Page Not Found!', 'ifind' ); ?></span>
			<h1 class="wd-page-title"><?php esc_html_e( '404', 'ifind' ); ?></h1>
		</div><!-- .page-header -->
		<div class="wd-page-content">
			<?php if ($show_search_form): ?>
				<?php get_search_form(); ?>
			<?php endif ?>
			<?php if ($show_back_to_home_btn): ?>
			<a class="wd-back-to-home-button <?php echo esc_attr($back_to_home_btn_class); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php echo esc_html($back_to_home_btn_text); ?></a>
		<?php endif ?>
		</div><!-- .page-content -->
	</section><!-- .error-404 -->
	<?php if($content_shortcode != '' && tvlgiao_wpdance_is_visual_composer()): ?> 
		<?php $content_shortcode = stripslashes($content_shortcode); ?>
		<div class="wd-404-custom-content">
			<?php echo do_shortcode( "{$content_shortcode}" ); 	?>
		</div>
	<?php endif; ?>
</div><!-- END CONTAINER  -->
<?php if ($show_header_footer): ?>
	<?php get_footer(); ?>
<?php else: ?>
	<?php get_footer('none'); ?>
<?php endif ?>