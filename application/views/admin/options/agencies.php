<div class="header">
	<h2><?php echo (@$ag ? 'Update' : 'Add'); ?> agency</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'ag_form')); ?>
		
		<table class="horizontal_form">
		
			<tr>
				<td>
					<label for="ag_name">Name of agency</label>
					<input type="text" name="ag_name" id="ag_name" class="text" value="<?php echo form_prep(@$ag['ag_name']); ?>" style="width:350px;" />
				</td>
				
				<td>
					<label for="ag_agt_id">Type of agency</label>
					<select name="ag_agt_id" id="ag_agt_id">
						<option value="">-- Please select --</option> 
						<?php foreach($ag_types as $code => $ag_type) : ?>  
						<option value="<?php echo $code; ?>" <?php if($code == @$ag['ag_agt_id']) echo 'selected="selected"'; ?>><?php echo $ag_type; ?></option>
						<?php endforeach; ;?>             
					</select>
				</td>
				   
				<td><a href="/admin/options/agencies"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>
					
				<td><input type="image" src="/img/btn/<?php echo (@$ag ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
				
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

	<?php if($agencies) : ?>
	
	<table class="results">
		
		<tr class="order">
			<th>Name</th>
			<th>Type</th>
			<th>Edit</th>
			<th>Delete</th>
		</tr>
		
		<?php foreach($agencies as $agency) : ?>
		<tr class="row">
			<td><?php echo $agency['ag_name']; ?></td>
			<td><?php echo $ag_types[$agency['ag_agt_id']]; ?>
			<td class="action"><a href="/admin/options/agencies/<?php echo $agency['ag_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
			<td class="action"><a href="?delete=<?php echo $agency['ag_id']; ?>" class="action" title="Are you sure you want to delete <?php echo $agency['ag_name']; ?> as an agency?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
		</tr>
		<?php endforeach; ?>
	
	</table>
	
	<?php else : ?>
	
	<p class="no_results">No results.</p>
	
	<?php endif; ?>

</div>