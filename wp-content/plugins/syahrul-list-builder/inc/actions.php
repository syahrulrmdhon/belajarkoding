<?php
    /* 5. ACTIONS */
    function slb_save_subscription()
    {
      $result = array(
        'status' => 0,
        'message' => 'Subscribers tidak tersimpan ',
        'error' => '',
        'errors' => array()
       );
       try {
         $list_id = (int)$_POST['slb_list'];
         if( check_ajax_referer( 'slb-register-subscription_'.$list_id ) ){
         $subscriber_data = array(
           'fname' => esc_attr($_POST['slb_fname']),
           'email' => esc_attr($_POST['slb_email']),
          );
          if(!strlen($subscriber_data['fname'])){
            $errors['fname'] = 'Nama Depan diperlukan';
          }
          if(!strlen($subscriber_data['email'])){
            $errors['email'] = 'Email diperlukan';
          }
          if(!strlen($subscriber_data['email']) && !is_email($subscriber_data['email'])){
            $errors['email'] = 'Email tidak valid';
          }
          if (count($errors)) {
            $result['error'] = 'Some fields are still required';
            $result['errors'] = $errors;
          }
          else {
          $subscriber_id = slb_save_subscriber($subscriber_data);
          if ($subscriber_id) {
            if (slb_subscriber_has_subscription($subscriber_id,$list_id)) {
              $list = get_post($list_id);
              $result['message'] .= esc_attr($subscriber_data['email'].' sudah subscribe '.$list->post_tittle.'.');
            }
            else {
              //$subscription_saved = slb_add_subscription($subscriber_id,$list_id);
              //if ($subscription_saved) {
                // send new subscriber a confirmation email, returns true if we were successful
                  $email_sent = slb_send_subscriber_email( $subscriber_id, 'new_subscription', $list_id);
                  // IF email was sent
                  if( !$email_sent ){
                    // remove subscription
                    slb_remove_subscription( $subscriber_id, $list_id);
                    // email could not be sent
                    $result['error'] = 'Unable to send email. ';
                  }else{
                    // email sent and subscription saved!
                    $result['status']=1;
                    $result['message']='Success! A confirmation email has been sent to '. $subscriber_data['email'];
                    // clean up: remove our empty error
                    unset( $result['error'] );
                    }
              /*}
              else {
                $result['error'] = 'Ndak bisa';
              }*/
            }
          }
        }
      }
       } catch (Exception $e) {
         $result['error'] = 'Caught Exception: '.$e->getMessage();
       }
       slb_return_json($result);
    }
    /* 5.2 create a new subsribers or edit the existing one */
      function slb_save_subscriber($subscriber_data)
      {
        $subscriber_id =0;
        try
        {
          $subscriber_id = slb_get_subscriber_id($subscriber_data['email']);
          if(!$subscriber_id){
            $subscriber_id = wp_insert_post(
              array(
                'post_type' => 'slb_subscriber',
                'post_tittle' => $subscriber_data['fname'],
                'post_status' => 'publish',
              ),true
            );
          }
          update_field(slb_get_acf_key('slb_fname'),$subscriber_data['fname'],$subscriber_id);
          update_field(slb_get_acf_key('slb_email'),$subscriber_data['email'],$subscriber_id);
        }
        catch(Exception $e){
        }
        wp_reset_query();
        return $subscriber_id;
      }
      /* 5.3 */
      function slb_add_subscription($subscriber_id,$list_id)
      {
        $subscription_saved = false;
        if(!slb_subscriber_has_subscription($subscriber_id,$list_id)){
          $subscriptions = slb_get_subscriptions($subscriber_id);
          $subscriptions[] = $list_id;
          update_field(slb_get_acf_key('slb_subscriptions'),$subscriptions,$subscriber_id);
          $subscription_saved = true;
        }
        return $subscription_saved;
      }
      // 5.4
      // hint: removes one or more subscriptions from a subscriber and notifies them via email
      // this function is a ajax form handler...
      // expects form post data: $_POST['subscriber_id'] and $_POST['list_id']
      function slb_unsubscribe() {
      	// setup default result data
      	$result = array(
      		'status' => 0,
      		'message' => 'Subscriptions were NOT updated. ',
      		'error' => '',
      		'errors' => array(),
      	);
      	$subscriber_id = ( isset($_POST['subscriber_id']) ) ? esc_attr( (int)$_POST['subscriber_id'] ) : 0;
      	$list_ids = ( isset($_POST['list_ids']) ) ? $_POST['list_ids'] : 0;
      	try {
      		// if there are lists to remove
      		if( is_array($list_ids) ):
      			// loop over lists to remove
      			foreach( $list_ids as &$list_id ):
      				// remove this subscription
      				slb_remove_subscription( $subscriber_id, $list_id );
      			endforeach;
      		endif;
      		// setup success status and message
      		$result['status']=1;
      		$result['message']='Subscriptions updated. ';
      		// get the updated list of subscriptions as html
      		$result['html']= slb_get_manage_subscriptions_html( $subscriber_id );
      	} catch( Exception $e ) {
      		// php error
      	}
      	// return result as json
      	slb_return_json( $result );
      }
      // 5.5
      // hint: removes a single subscription from a subscriber
      function slb_remove_subscription( $subscriber_id, $list_id ) {
        $subscription_saved = false;
        if( slb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
          $subscriptions = slb_get_subscriptions( $subscriber_id );
          $needle = array_search( $list_id, $subscriptions );
          unset( $subscriptions[$needle] );
          update_field(slb_get_acf_key( 'slb_subscriptions'), $subscriptions, $subscriber_id);
          $subscription_saved = true;
        endif;
        return $subscription_saved;
      }
      //5.6
      function slb_send_subscriber_email( $subscriber_id, $email_template_name, $list_id ) {
          $email_sent = false;
          $email_template_object = slb_get_email_template( $subscriber_id, $email_template_name, $list_id );
          if( !empty( $email_template_object ) ){
            $subscriber_data = slb_get_subscriber_data( $subscriber_id );
            $wp_mail_headers = array('Content-Type: text/html; charset=UTF-8');
            $email_sent = wp_mail( array( $subscriber_data['email'] ) , $email_template_object['subject'], $email_template_object['body'], $wp_mail_headers );
          }
          return $email_sent;
        }
    // 5.7
    // hint: adds subcription to database and emails subscriber confirmation email
    function slb_confirm_subscription( $subscriber_id, $list_id ) {
      // setup return variable
      $optin_complete = false;
      // add new subscription
      $subscription_saved = slb_add_subscription( $subscriber_id, $list_id );
      // IF subscription was saved
      if( $subscription_saved ):
        $email_sent = slb_send_subscriber_email( $subscriber_id, 'subscription_confirmed', $list_id );
        // return true
        $optin_complete = true;
      endif;
      // return result
      return $optin_complete;
    }
    // 5.8
    // hint: creates custom tables for our plugin
    function slb_create_plugin_tables() {
      global $wpdb;
      // setup return value
      $return_value = false;
      try {
        $table_name = $wpdb->prefix . "slb_reward_links";
        $charset_collate = $wpdb->get_charset_collate();
        // sql for our table creation
        $sql = "CREATE TABLE $table_name (
          id mediumint(11) NOT NULL AUTO_INCREMENT,
          uid varchar(128) NOT NULL,
          subscriber_id mediumint(11) NOT NULL,
          list_id mediumint(11) NOT NULL,
          attachment_id mediumint(11) NOT NULL,
          downloads mediumint(11) DEFAULT 0 NOT NULL ,
          UNIQUE KEY id (id)
          ) $charset_collate;";
        // make sure we include wordpress functions for dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // dbDelta will create a new table if none exists or update an existing one
        dbDelta($sql);
        // return true
        $return_value = true;
      } catch( Exception $e ) {
        // php error
      }
      // return result
      return $return_value;
    }
    // 5.9
    // hint: runs on plugin activation
    function slb_activate_plugin() {
      // setup custom database tables
      slb_create_plugin_tables();
    }
    // 5.10
    // hint: adds new reward links to the database
    function slb_add_reward_link( $uid, $subscriber_id, $list_id, $attachment_id ) {
      global $wpdb;
      // setup our return value
      $return_value = false;
      try {
        $table_name = $wpdb->prefix . "slb_reward_links";
        $wpdb->insert(
          $table_name,
          array(
            'uid' => $uid,
            'subscriber_id' => $subscriber_id,
            'list_id' => $list_id,
            'attachment_id' => $attachment_id,
          ),
          array(
            '%s',
            '%d',
            '%d',
            '%d',
          )
        );
        // return true
        $return_value = true;
      } catch( Exception $e ) {
        // php error
      }
      // return result
      return $return_value;
    }
    // 5.11
    // hint: triggers a download of the reward file
    function slb_trigger_reward_download() {
    	global $post;
    	if( have_posts() == slb_get_option( 'slb_reward_page_id') && isset($_GET['reward']) ):
    		$uid = ($_GET['reward']) ? (string)$_GET['reward'] : 0;
    		$reward = slb_get_reward( $uid );
    		if( $reward !== false && $reward['downloads'] < slb_get_option( 'slb_download_limit') ):
    			slb_update_reward_link_downloads( $uid );
    			$mimetype = $reward['file']['mime_type'];
    			$mimetype_array = explode('/',$mimetype);
    			$filetype = $mimetype_array[1];
    			// setup file headers
    			header("Content-type: ".$mimetype,true,200);
    		    header("Content-Disposition: attachment; filename=".$reward['title'] .'.'. $filetype);
    		    header("Pragma: no-cache");
    		    header("Expires: 0");
    		    readfile($reward['file']['url']);
    		    exit();
    	    endif;
    	endif;
    }
