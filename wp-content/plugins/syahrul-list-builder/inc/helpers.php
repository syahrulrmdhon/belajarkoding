<?php
/* 6. HELPERS */
/* 6.1 */
function slb_subscriber_has_subscription($subscriber_id,$list_id)
{
  $has_subscription = false;
  $subscriber = get_post($subscriber_id);
  $subscriptions = slb_get_subscriptions($subscriber_id);

  if(in_array($list_id,$subscriptions)){
    $has_subscription = true;
  }
  else{
    $has_subscription = false;
  }
  return $has_subscription;
}
// 6.2
// hint: retrieves a subscriber_id from an email address
function slb_get_subscriber_id( $email ) {

$subscriber_id = 0;

try {

  // check if subscriber already exists
  $subscriber_query = new WP_Query(
    array(
      'post_type'		=>	'slb_subscriber',
      'posts_per_page' => 1,
      'meta_key' => 'slb_email',
      'meta_query' => array(
          array(
              'key' => 'slb_email',
              'value' => $email,  // or whatever it is you're using here
              'compare' => '=',
          ),
      ),
    )
  );

  // IF the subscriber exists...
    if( $subscriber_query->have_posts() ):

      // get the subscriber_id
      $subscriber_query->the_post();
      $subscriber_id = get_the_ID();

    endif;

  } catch( Exception $e ) {

    // a php error occurred

  }

  // reset the Wordpress post object
  wp_reset_query();

  return (int)$subscriber_id;

}
/* 6.3 */
function slb_get_subscriptions( $subscriber_id ) {

  $subscriptions = array();

  // get subscriptions (returns array of list objects)
  $lists = get_field( slb_get_acf_key('slb_subscriptions'), $subscriber_id );

  // IF $lists returns something
  if( $lists ):

    // IF $lists is an array and there is one or more items
    if( is_array($lists) && count($lists) ):
      // build subscriptions: array of list id's
      foreach( $lists as &$list):
        $subscriptions[]= (int)$list->ID;
      endforeach;
    elseif( is_numeric($lists) ):
      // single result returned
      $subscriptions[]= $lists;
    endif;

  endif;

  return (array)$subscriptions;

}
/* 6.4 */
function slb_return_json($php_array)
{
  $json_result = json_encode($php_array);
  die($json_result);
  exit;
}
/* 6.5 */
function slb_get_acf_key($field_name)
{
  $field_key = $field_name;
  switch ($field_name) {
    case 'slb_fname':
      $field_key = 'field_5a5c886a3ee95';
      break;
    case 'slb_email':
      $field_key = 'field_5a5c887d3ee96';
      break;
    case 'slb_subscriptions':
      $field_key = 'field_5a5c88b83ee97';
      break;
    case 'slb_enable_reward':
      $field_key = 'field_5a65789e6e4ae';
      break;
    case 'slb_reward_title':
      $field_key = 'field_5a6578e96e4af';
      break;
    case 'slb_reward_file':
      $field_key = 'field_5a6579236e4b0';
      break;
  }
  return $field_key;
}
/* 6.6 */
function slb_get_subscriber_data($subscriber_id)
{
  $subscriber_data = array();
  $subscriber = get_post($subscriber_id);

  if(isset($subscriber->post_type) && $subscriber->post_type == 'slb_subscriber'){
    $subscriber_data = array(
      'name' =>  get_field(slb_get_acf_key('slb_fname'),$subscriber_id),
      'fname' => get_field(slb_get_acf_key('slb_fname'),$subscriber_id),
      'email' => get_field(slb_get_acf_key('slb_email'),$subscriber_id),
      'subscriptions' => slb_get_subscriptions($subscriber_id)
    );
  }
  return $subscriber_data;
}
// 6.7
// hint: returns html for a page selector
function slb_get_page_select( $input_name="slb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="" ) {

  $pages = get_pages(
    array(
      'sort_order' => 'asc',
      'sort_column' => 'post_title',
      'post_type' => 'page',
      'parent' => $parent,
      'status'=>array('draft','publish'),
    )
  );
  $select = '<select name="'. $input_name .'" ';
  if( strlen($input_id) ){
    $select .= 'id="'. $input_id .'" ';
  }
  $select .= '><option value="">- Select One -</option>';
  foreach ( $pages as &$page ){
    $value = $page->ID;
    switch( $value_field ) {
      case 'slug':
        $value = $page->post_name;
        break;
      case 'url':
        $value = get_page_link( $page->ID );
        break;
      default:
        $value = $page->ID;
    }
    $selected = '';
    if( $selected_value == $value ){
      $selected = ' selected="selected" ';
    }
    $option = '<option value="' . $value . '" '. $selected .'>';
    $option .= $page->post_title;
    $option .= '</option>';
    $select .= $option;
  }
  $select .= '</select>';
  return $select;
}
//6.8
function slb_get_default_options() {
   $defaults = array();
    try {
      $front_page_id = get_option('page_on_front');
      $default_email_footer = '
        <p>
          Sincerely, <br /><br />
          The '. get_bloginfo('name') .' Team<br />
          <a href="'. get_bloginfo('url') .'">'. get_bloginfo('url') .'</a>
        </p>
      ';
      $defaults = array(
        'slb_manage_subscription_page_id'=>$front_page_id,
        'slb_confirmation_page_id'=>$front_page_id,
        'slb_reward_page_id'=>$front_page_id,
        'slb_default_email_footer'=>$default_email_footer,
        'slb_download_limit'=>3,
      );

     }
      catch( Exception $e) {

     }
      return $defaults;
    }

  // 6.9
  function slb_get_option( $option_name ) {
    $option_value = '';
    try {
      $defaults = slb_get_default_options();
      switch( $option_name ) {
        case 'slb_manage_subscription_page_id':
          $option_value = (get_option('slb_manage_subscription_page_id')) ? get_option('slb_manage_subscription_page_id') : $defaults['slb_manage_subscription_page_id'];
          break;
        case 'slb_confirmation_page_id':
          $option_value = (get_option('slb_confirmation_page_id')) ? get_option('slb_confirmation_page_id') : $defaults['slb_confirmation_page_id'];
          break;
        case 'slb_reward_page_id':
          $option_value = (get_option('slb_reward_page_id')) ? get_option('slb_reward_page_id') : $defaults['slb_reward_page_id'];
          break;
        case 'slb_default_email_footer':
          $option_value = (get_option('slb_default_email_footer')) ? get_option('slb_default_email_footer') : $defaults['slb_default_email_footer'];
          break;
        case 'slb_download_limit':
          $option_value = (get_option('slb_download_limit')) ? (int)get_option('slb_download_limit') : $defaults['slb_download_limit'];
          break;
      }

     } catch( Exception $e) {

      }
    return $option_value;
  }

  // 6.10
  // hint: get's the current options and returns values in associative array
  function slb_get_current_options() {
    $current_options = array();
    try {
      $current_options = array(
        'slb_manage_subscription_page_id' => slb_get_option('slb_manage_subscription_page_id'),
        'slb_confirmation_page_id' => slb_get_option('slb_confirmation_page_id'),
        'slb_reward_page_id' => slb_get_option('slb_reward_page_id'),
        'slb_default_email_footer' => slb_get_option('slb_default_email_footer'),
        'slb_download_limit' => slb_get_option('slb_download_limit'),
      );
    } catch( Exception $e ) {

    }

    return $current_options;

  }
  // 6.11
  // hint: generates an html form for managing subscriptions
  function slb_get_manage_subscriptions_html( $subscriber_id ) {
    $output = '';
    try {
      $lists = slb_get_subscriptions( $subscriber_id );
      $subscriber_data = slb_get_subscriber_data( $subscriber_id );
      $title = $subscriber_data['fname'] .'\'s Subscriptions';

      $output = '
        <form id="slb_manage_subscriptions_form" class="slb-form" method="post"
        action="'.get_site_url().'/wp-admin/admin-ajax.php?action=slb_unsubscribe">
          <input type="hidden" name="subscriber_id" value="'. $subscriber_id .'">
          <h3 class="slb-title">'. $title .'</h3>';
          if( !count($lists) ):
            $output .='<p>There are no active subscriptions.</p>';
          else:
            $output .= '<table>
              <tbody>';
              foreach( $lists as &$list_id ):
                $list_object = get_post( $list_id );
                $output .= '<tr>
                  <td>'.
                    $list_object->post_title
                  .'</td>
                  <td>
                    <label>
                      <input
                        type="checkbox" name="list_ids[]"
                        value="'. $list_object->ID .'"
                      /> UNSUBSCRIBE
                    </label>
                  </td>
                </tr>';
              endforeach;
              $output .='</tbody>
            </table>
            <p><input type="submit" value="Save Changes" /></p>';
          endif;
        $output .='
          </form>
        ';
    } catch( Exception $e ) {

    }
    return $output;
  }
  // 6.12
  // hint: returns an array of email template data IF the template exists
  function slb_get_email_template( $subscriber_id, $email_template_name, $list_id ) {

    $template_data = array();

    $email_templates = array();
    $list = get_post( $list_id );

    // get subscriber object
    $subscriber = get_post( $subscriber_id );

    if( !slb_validate_list( $list ) || !slb_validate_subscriber( $subscriber ) ){

    }else{

      $subscriber_data = slb_get_subscriber_data( $subscriber_id );

      $manage_subscriptions_link = slb_get_manage_subscriptions_link( $subscriber_data['email'], $list_id );

      $default_email_header = '
        <b>
          Hello '. $subscriber_data['fname'] .',
        </b>
      ';

      $default_email_footer = slb_get_option('slb_default_email_footer');

      $unsubscribe_text = '
        <br /><br /><br /><br />
        <hr />
        <p><a href="'. $manage_subscriptions_link .'">Click here to unsubscribe</a> from this or any other email list.</p>';

      $reward = slb_get_list_reward( $list_id );

      $reward_text = '';
      if( $reward !== false ){
        switch( $email_template_name ) {
          case 'new_subscription':
            $reward_text = '<br/><p>After confirming your subscription, we will send you a link for a FREE DOWNLOAD of '. $reward['title'] .'</p>';
            break;
          case 'subscription_confirmed':
            $download_limit = slb_get_option('slb_download_limit');
            $download_link = slb_get_reward_link( $subscriber_id, $list_id );
            $reward_text = '<br/><p>Here is your <a href="'. $download_link .'">UNIQUE DOWNLOAD LINK</a> for '. $reward['title'] .'. This link will expire after '. $download_limit .' downloads</p>';
            break;

        }

      }
        $optin_link = slb_get_optin_link( $subscriber_data['email'], $list_id );

        $email_templates['new_subscription'] = array(
          'subject' => 'Thank you for subscribing to '. $list->post_title .'! Please confirm your subscription.',
          'body' => '
            '. $default_email_header .'
            <p>Thank you for subscribing to '. $list->post_title .'!</p>
            <p>Please <a href="'. $optin_link .'">click here to confirm your subscription.</a></p>
            '. $reward_text . $default_email_footer . $unsubscribe_text,
        );

        $email_templates['subscription_confirmed'] = array(
          'subject' => 'You are now subscribed to '. $list->post_title .'!',
          'body' => '
            '. $default_email_header .'
            <p>Thank you for confirming your subscription. You are now subscribed to '. $list->post_title .'!</p>
            '. $reward_text . $default_email_footer . $unsubscribe_text,
        );


    }

    if( isset( $email_templates[ $email_template_name ] ) ):

      $template_data = $email_templates[ $email_template_name ];

    endif;

    return $template_data;

  }

  // 6.13
  // hint: validates whether the post object exists and that it's a validate post_type
  function slb_validate_list( $list_object ) {
    $list_valid = false;
    if( isset($list_object->post_type) && $list_object->post_type == 'slb_list' ){
      $list_valid = true;
    }
    return $list_valid;
  }

  // 6.14
  // hint: validates whether the post object exists and that it's a validate post_type
  function slb_validate_subscriber( $subscriber_object ) {
    $subscriber_valid = false;
    if( isset($subscriber_object->post_type) && $subscriber_object->post_type == 'slb_subscriber' ):
      $subscriber_valid = true;
    endif;
    return $subscriber_valid;
  }

  // 6.15
  // hint: returns a unique link for managing a particular users subscriptions
  function slb_get_manage_subscriptions_link( $email, $list_id=0 ) {
    $link_href = '';
    try {
      $page = get_post( slb_get_option('slb_manage_subscription_page_id') );
      $slug = $page->post_name;
      $permalink = get_permalink($page);
      // get character to start querystring
      $startquery = slb_get_querystring_start( $permalink );
      $link_href = $permalink . $startquery .'email='. urlencode($email) .'&list='. $list_id;

    } catch( Exception $e ) {
      //$link_href = $e->getMessage();
    }
    return esc_url($link_href);

  }

  // 6.16
  // hint: returns the appropriate character for the begining of a querystring
  function slb_get_querystring_start( $permalink ) {
    // setup our default return variable
    $querystring_start = '&';
    // IF ? is not found in the permalink
    if( strpos($permalink, '?') === false ):
      $querystring_start = '?';
    endif;

    return $querystring_start;

  }
  //6.17
  function slb_get_optin_link( $email, $list_id=0 ) {
    $link_href = '';
    try {
      $page = get_post( slb_get_option('slb_confirmation_page_id') );
      $slug = $page->post_name;
      $permalink = get_permalink($page);
      // get character to start querystring
      $startquery = slb_get_querystring_start( $permalink );
      $link_href = $permalink . $startquery .'email='. urlencode($email) .'&list='. $list_id;
    } catch( Exception $e ) {
      //$link_href = $e->getMessage();
    }
    return esc_url($link_href);
  }

  // 6.18
  // hint: returns html for messags
  function slb_get_message_html( $message, $message_type ) {
    $output = '';
    try {
      $message_class = 'confirmation';

      switch( $message_type ) {
        case 'warning':
          $message_class = 'slb-warning';
          break;
        case 'error':
          $message_class = 'slb-error';
          break;
        default:
          $message_class = 'slb-confirmation';
          break;
      }

      $output .= '
        <div class="slb-message-container">
          <div class="slb-message '. $message_class .'">
            <p>'. $message .'</p>
          </div>
        </div>
      ';

    } catch( Exception $e ) {

    }

    return $output;

  }
  //6.19
  function slb_get_list_reward( $list_id ) {
    // setup return data
    $reward_data = false;
    // get enable_reward value
    $enable_reward = ( get_field( slb_get_acf_key('slb_enable_reward'), $list_id) ) ? true : false;
    // IF reward is enabled for this list
    if( $enable_reward ):
      // get reward file
      $reward_file = ( get_field( slb_get_acf_key('slb_reward_file'), $list_id) ) ? get_field( slb_get_acf_key('slb_reward_file'), $list_id) : false;
      // get reward title
      $reward_title = ( get_field(slb_get_acf_key('slb_reward_title'), $list_id) ) ? get_field(slb_get_acf_key('slb_reward_title'), $list_id) : 'Reward';
      // IF reward_file is a valid array
      if( is_array($reward_file) ):
        // setup return data
        $reward_data = array(
          'file' => $reward_file,
          'title' => $reward_title,
        );
      endif;
    endif;
    // return $reward_data
    return $reward_data;

  }

  //6.20
  function slb_get_reward_link( $subscriber_id, $list_id ) {

    $link_href = '';

    try {

      $page = get_post( slb_get_option('slb_reward_page_id') );
      $slug = $page->post_name;
      $permalink = get_permalink($page);

      // generate unique uid for reward link
      $uid = slb_generate_reward_uid( $subscriber_id, $list_id );

      // get list reward
      $reward = slb_get_list_reward( $list_id );

      // IF an attachment id was returned
      if( $uid && $reward !== false ):

        // add reward link to database
        $link_added = slb_add_reward_link( $uid, $subscriber_id, $list_id, $reward['file']['id'] );

        // IF link was added successfully
        if( $link_added === true ):

          // get character to start querystring
          $startquery = slb_get_querystring_start( $permalink );

          // build reward link
          $link_href = $permalink . $startquery .'reward='. urlencode($uid);

        endif;

      endif;

    } catch( Exception $e ) {

      //$link_href = $e->getMessage();

    }

    // return reward link
    return esc_url($link_href);

  }

  // 6.21
  // hint: generates a unique
  function slb_generate_reward_uid( $subscriber_id, $list_id ) {

    // setup our return variable
    $uid = '';

    // get subscriber post object
    $subscriber = get_post( $subscriber_id );

    // get list post object
    $list = get_post( $list_id );
    if( slb_validate_subscriber( $subscriber ) && slb_validate_list( $list ) ):
        $reward = slb_get_list_reward( $list_id );
        if( $reward !== false ):
          $uid = uniqid( 'slb', true );
        endif;
    endif;

    return $uid;

  }

  // 6.22
  // hint: returns false if list has no reward or returns the object containing file and title if it does
  function slb_get_reward( $uid ) {

    global $wpdb;
    // setup return data
    $reward_data = false;
    // reward links download table name
    $table_name = $wpdb->prefix . "slb_reward_links";
    $list_id = $wpdb->get_var(
      $wpdb->prepare(
        "
          SELECT list_id
          FROM $table_name
          WHERE uid = %s
        ",
        $uid
      )
    );
    // get downloads from reward link
    $downloads = $wpdb->get_var(
      $wpdb->prepare(
        "
          SELECT downloads
          FROM $table_name
          WHERE uid = %s
        ",
        $uid
      )
    );
    $reward = slb_get_list_reward( $list_id );
    if( $reward !== false ):
      $reward_data = $reward;
      $reward_data['downloads']=$downloads;
    endif;
    // return $reward_data
    return $reward_data;
  }

   //6.23
  // hint: returns an array of subscriber_id's
  function slb_get_list_subscribers( $list_id=0 ) {

  	// setup return variable
  	$subscribers = false;

  	// get list object
  	$list = get_post( $list_id );

  	if( slb_validate_list( $list ) ):
  		$subscribers_query = new WP_Query(
  			array(
  				'post_type' => 'slb_subscriber',
  				'published' => true,
  				'posts_per_page' => -1,
  				'orderby'=>'post_date',
  				'order'=>'DESC',
  				'status'=>'publish',
  				'meta_query' => array(
  					array(
  						'key' => 'slb_subscriptions',
  						'value' => ':"'.$list->ID.'"',
  						'compare' => 'LIKE'
  					)
  				)
  			)
  		);
  	elseif( $list_id === 0 ):
  		$subscribers_query = new WP_Query(
  			array(
  				'post_type' => 'slb_subscriber',
  				'published' => true,
  				'posts_per_page' => -1,
  				'orderby'=>'post_date',
  				'order'=>'DESC',
  			)
  		);
  	endif;
  	if( isset($subscribers_query) && $subscribers_query->have_posts() ):
  		$subscribers = array();
  		while ($subscribers_query->have_posts() ) :
  			$subscribers_query->the_post();
  			$post_id = get_the_ID();
  			array_push( $subscribers, $post_id);
  		endwhile;
  	endif;
  	wp_reset_query();
  	wp_reset_postdata();

  	// return result
  	return $subscribers;
  }
  //6.24
  // hint: returns the amount of subscribers in this list
  function slb_get_list_subscriber_count( $list_id = 0 ) {
  	$count = 0;
  	$subscribers = slb_get_list_subscribers( $list_id );
  	if( $subscribers !== false ):
  		$count = count($subscribers);
  	endif;
  	return $count;

  }

  // 6.25
  // hint: returns a unique link for downloading a subscribers csv
  function slb_get_export_link( $list_id=0 ) {
  	$link_href = 'admin-ajax.php?action=slb_download_subscribers_csv&list_id='. $list_id;
  	return esc_url($link_href);
  }

  // 6.26
  // hint: this function reads a csv file and converts the contents into a php array
  function slb_csv_to_array($filename='', $delimiter=',')
  {
  	ini_set('auto_detect_line_endings', true);
      if(!file_exists($filename) || !is_readable($filename))
          return FALSE;
      $return_data = array();
      if (($handle = fopen($filename, "r")) !== FALSE) {
  	  	$row = 0;
  	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
  	        $num = count($data);
  	        $row++;
  	        $row_data = array();
  	        for ($c=0; $c < $num; $c++) {
  				if( $row == 1):
  					$header[] = $data[$c];
  				else:
  					$return_data[$row-2][$header[$c]] = $data[$c];
  				endif;
  	        }
  	    }
  	    fclose($handle);
  	}
      return $return_data;
  }

  // 6.27
  // hint: returns html formatted for WP admin notices
  function slb_get_admin_notice( $message, $class ) {
  	$output = '';
  	try {
  		$output = '
  		 <div class="'. $class .'">
  		    <p>'. $message .'</p>
  		</div>
  		';
  	} catch( Exception $e ) {

  	}
  	return $output;

  }

  // 6.28
  // hint: get's an array of plugin option data (group and settings) so as to save it all in one place
  function slb_get_options_settings() {
  	$settings = array(
  		'group'=>'slb_plugin_options',
  		'settings'=>array(
  			'slb_manage_subscription_page_id',
  			'slb_confirmation_page_id',
  			'slb_reward_page_id',
  			'slb_default_email_footer',
  			'slb_download_limit',
  		),
  	);
  	return $settings;

  }





 ?>
