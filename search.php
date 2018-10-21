<?php
/**
 * The template for displaying search results pages
 *
 * @package Wordpress
 * @since wpdance
 */
?>
<?php get_header(); ?>
<?php
	/**
	 * package: search-layout
	 * var: layout 	
	 * var: sidebar_left 
	 * var: sidebar_right 
	 * var: type 
	 * var: columns 
	 */
	extract(tvlgiao_wpdance_get_data_package( 'search-layout' )); 
	
	if( ($layout == '1-0-0') || ($layout == '0-0-1') ){
		$content_class = "col-md-18 col-sm-24 wd-layout-1-sidebar";
	}elseif($layout == '1-0-1'){
		$content_class = "col-md-12 col-sm-24 wd-layout-2-sidebar";
	}else{
		$content_class = "wd-layout-fullwidth";
	}
	$columns_product 	= 'col-md-'.(24/$columns);

	$blog_search_masonry_class 		= (isset($_GET['post_type']) && $_GET['post_type'] == 'post' && have_posts() ) ? 'wd-search-result-page-masonry' : '';
	$blog_search_item_masonry_class = (isset($_GET['post_type']) && $_GET['post_type'] == 'post' && have_posts() ) ? 'wd-search-result-item' : '';
?>
<div id="main-content" class="main-content wd-search-result-page">
	<div class="container">
		<div class="row">
			<div class="row wd-content-page wd-archive-blog-page wd-main-content">
				<!-- Left Content -->
				<?php if( ($layout == '1-0-0') || ($layout == '1-0-1') ) : ?> 
					<?php tvlgiao_wpdance_display_left_sidebar($sidebar_left); ?>
				<?php endif; // Endif Left?>
				
				<!-- Content Index -->
					<div class="wd-default-blog-archive <?php echo esc_attr($blog_search_masonry_class); ?> <?php echo esc_attr($content_class); ?>">
						<?php if ( have_posts() ) : ?>
							<div class="row">
								<!-- Start the Loop --> 
								<?php while ( have_posts() ) : the_post(); ?>
									<div class="<?php echo esc_attr($blog_search_item_masonry_class); ?> <?php echo esc_attr($columns_product); ?>">
										<?php get_template_part( 'template-parts/content', get_post_format() ); ?>
									</div>
								<?php endwhile; ?>
								<!-- End the Loop -->
							</div>
						<?php else: ?>
							<?php get_template_part( 'template-parts/content', 'none' ); ?>
						<?php endif; // End If Have Post ?>
					</div>
					<div class="wd-pagination">
						<?php tvlgiao_wpdance_pagination(); ?>
					</div>
				
				<!-- Right Content -->
				<?php if( ($layout == '0-0-1') || ($layout == '1-0-1') ) : ?> 
					<?php tvlgiao_wpdance_display_right_sidebar($sidebar_right); ?>
				<?php endif; // Endif Right?>	
			</div>
		</div>
	</div>
</div><!-- END CONTAINER  -->
<?php get_footer(); ?>
