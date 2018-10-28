<?php
if (!class_exists('ifindThemeSetting')) {
	class ifindThemeSetting{
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

		//Variable
		protected $theme_name		= 'ifind';
		protected $theme_slug		= 'ifind';

		protected $arr_functions 	= array();

		//Constructor
		public function __construct(){
			$this->constant();
			$this->init_arr_functions();
			$this->after_setup_theme();
			$this->init_functions();

			$this->init_metabox();
			$this->init_theme_options();

			//Send email with HTML
			add_filter( 'wp_mail_content_type', array($this, 'send_mail_set_content_type') );

			//Notice alert
			add_action('admin_notices', array($this,'show_msg'));
		}

		// Function Setup Theme
		public function after_setup_theme(){
			//After setup theme
			add_action( 'after_setup_theme', array($this,'setup_theme_func'));
		}

		// Constant
		protected function constant(){			
			// Default
			define('TVLGIAO_WPDANCE_DS'						, DIRECTORY_SEPARATOR);	
			define('TVLGIAO_WPDANCE_THEME_NAME'				, $this->theme_name );
			define('TVLGIAO_WPDANCE_THEME_SLUG'				, $this->theme_slug.'_');
			define('TVLGIAO_WPDANCE_THEME_DIR'				, get_template_directory());
			define('TVLGIAO_WPDANCE_THEME_URI'				, get_template_directory_uri());
			define('TVLGIAO_WPDANCE_THEME_ASSET_URI'		, TVLGIAO_WPDANCE_THEME_URI 			. '/assets');
			// Style-Script-Image
			define('TVLGIAO_WPDANCE_THEME_IMAGES'			, TVLGIAO_WPDANCE_THEME_ASSET_URI 		. '/images');
			define('TVLGIAO_WPDANCE_THEME_CSS'				, TVLGIAO_WPDANCE_THEME_ASSET_URI 		. '/css');
			define('TVLGIAO_WPDANCE_THEME_JS'				, TVLGIAO_WPDANCE_THEME_ASSET_URI 		. '/js');
			define('TVLGIAO_WPDANCE_THEME_FONT'				, TVLGIAO_WPDANCE_THEME_ASSET_URI 		. '/fonts');
			define('TVLGIAO_WPDANCE_THEME_EXTEND_LIBS'		, TVLGIAO_WPDANCE_THEME_ASSET_URI 		. '/libs');
			//Framework Theme
			define('TVLGIAO_WPDANCE_THEME_FRAMEWORK'		, TVLGIAO_WPDANCE_THEME_DIR 			. '/framework');
			define('TVLGIAO_WPDANCE_THEME_FRAMEWORK_URI'	, TVLGIAO_WPDANCE_THEME_URI 			. '/framework');
			//Folder in Framework
			define('TVLGIAO_WPDANCE_THEME_FUNCTIONS'		, TVLGIAO_WPDANCE_THEME_FRAMEWORK 		. '/functions');	
			define('TVLGIAO_WPDANCE_THEME_PLUGIN'			, TVLGIAO_WPDANCE_THEME_FRAMEWORK 		. '/plugins');
			define('TVLGIAO_WPDANCE_THEME_SHORTCODES'		, TVLGIAO_WPDANCE_THEME_FRAMEWORK 		. '/shortcodes');
			define('TVLGIAO_WPDANCE_THEME_METABOX'			, TVLGIAO_WPDANCE_THEME_FRAMEWORK 		. '/metabox');
			define('TVLGIAO_WPDANCE_THEME_METABOX_URI'		, TVLGIAO_WPDANCE_THEME_FRAMEWORK_URI 	. '/metabox');
			//Folder WPDANCE
			define('TVLGIAO_WPDANCE_THEME_WPDANCE'			, TVLGIAO_WPDANCE_THEME_FRAMEWORK 		. '/wpdance');
			define('TVLGIAO_WPDANCE_THEME_WPDANCE_URI'		, TVLGIAO_WPDANCE_THEME_FRAMEWORK_URI 	. '/wpdance');
			define('TVLGIAO_WPDANCE_THEME_SUPPORT'			, TVLGIAO_WPDANCE_THEME_WPDANCE 		. '/supports');
			define('TVLGIAO_WPDANCE_THEME_SUPPORT_URI'		, TVLGIAO_WPDANCE_THEME_WPDANCE_URI 	. '/supports');
			define('TVLGIAO_WPDANCE_THEME_CUSTOMIZE'		, TVLGIAO_WPDANCE_THEME_SUPPORT 		. '/theme_customize');
			define('TVLGIAO_WPDANCE_THEME_CUSTOMIZE_URI'	, TVLGIAO_WPDANCE_THEME_SUPPORT_URI 	. '/theme_customize');
			define('TVLGIAO_WPDANCE_THEME_GUIDE'			, TVLGIAO_WPDANCE_THEME_SUPPORT 		. '/theme_guide');
			define('TVLGIAO_WPDANCE_THEME_GUIDE_URI'		, TVLGIAO_WPDANCE_THEME_SUPPORT_URI 	. '/theme_guide');
			define('TVLGIAO_WPDANCE_THEME_OPTIONS'			, TVLGIAO_WPDANCE_THEME_SUPPORT 		. '/theme_option');

		}

		//Setup Theme
		public function setup_theme_func(){
		    global $content_width;
		    if ( !isset($content_width) ) {
		        $content_width = 1080;
		    }
			//Make theme available for translation
			//Translations can be filed in the /languages/ directory
   			load_theme_textdomain('ifind', get_template_directory() . '/languages');
   			
   			//Import Theme Support
   			$this->theme_support();
   			//Import Script / Style
   			add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
			add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
			
			//Remove admin bar front end
			show_admin_bar(false);
		}

		//Theme Support
		public function theme_support(){
			// Enable support for Post Formats.
			add_theme_support('post-thumbnails');
			add_theme_support('title-tag');
			
			//Add Image Size
			//set_post_thumbnail_size( 640, 440, true );
			add_image_size('map_logo', 40, 40, false);
			add_image_size('small_banner', 1080, 360, true);
		}

		public function send_mail_set_content_type(){
			return "text/html";
		}

		public function show_msg(){
			$list_msg 	= array();
			$list_msg['refresh_window']['class'] 	= 'notice notice-success is-dismissible';
			$list_msg['refresh_window']['header'] 	= '';
			$list_msg['refresh_window']['message'] = '<span class="dashicons dashicons-image-rotate"></span> <a href="#" class="ifind-reload-browser">'.__( 'Click here to reload all browser!', 'ifind' ).'</a>';


			foreach ($list_msg as $key => $mess) {
				printf( '<div class="%1$s"><strong>%2$s<p>%3$s</p></strong></div>', esc_attr( $mess['class'] ),$mess['header'], $mess['message'] );
			}
		}	

		//Include Function
		protected function init_arr_functions(){
			$this->arr_functions = array(
				'class/class-tgm-plugin-activation',
				'class/qrcode',
				'wd_main',
				'wd_set_default',
				'wd_get_customize_data',
				'wd_ajax_function',
				'wd_register_tgmpa_plugin',
			);
		}
		
		//Include Customize
		protected function init_arr_customize(){
			$this->arr_customize = array(
				'libs/add-control-custom-radio-image',
				'libs/wd-add-control-custom-font',
				'libs/wd_customize_sanitize_callback',
				'wd_customize',
			);
		}
		// Load File
		protected function init_functions(){
			foreach($this->arr_functions as $function){
				if(file_exists(TVLGIAO_WPDANCE_THEME_FUNCTIONS."/{$function}.php")){
					require_once TVLGIAO_WPDANCE_THEME_FUNCTIONS."/{$function}.php";
				}	
			}
		}
		protected function init_theme_options(){
			if ( ! class_exists( 'ReduxFramework' ) ) return;
			if(file_exists(TVLGIAO_WPDANCE_THEME_OPTIONS. "/wd_theme_options.php")){
				require_once TVLGIAO_WPDANCE_THEME_OPTIONS. "/wd_theme_options.php";
			}
		}
		protected function init_metabox(){
			if(file_exists(TVLGIAO_WPDANCE_THEME_METABOX.'/wd_metaboxes.php')){
				require_once TVLGIAO_WPDANCE_THEME_METABOX.'/wd_metaboxes.php';
			}
		}
		
		//Enqueue Style And Script
		public function enqueue_scripts(){
			global $wp_query, $tvlgiao_wpdance_theme_options;
			$ajax_object_vars = array(
				'ajax_url' 			=> admin_url( 'admin-ajax.php' ),
				'query_vars'		=> json_encode( $wp_query->query )
			);
			/*----------------- Style ---------------------*/
			//Google font
			$font_family = array(
				'Lato:400,100,100italic,300,300italic,400italic,700,700italic,900,900italic', 
				'Pacifico:400', 
				'Roboto:300,400,500,700', 
				'Fredoka+One',
			);
			wp_enqueue_style( "ifind-google-font", "//fonts.googleapis.com/css?family=".implode('|', $font_family) );

			// LIB
			wp_enqueue_style('bootstrap-core', 			'//stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
			wp_enqueue_style('font-awesome-css', 		'//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
			wp_enqueue_style('flexslider-css', 			'//cdnjs.cloudflare.com/ajax/libs/flexslider/2.5.0/flexslider.min.css');
			wp_enqueue_style('fancybox-css', 			'//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.7/css/jquery.fancybox.min.css');
			wp_enqueue_style('slick-core', 				TVLGIAO_WPDANCE_THEME_EXTEND_LIBS.'/slick/slick.css');
			wp_enqueue_style('slick-theme-css', 		TVLGIAO_WPDANCE_THEME_EXTEND_LIBS.'/slick/slick-theme.css');
			wp_enqueue_style('wowjs-css', 				TVLGIAO_WPDANCE_THEME_EXTEND_LIBS.'/wowjs/css/animate.css');
			wp_enqueue_style('softkey-css', 			TVLGIAO_WPDANCE_THEME_EXTEND_LIBS.'/softkey/softkeys-0.0.1.css');
			
			// CSS OF THEME
			wp_enqueue_style('wd-theme-desc-css', 		TVLGIAO_WPDANCE_THEME_URI.'/style.css');

			/*----------------- Script ---------------------*/
			// Wordpress Libs
			wp_enqueue_script('jquery');
			// LIB
			wp_enqueue_script('bootstrap-core', 		'//stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), false, true);
			wp_enqueue_script('fancybox-js', 			'//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.7/js/jquery.fancybox.min.js' ,array('jquery'),false,true);
			wp_enqueue_script('googmap-geometry-js', 	'//cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js' ,array('jquery'),false,true);
			wp_enqueue_script('swal-js', 				'//unpkg.com/sweetalert/dist/sweetalert.min.js"' ,array('jquery'),false,true);
			wp_enqueue_script('slick-core', 			TVLGIAO_WPDANCE_THEME_EXTEND_LIBS.'/slick/slick.min.js',array('jquery'),false,true);
			wp_enqueue_script('wowjs-js', 				TVLGIAO_WPDANCE_THEME_EXTEND_LIBS.'/wowjs/js/wow.min.js',array('jquery'),false,true);
			wp_enqueue_script('softkey-js', 			TVLGIAO_WPDANCE_THEME_EXTEND_LIBS.'/softkey/softkeys-0.0.1.js',array('jquery'),false,true);

			
			wp_enqueue_script('wd-main-js', 			TVLGIAO_WPDANCE_THEME_JS.'/wd_main.js', array('jquery'), false, true);
			wp_localize_script('wd-main-js', 			'ajax_object', $ajax_object_vars);
			wp_localize_script('wd-main-js', 			'option_object', $tvlgiao_wpdance_theme_options);

			wp_enqueue_script('wd-slider-js', 			TVLGIAO_WPDANCE_THEME_JS.'/wd_slider.js', array('jquery'), false, true);
			wp_localize_script('wd-slider-js', 			'option_object', $tvlgiao_wpdance_theme_options);
		}

		//Enqueue Style And Script
		public function admin_enqueue_scripts(){
			global $tvlgiao_wpdance_theme_options;
			$ajax_object_vars = array(
				'ajax_url' 			=> admin_url( 'admin-ajax.php' ),
				'query_vars'		=> json_encode( $wp_query->query )
			);
			wp_enqueue_media();
			wp_enqueue_style('font-awesome-css', 		'//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

			wp_enqueue_script('wd-admin-js', 			TVLGIAO_WPDANCE_THEME_JS.'/wd_admin.js', array('jquery'), false, true);
			wp_localize_script('wd-admin-js', 			'option_object', $tvlgiao_wpdance_theme_options);
			wp_localize_script('wd-admin-js', 			'ajax_object', $ajax_object_vars);
		}
	}
	ifindThemeSetting::get_instance();
} ?>