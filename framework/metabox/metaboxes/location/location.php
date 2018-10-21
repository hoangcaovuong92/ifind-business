<?php
if (!class_exists('iFind_Location')) {
	class iFind_Location {
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

		protected $post_type 	= 'location';
		protected $taxonomy 	= 'location_categories';
		protected $arrShortcodes = array();

		public function __construct(){
			$this->constant();
			
			/****************************/
			// Register location post type
			add_action('init', array($this, 'register_post_type'));
			//add_action('init', array( $this, 'register_taxonomy' ) );

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
			define('LOCATION_BASE'		,   get_template_directory().'/framework/metabox/metaboxes/location');
			define('LOCATION_BASE_URI'	,   get_template_directory_uri().'/framework/metabox/metaboxes/location');
			define('LOCATION_JS'		, 	LOCATION_BASE_URI . '/assets/js'		);
			define('LOCATION_CSS'		, 	LOCATION_BASE_URI . '/assets/css'		);
			define('LOCATION_ADMIN_JS'	, 	LOCATION_BASE_URI . '/admin/js'		);
			define('LOCATION_ADMIN_CSS'	, 	LOCATION_BASE_URI . '/admin/css'		);
			define('LOCATION_ADMIN_LIB'	, 	LOCATION_BASE_URI . '/admin/libs'		);
			define('LOCATION_IMAGE'		, 	LOCATION_BASE_URI . '/images'	);
			define('LOCATION_TEMPLATE' 	, 	LOCATION_BASE . '/templates'	);
			define('LOCATION_WIDGET' 	, 	LOCATION_BASE . '/widgets'				);
		}

		/******************************** location POST TYPE ***********************************/
		public function register_post_type(){
			if (!post_type_exists($this->post_type)) {
				register_post_type($this->post_type, array(
					'exclude_from_search' 	=> true, 
					'labels' 				=> array(
		                'name' 				=> _x('Locations', 'post type general name','ifind'),
		                'singular_name' 	=> _x('Locations', 'post type singular name','ifind'),
		                'add_new' 			=> _x('Add Location', 'Location','ifind'),
		                'add_new_item' 			=> sprintf( __( 'Add New %s', 'ifind' ), __( 'Location', 'ifind' ) ),
						'edit_item' 			=> sprintf( __( 'Edit %s', 'ifind' ), __( 'Location', 'ifind' ) ),
						'new_item' 				=> sprintf( __( 'New %s', 'ifind' ), __( 'Location', 'ifind' ) ),
						'all_items' 			=> sprintf( __( 'All %s', 'ifind' ), __( 'Locations', 'ifind' ) ),
						'view_item' 			=> sprintf( __( 'View %s', 'ifind' ), __( 'Location', 'ifind' ) ),
						'search_items' 			=> sprintf( __( 'Search %a', 'ifind' ), __( 'Locations', 'ifind' ) ),
						'not_found' 			=>  sprintf( __( 'No %s Found', 'ifind' ), __( 'Locations', 'ifind' ) ),
						'not_found_in_trash' 	=> sprintf( __( 'No %s Found In Trash', 'ifind' ), __( 'Locations', 'ifind' ) ),
		                'parent_item_colon' => '',
		                'menu_name' 		=> __('Locations','ifind'),
					),
					'singular_label' 		=> __('Locations','ifind'),
					'taxonomies' 			=> array($this->taxonomy),
					'public' 				=> true,
					'has_archive' 			=> false,
					'supports' 			 	=>  array('title'),
					'has_archive' 			=> false,
					'rewrite' 				=>  array('slug'  =>  $this->post_type, 'with_front' =>  true),
					'show_in_nav_menus' 	=> false,
					'menu_icon'				=> 'dashicons-star-filled',
					'menu_position'			=> 5,
				));	
				flush_rewrite_rules();
			}
		}

		public function register_taxonomy(){
			register_taxonomy( $this->taxonomy, $this->post_type, array(
				'hierarchical'     		=> true,
				'labels'            	=> array(
					'name' 				=> esc_html__('Categories Location', 'ifind'),
					'singular_name' 	=> esc_html__('Category Location', 'ifind'),
	            	'new_item'          => esc_html__('Add New', 'ifind' ),
	            	'edit_item'         => esc_html__('Edit Post', 'ifind' ),
	            	'view_item'   		=> esc_html__('View Post', 'ifind' ),
	            	'add_new_item'      => esc_html__('Add New Category Location', 'ifind' ),
	            	'menu_name'         => esc_html__( 'Categories Location' , 'ifind' ),
				),
				'show_ui'           	=> true,
				'show_admin_column' 	=> true,
				'query_var'         	=> true,
				'rewrite'           	=> array( 'slug' => $this->taxonomy ),				
				'public'				=> true,
			));	
		}

		/******************************** location POST TYPE INIT START ***********************************/

		public function metabox_save_data($post_id) {

			if ( ! isset( $_POST['location_box_nonce'] ) )
				return $post_id;
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if (!wp_verify_nonce($_POST['location_box_nonce'],'location_box'))
				return $post->ID;
			if (!current_user_can('edit_post', $post->ID))
				return $post->ID;

			$data = array();
			if (isset($_POST['location'])) {
				$data['location'] = $_POST['location'];
			}
			update_post_meta($post_id,'location_meta_data', serialize($data));
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
				'location' 	=> array(
					'slogan'	=> '',
					'office_hours'	=> '',
					'contact_number'	=> '',
					'max_distance'	=> '',
					'location_data'	=> array(
						'lat'	=> 1,
						'lng'	=> 1,
						'address'	=> '',
					),
					'list_business'	=> '',
				),
			);
			return ($field && isset($default[$field])) ? $default[$field] : $default;
		}

		public function get_meta_data($field = ''){
			$default = $this->get_meta_data_default();
			$meta_data = get_post_meta( get_the_ID(), 'location_meta_data', true );
			$meta_data = ($meta_data) ? wp_parse_args( unserialize($meta_data), $default ) : array();
			return ($field && isset($meta_data[$field])) ? $meta_data[$field] : $meta_data;
		}	
		
		public function template_redirect(){
		}
		
		public function create_metabox() {
			if(post_type_exists($this->post_type)) {
				add_meta_box("wp_cp_location_info", "Location Metadata", array($this,"metabox_form"), $this->post_type, "normal", "high");
			}
		}

		public function metabox_form(){
			wp_nonce_field( 'location_box', 'location_box_nonce' );
			$random_id 	= 'wd-location-metabox-'.mt_rand();
			$meta_key 	= 'location';
			$meta_data 	= $this->get_meta_data($meta_key);
			$meta_data 	= empty($meta_data) ? $this->get_meta_data_default($meta_key) : $meta_data;
			?>
			<table id="<?php echo esc_attr( $random_id ); ?>" class="form-table wd-location-custom-meta-box wd-custom-meta-box-width">
				<tbody>
					<?php 
					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Slogan', 'ifind' ),
						"desc" => "",
						"placeholder" => "",
						"field_name" => "location[slogan]",
						"value" => $meta_data['slogan'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Office Hours', 'ifind' ),
						"desc" => "",
						"placeholder" => "",
						"field_name" => "location[office_hours]",
						"value" => $meta_data['office_hours'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Contact Number', 'ifind' ),
						"desc" => "",
						"placeholder" => "",
						"field_name" => "location[contact_number]",
						"value" => $meta_data['contact_number'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Max Distance', 'ifind' ),
						"desc" => esc_html__( 'If the distance from location to business exceeds "Max distance", it will not be displayed. Unit: km', 'ifind' ),
						"placeholder" => esc_html__( 'Set -1 or leave blank to remove this limit.', 'ifind' ),
						"field_name" => "location[max_distance]",
						"value" => $meta_data['max_distance'],
					));

					iFind_Admin_Metabox_Fields::get_checkbox_field(array(
						"title" => esc_html__( 'List Business', 'ifind' ),
						"desc" => "",
						"field_name" => "location[list_business]",
						"options" => $this->get_list_business(),
						"value" => $meta_data['list_business'],
					));

					iFind_Admin_Metabox_Fields::get_map_field(array(
						"title" => esc_html__( 'Location Address', 'ifind' ),
						"desc" => "",
						"placeholder" => __( 'Enter a location', 'francois' ),
						"field_name" => "location[location_data]",
						"value" => $meta_data['location_data'],
					));
				?>
				</tbody>
			</table>
		<?php
		}

		public function get_list_business($post_type = 'business', $args = array()){
			$args_default = array(
				'post_type'			=> $post_type,
				'post_status'		=> 'publish',
				'posts_per_page' 	=> -1,
			);
			$args = wp_parse_args( $args, $args_default );
			$data_array = array();
			global $post;
			$data = new WP_Query($args);
			if( $data->have_posts() ){
				while( $data->have_posts() ){
					$data->the_post();
					$link = sprintf(' <a target="_blank" href="%s">'.__('Edit', 'ifind').'</a>', get_edit_post_link( $post->ID ));
					$data_array[$post->ID] = html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' ).$link;
				}
			}else{
				$data_array[] = sprintf(__( "Please add data for \"%s\" before", 'wd_package' ), $post_type);
			}
			wp_reset_postdata();
			return $data_array;
		}

		public function change_title_text( $title ){
		    $screen = get_current_screen();
		  
		    if  ( $this->post_type == $screen->post_type ) {
		        $title = esc_html__("Enter the location name here", 'ifind' );
		    }
		    return $title;
		}
		
		public function rename_second_menu_name($safe_text, $text) {
			if (__('location Items', 'ifind') !== $text) {
				return $safe_text;
			}

			// We are on the main menu item now. The filter is not needed anymore.
			remove_filter('attribute_escape', array($this,'rename_second_menu_name') );

			return __('Location', 'ifind');
		}

	    protected function init_handle(){
			if( file_exists(LOCATION_TEMPLATE . "/functions.php") ){
				require_once LOCATION_TEMPLATE . "/functions.php";
			}
			//add_image_size('wd-location-thumb',400,400,true);  
		}	
		
		public function init_admin_script($hook) {
			$screen = get_current_screen();
			if ($hook = 'post.php' && $this->post_type == $screen->post_type) {
				wp_enqueue_style('wd-location-admin-custom-css', 	LOCATION_ADMIN_CSS.'/wd_admin.css');
				wp_enqueue_script( 'wd-location-scripts',		 	LOCATION_ADMIN_JS.'/wd_script.js',false,false,true);
			}
			
		}	
		
		
		public function init_script(){
			wp_enqueue_style('wd-location-custom-css', 	LOCATION_CSS.'/wd_custom.css');	
			wp_enqueue_script( 'wd-location-scripts',	LOCATION_JS.'/wd_ajax.js',false,false,true);
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
	iFind_Location::get_instance();  // Start an instance of the plugin class 
}