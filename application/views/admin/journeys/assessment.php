<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'client_form')); ?>

		<table class="form">
			
			<tr>
				<th><label for="j_type">Journey Type</label></th>
				<td><?php
						if($journey['j_type'] == 'C')
						{
							echo 'Client';
						}
						else if($journey['j_type'] == 'F')
						{
							echo 'Family';
						}
					?></td>
			</tr>
			
			<!-- CSOP Number -->
			
			<!-- Ever shared -->
				
		<tr>
			<th><label for="jd_hep_c_test_date">Hepatitis C test date</label></th>
			<td>
				<input type="text" name="jd_hep_c_test_date" id="jd_hep_c_test_date" class="text datepicker" value="<?php echo form_prep(@$drugs_info['jd_hep_c_test_date']); ?>" />
				<small>(dd/mm/yyyy)</small>
			</td>
		</tr>
		
		<tr>
			<th><label for="jd_hep_c_intervention">Hepatitis C intervention status</label></th>
			<td>
				<select name="jd_hep_c_intervention" id="jd_hep_c_intervention">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['hep_c_intervention_status_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_hep_c_intervention']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="jd_hep_c_tested">Hepatitis C tested?</label></th>
			<td>
				<select name="jd_hep_c_tested" id="jd_hep_c_tested">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($drugs_info['jd_hep_c_tested']) && $code == $drugs_info['jd_hep_c_tested']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="jd_hep_c_positive">Hepatitis C positive?</label></th>
			<td>
				<select name="jd_hep_c_positive" id="jd_hep_c_positive">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_hep_c_positive']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="jd_hep_b_prev_positive">Previously Hepatitis B infected?</label></th>
			<td>
				<select name="jd_hep_b_prev_positive" id="jd_hep_b_prev_positive">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($drugs_info['jd_hep_b_prev_positive']) && $code == $drugs_info['jd_hep_b_prev_positive']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="jd_hep_b_vac_count">Hepatitis B vaccination count</label></th>
			<td>
				<select name="jd_hep_b_vac_count" id="jd_hep_b_vac_count">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['hep_b_vaccination_count_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_hep_b_vac_count']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		
		<tr>
			<th><label for="jd_hep_b_intervention">Hepatitis B intervention status</label></th>
			<td>
				<select name="jd_hep_b_intervention" id="jd_hep_b_intervention">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['hep_b_intervention_status_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_hep_b_intervention']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		
		<tr>
			<th><label for="ci_consent_signed">Consent has been signed</label></th>
			<td>
				<select name="ci_consent_signed" id="ci_consent_signed">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_consent_signed']) && $code == $client['ci_consent_signed']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="ci_ndtms_consent">NDTMS Consent</label></th>
			<td>
				<select name="ci_ndtms_consent" id="ci_ndtms_consent">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_ndtms_consent']) && $code == $client['ci_ndtms_consent']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="ci_nta_consent">NTA Consent</label></th>
			<td>
				<select name="ci_nta_consent" id="ci_nta_consent">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_nta_consent']) && $code == $client['ci_nta_consent']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="ci_csop_consent">CSOP Consent</label></th>
			<td>
				<select name="ci_csop_consent" id="ci_csop_consent">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_csop_consent']) && $code == $client['ci_csop_consent']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<th><label for="ci_photography_consent">Photography Consent</label></th>
			<td>
				<select name="ci_photography_consent" id="ci_csop_consent">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_photography_consent']) && $code == $client['ci_photography_consent']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
					  
			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>
				
		</table>

	</form>

</div>