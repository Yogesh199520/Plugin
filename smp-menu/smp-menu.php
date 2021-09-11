<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://slicemypage.com/
 * @since             1.0.0
 * @package           Smp_Mega_Menu
 *
 * @wordpress-plugin
 * Plugin Name:       SMP Mega Menu
 * Plugin URI:        https://agency1.devsmp.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            SLICEmyPAGE
 * Author URI:        https://slicemypage.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smp-menu
 * Domain Path:       /languages
 */


if(!defined('ABSPATH')) exit;
if(!class_exists('SMPNav')):
final class SMPNav {

	private static $instance;
	private static $settings_api;
	private static $skins;
	private static $settings_defaults;
	private static $registered_icons;
	private static $current_instance ='smpnav-main';
	private static $is_mobile = null;
	private static $display_now = null;
	private static $support_url;

	public static function instance() {

		if (!isset( self::$instance )) {
			self::$instance = new SMPNav;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->activation_check();
		}
		return self::$instance;
	}

	
	private function setup_constants() {
		
		if(!defined('SMPNAV_VERSION') )
			define('SMPNAV_VERSION','1.0.0');

		if(! defined('SMPNAV_PRO') )
			define('SMPNAV_PRO', false );

		if(! defined('SMPNAV_BASENAME') )
			define('SMPNAV_BASENAME', plugin_basename( __FILE__ ) );

		if(! defined('SMPNAV_BASEDIR') ){
			define('SMPNAV_BASEDIR', dirname( plugin_basename(__FILE__) ) );
		}

		if(! defined('SMPNAV_URL') )
			define('SMPNAV_URL', plugin_dir_url( __FILE__ ) );

		if(! defined('SMPNAV_DIR') )
			define('SMPNAV_DIR', plugin_dir_path( __FILE__ ) );

		if(! defined('SMPNAV_FILE') )
			define('SMPNAV_FILE', __FILE__ );

		if(! defined('SMPNAV_MENU_ITEM_META_KEY') )
			define('SMPNAV_MENU_ITEM_META_KEY','_smpnav_settings');

		if(! defined('SMPNAV_EXTENDED') )
			define('SMPNAV_EXTENDED', false );

		if(!defined('SMPNAV_MENU_CONFIGURATIONS') )
			define('SMPNAV_MENU_CONFIGURATIONS','smpnav_menus');

		define('SMPNAV_MENU_STYLES','_smpnav_menu_styles');

		define('smpnav_GENERATED_STYLE_TRANSIENT','_smpnav_generated_styles');
		if(! defined('smpnav_GENERATED_STYLE_TRANSIENT_EXPIRATION') )
			define('smpnav_GENERATED_STYLE_TRANSIENT_EXPIRATION', 30 * DAY_IN_SECONDS );


		define('SMPNAV_SUPPORT_URL','https://slicemypage.com/');

		define('SMPNAV_PREFIX','smpnav_');
	}

	private function includes() {
		require_once SMPNAV_DIR .'includes/SMPNavWalker.class.php';
		require_once SMPNAV_DIR .'includes/functions.php';
		require_once SMPNAV_DIR .'includes/smpnav.api.php';
		require_once SMPNAV_DIR .'admin/admin.php';
	}
	private function activation_check(){

		
	}

	public function settings_api(){
		if(self::$settings_api == null ){
			self::$settings_api = new SMPNav_Settings_API();
		}
		return self::$settings_api;
	}

	public function get_current_instance(){
		return self::$current_instance;
	}

	public function set_current_instance( $instance_id ){
		return self::$current_instance = $instance_id;
	}

	public function get_skins(){
		return self::$skins;
	}
	public function register_skin( $id , $title , $src ){
		if( self::$skins == null ){
			self::$skins = array();
		}
		self::$skins[$id] = array(
			'title'	=> $title,
			'src'	=> $src,
		);
		wp_register_style('smpnav-'.$id , $src , false , SMPNAV_VERSION );
	}

	public function set_defaults( $fields ){
		if( self::$settings_defaults == null ) self::$settings_defaults = array();
		foreach( $fields as $section_id => $ops ){
			self::$settings_defaults[$section_id] = array();
			foreach( $ops as $op ){
				self::$settings_defaults[$section_id][$op['name']] = isset( $op['default'] ) ? $op['default'] :'';
			}
		}
	}

	function get_defaults( $section = null ){
		if( self::$settings_defaults == null ) self::set_defaults(smpnav_get_settings_fields() );
		if( $section!= null && isset( self::$settings_defaults[$section] ) ) return self::$settings_defaults[$section];
		return self::$settings_defaults;
	}

	function get_default( $option , $section ){
		if( self::$settings_defaults == null ) self::set_defaults(smpnav_get_settings_fields() );
		$default ='';
		
		if( isset( self::$settings_defaults[$section] ) && isset( self::$settings_defaults[$section][$option] ) ){
			$default = self::$settings_defaults[$section][$option];
		}
		return $default;
	}

	function register_icons( $group , $iconmap ){
		if(!is_array( self::$registered_icons ) ) self::$registered_icons = array();
		self::$registered_icons[$group] = $iconmap;
	}
	function degister_icons( $group ){
		if( is_array( self::$registered_icons ) && isset( self::$registered_icons[$group] ) ){
			unset( self::$registered_icons[$group] );
		}
	}
	function get_registered_icons(){
		return self::$registered_icons;
	}

	static function is_mobile(){
		if(self::$is_mobile === null ){
			self::$is_mobile = apply_filters('smpnav_is_mobile', wp_is_mobile() );
		}
		return self::$is_mobile;
	}
	function display_now(){
		if( self::$display_now === null ){
			$display = true;
			//Mobile only and this isn't mobile
			if( smpnav_op('mobile_only','general') =='on'&&!self::is_mobile() ){
				$display = false;
			}
			self::$display_now = apply_filters('smpnav_display_now', $display );
		}
		return self::$display_now;

	}

	function get_support_url(){
		if( self::$support_url ){
			return self::$support_url;
		}
		$url = SMPNAV_SUPPORT_URL;
		$data = array();

		//Site Data
		$data['site_url'] 		= get_site_url();
		$data['version']		= SMPNAV_VERSION;
		$data['timezone']		= get_option('timezone_string');

		//Theme Data
		$theme = wp_get_theme();
		$data['theme']			= $theme->get('Name');
		$data['theme_link']		='<a target="_blank" href="'.$theme->get('ThemeURI').'">'. $theme->get('Name').'v'.$theme->get('Version').'by'. $theme->get('Author').'</a>';
		$data['theme_slug']		= isset( $theme->stylesheet ) ? $theme->stylesheet :'';
		$data['theme_parent']	= $theme->get('Template');

		//User Data
		$current_user = wp_get_current_user();
		if( $current_user ){
			if( $current_user->user_firstname ){
				$data['first_name']		= $current_user->user_firstname;
			}
			if( $current_user->user_firstname ){
				$data['last_name']		= $current_user->user_lastname;
			}
			if( $current_user ){
				$data['email']			= $current_user->user_email;
			}
		}

		$query = http_build_query( $data );
		$support_url = "$url?$query";
		self::$support_url = $support_url;
		return $support_url;
	}

}


function deactivate_smpnav() {
	if(is_plugin_active('smpnav-responsive-mobile-menu/smpnav-responsive-mobile-menu.php') ) {
		deactivate_plugins('smpnav-responsive-mobile-menu/smpnav-responsive-mobile-menu.php');
	}
}
add_action('admin_init','deactivate_smpnav');


endif; 
// End if class_exists check


if(!function_exists('_smpnav') ){
	function _smpnav() {
		return SMPNav::instance();
	}
	_smpnav();
}
