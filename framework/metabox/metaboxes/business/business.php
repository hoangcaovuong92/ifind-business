<?php
if (!class_exists('iFind_Business')) {
	class iFind_Business {
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

		protected $post_type 	= 'business';
		protected $taxonomy 	= 'business_group';
		protected $arrShortcodes = array();

		public function __construct(){
			$this->constant();
			
			/****************************/
			// Register business post type
			add_action('init', array($this, 'register_post_type'));
			add_action('init', array( $this, 'register_taxonomy' ) );
			$this->category_metabox();

			add_filter('attribute_escape', array($this,'rename_second_menu_name') , 10, 2);
			add_theme_support('post-thumbnails', array($this->post_type) );

			//Change Placeholder Title Post
			add_filter( 'enter_title_here', array($this, 'change_title_text' ));
			
			add_action('admin_enqueue_scripts',array($this,'init_admin_script'));
			
			add_action('add_meta_boxes', array( $this,'create_metabox' ) );	
			add_action('pre_post_update', array($this,'metabox_save_data') , 10, 2);
			add_action('template_redirect', array($this,'template_redirect') );
			
			$this->init_handle();
		}
		
		protected function constant(){
			define('BUSINESS_BASE'		,   get_template_directory().'/framework/metabox/metaboxes/business');
			define('BUSINESS_BASE_URI'	,   get_template_directory_uri().'/framework/metabox/metaboxes/business');
			define('BUSINESS_JS'		, 	BUSINESS_BASE_URI . '/assets/js'		);
			define('BUSINESS_CSS'		, 	BUSINESS_BASE_URI . '/assets/css'		);
			define('BUSINESS_ADMIN_JS'	, 	BUSINESS_BASE_URI . '/admin/js'		);
			define('BUSINESS_ADMIN_CSS'	, 	BUSINESS_BASE_URI . '/admin/css'		);
			define('BUSINESS_ADMIN_LIB'	, 	BUSINESS_BASE_URI . '/admin/libs'		);
			define('BUSINESS_IMAGE'		, 	BUSINESS_BASE_URI . '/images'	);
			define('BUSINESS_TEMPLATE' 	, 	BUSINESS_BASE . '/templates'	);
			define('BUSINESS_WIDGET' 	, 	BUSINESS_BASE . '/widgets'				);
		}

		/******************************** business POST TYPE ***********************************/
		public function register_post_type(){
			if (!post_type_exists($this->post_type)) {
				register_post_type($this->post_type, array(
					'exclude_from_search' 	=> true, 
					'labels' 				=> array(
		                'name' 				=> _x('Business Directory', 'post type general name','ifind'),
		                'singular_name' 	=> _x('Business Directory', 'post type singular name','ifind'),
		                'add_new' 			=> _x('Add Business', 'Business','ifind'),
		                'add_new_item' 			=> sprintf( __( 'Add New %s', 'ifind' ), __( 'Business', 'ifind' ) ),
						'edit_item' 			=> sprintf( __( 'Edit %s', 'ifind' ), __( 'Business', 'ifind' ) ),
						'new_item' 				=> sprintf( __( 'New %s', 'ifind' ), __( 'Business', 'ifind' ) ),
						'all_items' 			=> sprintf( __( 'All %s', 'ifind' ), __( 'Businesss', 'ifind' ) ),
						'view_item' 			=> sprintf( __( 'View %s', 'ifind' ), __( 'Business', 'ifind' ) ),
						'search_items' 			=> sprintf( __( 'Search %a', 'ifind' ), __( 'Businesss', 'ifind' ) ),
						'not_found' 			=>  sprintf( __( 'No %s Found', 'ifind' ), __( 'Businesss', 'ifind' ) ),
						'not_found_in_trash' 	=> sprintf( __( 'No %s Found In Trash', 'ifind' ), __( 'Businesss', 'ifind' ) ),
		                'parent_item_colon' => '',
		                'menu_name' 		=> __('Business Directory','ifind'),
					),
					'singular_label' 		=> __('Business Directory','ifind'),
					'taxonomies' 			=> array($this->taxonomy),
					'public' 				=> true,
					'supports' 			 	=>  array('title'),
					'has_archive' 			=> false,
					'rewrite' 				=>  array('slug'  =>  $this->post_type, 'with_front' =>  true),
					'show_in_nav_menus' 	=> false,
					'menu_icon'				=> 'dashicons-businessman',
					'menu_position'			=> 6,
				));	
				flush_rewrite_rules();
			}
		}

		public function register_taxonomy(){
			register_taxonomy( $this->taxonomy, $this->post_type, array(
				'hierarchical'     		=> true,
				'labels'            	=> array(
					'name' 				=> esc_html__('Business Group', 'ifind'),
					'singular_name' 	=> esc_html__('Business Group', 'ifind'),
	            	'new_item'          => esc_html__('Add New', 'ifind' ),
	            	'edit_item'         => esc_html__('Edit Post', 'ifind' ),
	            	'view_item'   		=> esc_html__('View Post', 'ifind' ),
	            	'add_new_item'      => esc_html__('Add New Business Group', 'ifind' ),
	            	'menu_name'         => esc_html__( 'Business Group' , 'ifind' ),
				),
				'show_ui'           	=> true,
				'show_admin_column' 	=> true,
				'query_var'         	=> true,
				'rewrite'           	=> array( 'slug' => $this->taxonomy ),				
				'public'				=> true,
			));	
		}
		public function category_metabox(){
			if (file_exists(BUSINESS_BASE.'/metabox_category.php')) {
				require_once BUSINESS_BASE.'/metabox_category.php';
			}
		}

		/******************************** business POST TYPE INIT START ***********************************/

		public function metabox_save_data($post_id) {

			if ( ! isset( $_POST['business_box_nonce'] ) )
				return $post_id;
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if (!wp_verify_nonce($_POST['business_box_nonce'],'business_box'))
				return $post->ID;
			if (!current_user_can('edit_post', $post->ID))
				return $post->ID;

			$data = array();
			if (isset($_POST['business'])) {
				$data['business'] = $_POST['business'];
			}
			update_post_meta($post_id,'business_meta_data', serialize($data));
			
		}

		public function process_meta_data_repeatable_field_after_save($meta_key, $list_meta_name){
			$data 	= array();
			if (isset($_POST[$meta_key])) {
				foreach ($list_meta_name as $name) {
					if (count($_POST[$meta_key][$name]) > 0) {
						foreach ($_POST[$meta_key][$name] as $key => $value) {
							$data[$key][$name] = $value;
						}
					}
				}
				//Remove last item (repeatable field)
				unset($data[count($data)-1]);
			}
			return $data;
		}

		public function get_meta_data_default($field = ''){
			$default = array(
				'business' 	=> array(
					'office_address'	=> '',
					'office_phone'		=> '',
					'location_data'	=> array(
						'lat'	=> 1,
						'lng'	=> 1,
						'address'	=> '',
					),
					'logo'	=> '',
					'small_banner'	=> '',
					'large_banner'	=> '',
					'info_banner'	=> '',
					'youtube_video_id'	=> '',
				),
			);
			return ($field && isset($default[$field])) ? $default[$field] : $default;
		}

		public function get_meta_data($field = ''){
			$default = $this->get_meta_data_default();
			$meta_data = get_post_meta( get_the_ID(), 'business_meta_data', true );
			$meta_data = ($meta_data) ? wp_parse_args( unserialize($meta_data), $default ) : array();
			return ($field && isset($meta_data[$field])) ? $meta_data[$field] : $meta_data;
		}	
		
		public function template_redirect(){
		}
		
		public function create_metabox() {
			if(post_type_exists($this->post_type)) {
				add_meta_box("wp_cp_business_info", "Business Metadata", array($this,"metabox_form"), $this->post_type, "normal", "high");
			}
		}

		public function metabox_form(){
			wp_nonce_field( 'business_box', 'business_box_nonce' );
			$random_id 	= 'wd-business-metabox-'.mt_rand();
			$meta_key 	= 'business';
			$meta_data 	= $this->get_meta_data($meta_key);
			$meta_data 	= empty($meta_data) ? $this->get_meta_data_default($meta_key) : $meta_data;
			?>
			<table id="<?php echo esc_attr( $random_id ); ?>" class="form-table wd-business-custom-meta-box wd-custom-meta-box-width">
				<tbody>
				<?php 
					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Address', 'ifind' ),
						"desc" => esc_html__( 'Address of the office.', 'ifind' ),
						"placeholder" => esc_html__( 'Exam: Jax Tyres Noosaville, 139 Eumundi Noosa Road, Noosaville QLD 4566', 'ifind' ),
						"field_name" => "business[office_address]",
						"value" => $meta_data['office_address'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Contact Number', 'ifind' ),
						"desc" => esc_html__( 'Contact Number of the office.', 'ifind' ),
						"placeholder" => esc_html__( 'Exam: (07) 5473 3776', 'ifind' ),
						"field_name" => "business[office_phone]",
						"value" => $meta_data['office_phone'],
					));

					iFind_Admin_Metabox_Fields::get_map_field(array(
						"title" => esc_html__( 'Location', 'ifind' ),
						"desc" => "",
						"field_name" => "business[location_data]",
						"value" => $meta_data['location_data'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Logo', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 150x150 px', 'ifind' ),
						"field_name" => "business[logo]",
						"value" => $meta_data['logo'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Small Banner', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 1080x360 px. Banner appears on header slider.', 'ifind' ),
						"field_name" => "business[small_banner]",
						"value" => $meta_data['small_banner'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Large Banner', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 1080x1920 px. Banner appears on popup slider fullscreen.', 'ifind' ),
						"field_name" => "business[large_banner]",
						"value" => $meta_data['large_banner'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Info Banner', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 1036x736 px. Banner appears when click logo image.', 'ifind' ),
						"field_name" => "business[info_banner]",
						"value" => $meta_data['info_banner'],
					));
					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Youtube Video ID', 'ifind' ),
						"desc" => esc_html__( 'Video appears when click logo image instead of info banner.', 'ifind' ),
						"placeholder" => esc_html__( "Exam: HHmidNM2sOM", 'ifind' ),
						"field_name" => "business[youtube_video_id]",
						"value" => $meta_data['youtube_video_id'],
					));
					?>	
				</tbody>
			</table>
		<?php
		}

		public function change_title_text( $title ){
		    $screen = get_current_screen();
		  
		    if  ( $this->post_type == $screen->post_type ) {
		        $title = esc_html__("Enter the Business name here", 'ifind' );
		    }
		    return $title;
		}
		
		public function rename_second_menu_name($safe_text, $text) {
			if (__('Business Items', 'ifind') !== $text) {
				return $safe_text;
			}

			// We are on the main menu item now. The filter is not needed anymore.
			remove_filter('attribute_escape', array($this,'rename_second_menu_name') );

			return __('Business', 'ifind');
		}

	    protected function init_handle(){
			if( file_exists(BUSINESS_TEMPLATE . "/functions.php") ){
				require_once BUSINESS_TEMPLATE . "/functions.php";
			}
			//add_image_size('wd-business-thumb',400,400,true);  
		}	
		
		public function init_admin_script($hook) {
			$screen = get_current_screen();
			if ($hook = 'post.php' && $this->post_type == $screen->post_type) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style('wd-business-admin-custom-css', 	BUSINESS_ADMIN_CSS.'/wd_admin.css');
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'wd-business-scripts',		 	BUSINESS_ADMIN_JS.'/wd_script.js',false,false,true);
			}
			
		}	
		
		
		public function init_script(){
			wp_enqueue_style('wd-business-custom-css', 	BUSINESS_CSS.'/wd_custom.css');	
			wp_enqueue_script( 'wd-business-scripts',	BUSINESS_JS.'/wd_ajax.js',false,false,true);
		}


		/******************************** Check Visual Composer active ***********************************/
		protected function checkPluginVC(){
			$_active_vc = apply_filters('active_plugins',get_option('active_plugins'));
			if(in_array('js_composer/js_composer.php',$_active_vc)){
				return true;
			}else{
				return false;
			}
		}

	}
	iFind_Business::get_instance();  // Start an instance of the plugin class 
}