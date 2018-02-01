<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 2/1/18
 * Time: 11:36 AM
 */

class SalesforceWebToLead extends FrmFormAction {

  function __construct( $id_base = 'salesforce_webtolead', $name = 'Salesforce Web-to-Lead' ) {
    $action_ops = array(
      'classes'   => 'dashicons dashicons-format-aside',
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
  ?>
  <table class="form-table frm-no-margin">
    <tbody>
      <tr>
        <th>
          <label>Template name</label>
        </th>
        <td>
          <input type="text" class="large-text" value="<?php echo esc_attr($form_action->post_content['template_name']); ?>" name="<?php echo $action_control->get_field_name('template_name') ?>">
        </td>
      </tr>
      <tr>
        <th>
          <label>Content</label>
        </th>
        <td>
          <textarea class="large-text" rows="5" cols="50" name="<?php echo $action_control->get_field_name('my_content') ?>"><?php echo esc_attr($form_action->post_content['my_content']); ?></textarea>
        </td>
      </tr>
    </tbody>
  </table>
  <?php
  // If you have scripts to include, you can include theme here
  
  }

  /**
  * Add the default values for your options here
  */
  function get_defaults() {
    return array(
      'template_name' => '',
      'my_content'=> '',
    );
  }
}