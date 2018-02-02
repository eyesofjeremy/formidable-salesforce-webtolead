<?php
/*
Plugin Name: Formidable to SalesForce quick!
Description: Save your leads to SalesForce. Based heavily on code from Formidable team, Strategy 11. Fields are hard-coded into this plugin; if you need to edit **anything**, you will have to dig into the code.
Version: 1.1
Author URI: http://jeremycarlson.com
Author: Jeremy Carlson
*/

// More info: https://formidableforms.com/help-desk/salesforce-plugin/#comment-15620

// add_action('frm_after_create_entry', 'frm_save_sf_lead' , 20, 2);

// uses regex that accepts any word character or hyphen in last name
function frm_save_sf_split_name($name) {
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
    return array($first_name, $last_name);
}

function frm_save_sf_lead($entry_id, $form_id){

  $post = array();
  $push_to_salesforce = FALSE;

  // Check form ID to see how to set up lead info.
  switch( $form_id ) {
  
    case 4: // ID of "Stay in the Loop" Lead Form

      // Set up fields specific to this form
      // In this case we have one name field, so this is a quick split of the field into two names 
      $full_name = frm_save_sf_split_name( $_POST['item_meta'][18] ); // ID of full name field
      
      $post['first_name']     = $full_name[0];
      $post['last_name']      = $full_name[1];
      $post['email']          = $_POST['item_meta'][19]; // ID of the email field
      
      $post['Lead_Type__c']      = 'Prospect'; // Field key is Lead Type in SalesForce for this client
      $post['IDStatus__c']       = 'Basecamp - Residential Lead/Keep in Loop'; // Field key is ID Status in SalesForce for this client

      $push_to_salesforce = TRUE; // Yep, want to send to SalesForce
      break;
      
    case 2: // ID of Basecamp Deposit Form

      // Set up fields specific to this form
      // In this case we have one name field, so this is a quick split of the field into two names 
      $full_name = frm_save_sf_split_name( $_POST['item_meta'][9] ); // ID of full name field
      
      $post['first_name']     = $full_name[0];
      $post['last_name']      = $full_name[1];
      $post['email']          = $_POST['item_meta'][10]; // ID of the email field
      
      // TODO? Add Address information. Would require knowing how we parse the address field in Formidable,
      // And also getting right field keys in SalesForce.
      // A list of field keys can be found in Name (top right) > Setup > Customize > Leads > Fields
      // $post['street']         = $_POST['item_meta'][80]; // ID of ADDRESS field
      
      $post['Lead_Type__c']      = 'Buyer'; // Field key is Lead Type in SalesForce for this client
      $post['IDStatus__c']       = 'Basecamp - S&amp;R Priority List $250 Deposit'; // Field key is ID Status in SalesForce for this client

      $push_to_salesforce = TRUE; // Yep, want to send to SalesForce
      break;
      
    default:
      break; // Don't do nothin'.
      
  }

  // Okay, so let's see if we are saving the SF data
  if( $push_to_salesforce ) {
    
    $salesforce_url = 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
    
    // $post['Project__c']       = 'Basecamp'; // This does not seem to be working. And looks like it won't: https://www.google.com/search?q=web-to-lead+salesforce+lookup 
    $post['oid']              = '00DA0000000Zayi'; //set your OID number here
    $post['lead_source']      = 'Basecamp Website'; //This is a dropdown in SF so want to keep this generic
    $post['debug']            = 0;

    // Set SSL verify to false because of server issues.
    $args = array(     
      'body'         => $post,
      'headers'     => array(
        'user-agent' => 'Formidable to Salesforce plugin - WordPress; '. get_bloginfo('url')
      ),
      'sslverify'    => false,  
    );

    $result = wp_remote_post($salesforce_url, $args);
  }
}

// TODO: Show a notice, at least, letting admin know that a particular form is posting to SalesForce.
// Don't have that working yet. Not sure this is the right hook, etc.
// add_action('frm_additional_form_options', 'frm_save_sf_notice' , 20, 2);

function frm_save_sf_notice($entry_id, $form_id){
  // Check form ID.
  switch( $form_id ) {
  
    case 9:
    case 8:
      print('This form pushes data to SalesForce. To edit SalesForce settings, you will need to edit the Formidable to SalesForce Quick plugin.');
      break;
      
    default:
      break;
  }
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
  $split_name           = frm_save_sf_split_name( $_POST['item_meta'][ $full_name_field ] );
      
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
  
  print_R( $args );
  die;
  // $result = wp_remote_post($salesforce_url, $args);

  
}

?>