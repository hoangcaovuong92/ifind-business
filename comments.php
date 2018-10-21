<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to wpdance_comment which is
 * located in the functions.php file.
 *
 * @package Wordpress
 * @since wpdance
 */
?>
<div id="wd-comments">
	<?php 
	$comment_number 		= get_comments_number() < 10 && get_comments_number() > 0 ? '0'.get_comments_number() : get_comments_number() ;
	$comments_list_text 	= sprintf( _n( '%s comment', '%s comments', $comment_number, 'ifind' ), $comment_number );
	$comment_list_status 	= (get_comments_number() > 0) ? true : false;
 
	$comment_tabs		= array(
		'comments_list'	=> array(
				'status'	=> $comment_list_status,
				'callback' 	=> 'tvlgiao_wpdance_comments_list',
				'text'		=> $comments_list_text,
			),
		'comment_form'	=> array(
				'status'	=> comments_open(),
				'callback' 	=> 'tvlgiao_wpdance_comment_form',
				'text'		=> esc_html__( 'POST A COMMENT', 'ifind' ),
			),
	);

	/**
     * package: comment-layout
	 * var: display_tab
	 */
	extract(tvlgiao_wpdance_get_data_package( 'comment-layout' ));

	if ($display_tab && ($comment_tabs['comments_list']['status'] || $comment_tabs['comment_form']['status'])) { ?>
		<div class="wd-blog-comment-tab">
			<ul class="nav nav-tabs">
				<?php 
				$i = 0;
				foreach ($comment_tabs as $tab) { ?>
					<?php if ($tab['status']) { 
						$i++; ?>
						<li class="<?php echo $i == 1 ? 'active' : ''; ?>"><a data-toggle="tab" href="#wd-comment-tab-<?php echo $i; ?>"><?php echo esc_html($tab['text']); ?></a></li>
					<?php } 
				} ?>
			</ul>
		</div>
		<div class="tab-content">
			<?php 
			$i = 0;
			foreach ($comment_tabs as $tab) { ?>
				<?php if ($tab['status']) {
					$i++; ?>
					<div id="wd-comment-tab-<?php echo $i; ?>" class="tab-pane fade <?php echo $i == 1 ? 'active in active' : ''; ?>">
						<?php call_user_func($tab['callback']); ?>
					</div>	
				<?php } 
			} ?>
		</div>
	<?php
	}else{
		foreach ($comment_tabs as $tab) {
			if ($tab['status']) {
				call_user_func($tab['callback']);
			}
		}
	}
	?>
</div>