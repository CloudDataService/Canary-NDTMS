<h2>Alcohol</h2>

	<table class="form vat">
	 
		<tr>
			<th>
				<label for="jal_safe_at_home">Safe at home</label>
				<small>When client has consumed alcohol, do they feel safe at home?</small>
			</th>
			<td>
				<select name="jal_safe_at_home" id="jal_safe_at_home">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$alcohol_info['jal_safe_at_home']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr> 
		
		<tr>
			<th>
				<label for="jal_others_safe">Do others feel safe?</label>
				<small>When client has consumed alcohol, do others feel safe around them?</small>
			</th>
			<td>
				<select name="jal_others_safe" id="jal_others_safe">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$alcohol_info['jal_others_safe']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="jal_affect_bills">Bills affected?</label>
				<small>Has alcohol consumption affected the client's ability to pay bills?</small>
			</th>
			<td>
				<select name="jal_affect_bills" id="jal_affect_bills">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$alcohol_info['jal_affect_bills']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="jal_incurred_debt">Incurred debt?</label>
				<small>Has the client incurred debt as a result of alcohol consumption?</small>
			</th>
			<td>
				<select name="jal_incurred_debt" id="jal_incurred_debt">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$alcohol_info['jal_incurred_debt']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="jal_avg_daily_units">Average daily unit intake</label></th>
			<td><input type="text" name="jal_avg_daily_units" id="jal_avg_daily_units" class="text" value="<?php echo form_prep(@$alcohol_info['jal_avg_daily_units']); ?>" style="width:50px;" maxlength="3"></td>
		</tr>
		
		<tr>
			<th><label for="jal_last_28_drinking_days">Number of drinking days in the last 28 days</label></th>
			<td>
				<select name="jal_last_28_drinking_days" id="jal_last_28_drinking_days">
					<?php for($i = 0; $i <= 28; $i++) : ?>
					<option value="<?php echo $i; ?>" <?php if($i == @$alcohol_info['jal_last_28_drinking_days']) echo 'selected="selected"'; ?>><?php echo $i;?></option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="jal_age_started_drinking">Age client started drinking</label></th>
			<td><input type="text" name="jal_age_started_drinking" id="jal_age_started_drinking" class="text" value="<?php echo form_prep(@$alcohol_info['jal_age_started_drinking']); ?>" style="width:50px;" maxlength="2"></td>
		</tr>
		
	</table>