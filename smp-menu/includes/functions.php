<?php

function smpnav_togglebar($toggle_target = 'smpnav-main', $content = '', $args = array())
{

    extract(shortcode_atts(array(
        'bar_id' => '',
        'toggle_align' => smpnav_op('align', 'togglebar'),
        'toggle_style' => smpnav_op('toggle_bar_style', 'togglebar'),
        'toggle_target_area' => smpnav_op('toggle_target', 'togglebar'),
        'togglebar_gap' => smpnav_op('togglebar_gap', 'togglebar'),
        'togglebar_transparent' => smpnav_op('background_transparent', 'togglebar'),
        'hamburger_size' => smpnav_op('togglebar_hamburger_size', 'togglebar'),
        'font_size' => smpnav_op('font_size', 'togglebar'),
        'background_color' => smpnav_op('background_color', 'togglebar'),
        'text_color' => smpnav_op('text_color', 'togglebar'),
        'menu_item_bg' => smpnav_op('menu_item_bg', 'togglebar'),
        'font_family' => smpnav_op('font_family', 'general'),
    ), $args))

?>


<?php
    $disable_toggle = false; //true;
    $toggle_class = 'smpnav-toggle-main-align-' . $toggle_align;
    $toggle_class .= ' smpnav-toggle-style-' . $toggle_style;
    $toggle_class .= ' smpnav-togglebar-gap-' . $togglebar_gap;
    if(!empty($hamburger_size)) {
        echo '<style>#smpnav-toggle-main #smpnav-toggle-main-button i {font-size: '.$hamburger_size.'px;} </style>';
    }
    if(!empty($font_size)){
        echo '<style>a.smpnav-target{font-size:'.$font_size.'px;}</style>';
    } 
    if(!empty($font_family)){
        echo '<style>a.smpnav-target{font-family:"'.$font_family.'";}</style>';
    }
    if(!empty($background_color)){
       echo '<style>#smpnav-toggle-main{background-color: '.$background_color.';} </style>';
    }
    if(!empty($text_color)){
       echo '<style>.smpnav ul.smpnav-menu.smpnav-targets-text-large li.menu-item>.smpnav-target{color:'.$text_color.';}</style>';
       echo '<style>#smpnav-toggle-main #smpnav-toggle-main-button i{color:'.$text_color.';}</style>';
    }
     if(!empty($menu_item_bg)) {
        echo '<style>.smpnav.smpnav-skin-standard-dark, .smpnav.smpnav-skin-standard-dark ul.smpnav-menu {background-color: '.$menu_item_bg.';} </style>';
    }
    
    if ($toggle_target_area == 'entire_bar')
    {
        $toggle_class .= ' smpnav-toggle-main-entire-bar';
    }
    else
    {
        $disable_toggle = true;
        add_action('smpnav_toggle_before_content', 'smpnav_main_toggle_burger', 10, 3);
    }

    if ($togglebar_transparent == 'on')
    {
        $toggle_class .= ' smpnav-togglebar-transparent';
    }

    $toggle_target = apply_filters('smpnav_main_toggle_target', $toggle_target);

    if ($toggle_style === 'burger_only') $content = false;

    smpnav_toggle($toggle_target, $content, array(
        'id' => $bar_id,
        'el' => 'div',
        'class' => $toggle_class,
        'disable_toggle' => $disable_toggle,
        'tabindex' => 1,
    ));

    remove_action('smpnav_toggle_before_content', 'smpnav_main_toggle_burger', 10, 3);

}

function smpnav_direct_injection()
{

    if (!_smpnav()->display_now()) return;

    if (smpnav_op('display_toggle', 'togglebar') == 'on')
    {
        $content = '';
        $toggle_style = smpnav_op('toggle_bar_style', 'togglebar');
        if ($toggle_style !== 'burger_only') $content = smpnav_main_toggle_content();
        smpnav_togglebar('smpnav-main', $content, array(
            'bar_id' => 'smpnav-toggle-main'
        ));
    }

    if (smpnav_op('display_main', 'smpnav-main') == 'on')
    {
        smpnav('smpnav-main', array(
            'theme_location' => 'smpnav',
            'edge' => smpnav_op('edge', 'smpnav-main') ,
        ));
    }
}
add_action('wp_footer', 'smpnav_direct_injection');

function smpnav_main_toggle_burger($main_toggle, $target_id, $id)
{
    if ($main_toggle)
    {
        $main_toggle_target = apply_filters('smpnav_main_toggle_target', 'smpnav-main');
        $main_toggle_icon_class = apply_filters('smpnav_main_toggle_icon_class', 'fa fa-bars');
        $main_toggle_content = apply_filters('smpnav_main_toggle_content', '<i class="' . $main_toggle_icon_class . '"></i>');

        smpnav_toggle($main_toggle_target, $main_toggle_content, array(
            'id' => 'smpnav-toggle-main-button',
            'el' => 'button',
            'class' => 'smpnav-toggle-burger',
            'tabindex' => 1,
            'aria_label' => smpnav_op('aria_label', 'togglebar') ,
            'actions' => false,
        ));
    }
}

function smpnav_main_toggle_content()
{
    return '<div class="smpnav-main-toggle-content smpnav-toggle-main-block">' . do_shortcode(smpnav_op('toggle_content', 'togglebar')) . '</div>';
}

function _smpnav_toggle($target_id, $content = '', $args = array())
{

    extract(wp_parse_args($args, array(
        'id' => '',
        'el' => 'a',
        'class' => '',
        'disable_toggle' => false,
        'actions' => true,
        'icon' => '',
        'tabindex' => 0,
        'aria_label' => false,
    )));

    $content = do_shortcode($content);

    $main_toggle = false;
    if ($id && $id == 'smpnav-toggle-main') $main_toggle = true;

    if ($main_toggle)
    {
        $class .= ' smpnav-toggle-edge-' . smpnav_op('edge', 'smpnav-main');
        $class .= ' smpnav-toggle-icon-' . smpnav_op('toggle_close_icon', 'togglebar');

        if (smpnav_op('toggle_position', 'togglebar') == 'absolute')
        {
            $class .= ' smpnav-toggle-position-absolute';
        }

        if (smpnav_op('hide_bar_on_scroll', 'togglebar', 'off') === 'on')
        {
            $class .= ' smpnav--hide-scroll-down';
        }

    }

    $target_att = '';
    $tabindex_att = '';
    if (!$disable_toggle)
    {
        $tabindex_att = 'tabindex="' . $tabindex . '"';
        $target_att = 'data-smpnav-target="' . $target_id . '"';
        $class = 'smpnav-toggle smpnav-toggle-' . $target_id . ' ' . $class;
    }

    if ($aria_label) $aria_label = 'aria-label="' . $aria_label . '"';

    echo "<$el ";
    if ($id): ?>id="<?php echo $id; ?>"<?php
    endif;
?> class="<?php echo $class; ?>" <?php echo $tabindex_att; ?> <?php echo $target_att; ?> <?php echo $aria_label; ?>><?php
    if ($actions) do_action('smpnav_toggle_before_content', $main_toggle, $target_id, $id);
    if ($icon) echo '<i class="fa fa-' . $icon . '"></i> ';
    echo apply_filters('smpnav_toggle_content', $content, $target_id, $id);
    if ($actions) do_action('smpnav_toggle_after_content', $main_toggle, $target_id, $id);
    echo "</$el>"; ?>
	<?php
}

function smpnav_toggle_shortcode($atts, $content)
{

    extract(shortcode_atts(array(
        'target' => 'smpnav-main',
        'toggle_id' => '',
        'el' => 'a',
        'class' => '',
        'icon' => '',
        'disable_content' => '',
        'aria_label' => false,
    ) , $atts, 'smpnav_toggle'));

    if ($disable_content == 'true') $content = false;

    ob_start();

    smpnav_toggle($target, $content, array(
        'id' => $toggle_id,
        'el' => $el,
        'class' => $class,
        'icon' => $icon,
        'aria_label' => $aria_label
    ));

    $toggle = ob_get_contents();

    ob_end_clean();

    return $toggle;
}
add_shortcode('smpnav_toggle', 'smpnav_toggle_shortcode');

function smpnav_fallback()
{
    smpnav_show_tip('No menu to display');
}

function smpnav_register_theme_locations()
{
    register_nav_menu('smpnav', __('smpnav [Main]'));
}
add_action('init', 'smpnav_register_theme_locations');

function smpnav_load_include()
{

    if (!_smpnav()->display_now()) return;

    $include = SMPNAV_URL . 'include/';
    if (SCRIPT_DEBUG)
    {
        wp_enqueue_style('smpnav', $include . 'css/smpnav.css', false, SMPNAV_VERSION);
        wp_enqueue_style('smpnav-custom', $include . 'css/custom.css', false, SMPNAV_VERSION);
        wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    }
    else
    {
        wp_enqueue_style('smpnav', $include . 'css/smpnav.min.css', false, SMPNAV_VERSION);
        wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('smpnav-custom', $include . 'css/custom.css', false, SMPNAV_VERSION);
    }

    if (smpnav_op('load_fontawesome', 'general') == 'on')
    {
        wp_enqueue_style('smpnav-font-awesome', $include . 'css/fontawesome/css/font-awesome.min.css', false, SMPNAV_VERSION);
    }

    $skin = smpnav_op('skin', 'smpnav-main');
    if ($skin != 'none') smpnav_enqueue_skin($skin);

    wp_enqueue_script('jquery');
    if (SCRIPT_DEBUG)
    {
        wp_enqueue_script('smpnav', $include . 'js/smpnav.js', array('jquery') , SMPNAV_VERSION, true);
    }
    else
    {
        wp_enqueue_script('smpnav', $include . 'js/smpnav.min.js', array('jquery' ) , SMPNAV_VERSION, true);
        wp_enqueue_script('smpnav-custom', $include . 'js/custom.js', array('jquery') , SMPNAV_VERSION, true);
    }

    wp_localize_script('smpnav', 'smpnav_data', array(
        'smp_body' => smpnav_op('smp_body', 'general') ,
        'smp_body_wrapper' => smpnav_op('smp_body_wrapper', 'general') ,
        'lock_body' => smpnav_op('lock_body', 'general') ,
        'lock_body_x' => smpnav_op('lock_body_x', 'general') ,
        'open_current' => smpnav_op('open_current', 'general') ,
        //'collapse_accordions' => smpnav_op('collapse_accordions', 'general') ,
        //'scroll_panel' => smpnav_op('scroll_panel', 'general') ,
        //'breakpoint' => smpnav_op('breakpoint', 'togglebar') ,
        //'v' => SMPNAV_VERSION,
        //'pro' => 0,

        'touch_off_close' => smpnav_op('touch_off_close', 'general') ,
        'scroll_offset' => smpnav_op('scroll_offset', 'general') ,
        'disable_transforms' => smpnav_op('disable_transforms', 'general') ,
        'close_on_target_click' => smpnav_op('close_on_target_click', 'general') ,
        'scroll_top_boundary' => smpnav_op('scroll_top_boundary', 'general', 50) ,
        'scroll_tolerance' => smpnav_op('scroll_tolerance', 'general', 10) ,
        //'process_uber_segments' => smpnav_op('process_uber_segments', 'general') ,
    ));
}
add_action('wp_enqueue_scripts', 'smpnav_load_include', 101);

function smpnav_get_skin_ops()
{

    $registered_skins = _smpnav()->get_skins();
    if (!is_array($registered_skins)) return array();
    $ops = array();
    foreach ($registered_skins as $id => $skin)
    {
        $ops[$id] = $skin['title'];
    }
    return $ops;

}
function smpnav_register_skin($id, $title, $path)
{
    _smpnav()->register_skin($id, $title, $path);
}

add_action('init', 'smpnav_register_skins');
function smpnav_register_skins()
{
    $main = SMPNAV_URL . 'include/css/skins/';
    smpnav_register_skin('standard-dark', 'Standard Dark', $main . 'standard-dark.css');
    smpnav_register_skin('light', 'Standard Light', $main . 'light.css');
}
function smpnav_enqueue_skin($skin)
{
    wp_enqueue_style('smpnav-' . $skin);
}

function smpnav_bloginfo_shortcode($atts)
{
    extract(shortcode_atts(array(
        'key' => '',
    ) , $atts));
    return get_bloginfo($key);
}
add_shortcode('smpnav_bloginfo', 'smpnav_bloginfo_shortcode');

function smpnav_default_toggle_content($atts)
{
    return '<a href="' . get_home_url() . '">' . get_bloginfo('title') . '</a>';
}
add_shortcode('shift_toggle_title', 'smpnav_default_toggle_content');

function smpnav_main_site_title($instance_id)
{
    if (smpnav_op('display_site_title', $instance_id) == 'on'):
?>
	<h3 class="smpnav-menu-title smpnav-site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo(); ?></a></h3>
	<?php
    endif;

    if (smpnav_op('display_instance_title', $instance_id) == 'on'):
?>
	<h3 class="smpnav-menu-title smpnav-instance-title"><?php echo smpnav_op('instance_name', $instance_id); ?></h3>
	<?php
    endif;

}
add_action('smpnav_before', 'smpnav_main_site_title', 10);

/* Stop Interference */
add_action('wp_head', 'smpnav_prevent_interference');
function smpnav_prevent_interference()
{
    if (smpnav_op('force_filter', 'general') == 'on')
    {
        add_filter('wp_nav_menu_args', 'smpnav_force_filter', 1000);
    }
    if (smpnav_op('kill_class_filter', 'general') == 'on')
    {
        remove_all_filters('nav_menu_css_class');
    }
}

/* Force Filter */
function smpnav_force_filter($args)
{

    if (isset($args['smpnav']))
    {
        $args = smpnav_get_menu_args($args);
        /*$args['container_class'] 	= 'smpnav-nav';
        $args['container']			= 'nav';
        $args['menu_class']			= 'smpnav-menu';
        $args['walker']				= new SMPNavWalker;
        $args['fallback_cb']		= 'smpnav_fallback';
        $args['depth']				= 0;*/
    }

    if (isset($args['smpnav_segment']))
    {
        if (isset($args['smpnav_segment_args'])) $args = array_merge($args, $args['smpnav_segment_args']);
    }

    return $args;
}

function smpnav_get_menu_args($args, $id = 0)
{

    $args['container_class'] = 'smpnav-nav';
    $args['container'] = 'nav';
    $args['menu_class'] = 'smpnav-menu';
    $args['walker'] = new SMPNavWalker;
    $args['fallback_cb'] = 'smpnav_fallback';
    $args['depth'] = 0;
    $args['items_wrap'] = '<ul id="%1$s" class="%2$s">%3$s</ul>';

    if ($id === 0) $id = isset($args['smpnav']) ? $args['smpnav'] : 'smpnav-main';

    //Target size
    $args['menu_class'] .= ' smpnav-targets-' . smpnav_op('target_size', 'general');

    //Text size
    $args['menu_class'] .= ' smpnav-targets-text-' . smpnav_op('text_size', 'general');

    //Icon size
    $args['menu_class'].= ' smpnav-targets-icon-'.smpnav_op( 'icon_size' , 'general' );
    //Submenu indent
    //if (smpnav_op('indent_submenus', $id) == 'on') $args['menu_class'] .= ' smpnav-indent-subs';

    //Active on hover
    if (smpnav_op('active_on_hover', 'general') == 'on') $args['menu_class'] .= ' smpnav-active-on-hover';

    //Active Highlight
    if (smpnav_op('active_highlight', 'general') == 'on') $args['menu_class'] .= '	smpnav-active-highlight';

    return $args;
    //print_r($args);

}

function smpnav_user_is_admin()
{
    return current_user_can('manage_options');
}

function smpnav_show_tip($content)
{
    $showtips = false;
    if (smpnav_op('admin_tips', 'general') == 'on')
    {
        if (smpnav_user_is_admin())
        {
            echo '<div class="smpnav-admin-tip">' . $content . '</div>';
        }
    }
}

function smpnav_count_menus()
{
    $menus = wp_get_nav_menus(array(
        'orderby' => 'name'
    ));
    if (count($menus) == 0)
    {
        smpnav_show_tip('Oh no!  You don\'t have any menus yet.  <a href="' . admin_url('nav-menus.php') . '">Create a menu</a>');
    }
}

function smpnavp($d)
{
    echo '<pre>';
    print_r($d);
    echo '</pre>';

}

