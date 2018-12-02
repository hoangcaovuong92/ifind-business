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

		protected $post_type 				= 'business';
		protected $post_type_single_name 	= 'Business';
		protected $post_type_multiple_name 	= 'Businesses';
		protected $post_type_display_name 	= 'Business Directory';
		protected $post_type_icon 			= 'dashicons-businessman';
		protected $post_type_position		= 6;
		protected $taxonomy 				= 'business_group';
		protected $taxonomy_single_name		= 'Business Group';
		protected $arrShortcodes 			= array();

		public function __construct(){
			$this->constant();
			
			/****************************/
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
			add_action('deleted_post', array($this, 'delete_post'));

			add_action('template_redirect', array($this,'template_redirect') );
			
			$this->init_handle();
		}
		
		protected function constant(){
			define('BUSINESS_BASE'		,   get_template_directory().'/framework/metabox/metaboxes/business');
			define('BUSINESS_BASE_URI'	,   get_template_directory_uri().'/framework/metabox/metaboxes/business');
			define('BUSINESS_JS'		, 	BUSINESS_BASE_URI . '/assets/js'		);
			define('BUSINESS_CSS'		, 	BUSINESS_BASE_URI . '/assets/css'		);
			define('BUSINESS_ADMIN_JS'	, 	BUSINESS_BASE_URI . '/admin/js'			);
			define('BUSINESS_ADMIN_CSS'	, 	BUSINESS_BASE_URI . '/admin/css'		);
			define('BUSINESS_ADMIN_LIB'	, 	BUSINESS_BASE_URI . '/admin/libs'		);
			define('BUSINESS_IMAGE'		, 	BUSINESS_BASE_URI . '/images'			);
			define('BUSINESS_TEMPLATE' 	, 	BUSINESS_BASE . '/templates'			);
			define('BUSINESS_WIDGET' 	, 	BUSINESS_BASE . '/widgets'				);
		}

		/******************************** REGISTER POST TYPE ***********************************/
		public function register_post_type(){
			if (!post_type_exists($this->post_type)) {
				register_post_type($this->post_type, array(
					'exclude_from_search' 	=> true, 
					'labels' 				=> array(
		                'name' 				=> $this->post_type_display_name,
		                'singular_name' 	=> $this->post_type_display_name,
		                'add_new' 			=> sprintf( __( 'Add %s', 'ifind' ), $this->post_type_single_name ),
		                'add_new_item' 		=> sprintf( __( 'Add New %s', 'ifind' ), $this->post_type_single_name ),
						'edit_item' 		=> sprintf( __( 'Edit %s', 'ifind' ), $this->post_type_single_name ),
						'new_item' 			=> sprintf( __( 'New %s', 'ifind' ), $this->post_type_single_name ),
						'all_items' 		=> sprintf( __( 'All %s', 'ifind' ), $this->post_type_multiple_name ),
						'view_item' 		=> sprintf( __( 'View %s', 'ifind' ), $this->post_type_single_name ),
						'search_items' 		=> sprintf( __( 'Search %a', 'ifind' ), $this->post_type_multiple_name ),
						'not_found' 		=> sprintf( __( 'No %s Found', 'ifind' ), $this->post_type_multiple_name ),
						'not_found_in_trash'=> sprintf( __( 'No %s Found In Trash', 'ifind' ), $this->post_type_multiple_name ),
		                'parent_item_colon' => '',
		                'menu_name' 		=> $this->post_type_display_name,
					),
					'singular_label' 		=> $this->post_type_display_name,
					'taxonomies' 			=> array($this->taxonomy),
					'public' 				=> true,
					'supports' 			 	=>  array('title'),
					'has_archive' 			=> false,
					'rewrite' 				=>  array('slug'  =>  $this->post_type, 'with_front' =>  true),
					'show_in_nav_menus' 	=> false,
					'menu_icon'				=> $this->post_type_icon,
					'menu_position'			=> $this->post_type_position,
				));	
				flush_rewrite_rules();
			}
		}

		public function register_taxonomy(){
			register_taxonomy( $this->taxonomy, $this->post_type, array(
				'hierarchical'     		=> true,
				'labels'            	=> array(
					'name' 				=> $this->taxonomy_single_name,
					'singular_name' 	=> $this->taxonomy_single_name,
	            	'new_item'          => sprintf( esc_html__('Add New %s', 'ifind' ), $this->taxonomy_single_name ),
	            	'edit_item'         => sprintf( esc_html__('Edit %s', 'ifind' ), $this->taxonomy_single_name ),
	            	'view_item'   		=> sprintf( esc_html__('View %s', 'ifind' ), $this->taxonomy_single_name ),
	            	'add_new_item'      => sprintf( esc_html__('Add New %s', 'ifind' ), $this->taxonomy_single_name ),
	            	'menu_name'         => $this->taxonomy_single_name,
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

		public function get_meta_data_default($field = ''){
			$default = array(
				$this->post_type		=> array(
					'office_address'	=> '',
					'office_phone'		=> '',
					'email_address'		=> '',
					'qrcode'			=> array(
						'qr_type' 			=> 'url',
						'qr_content' 		=> ''
					),
					'location_data'		=> array(
						'lat'				=> 1,
						'lng'				=> 1,
						'address'			=> '',
					),
					'logo'				=> '',
					'small_banner'		=> '',
					'large_banner'		=> '',
					'info_banner_type'	=> 'image',
					'info_banner'		=> '',
					'info_banner_file'	=> '',
					'video_file'		=> '',
					'youtube_video_id'	=> '',
				),
			);
			return ($field && isset($default[$field])) ? $default[$field] : $default;
		}

		public function metabox_form(){
			wp_nonce_field( $this->post_type.'_box', $this->post_type.'_box_nonce' );
			$random_id 	= 'wd-'.$this->post_type.'-metabox-'.mt_rand();
			$meta_key 	= $this->post_type;
			$meta_data 	= $this->get_meta_data($meta_key);
			$meta_data 	= empty($meta_data) ? $this->get_meta_data_default($meta_key) : $meta_data;
			$info_banner_type_options = array(
				'image' => esc_html__( 'Image', 'ifind' ),
				'content-file' => esc_html__( 'Content File', 'ifind' ),
				'content-file-and-video' => esc_html__( 'Content File & Video', 'ifind' ),
				'video-file' => esc_html__( 'Video file', 'ifind' ),
				'video-youtube' => esc_html__( 'Youtube video', 'ifind' ),
			);
			?>
			<table id="<?php echo esc_attr( $random_id ); ?>" class="form-table wd-<?php echo $this->post_type; ?>-custom-meta-box wd-custom-meta-box-width">
				<tbody>
				<?php 
					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Address', 'ifind' ),
						"desc" => esc_html__( 'Address of the office.', 'ifind' ),
						"placeholder" => esc_html__( 'Exam: Jax Tyres Noosaville, 139 Eumundi Noosa Road, Noosaville QLD 4566', 'ifind' ),
						"field_name" => $this->post_type."[office_address]",
						"value" => $meta_data['office_address'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Contact Number', 'ifind' ),
						"desc" => esc_html__( 'Contact Number of the office.', 'ifind' ),
						"placeholder" => esc_html__( 'Exam: (07) 5473 3776', 'ifind' ),
						"field_name" => $this->post_type."[office_phone]",
						"value" => $meta_data['office_phone'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Email Address', 'ifind' ),
						"desc" => esc_html__( 'Email address of the office.', 'ifind' ),
						"placeholder" => esc_html__( 'Exam: hoangcaovuong92@gmail.com', 'ifind' ),
						"field_name" => $this->post_type."[email_address]",
						"value" => $meta_data['email_address'],
					));

					iFind_Admin_Metabox_Fields::get_qrcode_field(array(
						"title" => esc_html__( 'Website', 'ifind' ),
						"desc" => esc_html__( '.', 'ifind' ),
						"placeholder" => esc_html__( 'Exam: ifindsystem.com', 'ifind' ),
						"field_name" => $this->post_type."[qrcode]",
						"value" => $meta_data['qrcode'],
					));

					iFind_Admin_Metabox_Fields::get_map_field(array(
						"title" => esc_html__( 'Location', 'ifind' ),
						"desc" => "",
						"field_name" => $this->post_type."[location_data]",
						"value" => $meta_data['location_data'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Logo', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 150x150 px', 'ifind' ),
						"field_name" => $this->post_type."[logo]",
						"value" => $meta_data['logo'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Small Banner', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 1080x360 px. Banner appears on header slider.', 'ifind' ),
						"field_name" => $this->post_type."[small_banner]",
						"value" => $meta_data['small_banner'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Large Banner', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 1080x1920 px. Banner appears on popup slider fullscreen.', 'ifind' ),
						"field_name" => $this->post_type."[large_banner]",
						"value" => $meta_data['large_banner'],
					));

					iFind_Admin_Metabox_Fields::get_select_field(array(
						"title" => esc_html__( 'Info Banner Type', 'ifind' ),
						"desc" => esc_html__('Select the content will be displayed when click to business.', 'ifind' ),
						"field_name" => $this->post_type."[info_banner_type]",
						"options" => $info_banner_type_options,
						"value" => $meta_data['info_banner_type'],
					));

					iFind_Admin_Metabox_Fields::get_image_field(array(
						"title" => esc_html__( 'Info Banner Image', 'ifind' ),
						"desc" => esc_html__( 'Recommend size: 1036x736 px. Banner appears when click logo image.', 'ifind' ),
						"field_name" => $this->post_type."[info_banner]",
						"value" => $meta_data['info_banner'],
					));
					iFind_Admin_Metabox_Fields::get_file_field(array(
						"title" => esc_html__( 'Info Banner Content File', 'ifind' ),
						"desc" => esc_html__( 'Choose a html file ...', 'ifind' ),
						"placeholder" => '',
						"button_title" => esc_html__( 'Select HTML File', 'ifind' ),
						"field_name" => $this->post_type."[info_banner_file]",
						"value" => $meta_data['info_banner_file'],
					));
					iFind_Admin_Metabox_Fields::get_file_field(array(
						"title" => esc_html__( 'Video File', 'ifind' ),
						"desc" => esc_html__( 'Video appears when click logo image instead of info banner.', 'ifind' ),
						"placeholder" => esc_html__( "Exam: www.ifindsystem.com/test.mp4", 'ifind' ),
						"button_title" => esc_html__( 'Select Video File', 'ifind' ),
						"field_name" => $this->post_type."[video_file]",
						"value" => $meta_data['video_file'],
					));
					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Youtube Video ID', 'ifind' ),
						"desc" => esc_html__( 'Video appears when click logo image instead of info banner.', 'ifind' ),
						"placeholder" => esc_html__( "Exam: HHmidNM2sOM", 'ifind' ),
						"field_name" => $this->post_type."[youtube_video_id]",
						"value" => $meta_data['youtube_video_id'],
					));
					?>	
				</tbody>
			</table>
		<?php
		}
		/******************************** POST TYPE INIT START ***********************************/
		public function metabox_save_data($post_id) {
			if ( ! isset( $_POST[$this->post_type.'_box_nonce'] ) )
				return $post_id;
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if (!wp_verify_nonce($_POST[$this->post_type.'_box_nonce'],$this->post_type.'_box'))
				return $post->ID;
			if (!current_user_can('edit_post', $post->ID))
				return $post->ID;

			$data = array();
			if (isset($_POST[$this->post_type])) {
				$data[$this->post_type] = $_POST[$this->post_type];
			}
			update_post_meta($post_id, $this->post_type.'_meta_data', serialize($data));
		}

		public function delete_post( $post_id ){
			$meta_key = $this->post_type.'_meta_data';
			delete_post_meta($post_id, $meta_key);
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

		public function get_meta_data($field = ''){
			$default = $this->get_meta_data_default();
			$meta_data = get_post_meta( get_the_ID(), $this->post_type.'_meta_data', true );
			$meta_data = ($meta_data) ? wp_parse_args( unserialize($meta_data), $default ) : array();
			return ($field && isset($meta_data[$field])) ? $meta_data[$field] : $meta_data;
		}	
		
		public function template_redirect(){
		}
		
		public function create_metabox() {
			if(post_type_exists($this->post_type)) {
				add_meta_box("wp_cp_".$this->post_type."_info", $this->post_type_single_name." Metadata", array($this,"metabox_form"), $this->post_type, "normal", "high");
			}
		}

		public function change_title_text( $title ){
		    $screen = get_current_screen();
		  
		    if  ( $this->post_type == $screen->post_type ) {
		        $title = sprintf( __( 'Enter the %s name here', 'ifind' ), $this->post_type_single_name );
		    }
		    return $title;
		}
		
		public function rename_second_menu_name($safe_text, $text) {
			if (sprintf(__('%s Items', 'ifind'), $this->post_type_single_name) !== $text) {
				return $safe_text;
			}
			// We are on the main menu item now. The filter is not needed anymore.
			remove_filter('attribute_escape', array($this,'rename_second_menu_name') );
			return $this->post_type_single_name;
		}

	    protected function init_handle(){
			if( file_exists(BUSINESS_TEMPLATE . "/functions.php") ){
				require_once BUSINESS_TEMPLATE . "/functions.php";
			}
			//add_image_size('wd-'.$this->post_type.'-thumb',400,400,true);  
		}
		
		public function init_admin_script($hook) {
			$screen = get_current_screen();
			if ($hook = 'post.php' && $this->post_type == $screen->post_type) {
				wp_enqueue_style('wd-'.$this->post_type.'-admin-custom-css', 	BUSINESS_ADMIN_CSS.'/wd_admin.css');
				wp_enqueue_script( 'wd-'.$this->post_type.'-scripts',		 	BUSINESS_ADMIN_JS.'/wd_script.js',false,false,true);

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			}
		}
		
		
		public function init_script(){
			wp_enqueue_style('wd-'.$this->post_type.'-custom-css', 	BUSINESS_CSS.'/wd_custom.css');	
			wp_enqueue_script( 'wd-'.$this->post_type.'-scripts',	BUSINESS_JS.'/wd_ajax.js',false,false,true);
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