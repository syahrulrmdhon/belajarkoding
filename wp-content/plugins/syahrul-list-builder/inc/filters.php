<?php

/* 3. FILTERS */
  /* 3.1 */
    function slb_subscriber_column_headers($columns)
    {
      $columns = array(
        'cb' => '<input type="checkbox"></input>' ,
        'title' =>__('Subscribers Name') ,
        'email' =>__('Email Address','slb_email'),
      );
      return $columns;
    }
  /* 3.2 */
    function slb_subscriber_column_data($column , $post_id)
    {
      $output = '';
      switch ($column) {
        case 'title':
          $output .= get_field('slb_fname', $post_id );
          break;
        case 'email':
          $email = get_field('slb_email',$post_id);
          $output .= $email;
          break;
      }
      echo $output;
    }

  // 3.2.2
  // hint: registers special custom admin title columns
  function slb_register_custom_admin_titles() {
      add_filter(
          'the_title',
          'slb_custom_admin_titles',
          99,
          2
      );
  }

  // 3.2.3
  // hint: handles custom admin title "title" column data for post types without titles
  function slb_custom_admin_titles( $title, $post_id ) {

      global $post;

      $output = $title;

      if( isset($post->post_type) ):
          switch( $post->post_type ) {
            case 'slb_subscriber':
  	           $fname = get_field('slb_fname', $post_id );
  	           $output = $fname;
  	           break;
          }
      endif;

      return $output;
  }
  /* 3.3 */
  function slb_list_column_headers($columns)
  {
    $columns = array(
      'cb' => '<input type="checkbox"/>',
      'title' => __('List Name'),
      'reward' =>__('Opt-In Reward'),
      'subscribers' =>__('Subscribers'),
      'shortcode' => __('Shortcode'),
    );
    return $columns;
  }

  /* 3.4 */
  function slb_list_column_data($column,$post_id)
  {
    $output = '';
    switch ($column) {
      case 'shortcode':
        $output .= '[slb_form id="'.$post_id.'"]';
        break;
      case 'subscribers' :
    			// get the count of current susbcribers
    		$subscriber_count = slb_get_list_subscriber_count( $post_id );
    			// get our unique export link
    		$export_href = slb_get_export_link( $post_id );
    			// append the subscriber count to our output
    		$output .= $subscriber_count;
    		    // if we have more than one subscriber, add our new export link to $output
    		if( $subscriber_count ) $output.= ' <a href="'. $export_href .'">Export</a>';
    		break;
      case 'reward':
        $reward = slb_get_list_reward( $post_id );
        if( $reward !== false ):

          $output .= '<a href="'. $reward['file']['url'] .'" download="'. $reward['title'] .'">'. $reward['title'] .'</a>';

        endif;
      break;
    }
    echo $output;

  }

  //3.5
  function slb_admin_menu()
  {
    $top_menu_item = 'slb_dashboard_admin_page';
    add_menu_page('','Subscribers List','manage_options','slb_dashboard_admin_page','slb_dashboard_admin_page','dashicons-groups');
    add_submenu_page($top_menu_item,'','Dashboard','manage_options',$top_menu_item,$top_menu_item);
    add_submenu_page($top_menu_item,'','Email List','manage_options','edit.php?post_type=slb_list');
    add_submenu_page($top_menu_item,'','Subscribers','manage_options','edit.php?post_type=slb_subscriber');
    add_submenu_page($top_menu_item,'','Import Subscribers','manage_options','slb_import_admin_page','slb_import_admin_page');
    add_submenu_page($top_menu_item,'','Plugin Options','manage_options','slb_options_admin_page','slb_options_admin_page');
    add_submenu_page($top_menu_item,'','Announcement','manage_options','slb_annoucement_admin','slb_annoucement_admin');
  }



 ?>
