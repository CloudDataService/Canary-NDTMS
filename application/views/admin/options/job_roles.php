<div class="header">
	<h2><?php echo (empty($jr) ? 'Add' : 'Update') ?> Job Role</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'jr_form'), array('jr_active' => 1)); ?>

		<table class="horizontal_form">

			<tr>
				<td>
					<label for="jr_title">Name of job role</label>
					<input type="text" name="jr_title" id="jr_title" class="text" value="<?php echo set_value('jr_title', element('jr_title', $jr)); ?>" />
				</td>

				<td><a href="/admin/options/job-roles"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>

				<td><input type="image" src="/img/btn/<?php echo (empty($jr) ? 'add-new' : 'update') ?>.png" alt="Save" /></td>

			</tr>

		</table>

	<?php echo form_close(); ?>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if ($job_roles) : ?>

	<table class="results">

		<tr class="order">
			<th>Name</th>
			<th>Edit</th>
			<th>Delete</th>
		</tr>

		<?php foreach ($job_roles as $job_role) : ?>
		<tr class="row">
			<td><?php echo $job_role['jr_title']; ?></td>
			<td class="action"><a href="/admin/options/job-roles/<?php echo $job_role['jr_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
			<td class="action"><a href="?delete=<?php echo $job_role['jr_id']; ?>" class="action" title="Are you sure you want to delete the <?php echo $job_role['jr_title']; ?> job role?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
		</tr>
		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">No results.</p>

	<?php endif; ?>

</div>