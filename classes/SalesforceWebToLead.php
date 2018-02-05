<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 2/1/18
 * Time: 11:36 AM
 */

class SalesforceWebToLead extends FrmFormAction {

  function __construct( $id_base = 'salesforcewebtolead', $name = 'Salesforce Web-to-Lead' ) {
    $action_ops = array(
      'classes'   => 'dashicons dashicons-groups',
      'limit'     => 99,
      'active'    => true,
      'priority'  => 50,
    );
    
    $this->FrmFormAction($id_base, $name, $action_ops);
  }

  /**
   * Get the HTML for your action settings
   */
  function form( $form_action, $args = array() ) {

    extract($args);
    $action_control = $this;
    $form_fields = $this->get_field_options( $args['form']->id );
    $form_post_content = $form_action->post_content;
    ?>
  <table class="form-table frm-no-margin">
    <tbody>
      <tr>
        <th>
          <label>Organization ID</label>
        </th>
        <td>
          <input type="text" class="large-text" value="<?php echo esc_attr($form_action->post_content['oid']); ?>" name="<?php echo $action_control->get_field_name('oid') ?>"><br>
          <span class="description">Your Organization ID can be found in Salesforce by clicking on your name (upper right), then Setup > Company Profile > Company Information. It's listed under "Organization Detail".</span>
        </td>
      </tr>
      <tr>
        <th>
          <label>Lead Type</label>
        </th>
        <td>
            <select name="<?php echo $action_control->get_field_name['lead_type']; ?>">
              <option value=""><?php _e( '&mdash; Select &mdash;' ) ?></option>
              <option value="Contact" <?php selected( $form_action->post_content['lead_type'], 'Contact', true ); ?>>Contact</option>
              <option value="Lead" <?php selected( $form_action->post_content['lead_type'], 'Lead', true ); ?>>Lead</option>
            </select>
        </td>
      </tr>
      <tr>
        <th>
          <label>Lead Source</label>
        </th>
        <td>
          <input type="text" class="large-text" value="<?php echo esc_attr($form_action->post_content['lead_source']); ?>" name="<?php echo $action_control->get_field_name('lead_source') ?>"><br>
          <span class="description">(Optional) Note: values will populate a picklist in Salesforce which may get unwieldy if this is too specific for different forms or entries.</span>
        </td>
      </tr>
      <tr>
        <th>
          <label>IDStatus</label>
        </th>
        <td>
          <input type="text" class="large-text" value="<?php echo esc_attr($form_action->post_content['id_status']); ?>" name="<?php echo $action_control->get_field_name('id_status') ?>"><br>
          <span class="description">(Optional) This should be a value already set up in your list.</span>
        </td>
      </tr>
      <tr>
        <th>
          <label>Full Name</label>
        </th>
        <td>
          <?php $this->select_field_for('full_name', $form_post_content, $action_control, $form_fields); ?>
        </td>
      </tr>
      <tr>
        <th>
          <label>Email Address</label>
        </th>
        <td>
          <?php $this->select_field_for('email', $form_post_content, $action_control, $form_fields); ?>
        </td>
      </tr>
      <tr>
        <th>
          <label>Phone Number</label>
        </th>
        <td>
          <?php $this->select_field_for('phone', $form_post_content, $action_control, $form_fields); ?>
        </td>
      </tr>
    </tbody>
  </table>
  <?php
  // If you have scripts to include, you can include theme here
  
  }
  
  private function select_field_for( $action_name, $form_post_content, $action_control, $form_fields ) {
    ?>
    <select name="<?php echo esc_attr( $action_control->get_field_name( $action_name ) ) ?>">
      <option value=""><?php _e( '&mdash; Select &mdash;' ) ?></option>
      <?php
      $selected = false;
      foreach ( $form_fields as $field ) {
        if ( $form_post_content[ $action_name ] == $field->id ) {
          $selected = true;
        }
        ?>
        <option value="<?php echo esc_attr( $field->id ) ?>" <?php selected( $form_post_content[ $action_name ], $field->id ) ?>><?php
          echo esc_attr( FrmAppHelper::truncate( $field->name, 50, 1 ) );
          unset( $field );
          ?></option>
        <?php
      }
      ?>
    </select>
<?php
  }
  
  private function get_field_options( $form_id ) {
    $form_fields = FrmField::getAll( array(
      'fi.form_id' => absint( $form_id ),
      'fi.type not' => array( 'divider', 'end_divider', 'html', 'break', 'captcha', 'rte', 'form' ),
    ), 'field_order' );
    return $form_fields;
  }
  
  /**
  * Add the default values for your options here
  */
  function get_defaults() {
    return array(
      'oid'         => '',
      'lead_type'   => 'Lead',
      'lead_source' => 'Website',
      'id_status'   => '',
      'full_name'   => '',
      'email'       => '',
      'phone'       => '',
    );
  }
}