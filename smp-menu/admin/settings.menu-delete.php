<?php

add_action( 'admin_print_styles-nav-menus.php' , 'smpnav_admin_menu_load_include' );
function smpnav_admin_menu_load_include() {
	$include = SMPNAV_URL. 'admin/include/';
	wp_enqueue_style( 'smpnav-menu-admin', $include.'admin.menu.css' );
	wp_enqueue_style( 'smpnav-font-awesome' , 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	wp_enqueue_script( 'smpnav-menu-admin', $include.'admin.menu.js' , array( 'jquery' ) , SMPNAV_VERSION, true );
	
	$smpnav_menu_data = smpnav_get_menu_items_data();
	wp_localize_script( 'smpnav-menu-admin' , 'smpnav_menu_item_data' , $smpnav_menu_data );
	wp_localize_script( 'smpnav-menu-admin' , 'smpnav_meta' , array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'		=> smpnav_menu_item_settings_nonce(),
	) );
}

function smpnav_menu_item_settings_panel(){

	$panels = smpnav_menu_item_settings_panels();
	$settings = smpnav_menu_item_settings();

	?>
	<div class="smpnav-js-check">
		<div class="smpnav-js-check-peek"><i class="fa fa-truck"></i> smpnav is waiting to load...</div>
		<div class="smpnav-js-check-details">
			<p>
			If this message does not disappear, it means that smpnav has not been able to load.
			This most commonly indicates that you have a javascript error on this page, which will need to be resolved in order to allow smpnav to run.
			</p>
		</div>
	</div>
	<div class="smpnav-menu-item-settings-wrapper">

		<div class="smpnav-menu-item-panel smpnav-menu-item-panel-negative">

			<div class="smpnav-menu-item-panel-info" >

				<div class="smpnav-menu-item-stats shift-clearfix">
					<div class="smpnav-menu-item-title">Menu Item [Unknown]</div>
					<div class="smpnav-menu-item-id">#menu-item-X</div>
					<div class="smpnav-menu-item-type">Custom</div>
				</div>
				<ul class="smpnav-menu-item-tabs">
					<?php foreach( $panels as $panel_id => $panel ): ?>
					<li class="smpnav-menu-item-tab" ><a href="#" data-smpnav-tab="<?php echo $panel_id; ?>" ><?php echo $panel['title']; ?></a></li>
					<?php endforeach; ?>

					
				</ul>

			</div>

			<div class="smpnav-menu-item-panel-settings shift-clearfix" >
				<form class="smpnav-menu-item-settings-form" action="" method="post" enctype="multipart/form-data" >

					<?php foreach( $panels as $panel_id => $panel ):
							$panel_settings = $settings[$panel_id];
							ksort( $panel_settings );
							//smpnavp( $panel_settings );
						?>

						<div class="smpnav-menu-item-tab-content" data-smpnav-tab-content="<?php echo $panel_id; ?>">

							<?php foreach( $panel_settings as $setting_id => $setting ): ?>

								<div class="smpnav-menu-item-setting smpnav-menu-item-setting-<?php echo $setting['type']; ?>">
									<label class="smpnav-menu-item-setting-label"><?php echo $setting['title']; ?></label>
									<div class="smpnav-menu-item-setting-input-wrap">
										<?php smpnav_show_menu_item_setting( $setting ); ?>
									</div>
								</div>

							<?php endforeach; ?>

						</div>


					<?php endforeach; ?>


					<div class="smpnav-menu-item-save-button-wrapper">

						<a class="smpnav-menu-item-settings-close" href="#"><i class="fa fa-times"></i></a>

						<input class="smpnav-menu-item-save-button" type="submit" value="Save Menu Item" />
						<div class="smpnav-menu-item-status smpnav-menu-item-status-save">
							<i class="smpnav-status-save fa fa-floppy-o"></i>
							<i class="smpnav-status-success fa fa-check"></i>
							<i class="smpnav-status-working fa fa-cog" title="Working..."></i>
							<i class="smpnav-status-warning fa fa-exclamation-triangle"></i>
							<i class="smpnav-status-error fa fa-exclamation-circle"></i>

							<span class="smpnav-status-message"></span>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'admin_footer-nav-menus.php' , 'smpnav_menu_item_settings_panel');


function smpnav_show_menu_item_setting( $setting ){


	$id = $setting['id'];
	$type = $setting['type'];
	$default = $setting['default'];
	$desc = '<span class="smpnav-menu-item-setting-description">'.$setting['desc'].'</span>';

	$name = 'name="'.$id.'"';
	$value = 'value="'.$default.'"';
	$data_setting = 'data-smpnav-setting="'.$id.'"';

	$class = 'class="smpnav-menu-item-setting-input"';

	$ops;
	if( isset( $setting['ops'] ) ){
		$ops = $setting['ops'];
		if( !is_array( $ops ) && function_exists( $op ) ){
			$ops = $ops();
		}
	}

	switch( $type ){
		case 'checkbox': ?>
			<input <?php echo $class; ?> type="checkbox" <?php echo "$name $data_setting"; checked( $default , 'on' ); ?> />
			<?php break;

		case 'text': ?>
			<input <?php echo $class; ?> type="text" <?php echo "$name $value $data_setting"; ?> />
			<?php break;

		case 'select': ?>
			<select <?php echo $class; ?> <?php echo "$name $data_setting"; ?> >
				<?php foreach( $ops as $_val => $_name ): ?>
				<option value="<?php echo $_val; ?>" <?php selected( $default , $_val ); ?> ><?php echo $_name; ?></option>
				<?php endforeach; ?>
			</select>
			<?php break;

		case 'icon': ?>
			<div class="smpnav-icon-settings-wrap">
				<div class="smpnav-icon-selected">
					<i class="<?php echo $default; ?>"></i>
					<span class="smpnav-icon-set-icon">Set Icon</span>
				</div>
				<div class="smpnav-icons shift-clearfix">
					<div class="smpnav-icons-search-wrap">
						<input class="smpnav-icons-search" placeholder="Type to search" />
					</div>

				<?php foreach( $ops as $_val => $data ): if( $_val == '' ) continue; ?>
					<span class="smpnav-icon-wrap" title="<?php echo $data['title']; ?>" data-smpnav-search-terms="<?php echo strtolower( $data['title'] ); ?>"><i class="smpnav-icon <?php echo $_val; ?>" data-smpnav-icon="<?php echo $_val; ?>" ></i></span>
				<?php endforeach; ?>
					<span class="smpnav-icon-wrap smpnav-remove-icon" title="Remove Icon"><i class="smpnav-icon" data-smpnav-icon="" >Remove Icon</i></span>
				</div>
				<select <?php echo $class; ?> <?php echo "$name $data_setting"; ?> >
					<?php foreach( $ops as $_val => $data ): ?>
					<option value="<?php echo $_val; ?>" <?php selected( $default , $_val ); ?> ><?php echo $data['title']; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php break;

		default: ?>
			What's a "<?php echo $type; ?>"?
			<?php
	}

	echo $desc;

}


function smpnav_menu_item_settings_panels(){
	$panels = array();
	$panels['general'] = array(
		'title'	=> 'General',
	);
	$panels['submenu'] = array(
		'title'	=> 'Submenu',
	);

	return $panels;
}

function smpnav_menu_item_settings(){

	$settings = array();
	$panels = smpnav_menu_item_settings_panels();
	foreach( $panels as $id => $panel ){
		$settings[$id] = array();
		//print_r($settings[$id]);
	}

	$settings['general'][10] = array(
		'id' 		=> 'disable_link',
		'title'		=> __( 'Disable Link', 'smpnav' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Check this box to remove the link from this item; clicking a disabled link will not result in any URL being followed.  Instead, this item will act as a toggle for its submenu, if one exists.' , 'smpnav' ),
	);

	$settings['general'][20] = array(
		'id' 		=> 'highlight',
		'title'		=> __( 'Highlight Link', 'smpnav' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Highlight this menu item' , 'smpnav' ),
	);

	$settings['general'][30] = array(
		'id' 		=> 'icon',
		'title'		=> 'Icon',
		'type'		=> 'icon',
		'default' 	=> '',
		'desc'		=> '',
		'ops'		=> null,
		'pro_only'	=> true,
	);

	$settings['general'][40] = array(
		'id' 		=> 'scrollto',
		'title'		=> __( 'Scroll To' , 'smpnav' ),
		'type'		=> 'text',
		'default' 	=> '',
		'desc'		=> __( 'The selector for an item to scroll to when clicked, if present.  Example: <code>#section-1</code>', 'smpnav' )
	);

	$settings['general'][42] = array(
		'id' 		=> 'disable_current',
		'title'		=> __( 'Disable Current' , 'smpnav' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Disable the current menu item classes for this item', 'smpnav' )
	);

	$settings['general'][80] = array(
		'id' 		=> 'disable_item',
		'title'		=> __( 'Disable Item', 'smpnav' ),
		'type'		=> 'checkbox',
		'default' 	=> 'off',
		'desc'		=> __( 'Hide this item.  Useful if this menu is being reused in multiple locations, but item should not be displayed in smpnav', 'smpnav' ),
	);

	$settings['submenu'][20] = array(
		'id' 		=> 'submenu_type',
		'title'		=> __( 'Submenu Type', 'smpnav' ),
		'type'		=> 'select',
		'default'	=> 'default',
		'desc'		=> __( '[Requires Pro Version] Overrides the default submenu type.  For the Lite version, only "Always visible" is available.  Can be changed to "Accordion" or "Shift" with the Pro version.' , 'smpnav' ),
		'ops'		=> array(
						'default'	=>  __( 'Menu Default', 'smpnav' ),
						'always'	=>	__( 'Always visible', 'smpnav' ),
					),
	);

  	$settings['submenu'][40] = array(
		'id' 		=> 'disable_submenu',
		'title'		=> __( 'Disable Submenu', 'smpnav' ),
		'type'		=> 'checkbox',
		'default'	=> 'off',
		'desc'		=> __( 'Disable the submenu for this menu item within smpnav.' , 'smpnav' ),

	);

	return apply_filters( 'smpnav_menu_item_settings' , $settings );

}

function smpnav_menu_item_setting_defaults(){
	$defaults = array();
	$settings = smpnav_menu_item_settings();
	print_r($settings);
	foreach( $settings as $panel => $panel_settings ){
		foreach( $panel_settings as $setting ){
			$defaults[$setting['id']] = $setting['default'];
		}
	}
	return $defaults;
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

function smpnav_get_menu_item_data( $item_id ){
	return get_post_meta( $item_id , SMPNAV_MENU_ITEM_META_KEY , true );
}


function smpnav_menu_item_settings_nonce(){
	return wp_create_nonce( 'smpnav-menu-item-settings' );
}

add_action( 'wp_ajax_smpnav_save_menu_item', 'smpnav_save_menu_item_callback' );

function smpnav_save_menu_item_callback() {
	global $wpdb;
	
	check_ajax_referer( 'smpnav-menu-item-settings' , 'smpnav_nonce' );
	$menu_item_id = $_POST['menu_item_id'];
	$menu_item_id = substr( $menu_item_id , 10 );
	$serialized_settings = $_POST['settings'];
	$dirty_settings = array();
	parse_str( $serialized_settings, $dirty_settings );

	
	$_defined_settings = smpnav_menu_item_settings();
	foreach( $_defined_settings as $panel => $panel_settings ){
		foreach( $panel_settings as $_priority => $_setting ){
			if( $_setting['type'] == 'checkbox' ){
				$_id = $_setting['id'];
				if( !isset( $dirty_settings[$_id] ) ){
					$dirty_settings[$_id] = 'off';
				}
			}
		}
	}

	
	$settings = wp_parse_args( $dirty_settings, smpnav_menu_item_setting_defaults() );

	update_post_meta( $menu_item_id, SMPNAV_MENU_ITEM_META_KEY , $settings );


	$response = array();

	$response['settings'] = $settings;
	$response['menu_item_id'] = $menu_item_id;

	$response['nonce'] = smpnav_menu_item_settings_nonce();

	//print_r( $response );
	echo json_encode( $response );
	echo $data;

	die(); 
}
