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
			<input type="text" class="large-text" value="<?php echo esc_attr($form_action->post_content['lead_type']); ?>" name="<?php echo $action_control->get_field_name('lead_type') ?>">
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