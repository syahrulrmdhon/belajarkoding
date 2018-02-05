<?php

      /* 8. ADMIN PAGES */
      //8.1
      function slb_dashboard_admin_page()
      {
        $export_href = slb_get_export_link();
        $output = '
          <div class="wrap">
            <h2>Syahrul List Builder</h2>
            <p>Just another plugin...</p>
            <p><a href="'. $export_href .'"  class="button button-primary">Export All Subscriber Data</a></p>
          </div>
        ';
        echo $output;
      }

      //8.2
      function slb_import_admin_page() {
        // enque special scripts required for our file import field
        wp_enqueue_media();
        echo('
        <div class="wrap" id="import_subscribers">
            <h2>Import Subscribers</h2>
            <form id="import_form_1">
              <table class="form-table">
                <tbody>
                  <tr>
                    <th scope="row"><label for="slb_import_file">Import CSV</label></th>
                    <td>
                      <div class="wp-uploader">
                          <input type="text" name="slb_import_file_url" class="file-url regular-text" accept="csv">
                          <input type="hidden" name="slb_import_file_id" class="file-id" value="0" />
                          <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload">
                      </div>
                      <p class="description" id="slb_import_file-description">Expects a CSV file containing a   "Name" (First, Last or Full) and "Email Address".</p>
							        </td>
                  </tr>
                </tbody>
              </table>
            </form>
            <form id="import_form_2" method="post" action="'.get_site_url().'/wp-admin/admin-ajax.php?action=slb_import_subscribers">
              <table class="form-table">
                <tbody class="slb-dynamic-content">
                </tbody>
                <tbody class="form-table show-only-on-valid" style="display: none">
                  <tr>
                    <th scope="row"><label>Import To List</label></th>
                    <td>
                      <select name="slb_import_list_id">');
                          // get all our email lists
                          $lists = get_posts(
                            array(
                              'post_type'			=>'slb_list',
                              'status'			=>'publish',
                              'posts_per_page'   	=> -1,
                              'orderby'         	=> 'post_title',
                              'order'            	=> 'ASC',
                            )
                          );
                          // loop over each email list
                          foreach( $lists as &$list ):

                            // create the select option for that list
                            $option = '
                              <option value="'. $list->ID .'">
                                '. $list->post_title .'
                              </option>';

                            // echo the new option
                            echo $option;


                          endforeach;

                      echo('</select>
                      <p class="description"></p>
                    </td>
                  </tr>

                </tbody>

              </table>

              <p class="submit show-only-on-valid" style="display:none"><input type="submit" name="submit" id="submit" class="button button-primary" value="Import"></p>

            </form>

        </div>

        ');

      }


      // 8.3
      // hint: plugin options admin page
      function slb_options_admin_page() {

      // get the default values for our options
      $options = slb_get_current_options();

      echo('<div class="wrap">

        <h2>Syahrul List Builder Options</h2>

        <form action="options.php" method="post">');

          // outputs a unique nounce for our plugin options
          settings_fields('slb_plugin_options');
          // generates a unique hidden field with our form handling url
          @do_settings_sections('slb_plugin_options');

          echo('<table class="form-table">

            <tbody>

              <tr>
                <th scope="row"><label for="slb_manage_subscription_page_id">Manage Subscriptions Page</label></th>
                <td>
                  '. slb_get_page_select( 'slb_manage_subscription_page_id', 'slb_manage_subscription_page_id', 0, 'id', $options['slb_manage_subscription_page_id'] ) .'
                  <p class="description" id="slb_manage_subscription_page_id-description">This is the page where Syahrul List Builder will send subscribers to manage their subscriptions. <br />
                    IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_manage_subscriptions]</strong>.</p>
                </td>
              </tr>


              <tr>
                <th scope="row"><label for="slb_confirmation_page_id">Opt-In Page</label></th>
                <td>
                  '. slb_get_page_select( 'slb_confirmation_page_id', 'slb_confirmation_page_id', 0, 'id', $options['slb_confirmation_page_id'] ) .'
                  <p class="description" id="slb_confirmation_page_id-description">This is the page where Syahrul List Builder will send subscribers to confirm their subscriptions. <br />
                    IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_confirm_subscription]</strong>.</p>
                </td>
              </tr>


              <tr>
                <th scope="row"><label for="slb_reward_page_id">Download Reward Page</label></th>
                <td>
                  '. slb_get_page_select( 'slb_reward_page_id', 'slb_reward_page_id', 0, 'id', $options['slb_reward_page_id'] ) .'
                  <p class="description" id="slb_reward_page_id-description">This is the page where Syahrul List Builder will send subscribers to retrieve their reward downloads. <br />
                    IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_download_reward]</strong>.</p>
                </td>
              </tr>

              <tr>
                <th scope="row"><label for="slb_default_email_footer">Email Footer</label></th>
                <td>');


                  // wp_editor will act funny if it's stored in a string so we run it like this...
                  wp_editor( $options['slb_default_email_footer'], 'slb_default_email_footer', array( 'textarea_rows'=>8 ) );


                  echo('<p class="description" id="slb_default_email_footer-description">The default text that appears at the end of emails generated by this plugin.</p>
                </td>
              </tr>

              <tr>
                <th scope="row"><label for="slb_download_limit">Reward Download Limit</label></th>
                <td>
                  <input type="number" name="slb_download_limit" value="'. $options['slb_download_limit'] .'" class="" />
                  <p class="description" id="slb_download_limit-description">The amount of downloads a reward link will allow before expiring.</p>
                </td>
              </tr>

            </tbody>

          </table>');

          // outputs the WP submit button html
          @submit_button();


        echo('</form>

      </div>');

      }

      function slb_annoucement_admin(){
        echo ('
        <div class="wrap">
          <h2>Announcement to Subscribers</h2>
          <form class="annoucement_form" id="annoucement_form" method="post" action="'.get_site_url().'/wp-admin/admin-ajax.php?action=slb_annouce_subscribers">
            <table class="form-table">
              <tbody class="form-table">
                <tr>
                  <th scope="row"><label>Announce To :
                  </label></th>
                  <td>
                    <select class="slb_select_announce_to" name="list_id">');
                        // get all our email lists
                        $lists = get_posts(
                          array(
                            'post_type'			=>'slb_list',
                            'status'			=>'publish',
                            'posts_per_page'   	=> -1,
                            'orderby'         	=> 'post_title',
                            'order'            	=> 'ASC',
                          )
                        );
                        // loop over each email list
                        foreach( $lists as &$list ):

                          // create the select option for that list
                          $option = '
                            <option name="list_id" value="'. $list->ID .'">
                              '. $list->post_title .'
                            </option>';

                          // echo the new option
                          echo $option;


                        endforeach;

                    echo('</select>
                    <p class="description"></p>
                  </td>
                </tr>
                <tr>
                  <th scope="row"><label for="slb_subject_email_announce">Subject</label></th>
                  <td><input type="text" placeholder="Email Subject" id="slb_subject_email_announce" name="subject" class="regular-text"></input></td>
                </tr>
                <tr>
                  <th scope="row"><label for="slb_email_announce">Announce</label></th>
                  <td> <textarea name="message" placeholder="Put your content here..." rows="12" cols="100"></textarea>');


                    // wp_editor will act funny if it's stored in a string so we run it like this...
                    //wp_editor( 'Edit your content....', 'message', array( 'textarea_rows'=>8 ) );


                    echo('<p class="description" id="slb_default_email_footer-description">Announce to All subscribers from the list</p>
                  </td>
                </tr>

              </tbody>

            </table>
            <p><input type="submit" name="submit" id="submit" class="button button-primary" value="Announce that"></input></p>
            ');

            //@submit_button('Announce that!');


          echo('</form>

        </div>');

      }




 ?>
