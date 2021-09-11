<?php

class SMPNavWalker extends Walker_Nav_Menu {

	private $index = 0;
	protected $menuItemOptions;

	protected $submenu_type;
	protected $default_submenu_type = false;

	protected $offset_depth = 0;

	
	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$_depth = $depth+1;
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu sub-menu-$_depth\">\n";

		if( smpnav_op( 'back_button_top' , 'general' ) == 'on' ){
			$output .= $this->get_back_retractor();
		}

	}

	function get_back_retractor(){

		$back_tag = smpnav_op( 'back_tag' , 'general' );

		$back_text = smpnav_op( 'back_text' , 'general' );
		$back_text = $back_text ? $back_text : __( 'Back' , 'smpnav' );

		//Make Content Customizable
		$html = '<li class="smpnav-retract"><'.$back_tag.' tabindex="0" class="smpnav-target"><i class="fa fa-chevron-left"></i> '.$back_text.'</'.$back_tag.'></li>';

		return $html;
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {


		if( smpnav_op( 'back_button_bottom' , 'general' ) != 'off' ){
			$output .= $this->get_back_retractor();
		}

		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		if( $item->object == 'ubermenu-custom' && $item->type_label == '[UberMenu Menu Segment]' ){
			return $this->handle_menu_segment( $output , $item , $depth , $args , $id );
		}



		$data = smpnav_get_menu_item_data( $item->ID );
		//smpnavp( $data );

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/** smpnav Stuff **/
		$smpnav_atts = array();

		//Submenus
		$has_sub = $item->has_sub; 
		if( SMPNAV_PRO ){
			$this->submenu_type = $submenu_type = isset( $data['submenu_type'] ) ? $data['submenu_type'] : 'default';
			if( $submenu_type == 'default' ){
				if( !$this->default_submenu_type ){
					$this->default_submenu_type = smpnav_op( 'submenu_type_default' , '__current_instance__' );
				}
				$this->submenu_type = $submenu_type = $this->default_submenu_type;
			}
		}
		else $this->submenu_type = $submenu_type = 'always';

		if( $has_sub ){
			$classes[] = 'smpnav-sub-'.$submenu_type;
		}

		//Depth
		$classes[] = 'smpnav-depth-'.$depth;


		//Highlight
		if( isset( $data['highlight'] ) && ( $data['highlight'] == 'on' ) ){
			$classes[] = 'smpnav-highlight';
		}

		//ScrollTo
		if( isset( $data['scrollto'] ) && $data['scrollto'] != '' ){
			$classes[] = 'smpnav-scrollto';
			$smpnav_atts['data-smpnav-scrolltarget'] = $data['scrollto'];
		}

		//Icon
		$icon = $icon_class = '';
		//Main Icon Set
		if( isset( $data['icon'] ) && $data['icon'] != '' ){
			$icon_class = $data['icon'];
		}
		//Custom Icon Set
		if( isset( $data['icon_custom_class'] ) && $data['icon_custom_class'] != '' ){
			if( $icon_class ) $icon_class.= ' ';
			$icon_class.= $data['icon_custom_class'];
		}
		//If either has produced a class, create an icon
		if( $icon_class ){
			$classes[] = 'smpnav-has-icon';
			$icon = '<i class="smpnav-icon '.$icon_class.'"></i>';
		}


		//Disable Link
		$disable_link = isset( $data['disable_link'] ) && ( $data['disable_link'] == 'on' ) ? true : false;



		//Title
		$title = '';
		if( !( isset( $data['disable_text'] ) && $data['disable_text'] == 'on' ) ){
			/** This filter is documented in wp-includes/post-template.php */
			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = do_shortcode( $title );
		}
		else{
			$classes[] = 'smpnav-text-disabled';
		}


		

		if( isset( $data['disable_current'] ) && $data['disable_current'] == 'on' ){
			$remove_current = array( 'current-menu-item' , 'current-menu-parent' , 'current-menu-ancestor' );
			foreach( $classes as $k => $c ){
				if( in_array( $c ,  $remove_current ) ){
					unset( $classes[$k] );
				}
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args , $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';



		
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= /* $indent . */ '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';


		//Custom URL
		if( isset( $data['custom_url'] ) && $data['custom_url'] ){
			$atts['href'] = do_shortcode( $data['custom_url'] );
		}

		
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args , $depth );

		//Merge smpnav atts
		$atts = array_merge( $atts , $smpnav_atts );
		if( $disable_link ) unset( $atts['href'] );			//remove href for disabled links

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$el = 'a';
		if( $disable_link ) $el = 'span';

		// Anchor class
		$anchor_class = 'smpnav-target';
		if( isset( $data['anchor_class'] ) && ( $data['anchor_class'] ) ) $anchor_class.= ' '.esc_attr( $data['anchor_class'] );


		$item_output = $args->before;
		$item_output .= '<'.$el.' class="'.$anchor_class.'" '. $attributes .'>';



		if( $icon ) $title = '<span class="smpnav-target-text">'.$title.'</span>';
		$item_output .= $args->link_before . $icon . $title . $args->link_after;

		$item_output .= '</'.$el.'>';

		$item_output .= $args->after;

		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	
	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if( $item->object != 'ubermenu-custom' ||
			$item->type_label != '[UberMenu Menu Segment]' ||
			smpnav_op( 'process_uber_segments' , 'general' ) == 'off' ){

			$output .= "</li>";
		}
	}


	/* Recursive function to remove all children */
	function clear_children( &$children_elements , $id ){

		if( empty( $children_elements[ $id ] ) ) return;

		foreach( $children_elements[ $id ] as $child ){
			$this->clear_children( $children_elements , $child->ID );
		}
		unset( $children_elements[ $id ] );
	}


	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {

		if ( !$element )
			return;

		//Offset Depth
		$original_depth = $depth;
		$depth = $depth + $this->offset_depth;



		$id_field = $this->db_fields['id'];
		$id = $element->$id_field;


		//Ignore UberMenu Elements
		if( $element->object == 'ubermenu-custom' ){
			
			if( $element->type_label == '[UberMenu Menu Segment]' && smpnav_op( 'process_uber_segments' , 'general' ) !== 'off' ){
				//$element->smpnav_menu_segment = 'hi';
				//echo $element->ID ;
			}
			else{
				if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {
					foreach ( $children_elements[ $id ] as $child ){
						if ( !isset($newlevel) ) {
							$newlevel = true;
							//start the child delimiter
							$cb_args = array_merge( array(&$output, $depth), $args);
							
						}
						$this->display_element( $child, $children_elements, $max_depth, $depth+1, $args, $output );
					}
					unset( $children_elements[ $id ] );
				}

				return;
			}
		}


		$data = smpnav_get_menu_item_data( $id );

		//If the item is disabled, kill its children, Lannister-style
		if( isset( $data['disable_item'] ) && ( $data['disable_item'] == 'on' ) ){
			$this->clear_children( $children_elements , $id );
			return;
		}

		//Disabled Submenu
		if( isset( $data['disable_submenu'] ) && ( $data['disable_submenu'] == 'on' ) ){
			$this->clear_children( $children_elements , $id );
		}

		if( isset( $children_elements[$element->ID] ) ){
			$element->has_sub = 1;
		}
		else{
			$element->has_sub = 0;
		}


		//UberMenu Conditionals
		if( smpnav_op( 'inherit_ubermenu_conditionals' , 'general' ) == 'on' ){

			if( function_exists( 'ubermenu' ) ){

				$has_children = ! empty( $children_elements[$element->$id_field] );
				if ( isset( $args[0] ) && is_array( $args[0] ) ){
					$args[0]['has_children'] = $has_children;
				}
				$cb_args = array_merge( array(&$output, $element, $depth), $args);

				$umitem_object_class = apply_filters( 'ubermenu_item_object_class' , 'UberMenuItemDefault' , $element , $id , '' );
				
				$umitem = new dummy_um_item( $element->ID , $element );
				$display_on = apply_filters( 'ubermenu_display_item' , true , $this , $element , $max_depth, $depth, $args , $umitem );


			}
			else{
				$display_on = apply_filters( 'uberMenu_display_item' , true , $this , $element , $max_depth, $depth, $args );
			}

			if( !$display_on ){
				$this->clear_children( $children_elements , $id );
				return;
			}
		}

		Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	function getUberOption( $item_id , $id ){
		
		$option_id = 'menu-item-'.$id;

		//Initialize array
		if( !is_array( $this->menuItemOptions ) ){
			$this->menuItemOptions = array();
			$this->noUberOps = array();
		}

		//We haven't investigated this item yet
		if( !isset( $this->menuItemOptions[ $item_id ] ) ){

			$uber_options = false;
			if( empty( $this->noUberOps[ $item_id ] ) ) {
				$uber_options = get_post_meta( $item_id , '_uber_options', true );
				if( !$uber_options ) $this->noUberOps[ $item_id ] = true; 
			}

			//If $uber_options are set, use them
			if( $uber_options ){
				$this->menuItemOptions[ $item_id ] = $uber_options;
			}
			
			else{
				$option_id = '_menu_item_'.$id; 
				return get_post_meta( $item_id, $option_id , true );
			}
		}
		return isset( $this->menuItemOptions[ $item_id ][ $option_id ] ) ? stripslashes( $this->menuItemOptions[ $item_id ][ $option_id ] ) : '';
	}


	function handle_menu_segment( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		if( !defined( 'UBERMENU_MENU_ITEM_META_KEY' ) ){
			return;
		}

		$um_args = get_post_meta( $item->ID , UBERMENU_MENU_ITEM_META_KEY , true );
		$menu_segment = $um_args['menu_segment'];

		$output .= "<!-- begin Segment: Menu ID $menu_segment -->";

		if( $menu_segment == '_none' || !$menu_segment ){
			$output.='<!-- no menu set for segment-->';
		}

		$menu_object = wp_get_nav_menu_object( $menu_segment );
		if( !$menu_object ){
			return $html.'<!-- no menu exists with ID "'.$menu_segment.'" -->';
		}

		$current_depth_offset = $this->offset_depth;	
		$this->offset_depth = $depth;

		$menu_segment_args = array(
			'menu' 			=> $menu_segment,
			'menu_class'	=> 'na',	//just to prevent PHP notice
			'echo' 			=> false ,
			'container' 	=> false,
			'items_wrap'	=> '%3$s',
			'walker'		=> $this,
			'depth'			=> 0,
			'smpnav_segment' => $item->ID,
			'smpnav_instance' => $args->smpnav,
			'smpnav'		=> $args->smpnav,
			//'uber_instance'	=> $this->args->uber_instance,
			//'uber_segment'	=> $this->ID,
		);

		//Record the settings so we can easily replace when force-filtering
		$menu_segment_args['smpnav_segment_args'] = $menu_segment_args;

		//Generate the menu HTML
		$segment_html = wp_nav_menu( $menu_segment_args );

		$output .= $segment_html;


		$output .= "<!-- end Segment: Menu ID $menu_segment -->";

		$this->offset_depth = $current_depth_offset;
		
	}

}



class dummy_um_item{
	private $ID;
	private $settings;
	private $smpnav_item;
	private $url;

	function __construct( $id , &$item ){
		$this->ID = $id;
		$this->smpnav_item = $item;
	}

	function getSetting( $key ){
		if( !isset( $this->settings ) ){
			$this->settings = get_post_meta( $this->ID, UBERMENU_MENU_ITEM_META_KEY , true );
		}
		if( isset( $this->settings[$key] ) ){
			return $this->settings[$key];
		}
		return false;
	}

	function set_url( $url ){
		$this->url = $url;
		$this->smpnav_item->url = $url;
	}
	function get_url(){
		return $this->smpnav_item->url;
	}
}
