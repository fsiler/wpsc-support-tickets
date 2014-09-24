<?php

function wpsctDisplayCustomFieldsToFrontend($primkey) {
    global $wpdb;
    // Custom fields
    $table_name33 = $wpdb->prefix . "wpstorecart_meta";

    $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

    $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );
    if(isset($resultscf)) {
            echo '<table style="width:100%;"><tbody>';
            foreach ($resultscf as $field) {
                $specific_items = explode("||", $field['value']);
                $res = $wpdb->get_results("SELECT * FROM `{$table_name33}` WHERE `type`='wpsct_custom_{$field['primkey']}' AND `foreignkey`='{$primkey}';", ARRAY_A);
                if(@isset($res[0]['primkey'])) {
                    echo '<tr><td><h4 style="display:inline;">'.$specific_items[0].':</h4> '.strip_tags(base64_decode($res[0]['value'])).'</td></tr>';

                }
            }
            echo '</tbody></table>';
    }
}

function wpsctLoadTicket() {
    global $current_user, $wpdb;

    if (session_id() == "") {@session_start();};

    if((is_user_logged_in() || @isset($_SESSION['wpsct_email'])) && is_numeric($_POST['primkey'])) {

        $devOptions = get_option('wpscSupportTicketsAdminOptions');

        // Guest additions here
        if(is_user_logged_in()) {
            $wpscst_userid = $current_user->ID;
            $wpscst_email = $current_user->user_email;
            $wpscst_username = $current_user->display_name;
        } else {
            $wpscst_userid = 0;
            $wpscst_email = $wpdb->escape($_SESSION['wpsct_email']);
            $wpscst_username = __('Guest', 'wpsc-support-tickets').' ('.$wpscst_email.')';
        }

        $primkey = intval($_POST['primkey']);

        if($devOptions['allow_all_tickets_to_be_viewed']=='true') {
            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
        }
        if($devOptions['allow_all_tickets_to_be_viewed']=='false') {
            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' AND `user_id`='{$wpscst_userid}' AND `email`='{$wpscst_email}' LIMIT 0, 1;";
        }

        $results = $wpdb->get_results( $sql , ARRAY_A );
        if(isset($results[0])) {
            if($devOptions['allow_all_tickets_to_be_viewed']=='true') {
                $wpscst_username = $results[0]['email'];
            }
            echo '<div id="wpscst_meta">';

            if($devOptions['custom_field_frontend_position']=='before everything') {
                wpsctDisplayCustomFieldsToFrontend($primkey);
            }

            if ($results[0]['resolution'] == strtolower('open') ) {
                $resresolution = __('Open', 'wpsc-support-tickets');
            } elseif ($results[0]['resolution'] == strtolower('closed') ) {
                $resresolution = __('Closed', 'wpsc-support-tickets');
            } else {
                $resresolution = $results[0]['resolution'];
            }

            if (!function_exists('wpscSupportTicketDepartments')) {
                echo '<strong>'.base64_decode($results[0]['title']).'</strong> ('.$resresolution.' - '.base64_decode($results[0]['type']).')</div>';
            } else {
                wpscSupportTicketsPRODepartments($results[0], $resresolution);
            }


            if($devOptions['custom_field_frontend_position']=='before message') {
                wpsctDisplayCustomFieldsToFrontend($primkey);
            }

            echo '<table style="width:100%;">';
            echo '<thead><tr><th id="wpscst_results_posted_by">'.__('Posted by', 'wpsc-support-tickets').' '.$wpscst_username.' (<span id="wpscst_results_time_posted">'.date_i18n( get_option( 'date_format' ),$results[0]['time_posted']).'</span>)</th></tr></thead>';

            $messageData = strip_tags(base64_decode($results[0]['initial_message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
            $messageData = explode ( '\\', $messageData);
            $messageWhole = '';
            foreach ($messageData as $messagePart){
             $messageWhole .= $messagePart;
            }
            echo '<tbody><tr><td id="wpscst_results_initial_message"><br />'.$messageWhole;

            //echo '<tbody><tr><td id="wpscst_results_initial_message"><br />'.strip_tags(base64_decode($results[0]['initial_message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>').'</td></tr>';
            echo '</tbody></table>';

            $results = NULL;
            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}' ORDER BY `timestamp` ASC;";
            $result2 = $wpdb->get_results( $sql , ARRAY_A );
            if(isset($result2)) {
                foreach ($result2 as $results) {
                    $classModifier1 = NULL;$classModifier2 = NULL;$classModifier3 = NULL;
                    if($results['user_id']!=0) {
                        @$user=get_userdata($results['user_id']);
                        @$userdata = new WP_User($results['user_id']);
                        if ( $userdata->has_cap('manage_wpsct_support_tickets') ) {
                            $classModifier1 = ' class="wpscst_staff_reply_table" ';
                            $classModifier2 = ' class="wpscst_staff_reply_thead" ';
                            $classModifier3 = ' class="wpscst_staff_reply_tbody" ';
                        }
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }

                    echo '<br /><table style="width:100%;" '.$classModifier1.'>';
                    echo '<thead '.$classModifier2.'><tr><th class="wpscst_results_posted_by">'.__('Posted by', 'wpsc-support-tickets').' '.$theusersname.' (<span class="wpscst_results_timestamp">'.date_i18n( get_option( 'date_format' ),$results['timestamp']).'</span>)</th></tr></thead>';
                    $messageData = strip_tags(base64_decode($results['message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                    $messageData = explode ( '\\', $messageData);
                    $messageWhole = '';
                    foreach ($messageData as $messagePart){
                    $messageWhole .= $messagePart;
                    }
                    echo '<tbody '.$classModifier3.'><tr><td class="wpscst_results_message"><br />'.$messageWhole.'</td></tr>';
                    echo '</tbody></table>';
                }
            }

            if($devOptions['custom_field_frontend_position']=='after message') {
                wpsctDisplayCustomFieldsToFrontend($primkey);
            }



        }
    }

    exit();
}
add_action( 'wp_ajax_wpsct_save_issue', 'wpsctLoadTicket' );
add_action( 'wp_ajax_nopriv_wpsct_save_issue', 'wpsctLoadTicket' );


?>
