<?php
/*
Plugin Name: Extra Option In Setting Reading section
Plugin URI: 
Description: 
Author: 
Author URI: 
Text Domain: key-rest
Version: 1.0.0
*/


add_action('admin_init', 'smp_admin_init');
function smp_admin_init(){
    $args = array(
        'type' => 'string', 
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    );
    register_setting( 
        'reading', 
        'optional_page_setting', 
        $args 
    );
    add_settings_field(
        'optional_page_setting',
        __('Optional Page', 'textdomain'),
        'smp_setting_callback_function', 
        'reading', 
        'default', 
        array( 'label_for' => 'optional_page_setting' )
    );
}

function smp_setting_callback_function($args){
    $optional_page_id = get_option('optional_page_setting');
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'name',
        'order'            => 'ASC',
        'post_type'        => 'page',
    );
    $items = get_posts( $args );
    echo '<select id="optional_page_setting" name="optional_page_setting">';
    echo '<option value="0">'.__('— Select —', 'wordpress').'</option>';
    foreach($items as $item) {
        $selected = ($optional_page_id == $item->ID) ? 'selected="selected"' : '';
        echo '<option value="'.$item->ID.'" '.$selected.'>'.$item->post_title.'</option>';
    }
    echo '</select>';
}
add_filter('display_post_states', 'smp_add_custom_post_states');


function smp_add_custom_post_states($states) {
    global $post;
    $optional_page_id = get_option('optional_page_setting');
    if( 'page' == get_post_type($post->ID) && $post->ID == $optional_page_id && $optional_page_id != '0') {
        $states[] = __('My state', 'textdomain');
    }
    return $states;
}