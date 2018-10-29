<?php 
if (!class_exists('iFind_Admin_Metaboxes')) {
	class iFind_Admin_Metaboxes extends ifindThemeSetting{
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

		public $list_post_type = array(
			'location',
			'business'
		);
			
		public function __construct(){
			$this->constants();
			add_action('admin_enqueue_scripts',array($this,'load_script_style'));

			// Fields
			if (file_exists(TVLGIAO_WPDANCE_THEME_METABOX.'/library/fields/fields.php')) {
				require_once TVLGIAO_WPDANCE_THEME_METABOX.'/library/fields/fields.php';
			}

			if(file_exists(TVLGIAO_WPDANCE_THEME_METABOX."/admin_page/admin_page.php")){
				require_once TVLGIAO_WPDANCE_THEME_METABOX."/admin_page/admin_page.php";
			}

			// Custom Post type
			foreach ($this->list_post_type as $file) {
				if (file_exists(TVLGIAO_WPDANCE_THEME_METABOX.'/metaboxes/'.$file.'/'.$file.'.php')) {
					require_once TVLGIAO_WPDANCE_THEME_METABOX.'/metaboxes/'.$file.'/'.$file.'.php';
				}
			}
		}
		public function constants(){
			define('TVLGIAO_WPDANCE_THEME_METABOX_JS'		, TVLGIAO_WPDANCE_THEME_METABOX_URI . '/js');
			define('TVLGIAO_WPDANCE_THEME_METABOX_CSS'		, TVLGIAO_WPDANCE_THEME_METABOX_URI . '/css');
			define('TVLGIAO_WPDANCE_THEME_METABOX_IMAGES'	, TVLGIAO_WPDANCE_THEME_METABOX_URI . '/images');
		}

		public function load_script_style(){
			/*----------------- Style ---------------------*/
			wp_enqueue_style( 'jquery-ui-core' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'tvlgiao-wpdance-admin', 	TVLGIAO_WPDANCE_THEME_METABOX_CSS .'/wd-admin.css');
			/*----------------- Script ---------------------*/
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'wd-media-js', 			TVLGIAO_WPDANCE_THEME_METABOX_JS .'/wd_media.js',false,false,true);
			wp_enqueue_script( 'wd-custom-meta-box-js', TVLGIAO_WPDANCE_THEME_METABOX_JS .'/wd_custom_post_layout.js',false,false,true);
		}
	}
	iFind_Admin_Metaboxes::get_instance();
}
?>