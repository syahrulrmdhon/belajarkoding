<?php
/*
Plugin Name: Syahrul List Builder
Plugin URI: https://github.com/syahrulrmdhon/
Description: Just another plugins
Author: Syahrul Romadhon
Version: 1.1.1
Author URI: https://belajarrkoding.000webhostapp.com/
Text Domain: syahrul-list-builder
Domain Path: /languages
License: GPLv2

*/

    //1.8
    add_action('wp_enqueue_scripts','slb_admin_scripts');
    add_action('admin_enqueue_scripts','slb_admin_scripts');

    define( 'SLB_PLUGIN', __FILE__ );
    define( 'SLB_PLUGIN_DIR', untrailingslashit( dirname( SLB_PLUGIN ) ) );

    require SLB_PLUGIN_DIR . '/inc/hooks.php';
    require SLB_PLUGIN_DIR . '/inc/shortcodes.php';
    require SLB_PLUGIN_DIR . '/inc/filters.php';
    require SLB_PLUGIN_DIR . '/inc/actions.php';
    require SLB_PLUGIN_DIR . '/inc/helpers.php';
    require SLB_PLUGIN_DIR . '/inc/admins.php';

    register_activation_hook(__FILE__,'slb_activate_plugin');
    register_uninstall_hook(__FILE__,'slb_uninstall_plugin');

      /* 4. EXTERNAL SCRIPTS */
      //4.1
      function slb_public_scripts()
      {
        wp_register_script('syahrul-list-builder-js-public',plugins_url('/js/public/syahrul-list-builder.js',__FILE__),array('jquery'),'',true);
        wp_register_style('syahrul-list-builder-css-public',plugins_url('/css/public/style.css',__FILE__));
        wp_enqueue_script('syahrul-list-builder-js-public');
        wp_enqueue_style('syahrul-list-builder-css-public');
      }

      //4.2
      include_once(plugin_dir_path(__FILE__).'lib/advanced-custom-fields/acf.php');

      //4.3
      function slb_admin_scripts()
      {
        wp_register_script('syahrul-list-builder-js-private',plugins_url('/js/private/syahrul-list-builder.js',__FILE__),array('jquery'),'',true);
        wp_enqueue_script('syahrul-list-builder-js-private');
      }

      /* 7. CUSTOM POST TYPES */
      //7.1
      include_once(plugin_dir_path(__FILE__).'/cpt/slb_subscriber.php');

      //7.2
      include_once(plugin_dir_path(__FILE__).'/cpt/slb_list.php');

      /* 9. SETTINGS */
      // 9.1
      // hint: registers all our plugin options
      function slb_register_options() {
      	$options = slb_get_options_settings();
      	foreach( $options['settings'] as $setting ):
      		register_setting($options['group'], $setting);

      	endforeach;

      }
