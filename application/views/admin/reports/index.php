<style>
html {overflow: -moz-scrollbars-vertical;
overflow-y: scroll;}
</style>

<div class="journey_nav">
	<ul>
	<?php
	foreach ($groups as $uri => $group)
	{
		$href = site_url('admin/reports/index/' . $uri) . '?' . http_build_query($this->input->get());
		$selected = ($uri == $active_group ? 'selected' : '');
		echo '<li class="' . $selected . '"><a href="' . $href . '">' . $group['title'] . '</a></li>';
	}
	?>
	</ul>
	<div class="clear"></div>
</div>


<div class="item">

	<form action="<?php echo current_url() ?>" method="get" id="filter_form">

		<?php echo form_hidden('debug', $this->input->get('debug')) ?>

		<table class="filter report-group-<?php echo $active_group ?>" style="width: 100%">

			<tr>
				<th><label for="date_from">Date from</label></th>
				<th><label for="date_to">Date to</label></th>
				<th><label for="c_catchment_area">Catchment area</label></th>
				<th><label for="j_rc_id">Keyworker</label></th>
				<th><label for="j_status">Journey status</label></th>
			</tr>

			<tr>
				<td><input type="text" name="date_from" id="date_from" class="text datepicker" value="<?php echo form_prep($this->input->get('date_from')); ?>" /></td>
				<td><input type="text" name="date_to" id="date_to" class="text datepicker" value="<?php echo form_prep($this->input->get('date_to')); ?>" /></td>
				<td>
					<select name="c_catchment_area" id="c_catchment_area">
						<option value="">-- All --</option>
						<?php foreach($this->config->config['catchment_area_codes'] as $catchment_area => $values) : ?>
						<option value="<?php echo $catchment_area; ?>" <?php echo set_select('c_catchment_area', $catchment_area, ($catchment_area == $this->input->get('c_catchment_area'))) ?>><?php echo $catchment_area; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<select name="j_rc_id">
						<option value="">-- Any --</option>
						<option value="0" <?php echo set_select('j_rc_id', 0, $this->input->get('j_rc_id') === '0') ?>>-- N/A --</option>
						<?php foreach ($key_workers as $kw): ?>
						<option value="<?php echo $kw['rc_id'] ?>" <?php echo set_select('j_rc_id', $kw['rc_id'], ($kw['rc_id'] == $this->input->get('j_rc_id'))) ?>><?php echo $kw['rc_name'] ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<select name="j_status">
						<option value="">-- All --</option>
						<?php foreach($this->config->config['j_status_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if($code == @$_GET['j_status']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<th colspan="5"><br></th>
			</tr>

			<tr>
				<th colspan="2" class="filter-item-j_type"><label>Journey type</label></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>

			<tr>

				<td colspan="2" class="filter-item-j_type">
					<label class="check_label">
						<?php echo form_checkbox(array(
							'name' => 'j_type[]',
							'id' => 'j_type_c',
							'value' => 'C',
							'checked' => in_array('C', element('j_type', $this->input->get(), array())),
						)) ?>Service User
					</label>

					<label class="check_label">
						<?php echo form_checkbox(array(
							'name' => 'j_type[]',
							'id' => 'j_type_f',
							'value' => 'F',
							'checked' => in_array('F', element('j_type', $this->input->get(), array())),
						)) ?>Family
					</label>
				</td>

				<td></td>
				<td></td>

				<td style="text-align: left">
					<input type="image" src="/img/btn/filter.png" alt="Filter" />
					<a href="<?php echo current_url() ?>"><img src="/img/btn/clear.png" alt="Clear" /></a>
				</td>

			</tr>

		</table>

	</form>

</div>

<div class="report_wrapper">
	<?php echo $reports_html ?>
</div>

<script>
var report_data = <?php echo json_encode($json, JSON_NUMERIC_CHECK + ($this->input->get('debug') == 1 ? JSON_PRETTY_PRINT : NULL)) ?>;
</script>