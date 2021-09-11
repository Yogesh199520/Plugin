<?php

require_once( 'settings-api.class.php' );
require_once( 'settings.config.php' );
require_once( 'settings.menu.php' );

function smpnav_plugin_settings_link( $links ) {
	$settings_link = '<a href="'.admin_url( 'themes.php?page=smpnav-settings' ).'">Settings</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_'.SMPNAV_BASENAME, 'smpnav_plugin_settings_link' );

if( is_admin() ){
	add_action( 'admin_notices' , 'smpnav_display_admin_notices' );
}
function smpnav_display_admin_notices(){
	if( $messages = get_option( 'smpnav_admin_notices' ) ){
		$change = false;
		if( is_array( $messages ) ){
			foreach( $messages as $k => $m ){
				?>
				<div class="<?php echo $m['type']; ?>">
					<p><?php echo $m['text']; ?></p>
				</div>
				<?php
				if( $m['repeat'] > 0 ){
					$m['repeat']--;
					if( $m['repeat'] == 0 ){
						unset( $messages[$k] );
					}
					$change = true;
				}

				if( $change ){
					update_option( 'smpnav_admin_notices' , $messages );
				}
			}
		}
	}
}

function smpnav_set_admin_notice( $text , $type = 'updated' , $repeat = 1, $dismissable = false , $expiration = -1 ){
	$messages = get_option( 'smpnav_admin_notices' , array() );
	if( is_array( $messages ) ){
		$messages[] = array(
			'text'			=> $text,
			'type'			=> $type,
			'repeat'		=> $repeat,
			'dismissable'	=> $dismissable,
			'expiration'	=> $expiration,
		);
		update_option( 'smpnav_admin_notices' , $messages );
	}
}