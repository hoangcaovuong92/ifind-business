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

		protected $post_type 				= 'location';
		protected $post_type_single_name 	= 'Location';
		protected $post_type_multiple_name 	= 'Locations';
		protected $post_type_display_name 	= 'Locations';
		protected $post_type_icon 			= 'dashicons-location-alt';
		protected $post_type_position		= 5;
		protected $taxonomy 				= 'location_categories';
		protected $taxonomy_single_name		= 'Location Group';
		protected $arrShortcodes 			= array();

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

		public function get_meta_data_default($field = ''){
			$default = array(
				'location' 	=> array(
					'slogan'	=> '',
					'office_hours'	=> '',
					'contact_number'	=> '',
					'email_address'	=> '',
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

		public function metabox_form(){
			wp_nonce_field( $this->post_type.'_box', $this->post_type.'_box_nonce' );
			$random_id 	= 'wd-'.$this->post_type.'-metabox-'.mt_rand();
			$meta_key 	= $this->post_type;
			$meta_data 	= $this->get_meta_data($meta_key);
			$meta_data 	= empty($meta_data) ? $this->get_meta_data_default($meta_key) : $meta_data;
			?>
			<table id="<?php echo esc_attr( $random_id ); ?>" class="form-table wd-<?php echo $this->post_type; ?>-custom-meta-box wd-custom-meta-box-width">
				<tbody>
					<?php 
					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Slogan', 'ifind' ),
						"desc" => "",
						"placeholder" => "",
						"field_name" => $this->post_type."[slogan]",
						"value" => $meta_data['slogan'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Office Hours', 'ifind' ),
						"desc" => "",
						"placeholder" => "",
						"field_name" => $this->post_type."[office_hours]",
						"value" => $meta_data['office_hours'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Contact Number', 'ifind' ),
						"desc" => "",
						"placeholder" => "",
						"field_name" => $this->post_type."[contact_number]",
						"value" => $meta_data['contact_number'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Email Address', 'ifind' ),
						"desc" => esc_html__( 'Email address of the office.', 'ifind' ),
						"placeholder" => esc_html__( 'Exam: hoangcaovuong92@gmail.com', 'ifind' ),
						"field_name" => $this->post_type."[email_address]",
						"value" => $meta_data['email_address'],
					));

					iFind_Admin_Metabox_Fields::get_text_field(array(
						"title" => esc_html__( 'Max Distance', 'ifind' ),
						"desc" => esc_html__( 'If the distance from location to business exceeds "Max distance", it will not be displayed. Unit: km', 'ifind' ),
						"placeholder" => esc_html__( 'Set -1 or leave blank to remove this limit.', 'ifind' ),
						"field_name" => $this->post_type."[max_distance]",
						"value" => $meta_data['max_distance'],
					));

					iFind_Admin_Metabox_Fields::get_checkbox_field(array(
						"title" => esc_html__( 'List Business', 'ifind' ),
						"desc" => "",
						"field_name" => $this->post_type."[list_business]",
						"options" => $this->get_list_business(),
						"value" => $meta_data['list_business'],
					));

					iFind_Admin_Metabox_Fields::get_map_field(array(
						"title" => esc_html__( 'Location Address', 'ifind' ),
						"desc" => "",
						"placeholder" => __( 'Enter a location', 'ifind' ),
						"field_name" => $this->post_type."[location_data]",
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
				$data_array[] = sprintf(__( "Please add data for \"%s\" before", 'ifind' ), $post_type);
			}
			wp_reset_postdata();
			return $data_array;
		}
		
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
			if( file_exists(LOCATION_TEMPLATE . "/functions.php") ){
				require_once LOCATION_TEMPLATE . "/functions.php";
			}
			//add_image_size('wd-'.$this->post_type.'-thumb',400,400,true);  
		}
		
		public function init_admin_script($hook) {
			$screen = get_current_screen();
			if ($hook = 'post.php' && $this->post_type == $screen->post_type) {
				wp_enqueue_style('wd-'.$this->post_type.'-admin-custom-css', 	LOCATION_ADMIN_CSS.'/wd_admin.css');
				wp_enqueue_script( 'wd-'.$this->post_type.'-scripts',		 	LOCATION_ADMIN_JS.'/wd_script.js',false,false,true);
			}
		}
		
		public function init_script(){
			wp_enqueue_style('wd-'.$this->post_type.'-custom-css', 	LOCATION_CSS.'/wd_custom.css');	
			wp_enqueue_script( 'wd-'.$this->post_type.'-scripts',	LOCATION_JS.'/wd_ajax.js',false,false,true);
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