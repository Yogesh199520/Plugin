<?php

function smpnav( $id , $settings = array() ){

	_smpnav()->set_current_instance( $id );

	$ops = smpnav_get_instance_options( $id );
	//print_r($ops);
	extract( wp_parse_args( $settings , array(
		'theme_location'	=> !empty( $ops['theme_location'] ) ? $ops['theme_location'] : '_none',
		'menu'				=> !empty( $ops['menu'] ) ? $ops['menu'] : '_none',
		'container' 		=> 'nav',
		'edge'				=> !empty( $ops['edge'] ) ? $ops['edge'] : 'left',
		'skin'				=> !empty( $ops['skin'] ) ? $ops['skin'] : 'standard-dark',
	)));

	$class = "smpnav smpnav-nojs";
	$class.= " smpnav-$id";
	$class.= " smpnav-$edge-edge";
	$class.= " smpnav-skin-$skin";
	$class.= " smpnav-transition-standard";

	$id_att = strpos( $id , 'smpnav' ) !== 0 ? 'smpnav-'.$id : $id;

	?>
	
	<div class="<?php echo $class; ?>" id="<?php echo $id_att; ?>" data-smpnav-id="<?php echo $id; ?>">
		<div class="smpnav-inner">

		<?php if( smpnav_op( 'display_panel_close_button' , $id ) == 'on' ): ?>
			<button class="smpnav-panel-close"><i class="fa fa-times"></i></button>
		<?php endif; ?>

		<?php

		do_action( 'smpnav_before' , $id );

		$disable_menu = smpnav_op( 'disable_menu' , $id , 'off' ) == 'on' ? true : false;
		
		if( !$disable_menu ){

			$args = array(
				//'container_class' 	=> 'smpnav-nav', //$container_class,	//smpnav-transition-standard
				//'container_id'		=> $id,
				'container'			=> $container,
				// 'menu_class' 		=> 'smpnav-menu',
				// 'walker'			=> new SMPNavWalker,
				// 'fallback_cb'		=> 'smpnav_fallback',
				// 'depth'				=> 0,
				'smpnav'			=> $id,
			);

			$args = smpnav_get_menu_args( $args , $id );

			

			// //Target size
			//$args['menu_class'].= ' smpnav-targets-'.smpnav_op( 'target_size' , 'general' );

			// //Text size
			//$args['menu_class'].= ' smpnav-targets-text-'.smpnav_op( 'text_size' , 'general' );

			// //Icon size
			//$args['menu_class'].= ' smpnav-targets-icon-'.smpnav_op( 'icon_size' , 'general' );
			//echo smpnav_op('icon_size');
			// //Submenu indent
			// if( smpnav_op( 'indent_submenus' , $id ) == 'on' ) $args['menu_class'].= ' smpnav-indent-subs';

			// //Active on hover
			//if( smpnav_op( 'active_on_hover' , 'general' ) == 'on' ) $args['menu_class'].= ' smpnav-active-on-hover';

			// //Active Highlight
			//if( smpnav_op( 'active_highlight' , 'general' ) == 'on' ) $args['menu_class'].= '	smpnav-active-highlight';


			if( $menu != '_none' ){
				$args['menu'] = $menu;
			}
			else if( $theme_location != '_none' ){
				$args['theme_location'] = $theme_location;
				if( !has_nav_menu( $theme_location ) ){

					smpnav_count_menus();

					$locs = get_registered_nav_menus();
					$loc = $locs[$theme_location];
					smpnav_show_tip( 'Please <a href="'.admin_url('nav-menus.php?action=locations').'">assign a menu</a> to the <strong>'.$loc.'</strong> theme location' );
				}
			}
			else{
				smpnav_count_menus();
				smpnav_show_tip( 'Please <a href="'.admin_url( 'themes.php?page=smpnav-settings#smpnav_'.$id ).'">set a Theme Location or Menu</a> for this instance' );
			}

			wp_nav_menu( $args );

		}

		else{
			echo "\n\n\t\t<!-- smpnav Menu Disabled --> \n\n";
		}

		do_action( 'smpnav_after' , $id );

		?>

		<button class="smpnav-sr-close smpnav-sr-only smpnav-sr-only-focusable">
			<?php echo apply_filters( 'smpnav_panel_sr_close_text' , '&times; Close Panel' ); ?>
		</button>

		</div>
	</div>


	<?php
}

function smpnav_toggle( $target_id , $content = null, $args = array() ){

	$ops = smpnav_get_instance_options( $target_id );
	//smpnavp( $ops );

	if( $content == null && $content !== false ){
		$content = isset( $ops['toggle_content'] ) ? $ops['toggle_content'] : '';
		if( !$content ) $content = __( 'Toggle smpnav' , 'smpnav' );
	}

	_smpnav_toggle( $target_id , $content, $args );
}
