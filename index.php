<?php
/*
Plugin Name: Formidable SalesForce Web-To-Lead
Description: Save Leads to SalesForce using your Formidable Forms
Version: 1.2
Author URI: http://jeremycarlson.com
Author: Jeremy Carlson
*/

// More info: https://formidableforms.com/help-desk/salesforce-plugin/#comment-15620

// uses regex that accepts any word character or hyphen in last name
function frm_save_sf_split_name($name) {
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
    return array($first_name, $last_name);
}

add_action('frm_registered_form_actions', 'register_salesforcewebtolead_action');
function register_salesforcewebtolead_action( $actions ) {
  
  $actions['salesforcewebtolead'] = 'SalesforceWebToLead';

  include_once( dirname( __FILE__ ) . '/classes/SalesforceWebToLead.php');
  
  return $actions;
}

add_action('frm_trigger_salesforcewebtolead_create_action', 'sfw2l_create_action_trigger', 10, 3);
function sfw2l_create_action_trigger($action, $entry, $form) {
  salesforce_webtolead_go( $action, $entry, $form );
}

add_action('frm_trigger_salesforcewebtolead_update_action', 'sfw2l_update_action_trigger', 10, 3);
function sfw2l_update_action_trigger($action, $entry, $form) {
  salesforce_webtolead_go( $action, $entry, $form );
}

function salesforce_webtolead_go( $action, $entry, $form ) {
  $settings = $action->post_content;
  
  
  $post = array();
  
  $email      = $_POST['item_meta'][ $settings['email'] ];
  $oid        = $settings['oid'];
  
  // If email or organization are empty, can't do anything.
  if( empty( $email ) || empty( $oid ) ) {
  
    // TODO: send debug message to [admin_email]
    return;
  }
  
  $full_name            = $_POST['item_meta'][ $settings['full_name'] ];
  $phone                = $_POST['item_meta'][ $settings['phone'] ];
  $lead_type            = $settings['lead_type'];
  $id_status            = $settings['id_status'];
  
  // Split the name for saving purposes.
  $split_name           = frm_save_sf_split_name( $full_name );
      
  $post['first_name']   = $split_name[0];
  $post['last_name']    = $split_name[1];
  $post['email']        = $email;
  $post['phone']        = $phone;
      
  $post['Lead_Type__c'] = $lead_type;
  $post['IDStatus__c']  = $id_status;
  
  $salesforce_url       = 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
  
  // $post['Project__c']       = 'Basecamp'; // This does not seem to be working. And looks like it won't: https://www.google.com/search?q=web-to-lead+salesforce+lookup
  $post['oid']          = $oid;
  $post['lead_source']  = $settings['lead_source'];
  $post['debug']        = 0;
  
  // Set SSL verify to false because of server issues.
  $args = array(
    'body'         => $post,
    'headers'     => array(
      'user-agent' => 'Formidable to Salesforce plugin - WordPress; '. get_bloginfo('url')
    ),
    'sslverify'    => false,
  );
  
  $result = wp_remote_post($salesforce_url, $args);

  return $result;
}

?>