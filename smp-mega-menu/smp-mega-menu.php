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
 * Text Domain:       smp-mega-menu
 * Domain Path:       /languages
 */

if(!defined('ABSPATH')) {
    die;
}
define('PLUGIN', 'smp-mega-menu');


function SMP_menu_setting() {add_menu_page('SMP Menu', 'SMP Menu', 'manage_options','smp-menu-options', 'smp_tabs_setting','dashicons-welcome-widgets-menus', 22); }
add_action('admin_menu', 'SMP_menu_setting');

function SMP_menu_option_page() { 
    $settings = array(
        'setting_1_id' => array(
        'title'=>'Header Settings',
        'page'=>'header_setting_option',
        'fields'=> 
            array(array('id'=> 'header_style','title'=>'Header Style','callback'=> 'header_style_callback'),
            array('id'=> 'stick_header;','title'=>'Sticky Header','callback'=> 'stick_header_callback'),
            array('id'=> 'disable_logo','title'=>'Disable Logo/Text','callback'=> 'disable_logo_callback'),
            array('id'=> 'header_shadow','title'=>'Header Shadow','callback'=> 'header_shadow_callback'),
            array('id'=> 'header_height','title'=>'Disable Logo/Text','callback'=> 'header_height_callback'),
            array('id'=> 'header_text','title'=>'Header Text','callback'=> 'header_text_callback'),
            array('id'=> 'header_text_font_size','title'=>'Header Text Font Size','callback'=> 'header_text_font_size_callback'),
            array('id'=> 'header_text_font_alignment','title'=>'Header Logo/Text Alignment','callback'=> 'header_text_font_alignment_callback'),
            array('id'=> 'header_logo_left_margin','title'=>'Header Logo/Text Left Margin','callback'=> 'header_logo_left_margin_callback'),
            array('id'=> 'header_logo_right_margin','title'=>'Header Logo/Text Right Margin','callback'=> 'header_logo_right_margin_callback'),
            array('id'=> 'header_menu_font','title'=>'Header Menu Font','callback'=> 'header_menu_font_callback'),
        )),
    'setting_2_id' => array(
        'title'=>'Footer Settings',
        'page'=>'footer_setting_option',
        'fields'=> array(
            array('id'=> 'fixed_footer_bar','title'=>'Fixed Footer Bar','callback'=> 'text_callback' ),
            array('id'=> 'auto_hide_on_scroll','title'=>'Auto-hide on Scroll','callback'=> 'textarea_callback'),
            array('id'=> 'highlight_current_page','title'=>'Highlight current page', 'callback'=> 'text_callback'),
            array('id'=> 'diff_styles','title'=>'4 Different Styles', 'callback'=> 'text_callback'),
    )),
    'setting_3_id' => array(
        'title'=>'Left Menu Settings',
        'page'=>'left_menu_setting',
        'fields'=> array(
            array('id'=> 'box_third_title','title'=>'Title','callback'=> 'text_callback'),
            array( 'id'=> 'box_third_desc','title'=>'Description','callback'=> 'textarea_callback' ),
            array( 'id'=> 'box_third_link', 'title'=>'Link','callback'=> 'text_callback'),

        )),
    'setting_4_id' => array(
        'title'=>'Right Menu Settings',
        'page'=>'right_menu_setting',
        'fields'=> array(
            array('id'=> 'box_third_title','title'=>'Title','callback'=> 'text_callback'),
            array( 'id'=> 'box_third_desc','title'=>'Description','callback'=> 'textarea_callback' ),
            array( 'id'=> 'box_third_link', 'title'=>'Link','callback'=> 'text_callback'),

        )),
    );
    foreach( $settings as $id => $values){
        add_settings_section( 
            $id, 
            $values['title'],
            'boxes_front_page_callback', 
            $values['page']
        );
        
        foreach ($values['fields'] as $field) {
            add_settings_field(  
                $field['id'],  
                $field['title'],    
                $field['callback'],   
                $values['page'],      
                $id,   
                array(
                    $values['page'], 
                    $field['title'] 
                ) 
            );
        }
        register_setting($values['page'], $values['page']);
    }
}
add_action('admin_init', 'SMP_menu_option_page');

function boxes_front_page_callback() { 
    echo ''; 
}

function header_style_callback($args) { 
    $options = get_option($args[0]); 
    echo '<div class="lightswitch"><input type="checkbox" id="switch" /><label for="switch">Toggle</label></div>';
    echo '<input type="radio" id="hamburger-menu" name="header-style" value="hamburger"><label for="vehicle1">Hamburger Menu</label>';
    echo '<input type="radio" id="header-menu" name="header-style" value="header"><label for="vehicle2">Header Menu</label>';
}

function stick_header_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function disable_logo_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_shadow_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_height_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_text_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_text_font_size_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_text_font_alignment_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_logo_left_margin_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_logo_right_margin_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function header_menu_font_callback($args){
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';
}

function text_callback($args) { 
    $options = get_option($args[0]); 
    echo '<input type="text" class="regular-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']" value="' . $options[''  . $args[1] . ''] . '"></input>';

}


function textarea_callback($args) { 
    $options = get_option($args[0]); 
    echo '<textarea rows="8" cols="50" class="large-text" id="'  . $args[1] . '" name="'. $args[0] .'['  . $args[1] . ']">' . $options[''  . $args[1] . ''] . '</textarea>';

}


function smp_tabs_setting() {
?>
    <div class="wrap">  
        <div id="icon-themes" class="icon32"></div>  
        <?php settings_errors(); ?>  
        <?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'boks_pierwszy'; ?>  
        <h2 class="nav-tab-wrapper">  
            <a href="?page=smp-menu-options&tab=header" class="nav-tab <?php echo $active_tab == 'header' ? 'nav-tab-active' : ''; ?>">Header</a>  
            <a href="?page=smp-menu-options&tab=footer" class="nav-tab <?php echo $active_tab == 'footer' ? 'nav-tab-active' : ''; ?>">Footer</a>
            <a href="?page=smp-menu-options&tab=left-menu" class="nav-tab <?php echo $active_tab == 'left-menu' ? 'nav-tab-active' : ''; ?>">Left Menu</a>
            <a href="?page=smp-menu-options&tab=right-menu" class="nav-tab <?php echo $active_tab == 'right-menu' ? 'nav-tab-active' : ''; ?>">Right Menu</a>
        </h2>  


        <form method="post" action="options.php">  
            <?php 
            if( $active_tab == 'header' ) {  
                settings_fields( 'header_setting_option' );
                do_settings_sections( 'header_setting_option' ); 
            } else if( $active_tab == 'footer' ) {
                settings_fields( 'footer_setting_option' );
                do_settings_sections( 'footer_setting_option' );
            } else if( $active_tab == 'left-menu' ) {
                settings_fields( 'left_menu_setting' );
                do_settings_sections( 'left_menu_setting' );
            } 
            else if( $active_tab == 'right-menu' ) {
                settings_fields( 'right_menu_setting' );
                do_settings_sections( 'right_menu_setting' );
            } 
            ?>             
            <?php submit_button(); ?>
        </form> 
    </div> 
<?php
}


function smp_enqueue_script_style() {
    wp_enqueue_style('smp-menu-style', plugins_url('/includes/css/style.css', __DIR__ ), array(), '1.0');
    wp_enqueue_script('smp-menu-js', plugins_url('/includes/js/main.js', __DIR__ ), array( 'jquery' ), '1.0', true);
}
add_action( 'wp_enqueue_scripts', 'smp_enqueue_script_style' );

if(is_admin()) {
function admin_register_head() {
    $url = plugins_url(PLUGIN.'/admin/css/smp-admin.css');
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'admin_register_head');       
}









/*define( 'WP_SMP_MENU_VERSION', '1.0.0' );
define( 'WP_SMP_MENU_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_SMP_MENU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if(!class_exists( 'WP_SMP_MENU' )) {
    Class WP_SMP_MENU {
        public  $smp_fs ;
        public  $smpmenu_core ;

        public function __construct()
        {

        }
        public function smp_menu_page() {
            add_menu_page(
                'SMP Menu', 
                'SMP Menu', 
                'manage_options',
                'smp-menu', 
                'smp_menu_setting',
                'dashicons-welcome-widgets-menus', 
                22
            ); 
        }
        
        public function init_smp_menu() {
            global $hook;
            add_action('admin_menu', array($this, 'smp_menu_page'));
        }


        public function smp_menu_setting(){
            esc_html_e( 'Admin Page Test', 'textdomain' );  
        }

    }
}
$smp_menu_instance = new WP_SMP_MENU();

$smp_menu_instance->init_smp_menu();
$smp_menu_instance->smp_menu_setting();*/

?>