<?php
/*
  Plugin Name: wpsc Support Tickets
  Plugin URI: http://wpscsupporttickets.com/wordpress-support-ticket-plugin/
  Description: An open source help desk and support ticket system for Wordpress using jQuery. Easy to use for both users & admins.
  Version: 4.7.32
  Author: wpStoreCart, LLC
  Author URI: URI: http://wpstorecart.com/
  License: LGPL
  Text Domain: wpsc-support-tickets
 */

/*
  Copyright 2012, 2013, 2014 wpStoreCart, LLC  (email : admin@wpstorecart.com)

  This library is free software; you can redistribute it and/or modify it under the terms
  of the GNU Lesser General Public License as published by the Free Software Foundation;
  either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this
  library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330,
  Boston, MA 02111-1307 USA
 */

if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}


//Global variables:
global $wpscSupportTickets, $wpscSupportTickets_version, $wpscSupportTickets_db_version, $APjavascriptQueue, $wpsct_error_reporting;

$wpscSupportTickets_version = 4.7;
$wpscSupportTickets_db_version = 4.7;
$APjavascriptQueue = NULL;
$wpsct_error_reporting = false;


// Create the proper directory structure if it is not already created
if (!is_dir(WP_CONTENT_DIR . '/uploads/')) {
    mkdir(WP_CONTENT_DIR . '/uploads/', 0777, true);
}
if (!is_dir(WP_CONTENT_DIR . '/uploads/wpscSupportTickets/')) {
    mkdir(WP_CONTENT_DIR . '/uploads/wpscSupportTickets/', 0777, true);
}

/**
 * Action definitions
 */
function wpscSupportTickets_settings() {
    do_action('wpscSupportTickets_settings');
}

function wpscSupportTickets_saveSettings() {
    do_action('wpscSupportTickets_saveSettings');
}

function wpscSupportTickets_extraTabsIndex() {
    do_action('wpscSupportTickets_extraTabsIndex');
}

function wpscSupportTickets_extraTabsContents() {
    do_action('wpscSupportTickets_extraTabsContents');
}

function wpscSupportTickets_departmentsHook() {
    do_action('wpscSupportTickets_departmentsHook');
}

if(!function_exists('wpsctSlug')) {
        /**
         *
         * Returns a slug of the input string, suitable URLs, HTML and other space/character sensitive operations
         *
         * @param string $str
         * @return string
         */
        function wpsctSlug($str) {
                $str = strtolower(trim($str));
                $str = preg_replace('/[^a-z0-9-]/', '_', $str);
                $str = preg_replace('/-+/', "_", $str);
                return $str;
        }
}

require_once(WP_PLUGIN_DIR . '/wpsc-support-tickets/php/adminajax.php');
require_once(WP_PLUGIN_DIR . '/wpsc-support-tickets/php/publicajax.php');

if(!function_exists('wpsctPromptForCustomFields')) {
    /**
     *
     * @global type $wpdb
     * @return string
     */
    function wpsctPromptForCustomFields() {
        global $wpdb;

        $devOptions = get_option('wpscSupportTicketsAdminOptions');
        $output = '';
        if (session_id() == "") {@session_start();};

        // Custom form fields here
        $table_name33 = $wpdb->prefix . "wpstorecart_meta";
        $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

        $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );

        $wpsct_text_fields = array(
            'input (text)','shippingcity','firstname','lastname','shippingaddress','input (numeric)','zipcode'
        );

        $wpsct_states = array(
            "not applicable" => 'Other (Non-US)',
            "AL" => __('Alabama', 'wpsc-support-tickets'),      "AK" => __('Alaska', 'wpsc-support-tickets'),       "AZ" => __('Arizona', 'wpsc-support-tickets'),      "CA" => __('California', 'wpsc-support-tickets'),
            "CO" => __('Colorado', 'wpsc-support-tickets'),     "CT" => __('Connecticut', 'wpsc-support-tickets'),  "DE" => __('Delaware', 'wpsc-support-tickets'),     "DC" => __('District Of Columbia', 'wpsc-support-tickets'),
            "FL" => __('Florida', 'wpsc-support-tickets'),      "GA" => __('Georgia', 'wpsc-support-tickets'),      "HI" => __('Hawaii', 'wpsc-support-tickets'),       "ID" => __('Idaho', 'wpsc-support-tickets'),
            "IL" => __('Illinois', 'wpsc-support-tickets'),     "IN" => __('Indiana', 'wpsc-support-tickets'),      "IA" => __('Iowa', 'wpsc-support-tickets'),         "KS" => __('Kansas', 'wpsc-support-tickets'),
            "KY" => __('Kentucky', 'wpsc-support-tickets'),     "LA" => __('Louisiana', 'wpsc-support-tickets'),    "ME" => __('Maine', 'wpsc-support-tickets'),        "MD" => __('Maryland', 'wpsc-support-tickets'),
            "MA" => __('Massachusetts', 'wpsc-support-tickets'),"MI" => __('Michigan', 'wpsc-support-tickets'),     "MN" => __('Minnesota', 'wpsc-support-tickets'),    "MS" => __('Mississippi', 'wpsc-support-tickets'),
            "MO" => __('Missouri', 'wpsc-support-tickets'),     "MT" => __('Montana', 'wpsc-support-tickets'),      "NE" => __('Nebraska', 'wpsc-support-tickets'),     "NV" => __('Nevada', 'wpsc-support-tickets'),
            "NH" => __('New Hampshire', 'wpsc-support-tickets'),"NJ" => __('New Jersey', 'wpsc-support-tickets'),   "NM" => __('New Mexico', 'wpsc-support-tickets'),   "NY" => __('New York', 'wpsc-support-tickets'),
            "NC" => __('North Carolina', 'wpsc-support-tickets'),"ND" => __('North Dakota', 'wpsc-support-tickets'),"OH" => __('Ohio', 'wpsc-support-tickets'),         "OK" => __('Oklahoma', 'wpsc-support-tickets'),
            "OR" => __('Oregon', 'wpsc-support-tickets'),       "PA" => __('Pennsylvania', 'wpsc-support-tickets'), "RI" => __('Rhode Island', 'wpsc-support-tickets'), "SC" => __('South Carolina', 'wpsc-support-tickets'),
            "SD" => __('South Dakota', 'wpsc-support-tickets'), "TN" => __('Tennessee', 'wpsc-support-tickets'),    "TX" => __('Texas', 'wpsc-support-tickets'),        "UT" => __('Utah', 'wpsc-support-tickets'),
            "VT" => __('Vermont', 'wpsc-support-tickets'),      "VA" => __('Virginia', 'wpsc-support-tickets'),     "WA" => __('Washington', 'wpsc-support-tickets'),   "WV" => __('West Virginia', 'wpsc-support-tickets'),
            "WI" => __('Wisconsin', 'wpsc-support-tickets'),    "WY" => __('Wyoming', 'wpsc-support-tickets'),
        );

        $wpsct_countries = array(
            __("United States", 'wpsc-support-tickets'), __("Canada", 'wpsc-support-tickets'),__("United Kingdom", 'wpsc-support-tickets'),__("Ireland", 'wpsc-support-tickets'),__("Australia", 'wpsc-support-tickets'),__("New Zealand", 'wpsc-support-tickets'),__("Afghanistan", 'wpsc-support-tickets'),__("Albania", 'wpsc-support-tickets'),__("Algeria", 'wpsc-support-tickets'),__("American Samoa", 'wpsc-support-tickets'),
            __("Andorra", 'wpsc-support-tickets'),__("Angola", 'wpsc-support-tickets'),__("Anguilla", 'wpsc-support-tickets'),__("Antarctica", 'wpsc-support-tickets'),__("Antigua and Barbuda", 'wpsc-support-tickets'),__("Argentina", 'wpsc-support-tickets'),__("Armenia", 'wpsc-support-tickets'),__("Aruba", 'wpsc-support-tickets'),__("Australia", 'wpsc-support-tickets'),__("Austria", 'wpsc-support-tickets'),__("Azerbaijan", 'wpsc-support-tickets'),
            __("Bahamas", 'wpsc-support-tickets'),__("Bahrain", 'wpsc-support-tickets'),__("Bangladesh", 'wpsc-support-tickets'),__("Barbados", 'wpsc-support-tickets'),__("Belarus", 'wpsc-support-tickets'),__("Belgium", 'wpsc-support-tickets'),__("Belize", 'wpsc-support-tickets'),__("Benin", 'wpsc-support-tickets'),__("Bermuda", 'wpsc-support-tickets'),__("Bhutan", 'wpsc-support-tickets'),__("Bolivia", 'wpsc-support-tickets'),__("Bosnia and Herzegovina", 'wpsc-support-tickets'),
            __("Botswana", 'wpsc-support-tickets'),__("Bouvet Island", 'wpsc-support-tickets'),__("Brazil", 'wpsc-support-tickets'),__("British Indian Ocean Territory", 'wpsc-support-tickets'),__("Brunei Darussalam", 'wpsc-support-tickets'),__("Bulgaria", 'wpsc-support-tickets'),__("Burkina Faso", 'wpsc-support-tickets'),__("Burundi", 'wpsc-support-tickets'),__("Cambodia", 'wpsc-support-tickets'),
            __("Cameroon", 'wpsc-support-tickets'),__("Canada", 'wpsc-support-tickets'),__("Cape Verde", 'wpsc-support-tickets'),__("Cayman Islands", 'wpsc-support-tickets'),__("Central African Republic", 'wpsc-support-tickets'),__("Chad", 'wpsc-support-tickets'),__("Chile", 'wpsc-support-tickets'),__("China", 'wpsc-support-tickets'),__("Christmas Island", 'wpsc-support-tickets'),__("Cocos (Keeling) Islands", 'wpsc-support-tickets'),
            __("Colombia", 'wpsc-support-tickets'),__("Comoros", 'wpsc-support-tickets'),__("Congo", 'wpsc-support-tickets'),__("Congo, The Democratic Republic of The", 'wpsc-support-tickets'), __("Cook Islands", 'wpsc-support-tickets'),__("Costa Rica", 'wpsc-support-tickets'),__("Cote D\'ivoire", 'wpsc-support-tickets'),__("Croatia", 'wpsc-support-tickets'),__("Cuba", 'wpsc-support-tickets'),__("Cyprus", 'wpsc-support-tickets'),
            __("Czech Republic", 'wpsc-support-tickets'),__("Denmark", 'wpsc-support-tickets'),__("Djibouti", 'wpsc-support-tickets'),__("Dominica", 'wpsc-support-tickets'),__("Dominican Republic", 'wpsc-support-tickets'),__("Ecuador", 'wpsc-support-tickets'),__("Egypt", 'wpsc-support-tickets'),__("El Salvador", 'wpsc-support-tickets'),__("Equatorial Guinea", 'wpsc-support-tickets'),__("Eritrea", 'wpsc-support-tickets'),__("Estonia", 'wpsc-support-tickets'),
            __("Ethiopia", 'wpsc-support-tickets'),__("Falkland Islands (Malvinas)", 'wpsc-support-tickets'),__("Faroe Islands", 'wpsc-support-tickets'),__("Fiji", 'wpsc-support-tickets'),__("Finland", 'wpsc-support-tickets'),__("France", 'wpsc-support-tickets'),__("French Guiana", 'wpsc-support-tickets'),__("French Polynesia", 'wpsc-support-tickets'),__("French Southern Territories", 'wpsc-support-tickets'),
            __("Gabon", 'wpsc-support-tickets'),__("Gambia", 'wpsc-support-tickets'),__("Georgia", 'wpsc-support-tickets'),__("Germany", 'wpsc-support-tickets'),__("Ghana", 'wpsc-support-tickets'),__("Gibraltar", 'wpsc-support-tickets'),__("Greece", 'wpsc-support-tickets'),__("Greenland", 'wpsc-support-tickets'),__("Grenada", 'wpsc-support-tickets'),__("Guadeloupe", 'wpsc-support-tickets'),__("Guam", 'wpsc-support-tickets'),__("Guatemala", 'wpsc-support-tickets'),__("Guinea", 'wpsc-support-tickets'),__("Guinea-bissau", 'wpsc-support-tickets'),
            __("Guyana", 'wpsc-support-tickets'),__("Haiti", 'wpsc-support-tickets'),__("Heard Island and Mcdonald Islands", 'wpsc-support-tickets'),__("Holy See (Vatican City State)", 'wpsc-support-tickets'),__("Honduras", 'wpsc-support-tickets'),__("Hong Kong", 'wpsc-support-tickets'),__("Hungary", 'wpsc-support-tickets'),__("Iceland", 'wpsc-support-tickets'),__("India", 'wpsc-support-tickets'),__("Indonesia", 'wpsc-support-tickets'),
            __("Iran, Islamic Republic of", 'wpsc-support-tickets'),__("Iraq", 'wpsc-support-tickets'),__("Ireland", 'wpsc-support-tickets'),__("Israel", 'wpsc-support-tickets'),__("Italy", 'wpsc-support-tickets'),__("Jamaica", 'wpsc-support-tickets'),__("Japan", 'wpsc-support-tickets'),__("Jordan", 'wpsc-support-tickets'),__("Kazakhstan", 'wpsc-support-tickets'),__("Kenya", 'wpsc-support-tickets'),__("Kiribati", 'wpsc-support-tickets'),__("Korea, Democratic People\'s Republic of", 'wpsc-support-tickets'),
            __("Korea, Republic of", 'wpsc-support-tickets'),__("Kuwait", 'wpsc-support-tickets'),__("Kyrgyzstan", 'wpsc-support-tickets'),__("Lao People\'s Democratic Republic", 'wpsc-support-tickets'),__("Latvia", 'wpsc-support-tickets'),__("Lebanon", 'wpsc-support-tickets'),__("Lesotho", 'wpsc-support-tickets'),__("Liberia", 'wpsc-support-tickets'),__("Libyan Arab Jamahiriya", 'wpsc-support-tickets'),
            __("Liechtenstein", 'wpsc-support-tickets'),__("Lithuania", 'wpsc-support-tickets'),__("Luxembourg", 'wpsc-support-tickets'),__("Macao", 'wpsc-support-tickets'),__("Macedonia, The Former Yugoslav Republic of", 'wpsc-support-tickets'),__("Madagascar", 'wpsc-support-tickets'),__("Malawi", 'wpsc-support-tickets'),__("Malaysia", 'wpsc-support-tickets'),__("Maldives", 'wpsc-support-tickets'),__("Mali", 'wpsc-support-tickets'),
            __("Malta", 'wpsc-support-tickets'),__("Marshall Islands", 'wpsc-support-tickets'),__("Martinique", 'wpsc-support-tickets'),__("Mauritania", 'wpsc-support-tickets'),__("Mauritius", 'wpsc-support-tickets'),__("Mayotte", 'wpsc-support-tickets'),__("Mexico", 'wpsc-support-tickets'),__("Micronesia, Federated States of", 'wpsc-support-tickets'),__("Moldova, Republic of", 'wpsc-support-tickets'),
            __("Monaco", 'wpsc-support-tickets'),__("Mongolia", 'wpsc-support-tickets'),__("Montserrat", 'wpsc-support-tickets'),__("Morocco", 'wpsc-support-tickets'),__("Mozambique", 'wpsc-support-tickets'),__("Myanmar", 'wpsc-support-tickets'),__("Namibia", 'wpsc-support-tickets'),__("Nauru", 'wpsc-support-tickets'),__("Nepal", 'wpsc-support-tickets'),__("Netherlands", 'wpsc-support-tickets'),__("Netherlands Antilles", 'wpsc-support-tickets'),__("New Caledonia", 'wpsc-support-tickets'),
            __("New Zealand", 'wpsc-support-tickets'),__("Nicaragua", 'wpsc-support-tickets'),__("Niger", 'wpsc-support-tickets'),__("Nigeria", 'wpsc-support-tickets'),__("Niue", 'wpsc-support-tickets'),__("Norfolk Island", 'wpsc-support-tickets'),__("Northern Mariana Islands", 'wpsc-support-tickets'),__("Norway", 'wpsc-support-tickets'),__("Oman", 'wpsc-support-tickets'),__("Pakistan", 'wpsc-support-tickets'),__("Palau", 'wpsc-support-tickets'),
            __("Palestinian Territory, Occupied", 'wpsc-support-tickets'),__("Panama", 'wpsc-support-tickets'),__("Papua New Guinea", 'wpsc-support-tickets'),__("Paraguay", 'wpsc-support-tickets'),__("Peru", 'wpsc-support-tickets'),__("Philippines", 'wpsc-support-tickets'),__("Pitcairn", 'wpsc-support-tickets'),__("Poland", 'wpsc-support-tickets'),__("Portugal", 'wpsc-support-tickets'),__("Puerto Rico", 'wpsc-support-tickets'),
            __("Qatar", 'wpsc-support-tickets'),__("Reunion", 'wpsc-support-tickets'),__("Romania", 'wpsc-support-tickets'),__("Russian Federation", 'wpsc-support-tickets'),__("Rwanda", 'wpsc-support-tickets'),__("Saint Helena", 'wpsc-support-tickets'),__("Saint Kitts and Nevis", 'wpsc-support-tickets'),__("Saint Lucia", 'wpsc-support-tickets'),__("Saint Pierre and Miquelon", 'wpsc-support-tickets'),
            __("Saint Vincent and The Grenadines", 'wpsc-support-tickets'),__("Samoa", 'wpsc-support-tickets'),__("San Marino", 'wpsc-support-tickets'),__("Sao Tome and Principe", 'wpsc-support-tickets'),__("Saudi Arabia", 'wpsc-support-tickets'),__("Senegal", 'wpsc-support-tickets'),__("Serbia and Montenegro", 'wpsc-support-tickets'),__("Seychelles", 'wpsc-support-tickets'),
            __("Sierra Leone", 'wpsc-support-tickets'),__("Singapore", 'wpsc-support-tickets'),__("Slovakia", 'wpsc-support-tickets'),__("Slovenia", 'wpsc-support-tickets'),__("Solomon Islands", 'wpsc-support-tickets'),__("Somalia", 'wpsc-support-tickets'),__("South Africa", 'wpsc-support-tickets'),__("South Georgia and The South Sandwich Islands", 'wpsc-support-tickets'),__("Spain", 'wpsc-support-tickets'),
            __("Sri Lanka", 'wpsc-support-tickets'),__("Sudan", 'wpsc-support-tickets'),__("Suriname", 'wpsc-support-tickets'),__("Svalbard and Jan Mayen", 'wpsc-support-tickets'),__("Swaziland", 'wpsc-support-tickets'),__("Sweden", 'wpsc-support-tickets'),__("Switzerland", 'wpsc-support-tickets'),__("Syrian Arab Republic", 'wpsc-support-tickets'),__("Taiwan, Province of China", 'wpsc-support-tickets'),
            __("Tajikistan", 'wpsc-support-tickets'),__("Tanzania, United Republic of", 'wpsc-support-tickets'),__("Thailand", 'wpsc-support-tickets'),__("Timor-leste", 'wpsc-support-tickets'),__("Togo", 'wpsc-support-tickets'),__("Tokelau", 'wpsc-support-tickets'),__("Tonga", 'wpsc-support-tickets'),__("Trinidad and Tobago", 'wpsc-support-tickets'),__("Tunisia", 'wpsc-support-tickets'),__("Turkey", 'wpsc-support-tickets'),
            __("Turkmenistan", 'wpsc-support-tickets'),__("Turks and Caicos Islands", 'wpsc-support-tickets'),__("Tuvalu", 'wpsc-support-tickets'),__("Uganda", 'wpsc-support-tickets'),__("Ukraine", 'wpsc-support-tickets'),__("United Arab Emirates", 'wpsc-support-tickets'),__("United States Minor Outlying Islands", 'wpsc-support-tickets'),__("Uruguay", 'wpsc-support-tickets'),
            __("Uzbekistan", 'wpsc-support-tickets'),__("Vanuatu", 'wpsc-support-tickets'),__("Venezuela", 'wpsc-support-tickets'),__("Viet Nam", 'wpsc-support-tickets'),__("Virgin Islands, British", 'wpsc-support-tickets'),__("Virgin Islands, U.S.", 'wpsc-support-tickets'),__("Wallis and Futuna", 'wpsc-support-tickets'),__("Western Sahara", 'wpsc-support-tickets'),__("Yemen", 'wpsc-support-tickets'),
            __("Zambia", 'wpsc-support-tickets'),__("Zimbabwe", 'wpsc-support-tickets'),
        );

        $wpsct_style_width = '';
        $wpsct_style_inline = '';
        if ($devOptions['disable_inline_styles'] == 'false') {
            $wpsct_style_width ='style="width:100%"';
            $wpsct_style_inline = 'style="display:inline;"';
        }

        if(isset($resultscf)) {
                foreach ($resultscf as $field) {
                    $specific_items = explode("||", $field['value']);
                    $wpsct_required_item = '';
                    if ($specific_items[1]=='required'){
                        $wpsct_required_item = '<ins><div class="wpst-required-symbol" ' . $wpsct_style_inline . '>* </div></ins>';
                    }
                    $prev_val = $_SESSION['wpsct_custom_'.$field['primkey']];
                    foreach ($wpsct_text_fields as $wpsct_text_field) {
                        if($specific_items[2]==$wpsct_text_field) {
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                                . '</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'
                                . $_SESSION['wpsct_custom_'.$field['primkey']] .'" name="wpsct_custom_'.$field['primkey'].'" '
                                . $wpsct_style_width . '  /></td></tr>';
                        }
                    }
                    if ($specific_items[2] == 'textarea') {
                        $output .= '<tr><td><h3>' . $specific_items[0] . $wpsct_required_item
                            . '</h3><textarea  id="wpsct_custom_' . $field['primkey'] . '" name="wpsct_custom_'
                            . $field['primkey'] . '" ' . $wpsct_style_width . '>'
                            . $_SESSION['wpsct_custom_' . $field['primkey']] . '</textarea></td></tr>';
                    }
                    if($specific_items[2]=='states' || $specific_items[2]=='taxstates') {
                        $selected[$prev_val] = ' selected="selected"';
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                            . '</h3><select name="wpsct_custom_'.$field['primkey'].'" class="wpsct-states" ' . $wpsct_style_width . ">\n"
                            . "<option value=\"not applicable\" {$selected['']}>" . __('Other (Non-US)', 'wpsc-support-tickets').'</option>';
                        foreach ($wpsct_states as $wpsct_code => $wpcst_state) {
                            $output .="<option value=\"$wpsct_code\" {$selected[$wpsct_code]}>" . __($wpcst_state, 'wpsc-support-tickets').'</option>\n';
                        }
                        $output.= '</select></td></tr>';
                    }
                    if($specific_items[2]=='countries' || $specific_items[2]=='taxcountries') {
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                            . '</h3><select  name="wpsct_custom_'.$field['primkey'].'" class="wpsct-countries" '. $wpsct_style_width . ">\n";
                        $selected[$prev_val] = ' selected="selected"';
                        foreach ($wpsct_countries as $wpsct_country) {
                            $output .="<option value=\"$wpsct_country\" {$selected[$wpsct_country]}>" . __($wpsct_country, 'wpsc-support-tickets').'</option>\n';
                        }
                        $output.= '</select></td></tr>';
                    }
                    if($specific_items[2]=='email') {
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                            . '</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'
                            . $_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" ' . $wpsct_style_width
                            . ' /></td></tr>';
                    }
                    if($specific_items[2]=='separator') {
                        $output .= '<tr><td><center>'.$specific_items[0].'</center></td></tr>';
                    }
                    if($specific_items[2]=='header') {
                        $output .= '<tr><td><h2>'.$specific_items[0] .'</h2></td></tr>';
                    }
                    if($specific_items[2]=='text') {
                        $output .= '<tr><td>'.$specific_items[0] .'</td></tr>';
                    }
                    /** Added in 4.4.4 **/
                    if($specific_items[2]=='dropdown') {
                        $grabrecordz = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-custom-fields-mc' AND `foreignkey`='{$field['primkey']}';";
                        $resultszz = $wpdb->get_results( $grabrecordz , ARRAY_A );
                        if(@isset($resultszz[0]['primkey'])) {
                            $zzzoptions = explode(',', rtrim($resultszz[0]['value'], ',' ) );
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item . '</h3>';
                            $output .= '<select id="wpsct_custom_'.$field['primkey'].'" name="wpsct_custom_'.$field['primkey'].'">';
                            foreach ($zzzoptions as $zzzoption) {
                                $output .= '   <option value="'.$zzzoption.'">'.$zzzoption.'</option>';
                            }
                            $output .= '</select>';
                            $output .= '</td></tr>';
                        }
                    }
                    if($specific_items[2]=='checkbox') {
                        $grabrecordz = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-custom-fields-mc' AND `foreignkey`='{$field['primkey']}';";
                        $resultszz = $wpdb->get_results( $grabrecordz , ARRAY_A );
                        if(@isset($resultszz[0]['primkey'])) {
                            $zzzoptions = explode(',', rtrim($resultszz[0]['value'], ',' ) );
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item . '</h3>';
                            foreach ($zzzoptions as $zzzoption) {
                                $output .= '   <input type="checkbox" name="wpsct_custom_'.$field['primkey'].'[]" value="'.$zzzoption.'"></input> '.$zzzoption.'<br />';
                            }
                            $output .= '</td></tr>';
                        }
                    }
                    if($specific_items[2]=='radio') {
                        $grabrecordz = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-custom-fields-mc' AND `foreignkey`='{$field['primkey']}';";
                        $resultszz = $wpdb->get_results( $grabrecordz , ARRAY_A );
                        if(@isset($resultszz[0]['primkey'])) {
                            $zzzoptions = explode(',', rtrim($resultszz[0]['value'], ',' ) );
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item . '</h3>';
                            foreach ($zzzoptions as $zzzoption) {
                                $output .= '   <input type="radio" name="wpsct_custom_'.$field['primkey'].'" value="'.$zzzoption.'"></input> '.$zzzoption.'<br />';
                            }
                            $output .= '</td></tr>';
                        }
                    }

                }
        }

        return $output;
    }
}

/**
 * ===============================================================================================================
 * Main wpscSupportTickets Class
 */
if (!class_exists("wpscSupportTickets")) {

    class wpscSupportTickets {

        var $adminOptionsName = "wpscSupportTicketsAdminOptions";
        var $wpscstSettings = null;
        var $hasDisplayed = false;
        var $hasDisplayedCompat = false; // hack for Jetpack compatibility
        var $hasDisplayedCompat2 = false; // hack for Jetpack compatibility

        /**
         *
         * @deprecated since version 5.0
         * @global object $wp_roles
         */
        function wpscSupportTickets() { //constructor
            // Let's make sure the admin is always in charge
            if (function_exists('is_multisite') && is_multisite()) {

            } else {
                $this->addPermissions(); //
            }
        }

        function init() {
            $this->getAdminOptions();
        }

        /**
         * Beginnings of the new permission system for 5.0
         */
        function checkPermissions() {
            if ( !is_super_admin() || (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets')) ) {
                die(__('Unable to Authenticate', 'wpsc-support-tickets'));
            }
        }

        function addPermissions() {
            if (is_user_logged_in()) {
                if (is_super_admin() || is_admin()) {
                    global $wp_roles;
                    add_role('wpsct_support_ticket_manager', 'Support Ticket Manager', array('manage_wpsct_support_tickets', 'read', 'upload_files', 'publish_posts', 'edit_published_posts', 'publish_pages', 'edit_published_pages', 'Keymaster', 'keymaster', 'keep_gate', 'bbp_forums_admin', 'bbp_topics_admin', 'bbp_replies_admin'));
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'read');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'upload_files');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'publish_pages');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'publish_posts');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'edit_published_posts');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'edit_published_pages');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'manage_wpsct_support_tickets');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'Keymaster');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'keymaster');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'keep_gate');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'bbp_forums_admin');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'bbp_topics_admin');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'bbp_replies_admin');
                    $wp_roles->add_cap('administrator', 'manage_wpsct_support_tickets');
                }
            }
        }

        /**
         *
         * @param string $e
         * @return string
         */
        function change_mail_from( $e ) {
            $settings = $this->getAdminOptions();
            return $settings['email'];
        }

        /**
         *
         * @param string $n
         * @return string
         */
        function change_mail_name( $n ) {
            $settings = $this->getAdminOptions();
            return $settings['email_name'];
        }


        /**
         * Returns an array of admin options
         * @return array
         */
        function getAdminOptions() {

            // Default values.
            $apAdminOptions = array('mainpage' => '',
                'turnon_wpscSupportTickets' => 'true',
                'departments' => __('Support', 'wpsc-support-tickets') . '||' . __('Billing', 'wpsc-support-tickets'),
                'email' => get_bloginfo('admin_email'),
                'email_new_ticket_subject' => __('Your support ticket was received.', 'wpsc-support-tickets'),
                'email_new_ticket_body' => __('Thank you for opening a new support ticket.  We will look into your issue and respond as soon as possible.', 'wpsc-support-tickets'),
                'email_new_reply_subject' => __('Your support ticket reply was received.', 'wpsc-support-tickets'),
                'email_new_reply_body' => __('A reply was posted to one of your support tickets.', 'wpsc-support-tickets'),
                'disable_inline_styles' => 'false',
                'allow_guests' => 'false',
                'allow_all_tickets_to_be_replied' => 'false',
                'allow_all_tickets_to_be_viewed' => 'false',
                'allow_html' => 'false',
                'allow_closing_ticket' => 'false',
                'allow_uploads' => 'false',
                'custom_field_position' => 'after message',
                'custom_field_frontend_position' => 'after message',
                'use_ticket_in_email' => 'true',
                'use_reply_in_email' => 'true',
                'department_admins' => 'default',
                'email_name' => __('Support', 'wpsc-support-tickets'),
                'hide_email_on_frontend_list' => 'false'
            );

            if ($this->wpscstSettings != NULL) { // If we haven't cached stuff already
                $devOptions = $this->wpscstSettings; // Caches the settings array so that we don't keep reinitializing it
            } else {
                $devOptions = get_option($this->adminOptionsName);
            }
            if (!empty($devOptions)) { // If the default values don't exist.

                foreach ($devOptions as $key => $option) {
                    $apAdminOptions[$key] = $option;
                }
            }
            update_option($this->adminOptionsName, $apAdminOptions);
            return $apAdminOptions;
        }

        /**
         * Admin Header
         */
        function adminHeader() {

            $this->checkPermissions();

            echo '

            <div style="padding: 20px 10px 10px 10px;">';

            if (!function_exists('wpscSupportTicketsPRO')) {
                echo '<div style="float:left;"><img src="' . plugins_url() . '/wpsc-support-tickets/images/logo.png" alt="wpscSupportTickets" /></div>';
            } else {
                echo '<div style="float:left;"><img src="' . plugins_url() . '/wpsc-support-tickets-pro/images/logo_pro.png" alt="wpscSupportTickets" /></div>';
            }


            echo '
            </div>
            <br style="clear:both;" />
            ';
        }


        /*
         * Admin page for departments
         * Added in wpsc Support Tickets v5.0
         */
        function printAdminPageDepartments() {

            $devOptions = $this->getAdminOptions();

            echo '<div class="wrap">';
            $this->adminHeader();

            wpscSupportTickets_departmentsHook(); // Action hook for departments

            echo '</div>';
        }

        /*
         * Admin page for Settings
         */
        function printAdminPageSettings() {

            wpscSupportTickets_saveSettings(); // Action hook for saving

            $devOptions = $this->getAdminOptions();

            echo '<div class="wrap">';

            $this->adminHeader();

            if (@isset($_POST['update_wpscSupportTicketsSettings'])) {

                if (isset($_POST['wpscSupportTicketsmainpage'])) {
                    $devOptions['mainpage'] = esc_sql($_POST['wpscSupportTicketsmainpage']);
                }
                if (isset($_POST['turnwpscSupportTicketsOn'])) {
                    $devOptions['turnon_wpscSupportTickets'] = esc_sql($_POST['turnwpscSupportTicketsOn']);
                }
                if (isset($_POST['departments'])) {
                    $devOptions['departments'] = esc_sql($_POST['departments']);
                }
                if (isset($_POST['email'])) {
                    $devOptions['email'] = esc_sql($_POST['email']);
                }
                if (isset($_POST['email_new_ticket_subject'])) {
                    $devOptions['email_new_ticket_subject'] = esc_sql($_POST['email_new_ticket_subject']);
                }
                if (isset($_POST['email_new_ticket_body'])) {
                    $devOptions['email_new_ticket_body'] = esc_sql($_POST['email_new_ticket_body']);
                }
                if (isset($_POST['email_new_reply_subject'])) {
                    $devOptions['email_new_reply_subject'] = esc_sql($_POST['email_new_reply_subject']);
                }
                if (isset($_POST['email_new_reply_body'])) {
                    $devOptions['email_new_reply_body'] = esc_sql($_POST['email_new_reply_body']);
                }
                if (isset($_POST['disable_inline_styles'])) {
                    $devOptions['disable_inline_styles'] = esc_sql($_POST['disable_inline_styles']);
                }
                if (isset($_POST['allow_guests'])) {
                    $devOptions['allow_guests'] = esc_sql($_POST['allow_guests']);
                }
                if (isset($_POST['custom_field_position'])) {
                    $devOptions['custom_field_position'] = esc_sql($_POST['custom_field_position']);
                }
                if (isset($_POST['custom_field_frontend_position'])) {
                    $devOptions['custom_field_frontend_position'] = esc_sql($_POST['custom_field_frontend_position']);
                }
                if (isset($_POST['use_ticket_in_email'])) {
                    $devOptions['use_ticket_in_email'] = esc_sql($_POST['use_ticket_in_email']);
                }
                if (isset($_POST['use_reply_in_email'])) {
                    $devOptions['use_reply_in_email'] = esc_sql($_POST['use_reply_in_email']);
                }
                if (isset($_POST['display_severity_on_create'])) {
                    $devOptions['display_severity_on_create'] = esc_sql($_POST['display_severity_on_create']);
                }
                if(isset($_POST['email_name'])) {
                    $devOptions['email_name'] = esc_sql($_POST['email_name']);
                }
                if(isset($_POST['hide_email_on_frontend_list'])) {
                    $devOptions['hide_email_on_frontend_list'] = esc_sql($_POST['hide_email_on_frontend_list']);
                }

                update_option($this->adminOptionsName, $devOptions);

                echo '<div class="updated"><p><strong>';
                _e("Settings Updated.", "wpsc-support-tickets");
                echo '</strong></p></div>';
            }

            echo '

            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });
            </script>

            <form method="post" action="' . $_SERVER["REQUEST_URI"] . '">


        <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wst_tabs-1">' . __('Settings', 'wpsc-support-tickets') . '</a></li>
                <li><a href="#wst_tabs-2">' . __('PRO', 'wpsc-support-tickets') . '</a></li>
            </ul>


            <div id="wst_tabs-1">

            <br />
            <h1>' . __('General', 'wpsc-support-tickets') . '</h1>
            <table class="widefat" style="background:transparent;"><tr><td>

                <p><strong>' . __('Main Page', 'wpsc-support-tickets') . ':</strong> ' . __('You need to use a Page as the base for wpsc Support Tickets.', 'wpsc-support-tickets') . '  <br />
                <select name="wpscSupportTicketsmainpage">
                 <option value="">';
                attribute_escape(__('Select page', 'wpsc-support-tickets'));
                echo '</option>';

                $pages = get_pages();
                foreach ($pages as $pagg) {
                    $option = '<option value="' . $pagg->ID . '"';
                    if ($pagg->ID == $devOptions['mainpage']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg->post_title;
                    $option .= '</option>';
                    echo $option;
                }


                    echo '
                    </select>
                    </p>';

                    // Brand new Departments management coming in version 5
                    if (!function_exists('wpscSupportTicketDepartments')) {
                        echo '
                        <strong>' . __('Departments', 'wpsc-support-tickets') . ':</strong> ' . __('Separate these values with a double pipe, like this ||', 'wpsc-support-tickets') . ' <br /><input name="departments" value="' . $devOptions['departments'] . '" style="width:95%;" /><br /><br />

        ';
                    }

                echo '<p><strong>' . __('Allow user to select Severity on ticket creation?', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want the user to select the severity of their ticket when creating it.', 'wpsc-support-tickets') . '  <br />
                <select name="display_severity_on_create">
                 ';

                $pagesYXX[0] = 'true';
                $pagesYXX[1] = 'false';
                foreach ($pagesYXX as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['display_severity_on_create']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

            </td></tr></table>
            <br /><br /><br />
            <h1>' . __('Email', 'wpsc-support-tickets') . '</h1>
            <table class="widefat" style="background:transparent;"><tr><td>

                <strong>' . __('Email', 'wpsc-support-tickets') . ':</strong> ' . __('The admin email where all new ticket &amp; reply notification emails will be sent', 'wpsc-support-tickets') . '<br /><input name="email" value="' . $devOptions['email'] . '" style="width:95%;" /><br /><br />
                <strong>' . __('Name Sent From', 'wpsc-support-tickets') .'</strong> '. __('The name of the admin email sender, such as "Business Name Support Team", or whatever is appropriate for your situation.', 'wpsc-support-tickets') .'<br /><input name="email_name" value="' . $devOptions['email_name'] . '" style="width:95%;" /><br /><br />
                <strong>' . __('New Ticket Email', 'wpsc-support-tickets') . '</strong> ' . __('The subject &amp; body of the email sent to the customer when creating a new ticket.', 'wpsc-support-tickets') . '<br /><input name="email_new_ticket_subject" value="' . stripslashes(stripslashes($devOptions['email_new_ticket_subject'])) . '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_ticket_body">' . stripslashes(stripslashes($devOptions['email_new_ticket_body'])) . '</textarea>
                <br /><br />

                <p><strong>' . __('Include the ticket in New Ticket Email', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want the content of the ticket included in the new ticket email.', 'wpsc-support-tickets') . '  <br />
                <select name="use_ticket_in_email">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['use_ticket_in_email']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

                <strong>' . __('New Reply Email', 'wpsc-support-tickets') . '</strong> ' . __('The subject &amp; body of the email sent to the customer when there is a new reply.', 'wpsc-support-tickets') . '<br /><input name="email_new_reply_subject" value="' . stripslashes(stripslashes($devOptions['email_new_reply_subject'])) . '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_reply_body">' . stripslashes(stripslashes($devOptions['email_new_reply_body'])) . '</textarea>
                <br /><br />

                <p><strong>' . __('Include the reply in New Reply Email', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want the content of the reply included in the new reply email.', 'wpsc-support-tickets') . '  <br />
                <select name="use_reply_in_email">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['use_reply_in_email']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

            </td></tr></table>
            <br /><br /><br />
            <h1>' . __('Styling', 'wpsc-support-tickets') . '</h1>
            <table class="widefat" style="background:transparent;"><tr><td>

                <p><strong>' . __('Disable inline styles', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want to disable the inline CSS styles.', 'wpsc-support-tickets') . '  <br />
                <select name="disable_inline_styles">
                 ';

                $pagesX[0] = 'true';
                $pagesX[1] = 'false';
                foreach ($pagesX as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['disable_inline_styles']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>


            </td></tr></table>
            <br /><br /><br />
            <h1>' . __('Guests', 'wpsc-support-tickets') . '</h1>
            <table class="widefat" style="background:transparent;"><tr><td>

                <p><strong>' . __('Allow Guests', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want Guests to be able to use the support ticket system.', 'wpsc-support-tickets') . '  <br />
                <select name="allow_guests">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['allow_guests']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

                <p><strong>' . __('Show guest email address in front end list', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true and a guest\'s email address will be displayed in the ticket list.', 'wpsc-support-tickets') . '  <br />
                <select name="hide_email_on_frontend_list">
                 ';

                $pagesXn[0] = 'true';
                $pagesXn[1] = 'false';
                foreach ($pagesXn as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['hide_email_on_frontend_list']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

            </td></tr></table>
            <br /><br /><br />
            <h1>' . __('Custom Fields', 'wpsc-support-tickets') . '</h1>
            <table class="widefat" style="background:transparent;"><tr><td>

                <p><strong>' . __('Place custom form fields', 'wpsc-support-tickets') . ':</strong> ' . __('When creating a ticket, this determines where your custom fields are placed on the ticket submission form.', 'wpsc-support-tickets') . '  <br />
                <select name="custom_field_position">
                 ';

                $pagesXX[0]['valname'] = 'before everything';$pagesXX[0]['displayname'] = __('before everything', 'wpsc-support-tickets');
                $pagesXX[1]['valname'] = 'before message';$pagesXX[1]['displayname'] = __('before message', 'wpsc-support-tickets');
                $pagesXX[2]['valname'] = 'after message';$pagesXX[2]['displayname'] = __('after message', 'wpsc-support-tickets');
                $pagesXX[3]['valname'] = 'after everything';$pagesXX[3]['displayname'] = __('after everything', 'wpsc-support-tickets');

                foreach ($pagesXX as $pagg) {
                    $option = '<option value="' . $pagg['valname'] . '"';
                    if ($pagg['valname'] == $devOptions['custom_field_position']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg['displayname'];
                    $option .= '</option>';
                    echo $option;
                }

            echo '
                </select>
                </p>

                <p><strong>' . __('Display custom fields', 'wpsc-support-tickets') . ':</strong> ' . __('When a ticket creator views a ticket they created, this setting determines where the custom fields are placed on the page.', 'wpsc-support-tickets') . '  <br />
                <select name="custom_field_frontend_position">
                 ';

                $pagesXY[0]['valname'] = 'before everything';$pagesXY[0]['displayname'] = __('before everything', 'wpsc-support-tickets');
                $pagesXY[1]['valname'] = 'before message';$pagesXY[1]['displayname'] = __('before message', 'wpsc-support-tickets');
                $pagesXY[2]['valname'] = 'after message';$pagesXY[2]['displayname'] = __('after message &amp; replies', 'wpsc-support-tickets');

                foreach ($pagesXY as $pagg) {
                    $option = '<option value="' . $pagg['valname'] . '"';
                    if ($pagg['valname'] == $devOptions['custom_field_frontend_position']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg['displayname'];
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>
                <br /><br /><br /><br />
            </td></tr></table>


            </div>
            <div id="wst_tabs-2">';

            wpscSupportTickets_settings(); // Action hook

            echo '
                </div>


            <input type="hidden" name="update_wpscSupportTicketsSettings" value="update" />
            <div style="float:right;position:relative;top:-20px;"> <input class="button-primary" style="position:relative;z-index:999999;" type="submit" name="update_wpscSupportTicketsSettings_submit" value="';
            _e('Update Settings', 'wpsc-support-tickets');
            echo'" /></div>


            </div>
            </div>
            </form>


        ';

            if (!function_exists('wpscSupportTicketsPRO')) {
                echo '
                <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery(\'#buypro\').append(\'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="REYXW5BR8H5MU"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>\');
                });
                </script>
            ';
            }
        }

        //Prints out the admin page ================================================================================
        function printAdminPageStats() {

            echo '<div class="wrap">';

            $this->adminHeader();



            if (@!function_exists('wpscSupportTicketsPRO') ) {
                echo '<table class="widefat" style="width:98%;"><tr><td>';
                echo '

                                <h2>'.__('Upgrade now to wpsc Support Tickets PRO and unlock in depth statistics for the following and more:', 'wpsc-support-tickets').'</h2>
                                    <ul>
                                        <li>'.__('Average ticket resolution time', 'wpsc-support-tickets').'</li>
                                        <li>'.__('Number of tickets created in each category', 'wpsc-support-tickets').'</li>
                                        <li>'.__('Number of tickets in each severity level', 'wpsc-support-tickets').'</li>
                                        <li>'.__('Top 10 users who create the most tickets', 'wpsc-support-tickets').'</li>
                                        <li>'.__('The number of completed tickets', 'wpsc-support-tickets').'</li>
                                        <li>'.__('Display how long a ticket has been open', 'wpsc-support-tickets').'</li>
                                        <li>'.__('Display how long it took to resolve a closed ticket', 'wpsc-support-tickets').'</li>
                                        <li>'.__('Bar chart showing the amount of time it took to close the last 30 tickets', 'wpsc-support-tickets').'</li>
                                        <li>'.__('And much more, upgrade to PRO today:', 'wpsc-support-tickets').'</li>
                                    </ul>
                                    <div id="buyprostats"><strong>$19.99 USD</strong><br /></div>


                                ';
                echo '</td></tr></table>
                <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery(\'#buyprostats\').append(\'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="REYXW5BR8H5MU"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>\');
                });
                </script>  ';
            } else {
                if(@function_exists('wstPROStats')) {
                    @set_time_limit(0);
                    echo wstPROStats();
                } else {
                    echo '<table class="widefat" style="width:98%;"><tr><td>';
                    _e('Your version of wpsc Support Tickets is out of date.  Please email admin@wpstorecart.com with your PayPal transaction ID to recieve the latest version.', 'wpsc-support-tickets');
                    echo '</td></tr></table>';
                }

            }



            echo '</div>';

        }

        //Prints out the admin page ================================================================================
        function printAdminPage() {
            global $wpdb;

            $output = '';

            echo '
                        <script type="text/javascript">
                            jQuery(function() {
                                jQuery( "#wst_tabs" ).tabs();
                            });
                        </script>
                        <div class="wrap">';

            $this->adminHeader();

            echo '
                        <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
                            <ul>
                                <li><a href="#wst_tabs-1">' . __('Open', 'wpsc-support-tickets') . '</a></li>
                                <li><a href="#wst_tabs-2">' . __('Closed', 'wpsc-support-tickets') . '</a></li>';

            wpscSupportTickets_extraTabsIndex();
            echo '
                        </ul>

                        ';

            $resolution = 'Open';
            $output .= '<div id="wst_tabs-1">';
            $table_name = $wpdb->prefix . "wpscst_tickets";
            $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='{$resolution}' ORDER BY `last_updated` DESC;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results) && isset($results[0]['primkey'])) {
                if ($resolution == 'Open') {
                    $output .= '<h3>' . __('View Open Tickets:', 'wpsc-support-tickets') . '</h3>';
                } elseif ($resolution == 'Closed') {
                    $output .= '<h3>' . __('View Closed Tickets:', 'wpsc-support-tickets') . '</h3>';
                }
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('User', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
                foreach ($results as $result) {
                    if ($result['user_id'] != 0) {
                        @$user = get_userdata($result['user_id']);
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }
                    if (trim($result['last_staff_reply']) == '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                        }
                    }
                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td><a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a></td><td>' . date_i18n( get_option( 'date_format' ), $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
                }
                $output .= '</tbody></table>';
            }
            $output .= '</div>';
            echo $output;

            $resolution = 'Closed';
            $output = '<div id="wst_tabs-2">';
            $table_name = $wpdb->prefix . "wpscst_tickets";
            $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='{$resolution}' ORDER BY `last_updated` DESC;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results) && isset($results[0]['primkey'])) {
                if ($resolution == 'Open') {
                    $output .= '<h3>' . __('View Open Tickets:', 'wpsc-support-tickets') . '</h3>';
                } elseif ($resolution == 'Closed') {
                    $output .= '<h3>' . __('View Closed Tickets:', 'wpsc-support-tickets') . '</h3>';
                }
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('User', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
                foreach ($results as $result) {
                    if ($result['user_id'] != 0) {
                        @$user = get_userdata($result['user_id']);
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }
                    if (trim($result['last_staff_reply']) == '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                        }
                    }
                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td><a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a></td><td>' . date_i18n( get_option( 'date_format' ), $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
                }
                $output .= '</tbody></table>';
            }
            $output .= '</div>';
            echo $output;



            wpscSupportTickets_extraTabsContents();

            echo '
                        </div></div>';
        }


        function printAdminPageFields() {
            global $wpdb;

            echo '<div class="wrap">';

            $this->adminHeader();

            if (@isset($_POST['required_info_key']) && @isset($_POST['required_info_name']) && @isset($_POST['required_info_type'])) {
                $arrayCounter = 0;
                $table_name777 = $wpdb->prefix . "wpstorecart_meta";
                foreach ($_POST['required_info_key'] as $currentKey) {
                    $updateSQL = "UPDATE  `{$table_name777}` SET  `value` =  '{$_POST['required_info_name'][$arrayCounter]}||{$_POST['required_info_required_'.$currentKey]}||{$_POST['required_info_type'][$arrayCounter]}' WHERE  `primkey` ={$currentKey};";
                    $wpdb->query($updateSQL);
                    $arrayCounter++;
                }
            }


            echo '<br style="clear:both;" /><br />

            <h2>'.__('','wpsc-support-tickets').'</h2>

             <script type="text/javascript">
                /* <![CDATA[ */

                function addwpscfield() {

                    temp_mc_var = "";

                    jQuery(".custom_field_mc").each(function() {
                        temp_mc_var = temp_mc_var + jQuery(this).val() + ",";
                    });

                    jQuery.ajax({ url: ajaxurl, type:"POST", data:"action=wpsct_add_field&createnewfieldname="+jQuery("#createnewfieldname").val()+"&createnewfieldtype="+jQuery("#createnewfieldtype").val()+"&createnewfieldrequired="+jQuery("input:radio[name=createnewfieldrequired]:checked").val()+"&customfieldmc="+temp_mc_var.toString(), success: function(txt){
                        jQuery("#requiredul").prepend("<li style=\'font-size:90%;cursor:move;background-color: #EDEDED; border: 1px solid #DDDDDD;padding:4px 0 4px 30px;margin:8px;\' id=\'requiredinfo_"+txt+"\'><img onclick=\'delwpscfield("+txt+");\' style=\'cursor:pointer;position:relative;top:4px;\' src=\''.plugins_url().'/wpsc-support-tickets/images/delete.png\' /><input type=\'text\' value=\'"+jQuery("#createnewfieldname").val()+"\' name=\'required_info_name[]\' /><input type=\'hidden\' name=\'required_info_key[]\' value=\'"+txt+"\' /><select name=\'required_info_type[]\' id=\'ri_"+txt+"\'><option value=\'firstname\'>'.__('First name', 'wpsc-support-tickets').'</option><option value=\'lastname\'>'.__('Last name', 'wpsc-support-tickets').'</option><option value=\'shippingaddress\'>'.__('Address', 'wpsc-support-tickets').'</option><option value=\'shippingcity\'>'.__('City', 'wpsc-support-tickets').'</option><option value=\'taxstates\'>'.__('US States', 'wpsc-support-tickets').'</option><option value=\'taxcountries\'>'.__('Countries', 'wpsc-support-tickets').'</option><option value=\'zipcode\'>'.__('Zipcode', 'wpsc-support-tickets').'</option><option value=\'email\'>'.__('Email Address', 'wpsc-support-tickets').'</option><option value=\'input (text)\'>'.__('Input (text)', 'wpsc-support-tickets').'</option><option value=\'input (numeric)\'>'.__('Input (numeric)', 'wpsc-support-tickets').'</option><option value=\'textarea\'>'.__('Input textarea', 'wpsc-support-tickets').'</option><option value=\'dropdown\'>'.__('Input Dropdown list', 'wpsc-support-tickets').'</option><!--<option value=\'checkbox\'>'.__('Input Checkbox', 'wpsc-support-tickets').'</option>--><option value=\'radio\'>'.__('Input Radio button', 'wpsc-support-tickets').'</option><option value=\'separator\'>--- '.__('Separator', 'wpsc-support-tickets').' ---</option><option value=\'header\'>'.__('Header', 'wpsc-support-tickets').' &lt;h2&gt;&lt;/h2&gt;</option><option value=\'text\'>'.__('Text', 'wpsc-support-tickets').' &lt;p&gt;&lt;/p&gt;</option></select><label for=\'required_info_required_"+txt+"\'><input type=\'radio\' id=\'required_info_required_"+txt+"_yes\' name=\'required_info_required_"+txt+"\' value=\'required\' /> '.__('Required', 'wpsc-support-tickets').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for=\'required_info_required_"+txt+"_no\'><input type=\'radio\' id=\'required_info_required_"+txt+"_no\' name=\'required_info_required_"+txt+"\' value=\'optional\' /> '.__('Optional', 'wpsc-support-tickets').'</label></li>");
                        //jQuery("#requiredul").prepend("<li style=\'font-size:90%;cursor:move;background-color: #EDEDED; border: 1px solid #DDDDDD;padding:4px 0 4px 30px;margin:8px;\' id=\'requiredinfo_"+txt+"\'><img onclick=\'delwpscfield("+txt+");\' style=\'cursor:pointer;position:relative;top:4px;\' src=\''.plugins_url().'/wpsc-support-tickets/images/delete.png\' /><input type=\'text\' value=\'"+jQuery("#createnewfieldname").val()+"\' name=\'required_info_name[]\' /><input type=\'hidden\' name=\'required_info_key[]\' value=\'"+txt+"\' /><select name=\'required_info_type[]\' id=\'ri_"+txt+"\'><option value=\'firstname\'>'.__('First name', 'wpsc-support-tickets').'</option><option value=\'lastname\'>'.__('Last name', 'wpsc-support-tickets').'</option><option value=\'shippingaddress\'>'.__('Address', 'wpsc-support-tickets').'</option><option value=\'shippingcity\'>'.__('City', 'wpsc-support-tickets').'</option><option value=\'taxstates\'>'.__('US States', 'wpsc-support-tickets').'</option><option value=\'taxcountries\'>'.__('Countries', 'wpsc-support-tickets').'</option><option value=\'zipcode\'>'.__('Zipcode', 'wpsc-support-tickets').'</option><option value=\'email\'>'.__('Email Address', 'wpsc-support-tickets').'</option><option value=\'input (text)\'>'.__('Input (text)', 'wpsc-support-tickets').'</option><option value=\'input (numeric)\'>'.__('Input (numeric)', 'wpsc-support-tickets').'</option><option value=\'textarea\'>'.__('Input textarea', 'wpsc-support-tickets').'</option><option value=\'dropdown\'>'.__('Input Dropdown list', 'wpsc-support-tickets').'</option><option value=\'checkbox\'>'.__('Input Checkbox', 'wpsc-support-tickets').'</option><option value=\'radio\'>'.__('Input Radio button', 'wpsc-support-tickets').'</option><option value=\'separator\'>--- '.__('Separator', 'wpsc-support-tickets').' ---</option><option value=\'header\'>'.__('Header', 'wpsc-support-tickets').' &lt;h2&gt;&lt;/h2&gt;</option><option value=\'text\'>'.__('Text', 'wpsc-support-tickets').' &lt;p&gt;&lt;/p&gt;</option></select><label for=\'required_info_required_"+txt+"\'><input type=\'radio\' id=\'required_info_required_"+txt+"_yes\' name=\'required_info_required_"+txt+"\' value=\'required\' /> '.__('Required', 'wpsc-support-tickets').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for=\'required_info_required_"+txt+"_no\'><input type=\'radio\' id=\'required_info_required_"+txt+"_no\' name=\'required_info_required_"+txt+"\' value=\'optional\' /> '.__('Optional', 'wpsc-support-tickets').'</label></li>");
                        jQuery("#ri_"+txt).val(jQuery("#createnewfieldtype").val());
                        if(jQuery("input:radio[name=createnewfieldrequired]:checked").val()=="required") {
                            jQuery(\'input[name="required_info_required_\'+txt+\'"][value="required"]\').attr("checked", true);
                        } else {
                            jQuery(\'input[name="required_info_required_\'+txt+\'"][value="optional"]\').attr("checked", true);
                        }

                        jQuery("ri_"+txt).val(jQuery("#createnewfieldname").val());

                    }});
                }

                function delwpscfield(keytodel) {
                    jQuery.ajax({ url: ajaxurl, type:"POST", data:"action=wpsct_del_field&delete="+keytodel, success: function(){
                        jQuery("#requiredinfo_"+keytodel).remove();
                    }});
                }

                function wpstCheckFieldType() {
                    if ( jQuery("#createnewfieldtype").val()=="dropdown" ||  jQuery("#createnewfieldtype").val()=="checkbox" ||  jQuery("#createnewfieldtype").val()=="radio" ) {
                        jQuery(".wpstAddNewAdditionalOptionButton").show();
                        jQuery("#wpsc-support-tickets-custom-field").show();
                    } else {
                        jQuery(".wpstAddNewAdditionalOptionButton").hide();
                        jQuery("#wpsc-support-tickets-custom-field").hide();
                    }
                    return false;
                }

                function wpstCreateNewOption() {
                    jQuery("#wpsc-support-tickets-custom-field").append(\'<div><br />|_ <input name="" class="custom_field_mc" /> <img style="cursor:pointer;position:relative;top:4px;" src="'.plugins_url().'/wpsc-support-tickets/images/delete.png" onclick="jQuery(this).parent().remove();" /></div>\');
                }

                jQuery(document).ready(function(){

                        jQuery(function() {

                                jQuery("#requiredsort ul").sortable({ opacity: 0.6, cursor: \'move\', update: function() {
                                        var order = jQuery(this).sortable("serialize") + "&action=wpsct_sort_fields";
                                        jQuery.post(ajaxurl, order, function(theResponse){
                                                jQuery("#requiredsort ul").sortable(\'refresh\');
                                        });
                                }
                                });

                        wpstCheckFieldType();

                        });


                });

               /* ]]> */
            </script>
            ';

            /**
                 * The options for the checkout fields
                 */
            $theOptionszz[0] = 'firstname';$theOptionszzName[0] = __('First name', 'wpsc-support-tickets');
            $theOptionszz[1] = 'lastname';$theOptionszzName[1] = __('Last name', 'wpsc-support-tickets');
            $theOptionszz[2] = 'shippingaddress';$theOptionszzName[2] = __('Address', 'wpsc-support-tickets');
            $theOptionszz[3] = 'shippingcity';$theOptionszzName[3] = __('City', 'wpsc-support-tickets');
            $theOptionszz[4] = 'taxstates';$theOptionszzName[4] = __('US States', 'wpsc-support-tickets');
            $theOptionszz[5] = 'taxcountries';$theOptionszzName[5] = __('Countries', 'wpsc-support-tickets');
            $theOptionszz[6] = 'zipcode';$theOptionszzName[6] = __('Zipcode', 'wpsc-support-tickets');
            $theOptionszz[7] = 'email';$theOptionszzName[7] = __('Email Address', 'wpsc-support-tickets');
            $theOptionszz[8] = 'input (text)';$theOptionszzName[8] = __('Input (text)', 'wpsc-support-tickets');
            $theOptionszz[9] = 'input (numeric)';$theOptionszzName[9] = __('Input (numeric)', 'wpsc-support-tickets');
            $theOptionszz[10] = 'textarea';$theOptionszzName[10] = __('Input Textarea', 'wpsc-support-tickets');

            $theOptionszz[11] = 'dropdown';$theOptionszzName[11] = __('Input Dropdown list', 'wpsc-support-tickets');
            //$theOptionszz[12] = 'checkbox';$theOptionszzName[12] = __('Input Checkbox', 'wpsc-support-tickets');
            $theOptionszz[12] = 'radio';$theOptionszzName[12] = __('Input Radio button', 'wpsc-support-tickets');

            $theOptionszz[13] = 'separator';$theOptionszzName[13] = __('--- Separator ---', 'wpsc-support-tickets');
            $theOptionszz[14] = 'header';$theOptionszzName[14] = __('Header &lt;h2&gt;&lt;/h2&gt;', 'wpsc-support-tickets');
            $theOptionszz[15] = 'text';$theOptionszzName[15] = __('Text &lt;p&gt;&lt;/p&gt;', 'wpsc-support-tickets');


            echo'
                <form action="#" method="post">
            <table class="widefat">
            <thead><tr><th><h2>'.__('Add New Field', 'wpsc-support-tickets').'</h2><br /><strong>'.__('Name', 'wpsc-support-tickets').': </strong><input type="text" name="createnewfieldname" id="createnewfieldname" value="" /> <br /><strong>'.__('Type', 'wpsc-support-tickets').': </strong><select name="createnewfieldtype" id="createnewfieldtype" onchange="wpstCheckFieldType();">';

            $icounter = 0;
            foreach ($theOptionszz as $theOption) {

                    $option = '<option value="'.$theOption.'"';
                    $option .='>';
                    $option .= $theOptionszzName[$icounter];
                    $option .= '</option>';
                    echo $option;
                    $icounter++;
            }

            echo '</select>  <button style="display:none;" id="wpstAddNewAdditionalOptionButton" class="button-secondary wpstAddNewAdditionalOptionButton" onclick="wpstCreateNewOption();return false;">'.__('Add additional option', 'wpsc-support-tickets').'</button>

            <div id="wpsc-support-tickets-custom-field" style="margin-left:40px;display:none;">
                |_ <input name="" class="custom_field_mc" />
            </div>


            <br /><label for="createnewfieldrequired_yes"><input type="radio" id="createnewfieldrequired_yes" name="createnewfieldrequired" value="required" checked="checked" /> '.__('Required', 'wpsc-support-tickets').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="createnewfieldrequired_no"><input type="radio" id="createnewfieldrequired_no" name="createnewfieldrequired" value="optional" /> '.__('Optional', 'wpsc-support-tickets').'</label> <br /> <a href="#" style="margin-left:35%;" onclick="addwpscfield();return false;"> &nbsp;  &nbsp; <button class="button-primary"><img style="cursor:pointer;" src="'.plugins_url().'/wpsc-support-tickets/images/Add.png" /> '.__('Save New Field', 'wpsc-support-tickets').'</button></a></th></tr></thead>

            <tbody><tr><td>
            <div id="requiredsort" style="margin:0 auto 0 auto;">
                <ul id="requiredul" style="margin:0 auto 0 auto;list-style:none;">
                ';

                $table_name33 = $wpdb->prefix . "wpstorecart_meta";
                $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                if(isset($results)) {
                        foreach ($results as $result) {
                            $theKey = $result['primkey'];
                            $exploder = explode('||', $result['value']);
                            echo '<li style="font-size:90%;cursor:move;background-color: #EDEDED; border: 1px solid #DDDDDD;padding:4px 0 4px 30px;margin:8px;" id="requiredinfo_'.$theKey.'"><img onclick="delwpscfield('.$theKey.');" style="cursor:pointer;position:relative;top:4px;" src="'.plugins_url().'/wpsc-support-tickets/images/delete.png" /><input type="text" value="'.$exploder[0];echo '" name="required_info_name[]" /><input type="hidden" name="required_info_key[]" value="'.$theKey.'" /><select name="required_info_type[]">';

                            $icounter = 0;
                            foreach ($theOptionszz as $theOption) {

                                    $option = '<option value="'.$theOption.'"';
                                    if($theOption == $exploder[2]) {
                                            $option .= ' selected="selected"';
                                    }
                                    $option .='>';
                                    $option .= $theOptionszzName[$icounter];
                                    $option .= '</option>';
                                    echo $option;
                                    $icounter++;
                            }

                            echo '</select>';

                            echo '<label for="required_info_required_'.$theKey.'"><input type="radio" id="required_info_required_'.$theKey.'_yes" name="required_info_required_'.$theKey.'" value="required" '; if ($exploder[1]=='required') { echo 'checked="checked"'; }; echo '/> '.__('Required', 'wpsc-support-tickets').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="required_info_required_'.$theKey.'_no"><input type="radio" id="required_info_required_'.$theKey.'_no" name="required_info_required_'.$theKey.'" value="optional" '; if ($exploder[1]=='optional') { echo 'checked="checked"'; }; echo '/> '.__('Optional', 'wpsc-support-tickets').'</label>';

                            // If we're dealing with a dropdown, checkbox, or radio
                            //if ($exploder[2] == 'dropdown' || $exploder[2] == 'radio' || $exploder[2] == 'checkbox' ) {
                            //    echo '<br />';

                            //    echo '|_ <input name="" class="custom_field_mc" /> <button class="button-secondary wpstAddNewAdditionalOptionButtonInEdit" onclick="return false;">'.__('Add additional option', 'wpsc-support-tickets').'</button>';
                            //}

                            echo '</li>
                                ';
                        }
                }

                echo '
                </ul><br />
            </div>
            <br style="clear:both;" />
            <button class="button-primary">'.__('Save All Edits', 'wpsc-support-tickets').'</button>

            </td></tr></tbody></table>

            </form>
            <br style="clear:both;" /><br />';


            echo '</div>';
        }

        //END Prints out the admin page ================================================================================

        function printAdminPageCreateTicket() {

            $devOptions = $this->getAdminOptions();
            $devOptions['disable_inline_styles'] = 'false';

            echo '<div class="wrap">';

            $this->adminHeader();

            echo  '<br style="clear:both;" /><br />';

            echo  '<form action="' . plugins_url('/php/submit_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data">';





            echo  '<input type="hidden" name="admin_created_ticket" value="true" />';
            if (@isset($_POST['guest_email'])) {
                echo  '<input type="hidden" name="guest_email" value="' . esc_sql($_POST['guest_email']) . '" />';
            }
            echo  '<table class="widefat" ';

            echo 'style="width:100%;margin:12px;"';

            echo '><tr><th><img src="' . plugins_url('/images/Chat.png', __FILE__) . '" alt="' . __('Create a New Ticket', 'wpsc-support-tickets') . '" /> ' . __('Create a New Ticket', 'wpsc-support-tickets') . '</th></tr>';

            echo  '<tr><td><h3>' . __('Create ticket on behalf of user', 'wpsc-support-tickets') . ':</h3>';
            echo '<select name="wpscst_ticket_creator_assign" id="wpscst_ticket_creator_assign">';
            global $blog_id;
            $wpscBlogUsers = get_users("blog_id={$blog_id}&orderby=nicename");
            if(isset($wpscBlogUsers[0])) {
                foreach ($wpscBlogUsers as $wpscTempUser) {
                    echo  "<option value=\"{$wpscTempUser->ID}\">". htmlentities($wpscTempUser->display_name)."</option> ";
                }
            }
            echo '</select>';
            echo '</td></tr>';

            if($devOptions['custom_field_position'] == 'before everything') {
                echo  wpsctPromptForCustomFields();
            }

            echo  '<tr><td><h3>' . __('Title', 'wpsc-support-tickets') . '</h3><input type="text" name="wpscst_title" id="wpscst_title" value=""  ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="width:100%;margin:12px;"';
            } echo ' /></td></tr>';

            if($devOptions['custom_field_position'] == 'before message') {
                echo  wpsctPromptForCustomFields();
            }

            echo  '<tr><td><h3>' . __('Your message', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel" ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="display:block;width:100%;"';
            } echo '></div> <textarea name="wpscst_initial_message" id="wpscst_initial_message" ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"';
            } echo '></textarea></td></tr>';

            if($devOptions['custom_field_position'] == 'after message') {
                echo  wpsctPromptForCustomFields();
            }

            if ($devOptions['allow_uploads'] == 'true') {
                echo  '<tr><td><h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></td></tr>';
            }
            $exploder = explode('||', $devOptions['departments']);

            if($devOptions['custom_field_position'] == 'after everything') {
                echo  wpsctPromptForCustomFields();
            }

            echo  '<tr><td><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';
            if (isset($exploder[0])) {
                foreach ($exploder as $exploded) {
                    echo  '<option value="' . $exploded . '">' . $exploded . '</option>';
                }
            }
            echo  '</select><button class="wpscst-button" id="wpscst_cancel" onclick="cancelAdd();return false;"  ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:right;"';
            } echo ' ><img ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:left;border:none;margin-right:5px;"';
            } echo ' src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button><button class="wpscst-button" type="submit" name="wpscst_submit" id="wpscst_submit" ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:right;"';
            }echo '><img ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:left;border:none;margin-right:5px;"';
            } echo ' src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Submit Ticket', 'wpsc-support-tickets') . '" /> ' . __('Submit Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


            echo  '</table></form>';
            echo '</div></div></div></div>';
            echo  '<div style="clear:both;min-height:45px;height:45px;display:block;width:100%;" />&nbsp;</div>'
            . '<style>#wpfooter {position:relative;top:-45px;}</style>';

        }


        function printAdminPageEdit() {
            global $wpdb;

            $output = '';
            $devOptions = $this->getAdminOptions();

            echo '<div class="wrap">';

            $this->adminHeader();

            echo '<br style="clear:both;" /><br />';





            $primkey = intval($_GET['primkey']);

            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results[0])) {
                echo '<table class="widefat"><tr><td>';
                if ($results[0]['user_id'] != 0) {
                    @$user = get_userdata($results[0]['user_id']);
                    $theusersname = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $results[0]['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $user->user_nicename . ' </a>';
                } else {
                    $user = false; // Guest
                    $theusersname = __('Guest', 'wpsc-support-tickets') . ' - <strong>' . $results[0]['email'] . '</strong>';
                }
                echo '<div id="wpscst_meta"><h1>' . base64_decode($results[0]['title']) . '</h1> (' . $results[0]['resolution'] . ' - ' . base64_decode($results[0]['type']) . ')</div>';
                echo '<table class="widefat" style="width:100%;">';
                echo '<thead><tr><th id="wpscst_results_posted_by">' . __('Posted by', 'wpsc-support-tickets') . ' ' . $theusersname . ' (<span id="wpscst_results_time_posted">' . date_i18n( get_option( 'date_format' ), $results[0]['time_posted']) . '</span>)</th></tr></thead>';

                $messageData = strip_tags(base64_decode($results[0]['initial_message']), '<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                $messageData = explode('\\', $messageData);
                $messageWhole = '';
                foreach ($messageData as $messagePart) {
                    $messageWhole .= $messagePart;
                }
                echo '<tbody><tr><td id="wpscst_results_initial_message"><br />' . $messageWhole;

                echo '</tbody></table>';

                // Custom fields
                $table_name33 = $wpdb->prefix . "wpstorecart_meta";

                $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

                $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );
                if(isset($resultscf)) {
                        echo '<table class="widefat"><tbody>';
                        foreach ($resultscf as $field) {
                            $specific_items = explode("||", $field['value']);
                            $res = $wpdb->get_results("SELECT * FROM `{$table_name33}` WHERE `type`='wpsct_custom_{$field['primkey']}' AND `foreignkey`='{$primkey}';", ARRAY_A);
                            if(@isset($res[0]['primkey'])) {
                                echo '<tr><td><h4 style="display:inline;">'.$specific_items[0].':</h4> '.strip_tags(base64_decode($res[0]['value'])).'</td></tr>';

                            }
                        }
                        echo '</tbody></table>';
                }


                $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}' ORDER BY `timestamp` ASC;";
                $result2 = $wpdb->get_results($sql, ARRAY_A);
                if (isset($result2)) {
                    foreach ($result2 as $resultsX) {
                        $styleModifier1 = NULL;
                        $styleModifier2 = NULL;
                        if ($resultsX['user_id'] != 0) {
                            @$user = get_userdata($resultsX['user_id']);
                            @$userdata = new WP_User($resultsX['user_id']);
                            if ($userdata->has_cap('manage_wpsct_support_tickets')) {
                                $styleModifier1 = 'background:#FFF;';
                                $styleModifier2 = 'background:#e5e7fa;" ';
                            }
                            $theusersname = $user->user_nicename;
                        } else {
                            $user = false; // Guest
                            $theusersname = __('Guest', 'wpsc-support-tickets');
                        }

                        echo '<br /><table class="widefat" style="width:100%;' . $styleModifier1 . '">';
                        echo '<thead><tr><th class="wpscst_results_posted_by" style="' . $styleModifier2 . '">' . __('Posted by', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $resultsX['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a> (<span class="wpscst_results_timestamp">' . date_i18n( get_option( 'date_format' ), $resultsX['timestamp']) . '</span>)<div style="float:right;"><a onclick="if(confirm(\'' . __('Are you sure you want to delete this reply?', 'wpsc-support-tickets') . '\')){return true;}return false;" href="' . plugins_url('/php/delete_ticket.php', __FILE__) . '?replyid=' . $resultsX['primkey'] . '&ticketid=' . $primkey . '"><img src="' . plugins_url('/images/delete.png', __FILE__) . '" alt="delete" /> ' . __('Delete Reply', 'wpsc-support-tickets') . '</a></div></th></tr></thead>';
                        $messageData = strip_tags(base64_decode($resultsX['message']), '<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                        $messageData = explode('\\', $messageData);
                        $messageWhole = '';
                        foreach ($messageData as $messagePart) {
                            $messageWhole .= $messagePart;
                        }
                        echo '<tbody><tr><td class="wpscst_results_message"><br />' . $messageWhole . '</td></tr>';
                        echo '</tbody></table>';
                    }
                }
                echo '</td></tr></table>';
            }
            $output .= '
                            <script>
                                jQuery(document).ready(function(){
                                    jQuery(".nicEdit-main").width("100%");
                                    jQuery(".nicEdit-main").parent().width("100%");
                                    jQuery(".nicEdit-main").height("270px");
                                    jQuery(".nicEdit-main").parent().height("270px");
                                    jQuery(".nicEdit-main").parent().css( "background-color", "white" );
                                });
                            </script>
                            ';
            $output .= '<form action="' . plugins_url('/php/reply_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data"><input type="hidden" name="wpscst_is_staff_reply" value="yes" /><input type="hidden" name="wpscst_edit_primkey" value="' . $primkey . '" /><input type="hidden" name="wpscst_goback" value="yes" /> ';
            $output .= '<table class="wpscst-table" style="width:100%;display:none;">';
            $output .= '<tr><td><h3>' . __('Your message', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel2" style="display:block;width:100%;"></div> <textarea name="wpscst_reply" id="wpscst_reply" style="display:block;width:100%;margin:0 auto 0 auto;background-color:#FFF;" rows="5" columns="6"></textarea>';
            $output .= '</td></tr>';

            if (!function_exists('wpscSupportTicketDepartments')) {
                $exploder = explode('||', $devOptions['departments']);
            }

            $output .= '<tr><td><div style="float:left;"><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';

            if (!function_exists('wpscSupportTicketDepartments')) {
                // Old department system
                if (isset($exploder[0])) {
                    foreach ($exploder as $exploded) {
                        $output .= '<option value="' . $exploded . '"';
                        if (base64_decode($results[0]['type']) == $exploded) {
                            $output.= ' selected="selected" ';
                        } $output.='>' . $exploded . '</option>';
                    }
                }
            } else {
                $output.= wpscSupportTicketsPROListDepartments();
            }

            $output .= '</select></div>
                <div style="float:left;margin-left:20px;"><h3>' . __('Severity', 'wpsc-support-tickets') . '</h3>
                    <select name="wpscst_severity">
                        <option value="Low"';
                            if ($results[0]['severity'] == 'Low') {
                                $output.= ' selected="selected" ';
                            } $output.='>
                            ' . __('Low', 'wpsc-support-tickets') . '
                        </option>
                        <option value="Normal"';
                            if ($results[0]['severity'] == 'Normal') {
                                $output.= ' selected="selected" ';
                            } $output.='>
                            ' . __('Normal', 'wpsc-support-tickets') . '
                        </option>
                        <option value="High"';
                            if ($results[0]['severity'] == 'High') {
                                $output.= ' selected="selected" ';
                            } $output.='>
                            ' . __('High', 'wpsc-support-tickets') . '
                        </option>
                        <option value="Urgent"';
                            if ($results[0]['severity'] == 'Urgent') {
                                $output.= ' selected="selected" ';
                            } $output.='>
                            ' . __('Urgent', 'wpsc-support-tickets') . '
                        </option>
                        <option value="Critical"';
                            if ($results[0]['severity'] == 'Critical') {
                                $output.= ' selected="selected" ';
                            } $output.='>
                            ' . __('Critical', 'wpsc-support-tickets') . '
                        </option>
                    </select>
                </div>
                        <div style="float:left;margin-left:20px;"><h3>' . __('Status', 'wpsc-support-tickets') . '</h3><select name="wpscst_status">
                                <option value="Open"';
            if ($results[0]['resolution'] == 'Open') {
                $output.= ' selected="selected" ';
            } $output.='>' . __('Open', 'wpsc-support-tickets') . '</option>
                                <option value="Closed"';
            if ($results[0]['resolution'] == 'Closed') {
                $output.= ' selected="selected" ';
            } $output.='>' . __('Closed', 'wpsc-support-tickets') . '</option>
                        </select></div>
                        <div style="float:left;margin-left:20px;"><h3>' . __('Actions', 'wpsc-support-tickets') . '</h3>
                            <a onclick="if(confirm(\'' . __('Are you sure you want to delete this ticket?', 'wpsc-support-tickets') . '\')){return true;}return false;" href="' . plugins_url('/php/delete_ticket.php', __FILE__) . '?ticketid=' . $primkey . '"><img src="' . plugins_url('/images/delete.png', __FILE__) . '" alt="delete" /> ' . __('Delete Ticket', 'wpsc-support-tickets') . '</a><br />
                            <input type="checkbox" name="wpsctnoemail" id="wpsctnoemail" checked="checked" value="on" /> ' . __('Send email to ticket creator on reply.', 'wpsc-support-tickets') . '
                        </div>';
            if ($devOptions['allow_uploads'] == 'true' && @function_exists('wpscSupportTicketsPRO')) {
                $output .= '<div style="float:left;margin-left:20px;"><h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></div>';
            }
            $output .='
                        <button class="button-secondary" onclick="if(confirm(\'' . __('Are you sure you want to cancel?', 'wpsc-support-tickets') . '\')){window.location = \'' . get_admin_url() . 'admin.php?page=wpscSupportTickets-admin\';}return false;"  style="float:right;" ><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button> <button class="button-primary" type="submit" name="wpscst_submit" id="wpscst_submit" style="float:right;margin:0 5px 0 5px;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Update Ticket', 'wpsc-support-tickets') . '" /> ' . __('Update Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


            $output .= '</table></form>';
            echo $output;

            echo '
                        </div>';
        }

        // Dashboard widget code=======================================================================
        function wpscSupportTickets_main_dashboard_widget_function() {
            global $wpdb;

            $table_name = $wpdb->prefix . "wpscst_tickets";
            $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='Open' ORDER BY `last_updated` DESC;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results) && isset($results[0]['primkey'])) {
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
                foreach ($results as $result) {
                    if ($result['user_id'] != 0) {
                        @$user = get_userdata($result['user_id']);
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }
                    if (trim($result['last_staff_reply']) == '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a>';
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a>';
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                        }
                    }

                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td>' . $last_staff_reply . '</td></tr>';
                }
                $output .= '</tbody></table>';
            } else {
                $output .= '<tr><td><i>' . __('No open tickets!', 'wpsc-support-tickets') . '</i></td><td></td><td></td></tr>';
            }
            echo $output;
        }

        // Create the function use in the action hook
        function wpscSupportTickets_main_add_dashboard_widgets() {
            if (function_exists('current_user_can') && current_user_can('manage_wpsct_support_tickets')) {
                wp_add_dashboard_widget('wpscSupportTickets_main_dashboard_widgets', __('wpscSupportTickets Overview', 'wpsc-support-tickets'), array(&$this, 'wpscSupportTickets_main_dashboard_widget_function'));
            }
        }

        function addHeaderCode() {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            if (@!class_exists('AGCA')) {
                wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
            }
            wp_enqueue_style('plugin_name-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);
        }

        function addFieldsHeaderCode() {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-sortable');
            if (@!class_exists('AGCA')) {
                wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
            }
            wp_enqueue_style('plugin_name-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);
        }

        function addStatsHeaderCode() {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            if (@!class_exists('AGCA')) {
                wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
            }
            wp_enqueue_style('plugin_name-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);

            wp_enqueue_script('wpscstraphael', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/raphael.js', array('jquery'), '1.3.2');
            wp_enqueue_script('wpscstenumerable', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/jquery.enumerable.js', array('jquery'), '1.3.2');
            wp_enqueue_script('wpscsttufte', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/jquery.tufte-graph.js', array('jquery'), '1.3.2');
            wp_enqueue_style('tufte-admin-ui-css', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/tufte-graph.css', false, 2, false);
        }

        function wpscSupportTickets_install($network) {
            global $wpdb;
            if (function_exists('is_multisite') && is_multisite()) {
                if ($network) {
                    $old_blog = $wpdb->blogid;
                    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                    foreach ($blogids as $blog_id) {
                        switch_to_blog($blog_id);
                        $this->addPermissions();
                        $this->wpscSupportTickets_activate();
                    }
                    switch_to_blog($old_blog);
                    return;
                }
            }
            $this->wpscSupportTickets_activate();
        }

        // Installation ==============================================================================================
        function wpscSupportTickets_activate() {
            global $wpdb;
            global $wpscSupportTickets_db_version;

            $table_name = $wpdb->prefix . "wpscst_tickets";
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

                $sql = "
                                CREATE TABLE `{$table_name}` (
                                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                `title` VARCHAR(512) NOT NULL, `initial_message` TEXT NOT NULL,
                                `user_id` INT NOT NULL, `email` VARCHAR(256) NOT NULL,
                                `assigned_to` INT NOT NULL DEFAULT '0',
                                `severity` VARCHAR(64) NOT NULL,
                                `resolution` VARCHAR(64) NOT NULL,
                                `time_posted` VARCHAR(128) NOT NULL,
                                `last_updated` VARCHAR(128) NOT NULL,
                                `last_staff_reply` VARCHAR(128) NOT NULL,
                                `target_response_time` VARCHAR(128) NOT NULL,
                                `type` VARCHAR( 255 ) NOT NULL
                                );
                        ";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }

            $table_name = $wpdb->prefix . "wpscst_replies";
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

                $sql = "
                                CREATE TABLE `{$table_name}` (
                                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                                `ticket_id` INT NOT NULL ,
                                `user_id` INT NOT NULL ,
                                `timestamp` VARCHAR( 128 ) NOT NULL ,
                                `message` TEXT NOT NULL
                                );
                        ";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }

            $table_name = $wpdb->prefix . "wpstorecart_meta";
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

                $sql = "
                                    CREATE TABLE {$table_name} (
                                    `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    `value` TEXT NOT NULL,
                                    `type` VARCHAR(32) NOT NULL,
                                    `foreignkey` INT NOT NULL
                                    );
                                    ";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
            add_option("wpscSupportTickets_db_version", $wpscSupportTickets_db_version);
        }

        // END Installation ==============================================================================================
        // Shortcode =========================================
        function wpscSupportTickets_mainshortcode($atts) {
            global $wpdb;

            $table_name = $wpdb->prefix . "wpscst_tickets";

            $devOptions = $this->getAdminOptions();

            extract(shortcode_atts(array(
                        'display' => 'tickets'
                            ), $atts));

            if (session_id() == "") {
                @session_start();
            };

            if ($display == null || trim($display) == '') {
                $display = 'tickets';
            }

            $output = '';
            switch ($display) {
                case 'tickets': // =========================================================
                    if ($devOptions['allow_guests'] == 'true' && !is_user_logged_in() && !$this->hasDisplayed) {
                        if (@isset($_POST['guest_email'])) {
                            $_SESSION['wpsct_email'] = esc_sql($_POST['guest_email']);
                        }

                        $output .= '<br />
                                                <form name="wpscst-guestform" id="wpscst-guestcheckoutform" action="#" method="post">
                                                    <table>
                                                    <tr><td>' . __('Enter your email address', 'wpsc-support-tickets') . ': </td><td><input type="text" name="guest_email" value="' . $_SESSION['wpsct_email'] . '" /></td></tr>
                                                    <tr><td></td><td><input type="submit" value="' . __('Submit', 'wpsc-support-tickets') . '" class="wpsc-button wpsc-checkout" /></td></tr>
                                                    </table>
                                                </form>
                                                <br />
                                                ';
                    }
                    if (is_user_logged_in() || @isset($_SESSION['wpsct_email']) || @isset($_POST['guest_email'])) {
                        if (!$this->hasDisplayed) {
                            global $current_user;

                            $output .= '<div id="wpscst_top_page" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:inline;"';
                            } $output.='></div><button class="wpscst-button" id="wpscst-new" onclick="jQuery(\'.wpscst-table\').fadeIn(\'slow\');jQuery(\'#wpscst-new\').fadeOut(\'slow\');jQuery(\'#wpscst_edit_div\').fadeOut(\'slow\');jQuery(\'html, body\').animate({scrollTop: jQuery(\'#wpscst_top_page\').offset().top}, 2000);return false;"><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/Add.png', __FILE__) . '" alt="' . __('Create a New Ticket', 'wpsc-support-tickets') . '" /> ' . __('Create a New Ticket', 'wpsc-support-tickets') . '</button><br /><br />';
                            $output .= '<form action="' . plugins_url('/php/submit_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data">';
                            if (@isset($_POST['guest_email'])) {
                                $output .= '<input type="hidden" name="guest_email" value="' . esc_sql($_POST['guest_email']) . '" />';
                            }
                            $output .= '<table class="wpscst-table" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            }
                            $output .='><tr><th><img src="' . plugins_url('/images/Chat.png', __FILE__) . '" alt="' . __('Create a New Ticket', 'wpsc-support-tickets') . '" /> ' . __('Create a New Ticket', 'wpsc-support-tickets') . '</th></tr>';

                            if($devOptions['custom_field_position'] == 'before everything') {
                                $output .= wpsctPromptForCustomFields();
                            }

                            $output .= '<tr><td><h3>' . __('Title', 'wpsc-support-tickets') . '</h3><input type="text" name="wpscst_title" id="wpscst_title" value=""  ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            } $output .=' /></td></tr>';

                            if($devOptions['custom_field_position'] == 'before message') {
                                $output .= wpsctPromptForCustomFields();
                            }

                            $output .= '<tr><td><h3>' . __('Your message', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:block;width:100%;"';
                            } $output.='></div> <textarea name="wpscst_initial_message" id="wpscst_initial_message" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"';
                            } $output.='></textarea></td></tr>';

                            if($devOptions['custom_field_position'] == 'after message') {
                                $output .= wpsctPromptForCustomFields();
                            }

                            if ($devOptions['allow_uploads'] == 'true') {
                                $output .= '<tr><td><h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></td></tr>';
                            }
                            $exploder = explode('||', $devOptions['departments']);

                            if($devOptions['custom_field_position'] == 'after everything') {
                                $output .= wpsctPromptForCustomFields();
                            }

                            $output .= '<tr><td><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';
                            if (isset($exploder[0])) {
                                foreach ($exploder as $exploded) {
                                    $output .= '<option value="' . $exploded . '">' . $exploded . '</option>';
                                }
                            }
                            $output .= '</select>
                            <h3';
                            if ($devOptions['display_severity_on_create'] == 'false') {
                               $output .=' style="display:none;" ';
                            }
                            $output .='>' . __('Severity', 'wpsc-support-tickets') . '</h3><select name="wpscst_severity" id="wpscst_severity"';
                            if ($devOptions['display_severity_on_create'] == 'false') {
                               $output .=' style="display:none;" ';
                            }
                            $output .='>';
                            $output .= '<option value="Low">'. __('Low', 'wpsc-support-tickets') . '</option>
                            <option value="Normal">' . __('Normal', 'wpsc-support-tickets') . '
                            </option>
                            <option value="High">' . __('High', 'wpsc-support-tickets') . '
                            </option>
                            <option value="Urgent">' . __('Urgent', 'wpsc-support-tickets') . '
                            </option>
                            <option value="Critical">' . __('Critical', 'wpsc-support-tickets') . '
                            </option>';
                            $output .= '</select>

                                    <button class="wpscst-button" id="wpscst_cancel" onclick="cancelAdd();return false;"  ';


                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            } $output.=' ><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button><button class="wpscst-button" type="submit" name="wpscst_submit" id="wpscst_submit" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            }$output.='><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Submit Ticket', 'wpsc-support-tickets') . '" /> ' . __('Submit Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


                            $output .= '</table></form>';

                            $output .= '<form action="' . plugins_url('/php/reply_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data"><input type="hidden" value="0" id="wpscst_edit_primkey" name="wpscst_edit_primkey" />';
                            if (@isset($_POST['guest_email'])) {
                                $output .= '<input type="hidden" name="guest_email" value="' . esc_sql($_POST['guest_email']) . '" />';
                            }

                            $output .= '<div id="wpscst_edit_ticket"><div id="wpscst_edit_ticket_inner"><center><img src="' . plugins_url('/images/loading.gif', __FILE__) . '" alt="' . __('Loading', 'wpsc-support-tickets') . '" /></center></div>
                                                    <table ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            } $output.=' id="wpscst_reply_editor_table"><tbody>
                                                    <tr id="wpscst_reply_editor_table_tr1"><td><h3>' . __('Your reply', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel2" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:block;width:100%;"';
                            }$output.='></div> <textarea name="wpscst_reply" id="wpscst_reply" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"';
                            } $output .='></textarea></td></tr>
                                                    <tr id="wpscst_reply_editor_table_tr2"><td>';

                            if ($devOptions['allow_uploads'] == 'true') {
                                $output .= '<h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file">';
                            }

                            if ($devOptions['allow_closing_ticket'] == 'true') {
                                $output .= '
                                                        <select name="wpscst_set_status" id="wpscst_set_status">
                                                                            <option value="Open">' . __('Open', 'wpsc-support-tickets') . '</option>
                                                                            <option value="Closed">' . __('Closed', 'wpsc-support-tickets') . '</option>
                                                                    </select>
                                                        ';
                            }

                            $output .= '<button class="wpscst-button" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            } $output.=' onclick="cancelEdit();return false;"><img src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' /> ' . __('Cancel', 'wpsc-support-tickets') . '</button><button class="wpscst-button" type="submit" name="wpscst_submit2" id="wpscst_submit2" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            } $output.='><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Submit Reply', 'wpsc-support-tickets') . '" /> ' . __('Submit Reply', 'wpsc-support-tickets') . '</button></td></tr>
                                                    </tbody></table>
                                                </div>';
                            $output .= '</form>';

                            // Guest additions here
                            if (is_user_logged_in()) {
                                $wpscst_userid = $current_user->ID;
                                $wpscst_email = $current_user->user_email;
                                $wpscst_username = $current_user->display_name;
                            } else {
                                $wpscst_userid = 0;
                                $wpscst_email = esc_sql($_SESSION['wpsct_email']);
                                if ($devOptions['hide_email_on_frontend_list']=='true') {
                                    $wpscst_username = __('Guest', 'wpsc-support-tickets') . ' (' . $wpscst_email . ')';
                                } else {
                                    $wpscst_username = __('Guest', 'wpsc-support-tickets');
                                }
                            }

                            $output .= '<div id="wpscst_edit_div">';

                            if ($devOptions['allow_all_tickets_to_be_viewed'] == 'true') {
                                $sql = "SELECT * FROM `{$table_name}` ORDER BY `last_updated` DESC;";
                            }
                            if ($devOptions['allow_all_tickets_to_be_viewed'] == 'false') {
                                $sql = "SELECT * FROM `{$table_name}` WHERE `user_id`={$wpscst_userid} AND `email`='{$wpscst_email}' ORDER BY `last_updated` DESC;";
                            }

                            $results = $wpdb->get_results($sql, ARRAY_A);
                            if (isset($results) && isset($results[0]['primkey'])) {
                                $output .= '<h3>' . __('View Previous Tickets:', 'wpsc-support-tickets') . '</h3>';
                                $output .= '<table class="widefat" ';
                                if ($devOptions['disable_inline_styles'] == 'false') {
                                    $output.='style="width:100%"';
                                }$output.='><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr>';
                                foreach ($results as $result) {
                                    if (trim($result['last_staff_reply']) == '') {
                                        if ($devOptions['allow_all_tickets_to_be_viewed'] == 'false') {
                                            $last_staff_reply = __('you', 'wpsc-support-tickets');
                                        } else {
                                            $last_staff_reply = $result['email'];
                                        }
                                    } else {
                                        if ($result['last_updated'] > $result['last_staff_reply']) {
                                            $last_staff_reply = __('you', 'wpsc-support-tickets');
                                        } else {
                                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                                        }
                                    }
                                    if ($devOptions['allow_closing_ticket'] == 'true') {
                                        if ($result['resolution'] == 'Closed') {
                                            $canReopen = 'Reopenable';
                                        } else {
                                            $canReopen = $result['resolution'];
                                        }
                                    } else {
                                        $canReopen = $result['resolution'];
                                    }
                                    $output .= '<tr><td><a href="" onclick="loadTicket(' . $result['primkey'] . ',\'' . $canReopen . '\');return false;" ';
                                    if ($result['resolution'] == strtolower('open') ) {
                                        $resresolution = __('Open', 'wpsc-support-tickets');
                                    } elseif ($result['resolution'] == strtolower('closed') ) {
                                        $resresolution = __('Closed', 'wpsc-support-tickets');
                                    } else {
                                        $resresolution = $result['resolution'];
                                    }
                                    if ($devOptions['disable_inline_styles'] == 'false') {
                                        $output.='style="border:none;text-decoration:none;"';
                                    }$output.='><img';
                                    if ($devOptions['disable_inline_styles'] == 'false') {
                                        $output.=' style="float:left;border:none;margin-right:5px;"';
                                    }$output.=' src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $resresolution . '</td><td>' . date_i18n( get_option( 'date_format' ), $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
                                }
                                $output .= '</table>';
                            }
                            $output .= '</div>';
                        }
                    } else {
                        $output .='<div id="wpscSupportTicketsRegisterMessage">'. __('Please', 'wpsc-support-tickets') . ' <a href="' . wp_login_url(get_permalink()) . '">' . __('log in', 'wpsc-support-tickets') . '</a> ' . __('or', 'wpsc-support-tickets') . ' <a href="' . site_url('/wp-login.php?action=register&redirect_to=' . get_permalink()) . '">' . __('register', 'wpsc-support-tickets') . '</a>.</div>';
                    }



                    break;
            }

            // Jetpack incompatibilities hack
            if (@!file_exists(WP_PLUGIN_DIR . '/jetpack/jetpack.php')) {
                $this->hasDisplayed = true;
            } else {
                @include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                if (@is_plugin_active(WP_PLUGIN_DIR . '/jetpack/jetpack.php')) {

                    if ($this->hasDisplayedCompat == true) {
                        if ($this->hasDisplayedCompat2 == true) {
                            $this->hasDisplayed = true;
                        }
                        $this->hasDisplayedCompat2 = true;
                    }
                    $this->hasDisplayedCompat = true;
                } else {
                    $this->hasDisplayed = true;
                }
            }


            return $output;
        }

        // END SHORTCODE ================================================
    }

    /**
     * ===============================================================================================================
     * End Main wpscSupportTickets Class
     */
}
// The end of the IF statement










/**
 * ===============================================================================================================
 * Initialize the admin panel
 */
if (!function_exists("wpscSupportTicketsAdminPanel")) {

    function wpscSupportTicketsAdminPanel() {
        global $wpscSupportTickets;
        if (!isset($wpscSupportTickets)) {
            return;
        }
        if (function_exists('add_menu_page')) {
            add_menu_page(__('wpsc Support Tickets', 'wpsc-support-tickets'), __('Support Tickets', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-admin', array(&$wpscSupportTickets, 'printAdminPage'), plugins_url() . '/wpsc-support-tickets/images/controller.png', '94.7');
            $newTicketPage = add_submenu_page('wpscSupportTickets-admin', __('Create Ticket', 'wpsc-support-tickets'), __('Create Ticket', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-newticket', array(&$wpscSupportTickets, 'printAdminPageCreateTicket'));
            $settingsPage = add_submenu_page('wpscSupportTickets-admin', __('Settings', 'wpsc-support-tickets'), __('Settings', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-settings', array(&$wpscSupportTickets, 'printAdminPageSettings'));
            $editPage = add_submenu_page(NULL, __('Reply to Support Ticket', 'wpsc-support-tickets'), __('Reply to Support Tickets', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-edit', array(&$wpscSupportTickets, 'printAdminPageEdit'));
            if(@function_exists('wpscSupportTicketDepartments')) { // For wpsc Support Tickets v5.0+
                $departmentsPage = add_submenu_page('wpscSupportTickets-admin', __('Departments', 'wpsc-support-tickets'), __('Departments', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-departments', array(&$wpscSupportTickets, 'printAdminPageDepartments'));
            }
            $statsPage = add_submenu_page('wpscSupportTickets-admin', __('Statistics', 'wpsc-support-tickets'), __('Statistics', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-stats', array(&$wpscSupportTickets, 'printAdminPageStats'));
            if(@function_exists('wstPROStats')) {
                $statsHeaderCode = 'addStatsHeaderCode';
            } else {
                $statsHeaderCode = 'addHeaderCode';
            }
            $fieldsPage = add_submenu_page('wpscSupportTickets-admin', __('Edit User Fields Collected', 'wpsc-support-tickets'), __('User Fields', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-fields', array(&$wpscSupportTickets, 'printAdminPageFields'));
            add_action("admin_print_scripts-$newTicketPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            add_action("admin_print_scripts-$editPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            if(@function_exists('wpscSupportTicketDepartments')) { // For wpsc Support Tickets v5.0+
                add_action("admin_print_scripts-$departmentsPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            }
            add_action("admin_print_scripts-$statsPage", array(&$wpscSupportTickets, $statsHeaderCode));
            add_action("admin_print_scripts-$settingsPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            add_action("admin_print_scripts-$fieldsPage", array(&$wpscSupportTickets, 'addFieldsHeaderCode'));
        }
    }

}

/**
 * ===============================================================================================================
 * END Initialize the admin panel
 */
function wpscLoadInit() {
    load_plugin_textdomain('wpsc-support-tickets', false, '/wpsc-support-tickets/languages/');

    wp_enqueue_script('wpsc-support-tickets', plugins_url() . '/wpsc-support-tickets/js/wpsc-support-tickets.js', array('jquery'));
    $wpscst_params = array(
        'wpscstPluginsUrl' => plugins_url(),
        'wpscstAjaxUrl' => admin_url('admin-ajax.php'),
    );
    wp_localize_script('wpsc-support-tickets', 'wpscstScriptParams', $wpscst_params);
}

/**
 * ===============================================================================================================
 * Call everything
 */
if (class_exists("wpscSupportTickets")) {
    $wpscSupportTickets = new wpscSupportTickets();
}

//Actions and Filters
if (isset($wpscSupportTickets)) {
    //Actions

    register_activation_hook(__FILE__, array(&$wpscSupportTickets, 'wpscSupportTickets_install')); // Install DB schema
    add_action('wpsc-support-tickets/wpscSupportTickets.php', array(&$wpscSupportTickets, 'init')); // Create options on activation

    add_action('wp_dashboard_setup', array(&$wpscSupportTickets, 'wpscSupportTickets_main_add_dashboard_widgets')); // Dashboard widget
    //add_action('wp_head', array(&$wpscSupportTickets, 'addHeaderCode')); // Place wpscSupportTickets comment into header
    add_shortcode('wpscSupportTickets', array(&$wpscSupportTickets, 'wpscSupportTickets_mainshortcode'));

    add_action("wp_print_scripts", array(&$wpscSupportTickets, "addHeaderCode"));
    add_action('init', 'wpscLoadInit'); // Load other languages, and javascript

    add_action('admin_menu', 'wpscSupportTicketsAdminPanel'); // Create admin panel
    add_filter( 'wp_mail_from', array(&$wpscSupportTickets, 'change_mail_from') );
    add_filter( 'wp_mail_from_name', array(&$wpscSupportTickets, 'change_mail_name') );
}
/**
 * ===============================================================================================================
 * Call everything
 */
if (!function_exists('wpscSupportTicketsPRO')) {

    function wstPROSettingsFakeForm() {
        echo '<div style="opacity:0.5;">
<p><center><u><h3>wpsc-Support-Tickets PRO Settings:</h3></u></center></p>
        <p><strong>Allow ticket creators to upload file attachments:</strong> Set this to true if you want ticket creators to be able to upload files.  <br />
        <select name="allow_uploads" disabled>
            <option value="true">true</option><option value="false" selected="selected">false</option>
        </select>
        </p>

        <p><strong>Who can view &amp; administrate all tickets:</strong> Users with the following roles will have full access to edit, reply to, close, re-open, and delete all tickets.  <br />
            <ul>
            <li><input type="checkbox" name="wstpro_admin[]" value="administrator" checked disabled /> Administrator</li><li><input type="checkbox" name="wstpro_admin[]" value="editor" disabled /> Editor</li><li><input type="checkbox" name="wstpro_admin[]" value="author" disabled /> Author</li><li><input type="checkbox" name="wstpro_admin[]"  value="contributor" disabled /> Contributor</li></ul>
        </p>

        <p><strong>Allow users to close and reopen tickets?:</strong> Setting this to true, allows users (and/or guests, if the setting is turned on) to reopen or close tickets that they have permission to view.    <br />
        <select name="allow_closing_ticket" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>

        <p><strong>Send HTML Emails?:</strong> Set this to true if you want emails to be sent in HTML format.  Note that you will need to add HTML markup to the emails in the Settings tab to take advantage of this feature.  <br />
        <select name="allow_html" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>


        <p><strong>Allow everyone to see all tickets?:</strong> Setting this to true, allows all guests and users to view all tickets created by anyone. Do not use this setting if tickets will contain ANY confidential information, and be sure to inform your users that their information is being posted publically.  <br />
        <select name="allow_all_tickets_to_be_viewed" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>

        <p id="wstpro_reply"><strong>Allow everyone to reply to all open tickets?:</strong> Setting this to true, allows users (and/or guests, if the setting is turned on) to reply to all open tickets created by anyone.  Requires the *Allow everyone to see all tickets* setting to be set to True.  Do not use this setting if tickets will contain ANY confidential information, and be sure to inform your users that their information is being posted publically.  <br />
        <select name="allow_all_tickets_to_be_replied" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>     </div>

        <center><div style="border:3px solid red;width:40%;padding:10px;background:#FFF;position:relative;top:-480px;">Upgrade to wpsc Support Tickets PRO to unlock this page of settings, and much more, including:<br /><br /><ul style="text-align:left;font-size:0.8em;font-weight:bold;"><li>File Uploads & attachments (optional)</li><li>Minimum level to access admin panel can be set by admin</li><li>Bulk edit many tickets at a time</li><li>Send HTML Emails</li><li>Optionally allow all users to see all tickets</li><li>Advanced ticketing system with severity, categories, departments</li><li>Optionally allow users to reopen their own tickets</li> <li>Advanced ticket overview in admin panel</li>       <li>3 Premium Wordpress Themes</li>   </ul><br />$19.99 USD<br /><div  id="buypro"></div></div></center>

        ';
    }

    add_action('wpscSupportTickets_settings', 'wstPROSettingsFakeForm');
}
?>
