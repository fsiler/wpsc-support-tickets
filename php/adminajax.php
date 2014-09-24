<?php

function wpsctAjaxAddField() {

    global $current_user, $wpdb;
    wp_get_current_user();

    if ( 0 == $current_user->ID ) {
        // Not logged in.
    } else {

        if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') ) {
            die(__('Unable to Authenticate', 'wpsc-support-tickets'));
        }

        $table_name = $wpdb->prefix . "wpstorecart_meta";
        $createnewfieldname = $wpdb->escape($_POST['createnewfieldname']);
        $createnewfieldtype = $wpdb->escape($_POST['createnewfieldtype']);
        $createnewfieldrequired = $wpdb->escape($_POST['createnewfieldrequired']);

        $insert = "
        INSERT INTO `{$table_name}` (
        `primkey`, `value`, `type`, `foreignkey`)
        VALUES (
                NULL,
                '{$createnewfieldname}||{$createnewfieldrequired}||{$createnewfieldtype}',
                'wpst-requiredinfo',
                '0'
        );
        ";

        $results = $wpdb->query($insert);
        $lastID = $wpdb->insert_id;


        $customfieldmc = $wpdb->escape($_POST['customfieldmc']);
        if (@isset($customfieldmc) && trim($customfieldmc)!='') {
            $insert = "
            INSERT INTO `{$table_name}` (
            `primkey`, `value`, `type`, `foreignkey`)
            VALUES (
                    NULL,
                    '{$customfieldmc}',
                    'wpst-custom-fields-mc',
                    '{$lastID}'
            );
            ";

            $results = $wpdb->query($insert);
        }

        echo $lastID;
        exit();
    }
}
add_action( 'wp_ajax_wpsct_add_field', 'wpsctAjaxAddField' );



function wpsctAjaxDelField() {
    global $current_user, $wpdb;

    wp_get_current_user();
    if ( 0 == $current_user->ID ) {
        // Not logged in.
    } else {

        if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') ) {
            die(__('Unable to Authenticate', 'wpsc-support-tickets'));
        }

        $delete = $wpdb->escape($_POST['delete']);

        $table_name = $wpdb->prefix . "wpstorecart_meta";

        $results = $wpdb->query("DELETE FROM `{$table_name}` WHERE `primkey`={$delete};");

        $results = $wpdb->query("DELETE FROM `{$table_name}` WHERE `type`='wpst-custom-fields-mc' AND `foreignkey`='{$delete}';");


    }
}
add_action( 'wp_ajax_wpsct_del_field', 'wpsctAjaxDelField' );



function wpsctAjaxSortFields() {
    global $current_user, $wpdb;

    wp_get_current_user();
    if ( 0 == $current_user->ID ) {
        // Not logged in.
    } else {

        if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') ) {
            die(__('Unable to Authenticate', 'wpsc-support-tickets'));
        }


        $table_name = $wpdb->prefix . "wpstorecart_meta";

        // Grab the sort order
        $ordernum = 1;
        $sortorder = $_POST['requiredinfo'];
        foreach ($sortorder as $sort) {
            $updateSQL = "UPDATE  `{$table_name}` SET  `foreignkey` =  '{$ordernum}' WHERE  `primkey` ={$sort};";
            $results = $wpdb->query($updateSQL);
            $ordernum++;
        }

    }
}
add_action( 'wp_ajax_wpsct_sort_fields', 'wpsctAjaxSortFields' );

?>
