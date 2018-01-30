<?php
/* 2. SHORTCODES */
  /* 2.1 - register semua custom shortcode */
      function slb_register_shortcodes(){
        add_shortcode('slb_form' , 'slb_form_shortcode');
        add_shortcode('slb_manage_subscriptions' , 'slb_manage_subscriptions_shortcode');
        add_shortcode('slb_confirm_subscription','slb_confirm_subscription_shortcode');
        add_shortcode('slb_download_reward','slb_download_reward_shortcode');
        add_shortcode('testing','test');
        add_shortcode('testing2','test2');
      }
  /* 2.2 - slb_form*/
      function slb_form_shortcode($args, $content=''){
        //setup output variables
        $list_id = 0;
        if(isset($args['id'])){
          $list_id = $args['id'];
        }
        $output = '
            <form id="slb_register_form"
            name="slb_form" class="slb-form" method="POST"
            action="'.get_site_url().'/wp-admin/admin-ajax.php?action=slb_save_subscription" method="POST">
            <input type="hidden" name="slb_list" value="'.$list_id.'"/>'. wp_nonce_field( 'slb-register-subscription_'.$list_id, '_wpnonce', true, false );
            $output.='
              <div class="form-group">
                <label>First Name</label>
                <input class="form-control" type="text" name="slb_fname" placeholder="First Name"></input>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input class="form-control" placeholder="name@example.com" type="email" name="slb_email"></input>
              </div>';
              if(strlen($content)){
                $output .= wpautop($content);
              }
              // get reward
              $reward = slb_get_list_reward( $list_id );

              // IF reward exists
              if( $reward !== false ):

                // include message about reward
                $output .='
                  <div class="slb-content slb-reward-message">
                    <p>Get a FREE DOWNLOAD of <strong>'. $reward['title'] .'</strong> when you join this list!</strong></p>
                  </div>
                ';

              endif;
              $output.='
              <br/>
              <div class="form-group">
                <label>&nbsp;</label>
                <input type="submit" name="slb_submit" class="btn btn-danger" value="Sign me up!"></input>
              </div>
          </form>
        ';
        return $output;
      }

      function test($args, $content=''){
        $a = slb_get_list_subscribers(0);
        $b = print_r($a,true);
        return $b;
      }

      function test2($args, $content=''){
        $subscribers = slb_get_list_subscribers( 223 );
        $i = 0;
        foreach ($subscribers as $key) {
          $a = slb_get_subscriber_data($subscribers[$i]);
          $c[] = $a['email'];
          $i++;
        }
        $b = print_r($c,true);
        return $b;
      }

      //2.3
      function slb_manage_subscriptions_shortcode( $args, $content="" ) {
        // setup our return string
        $output = '<div class="slb slb-manage-subscriptions">';
        try {
          // get the email address from the URL
          $email = ( isset( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';
          // get the subscriber id from the email address
          $subscriber_id = slb_get_subscriber_id( $email );
          // get subscriber data
          $subscriber_data = slb_get_subscriber_data( $subscriber_id );
          // IF subscriber exists
          if( $subscriber_id ){
            $output .= slb_get_manage_subscriptions_html( $subscriber_id );
          }
          else{
            $output .= esc_attr($_GET['email']).'<p>This link is invalid.</p>'.$subscriber_id;

          }

        } catch(Exception $e) {

          // php error
        }

        // close our html div tag
        $output .= '</div>';
        // return our html
        return $output;

      }
      // 2.4
      // hint: displays subscription opt-in confirmation text and link to manage sunscriptions
      // example: [slb_confirm_subscription]
      function slb_confirm_subscription_shortcode( $args, $content="" ) {
        $output = '<div class="slb">';
        $email = ( isset( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';
        $list_id = ( isset( $_GET['list'] ) ) ? esc_attr( $_GET['list'] ) : 0;
        $subscriber_id = slb_get_subscriber_id( $email );
        $subscriber = get_post( $subscriber_id );

        if( $subscriber_id && slb_validate_subscriber( $subscriber ) ):
          $list = get_post( $list_id );
          if( slb_validate_list( $list ) ):
            if( !slb_subscriber_has_subscription( $subscriber_id, $list_id) ):
              $optin_complete = slb_confirm_subscription( $subscriber_id, $list_id );
              if( !$optin_complete ):
                $output .= slb_get_message_html('Due to an unknown error, we were unable to confirm your subscription.', 'error');
                $output .= '</div>';
                return $output;
              endif;
            endif;
            $output .= slb_get_message_html( 'Your subscription to '. $list->post_title .' has now been confirmed.', 'confirmation' );
            $manage_subscriptions_link = slb_get_manage_subscriptions_link( $email );
            $output .= '<p><a href="'. $manage_subscriptions_link .'">Click here to manage your subscriptions.</a></p>';
          else:
            $output .= slb_get_message_html( 'This link is invalid.', 'error');
          endif;
        else:
          $output .= slb_get_message_html( 'This link is invalid. Invalid Subscriber '. $email .'.', 'error');
        endif;
        $output .= '</div>';
        return $output;
      }
      // 2.5
      // [slb_download_reward]
      // hint: returns a message if the download link has expired or is invalid
      function slb_download_reward_shortcode( $args, $content="" ) {

        $output = '';

        $uid = ($_GET['reward']) ? (string)$_GET['reward'] : 0;
        $reward = slb_get_reward( $uid );
        if( $reward !== false ):
          if( $reward['downloads'] >= slb_get_option( 'slb_download_limit') ):
            $output .= slb_get_message_html( 'This link has reached it\'s download limit.', 'warning');
          endif;
        else:
          $output .= slb_get_message_html( 'This link is invalid.', 'error');
        endif;

        return $output;

      }
