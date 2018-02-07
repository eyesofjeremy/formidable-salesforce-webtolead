<?php
/**
 * SalesforceWebToLead
 *
 * @description: extension of Formidable FrmFormAction specific to this plugin
 * @since: 1.2
 * @created: 2/1/18
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
  
  public static function path() {
      return dirname( dirname( __FILE__ ) );
  }

  /**
   * Get the HTML for your action settings
   */
  function form( $form_action, $args = array() ) {

    extract($args);
    $action_control = $this;
    $form_fields = $this->get_field_options( $args['form']->id );
    $form_post_content = $form_action->post_content;

    include( SalesforceWebToLead::path() . '/views/form.php' );
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
      'lead_type'   => '',
      'lead_source' => 'Website',
      'id_status'   => '',
      'full_name'   => '',
      'first_name'  => '',
      'last_name'   => '',
      'email'       => '',
      'phone'       => '',
    );
  }
}