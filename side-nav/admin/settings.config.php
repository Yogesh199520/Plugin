<?php
add_action('smpnav_settings_before_title', 'smpnav_settings_links');
function smpnav_get_settings_fields()
{
    $prefix = SMPNAV_PREFIX;

    $main_assigned = '';
    if (!has_nav_menu('smpnav'))
    {
        $main_assigned = 'No Menu Assigned';
    }
    else
    {
        $menus = get_nav_menu_locations();
        $menu_title = wp_get_nav_menu_object($menus['smpnav'])->name;
        $main_assigned = $menu_title;
    }

    $main_assigned = '<span class="smpnav-main-assigned">' . $main_assigned . '</span>  <p class="smpnav-desc-understated">The menu assigned to the <strong>SMPNav [Main]</strong> theme location will be displayed.  <a href="' . admin_url('nav-menus.php?action=locations') . '">Assign a menu</a></p>';

    $menu = 'smpnav-main';
    /*$hint = '
            <div class="smpnav-desc-row">
                <span class="smpnav-code-snippet-type">PHP</span> <code class="smpnav-highlight-code">&lt;?php smpnav_toggle( \''.$menu.'\' , \'Toggle Menu\' , array( \'icon\' => \'bars\' , \'class\' => \'smpnav-toggle-button\') ); ?&gt;</code>
            </div>
            <div class="smpnav-desc-row">
                <span class="smpnav-code-snippet-type">Shortcode</span> <code class="smpnav-highlight-code">[smpnav_toggle target="'.$menu.'" class="smpnav-toggle-button" icon="bars"]Toggle Menu[/smpnav_toggle]</code>'.
            '</div>';*/

    $fields = array(

        $prefix . 'smpnav-main' => array(

            10 => array(
                'name' => 'menu_assignment',
                'label' => __('Assigned Menu', 'smpnav') ,
                'desc' => $main_assigned,
                'type' => 'html',
            ) ,

            20 => array(
                'name' => 'display_main',
                'label' => __('Display Main SMPNav Panel', 'smpnav') ,
                'desc' => __('Do not uncheck this unless you want to disable the main SMPNav panel entirely.', 'smpnav') ,
                'type' => 'checkbox',
                'default' => 'on',
                'customizer' => true,
                'customizer_section' => 'config',
            ) ,

            30 => array(
                'name' => 'edge',
                'label' => __('Edge', 'smpnav') ,
                'type' => 'radio',
                'desc' => __('Which edge of the viewport should the SMPNav panel appear on?', 'ubermenu') ,
                'options' => array(
                    'left' => 'Left',
                    'right' => 'Right',
                ) ,
                'default' => 'left',
                'customizer' => true,
                'customizer_section' => 'config',

            ) ,

            40 => array(
                'name' => 'skin',
                'label' => __('Skin', 'smpnav') ,
                'type' => 'select',
                'desc' => 'Select the base skin for your menu.  You can override styles in the Customizer settings.',
                'options' => smpnav_get_skin_ops() ,
                'default' => 'standard-dark',
                'customizer' => true,
            ) ,

            50 => array(
                'name' => 'display_site_title',
                'label' => __('Display Site Title', 'smpnav') ,
                'desc' => __('Display the site title in the menu panel', 'smpnav') ,
                'type' => 'checkbox',
                'default' => 'on',
                'customizer' => true,
                'customizer_section' => 'content',
            ) ,

            //After pro settings, before customizer settings
            /*1100 => array(
                'name'  => 'php',
                'label' => __( 'How to use' , 'smpnav' ),
                'desc'  => $hint,
                'type'  => 'html',
            ),*/
        ) ,

        $prefix . 'togglebar' => array(
            10 => array(
                'name' => 'display_toggle',
                'label' => __('Display Toggle Bar', 'smpnav') ,
                'desc' => __('Uncheck this to disable the default toggle bar and add your own custom toggle', 'smpnav') ,
                'type' => 'checkbox',
                'default' => 'on',
                'customizer' => true,
                'customizer_section' => 'config',
            ) ,
            15 => array(
                'name' => 'toggle_bar_style',
                'label' => __('Toggle Bar Style', 'smpnav') ,
                'desc' => __('Choose whether to have a full width bar, which can include a title and other content, or just a hamburger button only which will appear in the upper corner of the site.', 'smpnav') ,
                'type' => 'radio',
                'options' => array(
                    'full_bar' => __('Full Bar', 'smpnav') ,
                    'burger_only' => __('Hamburger button only', 'smpnav') ,
                ) ,
                'default' => 'full_bar',
                'customizer' => true,
                'customizer_section' => 'config',
            ) ,

            20 => array(
                'name' => 'toggle_target',
                'label' => __('Toggle Target', 'smpnav') ,
                'desc' => __('The area which will trigger the smpnav Panel to open.  (Not relevant for Full Bar toggle style)') ,
                'type' => 'radio',
                'options' => array(
                    'burger_only' => __('Bars/Burger Icon Only', 'smpnav') ,
                    'entire_bar' => __('Entire Bar', 'smpnav') ,
                ) ,
                'default' => 'burger_only',
            ) ,

            25 => array(
                'name' => 'toggle_close_icon',
                'label' => __('Close Icon', 'smpnav') ,
                'desc' => __('When the toggle is open, choose which icon to display.', 'smpnav') ,
                'type' => 'radio',
                'options' => array(
                    'bars' => '<i class="fa fa-bars"></i> Hamburger Bars',
                    'x' => '<i class="fa fa-times"></i> Close button',
                ) ,
                'default' => 'x',
                'customizer' => true,
                'customizer_section' => 'config',
                'customizer_control' => 'radio_html'
            ) ,
            30 => array(
                'name' => 'toggle_position',
                'label' => __('Toggle Bar Position', 'smpnav') ,
                'desc' => __('Choose Fixed if you\'d like the toggle bar to always be visible, or Absolute if you\'d like it only to be visible when scrolled to the very top of the page', 'smpnav') ,
                'type' => 'radio',
                'options' => array(
                    'fixed' => __('Fixed (always in viewport)', 'smpnav') ,
                    'absolute' => __('Absolute (scrolls out of viewport)', 'smpnav') ,
                ) ,
                'default' => 'fixed',
                'customizer' => true,
                'customizer_section' => 'config',
            ) ,

            40 => array(
                'name' => 'align',
                'label' => __('Align Icon', 'smpnav') ,
                'desc' => __('Align Icon left, right, or center.  Applies to inline elements only.', 'smpnav') ,
                'type' => 'radio',
                'options' => array(
                    'center' => 'Center',
                    'left' => 'Left',
                    'right' => 'Right',
                ) ,
                'default' => 'center',
                'customizer' => true,
                'customizer_section' => 'config',
            ) ,

            50 => array(
                'name' => 'background_color',
                'label' => __('Humburger Hover Color', 'smpnav') ,
                'desc' => __('', 'smpnav') ,
                'type' => 'color',
                'custom_style' => 'togglebar_background',
                'customizer' => true,
                'customizer_section' => 'styles',
            ) ,

            55 => array(
                'name' => 'background_transparent',
                'label' => __('Transparent Humburger Background', 'smpnav') ,
                'desc' => __('Make the toggle bar transparent.  Note that this only make sense if you are using a hamburger-only Toggle Bar Style, or remove the Toggle Bar Gap', 'smpnav') ,
                'type' => 'checkbox',
                'default' => 'off',
                'customizer' => true,
                'customizer_section' => 'styles',
            ) ,

            60 => array(
                'name' => 'text_color',
                'label' => __('Text/Burger Color', 'smpnav') ,
                'desc' => __('This will change nav item color and humburger icon color', 'smpnav') ,
                'type' => 'color',
                'custom_style' => 'togglebar_font_color',
                'customizer' => true,
                'customizer_section' => 'styles',
            ) ,

            70 => array(
                'name' => 'font_size',
                'label' => __('Font Size', 'smpnav') ,
                'desc' => __('Override the default font size of the toggle bar by setting a value here.', 'smpnav') ,
                'type' => 'text',
                'default' => '',
                'custom_style' => 'togglebar_font_size',
                'customizer' => true,
                'customizer_section' => 'styles',
            ) ,

            80 => array(
                'name' => 'togglebar_hamburger_size',
                'label' => __('Hamburger Size', 'smpnav') ,
                'desc' => __('Size of the hamburger icon in pixes (font size).', 'smpnav') ,
                'type' => 'text',
                'default' => '',
                'custom_style' => 'togglebar_hamburger_size',
                'customizer' => true,
                'customizer_section' => 'styles',
            ) ,

            90 => array(
                'name' => 'togglebar_gap',
                'label' => __('Toggle Bar Gap', 'smpnav') ,
                'desc' => __('', 'smpnav') ,
                'type' => 'radio',
                'options' => array(
                    'auto' => __('Automatic', 'smpnav') ,
                    'off' => __('Disable Gap', 'smpnav') ,
                    'on' => __('Enable Gap', 'smpnav') ,
                ) ,
                'default' => 'auto',
                'customizer' => true,
                'customizer_section' => 'styles',
            ) ,

            100 => array(
                'name' => 'menu_item_bg',
                'label' => __('Menu Item BG Color', 'smpnav') ,
                'desc' => __('This color will be shown in menu item as background color', 'smpnav') ,
                'type' => 'color',
                'custom_style' => 'togglebar_bg_color',
                'customizer' => true,
                'customizer_section' => 'styles',
            ) ,

        ) ,

    );

    $fields = apply_filters('smpnav_settings_panel_fields', $fields);

    $fields[$prefix . 'general'] = array(

        10 => array(
            'name' => 'target_size',
            'label' => __('Button Size', 'smpnav') ,
            'desc' => __('The size of the padding on the links in the menu.  The larger the setting, the easier to click; but fewer menu items will appear on the screen at a time.', 'smpnav') ,
            'type' => 'radio',
            'options' => array(
                'default' => 'Default',
                'medium' => 'Medium',
                'large' => 'Large',
                'enormous' => 'Enormous',
            ) ,
            'default' => 'default',
        ) ,

        20 => array(
            'name' => 'text_size',
            'label' => __('Text Size', 'smpnav') ,
            'desc' => __('The size of the font on the links in the menu (will override all levels).', 'smpnav') ,
            'type' => 'radio',
            'options' => array(
                'default' => 'Default',
                'small' => 'Small',
                'medium' => 'Medium',
                'large' => 'Large',
                'enormous' => 'Enormous',
            ) ,
            'default' => 'default',
        ) ,

        30 => array(
            'name' => 'active_on_hover',
            'label' => __('Highlight Targets on Hover', 'smpnav') ,
            'desc' => __('With this setting enabled, the links will be highlighted when hovered or touched.', 'smpnav') ,
            'type' => 'checkbox',
            'default' => 'off'
        ) ,

        40 => array(
            'name' => 'active_highlight',
            'label' => __('Highlight Targets on :active', 'smpnav') ,
            'desc' => __('With this setting enabled, the links will be highlighted while in the :active state.  May not be desirable for touch scrolling.', 'smpnav') ,
            'type' => 'checkbox',
            'default' => 'off'
        ) ,

        45 => array(
            'name'  => 'menu_type',
            'label' => __('Menu Type', 'smpnav') ,
            'desc' => __('Select menu type option, default is custom', 'smpnav') ,
            'type' => 'select',
            'options' =>array(
                'custom' => __('Custom', 'smpnav') ,
                'bs5' => __('Bootstrap 5', 'smpnav') ,
                'bs4' => __('Bootstrap 4', 'smpnav') ,
            ),
            'default' => 'on'
        ) ,

        50 => array(
                'name' => 'font_family',
                'label' => __('Font Family', 'smpnav') ,
                'desc' => __('', 'smpnav') ,
                'type' => 'select',
                'options' => array(
                    'Lato' => __('Lato', 'smpnav') ,
                    'Noto Sans HK' => __('Noto Sans HK', 'smpnav') ,
                    'Nunito' => __('Nunito', 'smpnav') ,
                    'Nunito Sans' => __('Nunito Sans', 'smpnav') ,
                    'Open Sans' => __('Open Sans', 'smpnav') ,
                    'Oswald' => __('Oswald', 'smpnav') ,
                    'Rajdhani' => __('Rajdhani', 'smpnav') ,
                    'Roboto' => __('Roboto', 'smpnav') ,
                    'Roboto Slab' => __('Roboto Slab', 'smpnav') ,
                    'Urbanist' => __('Urbanist', 'smpnav') ,
                    'Fredoka One' =>__('Fredoka One','smpnav'),
                    'Gloria Hallelujah' =>__('Gloria Hallelujah','smpnav'),
                    'Handlee' =>__('Handlee','smpnav'),
                    'Homemade Apple' =>__('Homemade Apple','smpnav'),
                   
                ) ,
                'default' => 'default',
            ) ,

    );

    foreach ($fields as $section_id => $section_fields)
    {
        ksort($fields[$section_id]);
    }

    $fields = apply_filters('smpnav_settings_panel_fields_after', $fields);

    return $fields;
}

function smpnav_get_settings_sections()
{

    $prefix = SMPNAV_PREFIX;
    $sections = array(
        array(
            'id' => $prefix . 'smpnav-main',
            'title' => __('SMP NAV Settings', 'smpnav') ,
        ) ,
        array(
            'id' => $prefix . 'togglebar',
            'title' => __('Toggle Bar', 'smpnav')
        )
    );
    $sections = apply_filters('smpnav_settings_panel_sections', $sections);
    $sections[] = array(
        'id' => $prefix . 'general',
        'title' => __('General Settings', 'smpnav') ,
    );
    return $sections;

}

//RESET BUTTONS
add_filter('smpnav_settings_panel_fields_after', 'smpnav_settings_panel_resets', 100);

function smpnav_settings_panel_resets($fields = array())
{
    $sections = smpnav_get_menu_configurations(true);
    $sections[] = 'togglebar';
    $sections[] = 'general';
    foreach ($sections as $section)
    {
        $fields[SMPNAV_PREFIX . $section][10000] = array(
            'name' => 'reset',
            'label' => __('Reset Settings', 'smpnav') ,
            'desc' => '<a class="smpnav_button_reset" href="' . admin_url('themes.php?page=smpnav-settings&do=reset-section&section_id=' . $section . '&smpnav_nonce=' . wp_create_nonce('smpnav-control-panel')) . '" >' . __('Reset Settings', 'smpnav') . '</a>',
            'type' => 'html',
        );
    }
    return $fields;
}

function smpnav_admin_init()
{
    $prefix = SMPNAV_PREFIX;
    $sections = smpnav_get_settings_sections();
    $fields = smpnav_get_settings_fields();
    _smpnav()->set_defaults($fields);
    $settings_api = _smpnav()->settings_api();
    $settings_api->set_sections($sections);
    $settings_api->set_fields($fields);
    $settings_api->admin_init();

}
add_action('admin_init', 'smpnav_admin_init');
function smpnav_init_frontend_defaults()
{
    if (!is_admin())
    {
        _smpnav()->set_defaults(smpnav_get_settings_fields());
    }
}
add_action('init', 'smpnav_init_frontend_defaults');
function smpnav_admin_menu()
{
    add_submenu_page(
        'themes.php', 
        'SMPNAV Settings', 
        'SMPNAV', 
        'manage_options', 
        'smpnav-settings', 
        'smpnav_settings_panel'
    );
}
add_action('admin_menu', 'smpnav_admin_menu');

function smpnav_get_nav_menu_ops()
{
    $menus = wp_get_nav_menus(array(
        'orderby' => 'name'
    ));
    $m = array(
        '_none' => 'Choose Menu, or use Theme Location Setting'
    );
    foreach ($menus as $menu)
    {
        $m[$menu->slug] = $menu->name;
    }
    return $m;
}

function smpnav_get_theme_location_ops()
{
    $locs = get_registered_nav_menus();
    $default = array(
        '_none' => 'Select Theme Location or use Menu Setting'
    );
    $locs = $default + $locs;
    return $locs;
}

function smpnav_admin_back_to_settings_button()
{
?>
    <a class="button" href="<?php echo admin_url('themes.php?page=smpnav-settings'); ?>">&laquo; Back to smpnav Control Panel</a>
    <?php
}

function smpnav_reset_settings($section)
{

    delete_option(SMPNAV_PREFIX . $section);

}

function smpnav_settings_panel()
{
    if (isset($_GET['do']))
    {
        check_admin_referer('smpnav-control-panel', 'smpnav_nonce');
        switch ($_GET['do'])
        {
            case 'reset-section':
                $section_id = sanitize_key($_GET['section_id']);
                smpnav_reset_settings($section_id);
                echo "<h3>Completed Settings Reset for Section [$section_id]</h3>";
                smpnav_admin_back_to_settings_button();
                return;
            break;
        }
    }

    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true')
    {
        do_action('smpnav_settings_panel_updated');
    }
    do_action('smpnav_settings_panel');
    $settings_api = _smpnav()->settings_api();
?>
    <div class="wrap smpnav-wrap">
        <?php
    settings_errors();
    do_action('smpnav_settings_before');
    $settings_api->show_navigation();
    $settings_api->show_forms();
    do_action('smpnav_settings_after');
?>
    </div>
    <?php
}

function smpnav_get_menu_configurations($main = false)
{
    $configs = get_option(SMPNAV_MENU_CONFIGURATIONS, array());

    if ($main)
    {
        $configs[] = 'smpnav-main';
    }

    return $configs;
}

function smpnav_op($option, $section, $default = null)
{

    if ($section == '__current_instance__')
    {
        $section = _smpnav()->get_current_instance();
    }

    $options = get_option(SMPNAV_PREFIX . $section);
    $value = '';
    $defaulted = false;

    if (isset($options[$option]))
    {
        $value = $options[$option];
    }
    else if ($default === null)
    {
        $value = $default = _smpnav()->get_default($option, SMPNAV_PREFIX . $section);
        $defaulted = true;
    }
    else
    {
        $value = $default;
    }

    return apply_filters('smpnav_op', $value, $option, $section, $default, $defaulted);
}

function smpnav_get_instance_options($instance)
{
    $defaults = _smpnav()->get_defaults(SMPNAV_PREFIX . $instance);
    $options = get_option(SMPNAV_PREFIX . $instance, $defaults);
    if (!is_array($options) || count($options) == 0) return $defaults;
    return $options;
}

function smpnav_admin_panel_styles()
{

}

function smpnav_admin_panel_include($hook)
{
    if ($hook == 'appearance_page_smpnav-settings')
    {
        wp_enqueue_script('smpnav', SMPNAV_URL . 'admin/include/admin.settings.js');
        wp_enqueue_style('smpnav-settings-styles', SMPNAV_URL . 'admin/include/admin.settings.css');
        wp_enqueue_style('smpnav-font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    }
}
add_action('admin_enqueue_scripts', 'smpnav_admin_panel_include');

function smpnav_check_menu_assignment()
{
    $display = smpnav_op('display_main', 'smpnav-main');

    if ($display == 'on')
    {
        if (!has_nav_menu('smpnav'))
        {
?>
            <div class="update-nag"><strong>Important!</strong> There is no menu assigned to the <strong>SMP NAV [Main]</strong> Menu Location.  <a href="<?php echo admin_url('nav-menus.php?action=locations'); ?>">Assign a menu</a></div>
            <br/><br/>
            <?php
        }
    }
}
add_action('smpnav_settings_before', 'smpnav_check_menu_assignment');

function smpnav_allow_html($str)
{
    return $str;
}

