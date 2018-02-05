<?php
/* 1. HOOKS */
  /*1.1 register semua custom shortcode di init*/
  add_action('init','slb_register_shortcodes');

  /*1.2 register custom admin column headers */
  add_filter('manage_edit-slb_subscriber_columns','slb_subscriber_column_headers');
  add_filter('manage_edit-slb_list_columns','slb_list_column_headers');

  /* 1.3 */
  add_filter('manage_slb_subscriber_posts_custom_column','slb_subscriber_column_data',1,2);
  add_filter('manage_slb_list_posts_custom_column','slb_list_column_data',1,2);
  add_action(
    'admin_head-edit.php',
    'slb_register_custom_admin_titles'
);

  /* 1.4 */
  add_action('wp_ajax_nopriv_slb_save_subscription', 'slb_save_subscription'); // regular website visitor
  add_action('wp_ajax_slb_save_subscription', 'slb_save_subscription'); // admin user
  add_action('wp_ajax_nopriv_slb_unsubscribe', 'slb_unsubscribe'); // regular website visitor
  add_action('wp_ajax_slb_unsubscribe', 'slb_unsubscribe'); // admin user
  add_action('wp_ajax_slb_download_subscribers_csv', 'slb_download_subscribers_csv'); // admin users
  add_action('wp_ajax_slb_import_subscribers', 'slb_import_subscribers'); // admin users
  add_action('wp_ajax_slb_parse_import_csv', 'slb_parse_import_csv'); // admin users
  add_action('wp_ajax_slb_announce_subscribers', 'slb_annouce_subscribers');

  //1.5
  add_action('wp_enqueue_scripts','slb_public_scripts');

  //1.6
  add_filter('acf/settings/path', 'slb_acf_settings_path');
  add_filter('acf/settings/dir','slb_acf_settings_dir');
  add_filter('acf/settings/show_admin','slb_acf_show_admin');

  //1.7
  add_action('admin_menu','slb_admin_menu');




  //1.9
  add_action('admin_init','slb_register_options');

  //1.10


  add_action( 'admin_notices', 'slb_check_wp_version' );


  //1.11
  add_action('wp','slb_trigger_reward_download');

?>
