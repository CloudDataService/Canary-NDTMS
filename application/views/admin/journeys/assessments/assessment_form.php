<style>
	#assessment_form_table th { width: 250px; }
</style>

<div id="assessment" title="Assessment form" style="display:none;">
	
	<?php echo form_open('/admin/assessments/set_assessment/' . $journey['j_id'], array('id' => 'assessment_form')); ?>
		
		<table class="horizontal_form">
			<tr>
				<td style="vertical-align: middle; padding-left: 0;"><label>Choose criteria from list: </label></td>
				<td>
					<?php
					$acls_list = array('' => '-- Select --');
					$acls_list += $acls_dropdown;
					echo form_dropdown('jas[jas_acl_id]', $acls_list, NULL, 'id="acl_id"');
					?>
				</td>
			</tr>
		</table>
		
		<hr size="1">
		<br>
		
		<table class="form" id="assessment_form_table">
			
			<tbody class="date">
				<tr class="jas_date">
					<th style="width: 50%"><label for="jas_date">Assessment date</label></th>
					<td>
						<input type="text" name="jas[jas_date]" id="jas_date" class="text datepicker" />
						<small>(dd/mm/yyyy)</small>
					</td>
				</tr>
			</tbody>
			
			<!-- This is the empty table section where the outcomes are copied to via javscript -->
			<tbody class="outcomes"></tbody>
			
			<tbody class="meta">
			
				<tr class="vat">
					<th><label for="jas[jas_notes]">Notes</label></th>
					<td><textarea name="jas[jas_notes]" id="jas[jas_notes]" class="text" style="width: 300px; height: 100px;"></textarea></td>    
				</tr>
				
				<tr>
					<th></th>
					<td><input type="image" src="/img/btn/save.png" alt="Save" /></td>
				</tr>
			</tbody>
			
		</table>
	
	</form>
	
	
	
	<!--
		Hidden table with rows for ALL outcomes for ALL assessment criteria lists.
		Javascript copies rows from this to the main assessment form table on-demand.
	-->
	
	<table id="all_outcomes" style="display: none">

		<?php foreach ($acls as $acl): ?>

		<tbody data-acl_id="<?php echo $acl['acl_id'] ?>">
			
			<?php foreach ($acl['acl_criteria'] as $num => $title): ?>
		
			<tr class="outcome">
				<th><label for="jacs_<?php echo $acl['acl_id'] ?>_<?php echo $num ?>"><?php echo $title ?></th>
				<td>
					<select name="jacs[<?php echo $num ?>]" id="jacs_<?php echo $acl['acl_id'] ?>_<?php echo $num ?>">
						<?php
						$max = (in_array($acl['acl_type'], array('csop', 'top')) ? 28 : 10);
						for ($i = 0; $i <= $max; $i++)
						{
							echo '<option value="' . $i . '">' . $i . '</option>';	
						}
						?>
					</select>
				</td>
			</tr>
			
			<?php endforeach; ?>
			
		</tbody>
		
		<?php endforeach; ?>
		
	</table>
	
</div>

