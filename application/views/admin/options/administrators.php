<?php if ( ! isset($permission_type)): ?>
	<div class="header">
		<h2><?php echo $title; ?></h2>
	</div>

	<div class="item">
		<?php echo form_open(current_url(), array('id' => 'admin_form')); ?>
			<input type="hidden" name="admin_id" id="admin_id" value="<?php echo @$admin['a_id']; ?>" />

			<table class="form">
				<tr>
					<th><label for="fname">First name</label></th>
					<td><input type="text" name="fname" id="fname" value="<?php echo form_prep(@$admin['a_fname']); ?>" class="text" style="text-transform:capitalize;" /></td>
				</tr>

				<tr>
					<th><label for="sname">Surname</label></th>
					<td><input type="text" name="sname" id="sname" value="<?php echo form_prep(@$admin['a_sname']); ?>" class="text" style="text-transform:capitalize;" /></td>
				</tr>

				<tr>
					<th><label for="email">Email</label></th>
					<td><input type="text" name="email" id="email" value="<?php echo form_prep(@$admin['a_email']); ?>" class="text" style="width:225px;" /></td>
				</tr>

				<tr>
					<th><label for="email_confirmed">Confirm email</label></th>
					<td><input type="text" name="email_confirmed" id="email_confirmed" value="<?php echo form_prep(@$admin['a_email']); ?>" class="text" style="width:225px;" /></td>
				</tr>

				<tr>
					<th><label for="password">Password</label></th>
					<td><input type="password" name="password" id="password" class="text" /></td>
				</tr>

				<tr>
					<th><label for="password_confirmed">Confirm password</label></th>
					<td><input type="password" name="password_confirmed" id="password_confirmed" class="text" /></td>
				</tr>

				<?php echo form_hidden('a_type', '') ?>

				<?php /* CR: Removed. No longer used.
				<tr>
					<th><label for="a_type">Administrator Type</label></th>
					<td>
						<select name="a_type" id="a_type">
							<option value="">- Select -</option>
							<option value="Family" <?php if("Family" == @$admin['a_type']) echo 'selected="selected"'; ?>>Family</option>
							<option value="Service" <?php if("Service" == @$admin['a_type']) echo 'selected="selected"'; ?>>Service</option>
						</select>
					</td>
				</tr> */ ?>

				<tr>
					<th><label for="a_apt_id">Permission Type</label></th>
					<td>
						<select name="a_apt_id" id="a_apt_id">
							<option value="">- Select -</option>
							<?php
								foreach($permission_types as $apt)
								{
									echo '<option value="'. $apt['apt_id'] .'" '. ($apt['apt_id'] == @$admin['a_apt_id'] ? 'selected="selected"' : '' ) .'>'. $apt['apt_name'] .'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<?php if($this->session->userdata('a_master') == 1 ) : ?>
					<tr>
						<th><label for="master">Master admin</label></th>
						<td><input type="checkbox" name="master" id="master" value="1" /></td>
					</tr>
				<?php endif; ?>

				<tr class="vat">
					<th><label for="verified">Account &amp; e-mail veririfed</label></th>
					<td>
						<?php
							if(isset($admin['a_verified']) && $admin['a_verified'] == 1)
							{
								echo 'Yes';
							}
							else if (isset($admin['a_verified']) && $admin['a_verified'] == 0 && $admin['a_id'])
							{
								echo 'No - <a href="'. current_url() .'?verify='. $admin['a_id'] .'">click here to verify</a>';
							}
							else
							{
								echo '<em>An e-mail will be sent to the new administrator <br />with a link to verify their address.</em>';
							}
						?>
					</td>
				</tr>

				<tr>
					<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/<?php echo (@$admin ? 'update' : 'add-new'); ?>.png" alt="Save" id="save" /></td>
				</tr>
			</table>
		</form>
	</div>

	<div class="header">
		<h2>Administrators</h2>
	</div>

	<div class="item results">
		<?php if($admins) : ?>
			<table class="results">
				<tr class="order">
					<th>Name</th>
					<th>Email</th>
					<th>Last login</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>

				<?php foreach($admins as $admin) : ?>
					<tr class="row">
						<td><?php echo $admin['a_fname'] . ' ' . $admin['a_sname']; ?></td>
						<td><?php echo $admin['a_email']; ?></td>
						<td><?php echo $admin['a_datetime_last_login_format']; ?></td>
						<td class="action"><a href="/admin/options/administrators/<?php echo $admin['a_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
						<td class="action"><a href="?delete=<?php echo $admin['a_id']; ?>" class="action" title="Are you sure you want to delete <?php echo $admin['a_fname']; ?> as an administrator?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else : ?>
			<p class="no_results">There are no other administrators.</p>
		<?php endif; ?>
	</div>
<?php endif; ?>

<div class="header">
	<h2>Add/Edit Permission Types</h2>
</div>

<div class="item">
		<?php echo form_open(current_url(), array('id' => 'admin_permissions_form')); ?>
			<input type="hidden" name="apt_id" id="apt_id" value="<?php echo @$permission_type['apt_id']; ?>" />

			<table class="form">
				<tr>
					<th><label for="apt_name">Name/Label</label></th>
					<td colspan="3"><input type="text" name="apt_name" id="apt_name" value="<?php echo form_prep(@$permission_type['apt_name']); ?>" class="text" style="text-transform:capitalize; width:250px;" /></td>
				</tr>
				<tr>
					<th colspan="2" class="vat" style="text-align:center">Client Records</th>
					<th colspan="2" class="vat" style="text-align:center">Family Records</th>
				</tr>
				<tr>
					<th><label for="apt_can_read_client">Read</label></th>
					<td><input type="checkbox" name="apt_can_read_client" id="apt_can_read_client" value="1" <?php if(isset($permission_type['apt_can_read_client']) && $permission_type['apt_can_read_client'] == 1) { echo 'checked="checked"'; } ?> /></td>
					<th><label for="apt_can_read_family">Read</label></th>
					<td><input type="checkbox" name="apt_can_read_family" id="apt_can_read_family" value="1" <?php if(isset($permission_type['apt_can_read_family']) && $permission_type['apt_can_read_family'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<th><label for="apt_can_edit_client">Edit</label></th>
					<td><input type="checkbox" name="apt_can_edit_client" id="apt_can_edit_client" value="1" <?php if(isset($permission_type['apt_can_edit_client']) && $permission_type['apt_can_edit_client'] == 1) { echo 'checked="checked"'; } ?> /></td>
					<th><label for="apt_can_edit_family">Edit</label></th>
					<td><input type="checkbox" name="apt_can_edit_family" id="apt_can_edit_family" value="1" <?php if(isset($permission_type['apt_can_edit_family']) && $permission_type['apt_can_edit_family'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<th><label for="apt_can_add_client">Add</label></th>
					<td><input type="checkbox" name="apt_can_add_client" id="apt_can_add_client" value="1" <?php if(isset($permission_type['apt_can_add_client']) && $permission_type['apt_can_add_client'] == 1) { echo 'checked="checked"'; } ?> /></td>
					<th><label for="apt_can_add_family">Add</label></th>
					<td><input type="checkbox" name="apt_can_add_family" id="apt_can_add_family" value="1" <?php if(isset($permission_type['apt_can_add_family']) && $permission_type['apt_can_add_family'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<th><label for="apt_can_approve_client">Approve</label></th>
					<td><input type="checkbox" name="apt_can_approve_client" id="apt_can_approve_client" value="1" <?php if(isset($permission_type['apt_can_approve_client']) && $permission_type['apt_can_approve_client'] == 1) { echo 'checked="checked"'; } ?> /></td>
					<th><label for="apt_can_approve_family">Approve</label></th>
					<td><input type="checkbox" name="apt_can_approve_family" id="apt_can_approve_family" value="1" <?php if(isset($permission_type['apt_can_approve_family']) && $permission_type['apt_can_approve_family'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr class="vat" style="height:45px;">
					<th><label for="apt_can_delete_client">Delete</label></th>
					<td><input type="checkbox" name="apt_can_delete_client" id="apt_can_delete_client" value="1" <?php if(isset($permission_type['apt_can_delete_client']) && $permission_type['apt_can_delete_client'] == 1) { echo 'checked="checked"'; } ?> /></td>
					<th><label for="apt_can_delete_family">Delete</label></th>
					<td><input type="checkbox" name="apt_can_delete_family" id="apt_can_delete_family" value="1" <?php if(isset($permission_type['apt_can_delete_family']) && $permission_type['apt_can_delete_family'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<th><label for="apt_can_manage_options">Manage Options</label></th>
					<td><input type="checkbox" name="apt_can_manage_options" id="apt_can_manage_options" value="1" <?php if(isset($permission_type['apt_can_manage_options']) && $permission_type['apt_can_manage_options'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<th><label for="apt_can_manage_admins">Manage Admins</label></th>
					<td><input type="checkbox" name="apt_can_manage_admins" id="apt_can_manage_admins" value="1" <?php if(isset($permission_type['apt_can_manage_admins']) && $permission_type['apt_can_manage_admins'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<th><label for="apt_reports">Access Reports</label></th>
					<td><input type="checkbox" name="apt_reports" id="apt_reports" value="1" <?php if(isset($permission_type['apt_reports']) && $permission_type['apt_reports'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<th><label for="apt_can_unpublish">Unpublish Journeys</label></th>
					<td><input type="checkbox" name="apt_can_unpublish" id="apt_can_unpublish" value="1" <?php if(isset($permission_type['apt_can_unpublish']) && $permission_type['apt_can_unpublish'] == 1) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2"><input type="image" src="/img/btn/<?php echo (@$permission_type ? 'update' : 'add-new'); ?>.png" alt="Save" id="save" /></td>
				</tr>
				<tr>
					<td colspan="4"><small>Note: if users are logged in, permission changes will not take affect until they next login.</small></td>
				</tr>
			</table>
	<?php echo form_close() ?>
</div>

<div class="header">
	<h2>Permission Types</h2>
</div>

<div class="item results">
	<?php if($permission_types) : ?>
		<table class="results">
			<tr class="order">
				<th>Name</th>
				<th>Client Records</th>
				<th>Family Records</th>
				<th>Manage Options</th>
				<th>Manage Admins</th>
				<th>Unpublish Journeys</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
				<?php foreach($permission_types as $apt) : ?>
					<tr class="row">
						<td><?php echo $apt['apt_name']; ?><?php if($apt['apt_id'] == 0) {echo ' *';} ?></td>
						<td><?php
								if($apt['apt_can_read_client'] == 1) {echo 'R/';} else {echo '-/';}
								if($apt['apt_can_edit_client'] == 1) {echo 'E/';} else {echo '-/';}
								if($apt['apt_can_add_client'] == 1) {echo 'A/';} else {echo '-/';}
								if($apt['apt_can_approve_client'] == 1) {echo 'A/';} else {echo '-/';}
								if($apt['apt_can_delete_client'] == 1) {echo 'D';} else {echo '-';}
						 ?></td>
						<td><?php
								if($apt['apt_can_read_family'] == 1) {echo 'R/';} else {echo '-/';}
								if($apt['apt_can_edit_family'] == 1) {echo 'E/';} else {echo '-/';}
								if($apt['apt_can_add_family'] == 1) {echo 'A/';} else {echo '-/';}
								if($apt['apt_can_approve_family'] == 1) {echo 'A/';} else {echo '-/';}
								if($apt['apt_can_delete_family'] == 1) {echo 'D';} else {echo '-';}
						 ?></td>
						<td><?php
								if($apt['apt_can_manage_options'] == 1) {echo 'Yes';} else {echo 'No';}
						 ?></td>
						<td><?php
								if($apt['apt_can_manage_admins'] == 1) {echo 'Yes';} else {echo 'No';}
						 ?></td>
						<td><?php
								if($apt['apt_can_unpublish'] == 1) {echo 'Yes';} else {echo 'No';}
						 ?></td>
						<td class="action"><a href="/admin/options/administrators/?apt_id=<?php echo $apt['apt_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
						<td class="action"><a href="?apt_id=<?php echo $apt['apt_id']; ?>&delete_apt=<?php echo $apt['apt_id']; ?>" class="action" title="Are you sure you want to delete this permission type?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
					</tr>
				<?php endforeach; ?>
			<tr>
				<td colspan="7" style="padding-left:10px"><small>* Default permission type for administrators</small></td>
			</tr>
		</table>
	<?php else : ?>
		<p class="no_results">There are no permission types defined.</p>
	<?php endif; ?>
</div>
