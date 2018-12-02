<?php
if (!class_exists('iFind_Admin_Page')) {
	class iFind_Admin_Page {
		/**
		 * Refers to a single instance of this class.
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct(){
			$this->constant();
			add_action('admin_enqueue_scripts', array( $this, 'admin_init_script' ));
			if($this->get_current_user_role() == 'administrator') {
				add_action('admin_menu', array($this, 'report_menu'));
			}
		}
		
		protected function constant(){
			define('WDADMIN_BASE'		,   plugin_dir_path( __FILE__ ));
			define('WDADMIN_BASE_URI'	,   plugins_url( '', __FILE__ ));
			
			define('WDADMIN_JS'			, 	WDADMIN_BASE_URI . '/js'		);
			define('WDADMIN_CSS'		, 	WDADMIN_BASE_URI . '/css'		);
			define('WDADMIN_IMAGE'		, 	WDADMIN_BASE_URI . '/images'	);
		}
		/******************************** Team POST TYPE INIT START ***********************************/

		public function report_menu(){
		    add_menu_page( //or add_theme_page
		        'Statistics',     // page title
		        'Statistics',     // menu title
		        'manage_options',   // capability
		        'ifind-setting',     // menu slug
		        array($this, 'admin_page_callback'), // callback function
		        'dashicons-media-spreadsheet', //icon (dashicons-universal-access-alt)
		        7 //position
		    );
		}

		public function admin_page_callback(){ ?>
			<div class="wrap">
				<h1><?php esc_html_e("Statistics", 'ifind'); ?></h1>
				<div class="ifind-statistics-wrap">
					<table class="form-table">
						<?php $this->display_list_business_form(); ?>
					</table>
				</div>
				<div id="ifind-business-statistics-result"></div>
				<form method="post" class="send-statistics-mail-form" name="send-statistics-mail-form" action="">
					<img src="<?php echo TVLGIAO_WPDANCE_THEME_IMAGES.'/send_email.png'; ?>" alt="">
					<table class="form-table">
						<tr valign="top">
							<td class="send-statistics-mail-form-input-wrap"><span><?php esc_html_e("Email Address:", 'ifind'); ?></span>
								<input type="email" name="email" value="" placeholder="<?php esc_html_e("Enter email here...", 'ifind'); ?>" /></td>
							<th scope="row"></th>
							<td>
							<label><input type="checkbox" checked name="attachment" value="0" />
								<?php esc_html_e("Attachment (PDF)", 'ifind'); ?></label>
							</td>
							<td><p id="attachment_link"></p></td>
							<td>
								<span class="ifind-loading-icon"><img src="<?php echo TVLGIAO_WPDANCE_THEME_IMAGES.'/loading.gif'; ?>" alt=""></span>
								<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e("Send An Email", 'ifind'); ?>">
							</td>
						</tr>
					</table>
				</form>
			</div>
		<?php
		} //end content admin page

		public function display_list_business_form(){ ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Select a location name:", 'ifind'); ?></th>
					<td>
						<select style="min-width: 330px;" name="ifind-location-list-select" id="ifind-location-list-select">
							<?php
								$args = array(
									'post_type'			=> 'location',
									'post_status'		=> 'publish',
								);
								$location_list = array('-1' => esc_html__("All locations", 'ifind'));
								$data = new WP_Query($args);
								if( $data->have_posts() ){
									while( $data->have_posts() ){
										$data->the_post();
										global $post;
										$location_list[$post->ID] = html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' );
									}
								}
								wp_reset_postdata(); ?>
								<?php if (count($location_list) > 0) { ?>
									<?php foreach ($location_list as $key => $value): ?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php endforeach; ?>
								<?php } ?>
							?>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e("Select a business name:", 'ifind'); ?></th>
					<td>
						<select style="min-width: 330px;" name="ifind-business-list-select" id="ifind-business-list-select">
							<?php 
							$args = array(
								'post_type'			=> 'business',
								'post_status'		=> 'publish',
								'posts_per_page' 	=> -1,
							);
							$business_list = array();
							$data = new WP_Query($args);
							if( $data->have_posts() ){
								while( $data->have_posts() ){
									$data->the_post();
									global $post;
									$business_list[$post->ID] = html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' );
								}
							}
							wp_reset_postdata(); ?>
							<?php if (count($business_list) > 0) { ?>
								<?php foreach ($business_list as $key => $value): ?>
									<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
								<?php endforeach; ?>
							<?php } ?>
						</select>
					</td>
				</tr>
				<!-- <tr valign="top">
					<th scope="row"><?php //esc_html_e("Filter by day:", 'ifind'); ?></th>
					<td><input type="checkbox" name="ifind-business-filter-by-day" id="ifind-business-filter-by-day"></td>
				</tr> -->
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Select a day:", 'ifind'); ?></th>
					<td>
						<input type="text" class="datepicker" name="ifind-business-select-day-from" id="datepicker_from" placeholder="<?php esc_html_e("From...", 'ifind'); ?>">
						<input type="text" class="datepicker" name="ifind-business-select-day-to" id="datepicker_to" placeholder="<?php esc_html_e("To...", 'ifind'); ?>">
					</td>
				</tr>
		<?php
		} //end content admin page

		public function admin_init_script($hook){
			if ($hook == 'toplevel_page_ifind-setting') {
				//wp_enqueue_style('ifind-form-css', 				WDADMIN_CSS.'/form.css');
				//wp_enqueue_style('ifind-admin-page-css', 			WDADMIN_CSS.'/style.css');
			}
		}

		function get_current_user_role( $user = null ) {
			$user = $user ? new WP_User( $user ) : wp_get_current_user();
			return $user->roles ? $user->roles[0] : false;
		}
	}
	iFind_Admin_Page::get_instance();  // Start an instance of the plugin class 
}

