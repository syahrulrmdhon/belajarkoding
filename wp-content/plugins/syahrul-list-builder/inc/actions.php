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
        if( slb_subscriber_has_subscription( $subscriber_id, $list_id ) ){
          $subscriptions = slb_get_subscriptions( $subscriber_id );
          $needle = array_search( $list_id, $subscriptions );
          unset( $subscriptions[$needle] );
          update_field(slb_get_acf_key( 'slb_subscriptions'), $subscriptions, $subscriber_id);
          $subscription_saved = true;
        }
        $subscriptions = slb_get_subscriptions($subscriber_id);
        if (empty($subscriptions)) {
          wp_delete_post( $subscriber_id, true);
          $subscription_saved = true;
        }
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


    //5.12
    function slb_update_reward_link_downloads( $uid ) {

      global $wpdb;
      $return_value = false;
      try {

        $table_name = $wpdb->prefix . "slb_reward_links";

        // get current download count
        $current_count = $wpdb->get_var(
          $wpdb->prepare(
            "
              SELECT downloads
              FROM $table_name
              WHERE uid = %s
            ",
            $uid
          )
        );

        // set new count
        $new_count = (int)$current_count+1;

        // update downloads for this reward link entry
        $wpdb->query(
          $wpdb->prepare(
            "
              UPDATE $table_name
              SET downloads = $new_count
              WHERE uid = %s
            ",
            $uid
          )
        );

        $return_value = true;

      } catch( Exception $e ) {

        // php error

      }

      return $return_value;

    }

    // 5.13
    // hint: generates a .csv file of subscribers data
    // expects $_GET['list_id'] to be set in the URL
    function slb_download_subscribers_csv() {
    	$list_id = ( isset($_GET['list_id']) ) ? (int)$_GET['list_id'] : 0;
    	$csv = '';
    	$list = get_post( $list_id );
    	$subscribers = slb_get_list_subscribers( $list_id );
    	if( $subscribers !== false ):
    		$now = new DateTime();
    		$fn1 = 'slb-export-list_id-'. $list_id .'-date-'. $now->format('Ymd'). '.csv';

    		$fn2 = plugin_dir_path(__FILE__).'exports/'.$fn1;
    		$fp = fopen($fn2, 'w');

    		$subscriber_data = slb_get_subscriber_data( $subscribers[0] );

    		unset($subscriber_data['subscriptions']);
    		unset($subscriber_data['name']);

    		$csv_headers = array();
    		foreach( $subscriber_data as $key => $value ){
    			array_push($csv_headers, $key);
    		}

    		fputcsv($fp, $csv_headers);

    		foreach( $subscribers as &$subscriber_id ){

    			$subscriber_data = slb_get_subscriber_data( $subscriber_id );

    			unset($subscriber_data['subscriptions']);
    			unset($subscriber_data['name']);

    			fputcsv($fp, $subscriber_data);

    		}

    		$fp = fopen($fn2, 'r');
    		$fc = fread($fp, filesize($fn2) );
    		fclose($fp);

    		header("Content-type: application/csv");
    		header("Content-Disposition: attachment; filename=".$fn1);
    		echo($fc);
    		exit;
    	endif;
    	return false;
    }
    // 5.14
    // hint: this function retrieves a csv file from the server and parses the data into a php array
    // it then returns that array in a json formatted object
    // this function is a ajax post form handler
    // expects: $_POST['slb_import_file_id']
    function slb_parse_import_csv() {
    	$result = array(
    		'status'=>0,
    		'message'=>'Could not parse import CSV. ',
    		'error'=>'',
    		'data'=>array(),
    	);
    	try {
    		$attachment_id = (isset($_POST['slb_import_file_id'])) ? esc_attr( $_POST['slb_import_file_id'] ) : 0;

    		$filename = get_attached_file( $attachment_id );

    		if( $filename !== false):
    			$csv_data = slb_csv_to_array($filename,',');
    			if( $csv_data !== false && count($csv_data) ):

    				$result = array(
    					'status'=>1,
    					'message'=>'CSV Import data parsed successfully',
    					'error'=>'',
    					'data'=>$csv_data,
    				);

    			endif;
    		else:
    			$result['error']='The import file does not exist. ';

    		endif;

    	} catch( Exception $e ) {

    		// php error
    	}

    	// return the result as json
    	slb_return_json( $result );

    }

    // 5.15
    // hint: imports new subscribers from our import admin page
    // this function is a form handler and expect subscriber data in the $_POST scope
    function slb_import_subscribers() {
    	$result = array(
    		'status'=>0,
    		'message'=>'Could not import subscribers. ',
    		'error'=>'',
    		'errors'=>array(),
    	);

    	try {
    		$fname_column = (isset($_POST['slb_fname_column'])) ? (int)$_POST['slb_fname_column'] : 0;
    		$email_column = (isset($_POST['slb_email_column'])) ? (int)$_POST['slb_email_column'] : 0;

    		$list_id = (isset($_POST['slb_import_list_id'])) ? (int)$_POST['slb_import_list_id'] : 0;

    		$selected_rows = (isset($_POST['slb_import_rows'])) ? (array)$_POST['slb_import_rows'] : array();

    		$subscribers = array();

    		$added_count = 0;

    		foreach( $selected_rows as &$row_id ):

    			$subscriber_data = array(
    				'fname'=>(string)$_POST['s_'. $row_id .'_'. $fname_column],
    				'email'=>(string)$_POST['s_'. $row_id .'_'. $email_column],
    			);

    			if( !is_email($subscriber_data['email']) ):

    				$result['errors'][] = 'Invalid email detected: '. $subscriber_data['email'] .'. This subscriber was not added';

    			else:

    				$subscriber_id = slb_save_subscriber( $subscriber_data );

    				if( $subscriber_id ):

    					$subscription_added = slb_add_subscription( $subscriber_id, $list_id );

    					$added_count++;

    				endif;

    			endif;

    		endforeach;

    		if( $added_count == 0 ):

    			$result['error'] = 'No subscribers were imported. ';

    		else:

    			$result = array(
    				'status'=>1,
    				'message'=> $added_count .' Subscribers imported successfully. ',
    				'error'=>'',
    				'errors'=>array(),
    			);

    		endif;

    	} catch( Exception $e ) {


    	}

    	slb_return_json( $result );
    }

    // 5.16
    // hint: checks the current version of wordpress and displays a message in the plugin page if the version is untested
    function slb_check_wp_version() {

    	global $pagenow;


    	if ( $pagenow == 'plugins.php' && is_plugin_active('syahrul-list-builder/syahrul-list-builder.php') ):

    		// get the wp version
    		$wp_version = get_bloginfo('version');

    		// tested vesions
    		// these are the versions we've tested our plugin in
    		$tested_versions = array(
    			'4.9.0',
          '4.9.1',
          '4.9.2',
    		);

    		// IF the current wp version is not in our tested versions...
    		if( !in_array( $wp_version, $tested_versions ) ):

    			// get notice html
    			$notice = slb_get_admin_notice('Syahrul List Builder has not been tested in your version of WordPress. It still may work though...','error');

    			// echo the notice html
    			echo( $notice );

    		endif;

    	endif;

    }

    // 5.18
    // hint: runs functions for plugin deactivation
    /*function slb_uninstall_plugin() {

    	// remove our custom plugin tables
    	slb_remove_plugin_tables();
    	// remove custom post types posts and data
    	slb_remove_post_data();
    	// remove plugin options
    	slb_remove_options();

    }*/

    // 5.19
    // hint: removes our custom database tabels
    function slb_remove_plugin_tables() {

    	// get WP's wpdb class
    	global $wpdb;

    	// setup return variable
    	$tables_removed = false;

    	try {

    		// get our custom table name
    		$table_name = $wpdb->prefix . "slb_reward_links";

    		// delete table from database
    		$tables_removed = $wpdb->query("DROP TABLE IF EXISTS $table_name;");

    	} catch( Exception $e ) {


    	}

    	// return result
    	return $tables_removed;

    }

    // 5.20
    // hint: removes plugin related custom post type post data
    function slb_remove_post_data() {

    	// get WP's wpdb class
    	global $wpdb;

    	// setup return variable
    	$data_removed = false;

    	try {

    		// get our custom table name
    		$table_name = $wpdb->prefix . "posts";

    		// set up custom post types array
    		$custom_post_types = array(
    			'slb_subscriber',
    			'slb_list'
    		);

    		// remove data from the posts db table where post types are equal to our custom post types
    		$data_removed = $wpdb->query(
    			$wpdb->prepare(
    				"
    					DELETE FROM $table_name
    					WHERE post_type = %s OR post_type = %s
    				",
    				$custom_post_types[0],
    				$custom_post_types[1]
    			)
    		);

    		// get the table names for postmet and posts with the correct prefix
    		$table_name_1 = $wpdb->prefix . "postmeta";
    		$table_name_2 = $wpdb->prefix . "posts";

    		// delete orphaned meta data
    		$wpdb->query(
    			$wpdb->prepare(
    				"
    				DELETE pm
    				FROM $table_name_1 pm
    				LEFT JOIN $table_name_1 wp ON wp.ID = pm.post_id
    				WHERE wp.ID IS NULL
    				"
    			)
    		);



    	} catch( Exception $e ) {

    		// php error

    	}

    	// return result
    	return $data_removed;

    }

    // 5.21
    // hint: removes any custom options from the database
    function slb_remove_options() {

    	$options_removed = false;

    	try {

    		// get plugin option settings
    		$options = slb_get_options_settings();

    		// loop over all the settings
    		foreach( $options['settings'] as &$setting ):

    			// unregister the setting
    			unregister_setting( $options['group'], $setting );

    		endforeach;

    		// return true if everything worked
    		$options_removed = true;

    	} catch( Exception $e ) {

    		// php error

    	}
    	return $options_removed;

    }

    function slb_annouce_subscribers()
    {
      $result = array(
        'status' => 0,
        'message' => 'Failed!',
        'error' => '',
        'errors' => array()
       );
       try {
         $list_id = (int)$_POST['list_id'];
         $message = $_POST['message'];
         $subject = $_POST['subject'];
          if(!strlen($message)){
            $errors['message'] = 'Gotta write the message, Im Sorry';
          }
          if(!strlen($subject)){
            $errors['subject'] = 'Subject is Necessary';
          }
          if (count($errors)) {
            $result['error'] = 'Some fields are still required';
            $result['errors'] = $errors;
          }
          $email_sent = slb_send_announcement( $list_id, $subject, $message);
          if( !$email_sent ){
              $result['error'] = 'Unable to send email. ';
          }else{
              $result['status']=1;
              $result['message']='Success!';
              unset( $result['error'] );
          }
       } catch (Exception $e) {
         $result['error'] = 'Caught Exception: '.$e->getMessage();
       }
       slb_return_json($result);
    }

    function slb_send_announcement($list_id, $subject, $message)
    {
      $subscribers = slb_get_list_subscribers($list_id);
      $wp_mail_headers = array('Content-Type: text/html; charset=UTF-8');
      $sendmail = false;
      $i = 0;
      foreach ($subscribers as $key) {
        $a = slb_get_subscriber_data($subscribers[$i]);
        $c[] = $a['email'];
        $i++;
      }
      $sendmail = wp_mail($c , $subject, $message, $wp_mail_headers);

      return $sendmail;
    }
