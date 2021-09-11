<?php

add_action( 'admin_print_styles-nav-menus.php' , 'smpnav_admin_menu_load_include' );
function smpnav_admin_menu_load_include() {
	wp_enqueue_style( 'smpnav-font-awesome' , 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	$smpnav_menu_data = smpnav_get_menu_items_data();
	wp_localize_script( 'smpnav-menu-admin' , 'smpnav_menu_item_data' , $smpnav_menu_data );
	wp_localize_script( 'smpnav-menu-admin' , 'smpnav_meta' , array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'		=> smpnav_menu_item_settings_nonce(),
	) );
}

function smpnav_get_menu_item_data( $item_id ){
	return get_post_meta( $item_id , SMPNAV_MENU_ITEM_META_KEY , true );
}


function smpnav_get_menu_items_data( $menu_id = -1 ){

	if( $menu_id == -1 ){
		global $nav_menu_selected_id;
		$menu_id = $nav_menu_selected_id;
	}

	if( $menu_id == 0 ) return array();

	$smpnav_menu_data = array();
	$menu_items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );

	foreach( $menu_items as $item ){
		$_item_settings = smpnav_get_menu_item_data( $item->ID );
		if( $_item_settings != '' ){
			$smpnav_menu_data[$item->ID] = $_item_settings;
		}
	}
	//smpnavp( $smpnav_menu_data );

	return $smpnav_menu_data;
}


function smpnav_menu_item_settings_nonce(){
	return wp_create_nonce( 'smpnav-menu-item-settings' );
}