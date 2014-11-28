<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<?php echo form_open(current_url().'?j_type='.$this->input->get('j_type'), array('id' => 'client_form')); ?>

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
			
			<?php
			if(!isset($journey['j_date_of_referral']))
			{
				echo '<input type="hidden" name="j_status" value="3" />';  //3 == Initial Contact
			}
			?>
			
		
			<tr>
				<th><label for="j_date_of_referral">Date of referral</label></th>
				<td>
					<input type="text" name="j_date_of_referral" id="j_date_of_referral" class="text datepicker" value="<?php echo form_prep(element('j_date_of_referral_format', $journey_info, '')); ?>" />
					<small>(dd/mm/yyyy)</small>
				</td>
			</tr>
			
			<tr>
				<th><label for="ji_date_referral_received">Date referral received</label></th>
				<td>
					<input type="text" name="ji_date_referral_received" id="ji_date_referral_received" class="text datepicker" value="<?php echo form_prep(element('ji_date_referral_received_format', $journey_info, '')); ?>" />
					<small>(dd/mm/yyyy)</small>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_previous_id">Previous Client ID</label></th>
				<td><input type="text" name="ci_previous_id" id="ci_previous_id" class="text" value="<?php echo form_prep(element('ci_previous_id', $client, '')); ?>" /></td>
			</tr>
			
			<tr>
				<th><label for="ci_fname">Forename</label></th>
				<td><input type="text" name="ci_fname" id="ci_fname" class="text" value="<?php echo form_prep(element('ci_fname', $client, '')); ?>" style="text-transform:capitalize;" /></td>
			</tr>
			
			<tr>
				<th><label for="ci_sname">Surname</label></th>
				<td><input type="text" name="ci_sname" id="ci_sname" class="text" value="<?php echo form_prep(element('ci_sname', $client, '')); ?>" style="text-transform:capitalize;" /></td>
			</tr>
			
			<tr>
				<th><label for="ci_date_of_birth">Date of birth</label></th>
				<td>
					<input type="text" name="ci_date_of_birth" id="ci_date_of_birth" class="text datepicker" value="<?php echo form_prep(element('ci_date_of_birth', $client, '')); ?>" />
					<small>(dd/mm/yyyy)</small>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_gender">Gender</label></th>
				<td>
					<select name="ci_gender" id="ci_gender">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['gender_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_gender']) && $code == $client['ci_gender']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr class="vat">
				<th><label for="ci_address">Address</label></th>
				<td><textarea name="ci_address" id="ci_address" style="width:225px; height:100px;"><?php echo form_prep(element('ci_address', $client, '')); ?></textarea></td>
			</tr>
			
			<tr>
				<th><label for="ci_post_code">Post code</label></th>
				<td><input type="text" name="ci_post_code" id="ci_post_code" class="text" value="<?php echo form_prep(element('ci_post_code', $client, '')); ?>" style="width:75px; text-transform:uppercase;" /></td>
			</tr>
			
			<tr>
				<th><label for="ci_authority_code">Local Authority</label></th>
				<td>
					<span id="ci_authority_label">
					<?php
						if(isset($client['ci_authority_code']) && isset($client['ci_authority_name']))
						{
							echo $client['ci_authority_name'] .'('. $client['ci_authority_code'] .')';
						}
						else
						{
							echo 'N/A';
						}
					?>
					</span>
					<input type="hidden" name="ci_authority_code" id="ci_authority_code" value="<?php echo form_prep(element('ci_authority_code', $client, '')); ?>" />
					<input type="hidden" name="ci_authority_name" id="ci_authority_name" value="<?php echo form_prep(element('ci_authority_name', $client, '')); ?>" />
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_tel_home">Home telelphone</label></th>
				<td><input type="text" name="ci_tel_home" id="ci_tel_home" class="text" value="<?php echo form_prep(element('ci_tel_home', $client, '')); ?>" /></td>
			</tr>
			
			<tr>
				<th><label for="ci_tel_mob">Mobile telelphone</label></th>
				<td><input type="text" name="ci_tel_mob" id="ci_tel_mob" class="text" value="<?php echo form_prep(element('ci_tel_mob', $client, '')); ?>" /></td>
			</tr>
				 
			<tr>
				<th><label for="ci_catchment_area">Catchment Area</label></th>
				<td>
					<select name="ci_catchment_area" id="ci_catchment_area">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['catchment_area_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_catchment_area']) && $code == $client['ci_catchment_area']) echo 'selected="selected"'; ?>><?php echo $code;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr class="vat">
				<th><label for="ji_summary_of_needs">Summary of needs</label></th>
				<td><textarea name="ji_summary_of_needs" id="ji_summary_of_needs" style="width:400px; height:150px;"><?php echo form_prep(element('ji_summary_of_needs', $journey_info, '')); ?></textarea></td>
			</tr>
			
			<tr>
				<th><label for="ji_referral_received_by">Referral received by</label></th>
				<td>
					<select name="ji_referral_received_by" id="ji_referral_received_by">
						<option value="">- Select -</option>
						<?php foreach($staff as $s) : ?>
						<option value="<?php echo $s['s_id']; ?>" <?php if(isset($journey_info['ji_referral_received_by']) && $s['s_id'] == $journey_info['ji_referral_received_by']) echo 'selected="selected"'; ?>><?php echo $s['s_name'];?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_interpreter_required">Requires an interpreter</label></th>
				<td>
					<select name="ci_interpreter_required" id="ci_interpreter_required">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_interpreter_required']) && $code == $client['ci_interpreter_required']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
				 
			<tr>
				<th><label for="ci_preferred_contact_method">Preferred contact method</label></th>
				<td>
					<select name="ci_preferred_contact_method" id="ci_preferred_contact_method">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['contact_method_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_preferred_contact_method']) && $code == $client['ci_preferred_contact_method']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
				 
			<tr>
				<th><label for="ci_preferred_contact_time">Preferred contact time</label></th>
				<td>
					<select name="ci_preferred_contact_time" id="ci_preferred_contact_time">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['contact_time_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_preferred_contact_time']) && $code == $client['ci_preferred_contact_time']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_staff_can_leave_message">Staff can leave messages</label></th>
				<td>
					<select name="ci_staff_can_leave_message" id="ci_staff_can_leave_message">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_staff_can_leave_message']) && $code == $client['ci_staff_can_leave_message']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_staff_can_identify_themselves">Staff can identify themselves</label></th>
				<td>
					<select name="ci_staff_can_identify_themselves" id="ci_staff_can_identify_themselves">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_staff_can_identify_themselves']) && $code == $client['ci_staff_can_identify_themselves']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
				 
			<tr>
				<th><label for="ci_preferred_appointment_time">Preferred appointment time</label></th>
				<td>
					<select name="ci_preferred_appointment_time" id="ci_preferred_appointment_time">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['contact_time_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_preferred_appointment_time']) && $code == $client['ci_preferred_appointment_time']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_escape_write_to_you">Can Escape write to you</label></th>
				<td>
					<select name="ci_escape_write_to_you" id="ci_escape_write_to_you">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_escape_write_to_you']) && $code == $client['ci_escape_write_to_you']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
				 
			<tr>
				<th><label for="disabilities">Add new disability</label></th>
				<td>
					<select name="disabilities" id="disabilities">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['disability_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>"><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
					<button type="button" class="small button action-add-disability">Add</button>
				</td>
			</tr>
				 
			<tr class="vat">
				<th><label for="ci_disabilities">Disabilities</label></th>
				<td>
					<ul class="multi-add disabilities">
					<?php
					if ( ! empty($client['disabilities']))
					{
						foreach ($client['disabilities'] as $d_code => $d_label)
						{
							echo '<li data-id="' . $d_code . '">';
							echo '<img src="/img/style/x14.png" title="Remove" class="action-remove">';
							echo '<span>' . $d_label . '</span>';
							echo form_hidden('d_id[]', $d_code);
							echo '</li>';
						}
					}					
					?>
					</ul>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_previous_offender">Are they a previous offender</label></th>
				<td>
					<select name="ci_previous_offender" id="ci_previous_offender">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_previous_offender']) && $code == $client['ci_previous_offender']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_current_offender">Are they a current offender</label></th>
				<td>
					<select name="ci_current_offender" id="ci_current_offender">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_current_offender']) && $code == $client['ci_current_offender']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
				 
			<tr>
				<th><label for="ci_ethnicity">Ethnicity</label></th>
				<td>
					<select name="ci_ethnicity" id="ci_ethnicity">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['ethnicity_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client_info['ci_ethnicity']) && $code == $client_info['ci_ethnicity']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_nationality">Religion</label></th>
				<td>
					<select name="ci_religion" id="ci_religion">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['religions'] as $value) : ?>
						<option value="<?php echo $value; ?>" <?php if(isset($client_info['ci_religion']) && $value == $client_info['ci_religion']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_nationality">Nationality</label></th>
				<td>
					<select name="ci_nationality" id="ci_nationality">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['country_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client_info['ci_nationality']) && $code == $client_info['ci_nationality']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_sexuality">Sexuality</label></th>
				<td>
					<select name="ci_sexuality" id="ci_sexuality">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['sexuality_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_sexuality']) && $code == $client['ci_sexuality']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		 
			<tr>
				<th><label for="jd_substance_1">Problem substance 1</label></th>
				<td>
					
					<select name="jd_substance_1" id="jd_substance_1">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['drug_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($drugs_info['jd_substance_1']) && $code == $drugs_info['jd_substance_1']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		
			<tr>
				<th><label for="jd_substance_2">Problem substance 2</label></th>
				<td>
					<select name="jd_substance_2" id="jd_substance_2">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['drug_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($drugs_info['jd_substance_2']) && $code == $drugs_info['jd_substance_2']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_consents_to_ndtms">Consents to data sharing</label></th>
				<td>
					<select name="ci_consents_to_ndtms" id="ci_consents_to_ndtms">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_consents_to_ndtms']) && $code == $client['ci_consents_to_ndtms']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ji_rs_id">Referral source</label></th>
				<td>
					<select name="ji_rs_id" id="ji_rs_id">
						<option value="">- Select -</option>
						<?php foreach($referral_sources as $rs) : ?>
						<option value="<?php echo $rs['rs_id']; ?>" <?php if(isset($journey_info['ji_rs_id']) && $rs['rs_id'] == $journey_info['ji_rs_id']) echo 'selected="selected"'; ?>><?php echo $rs['rs_name'];?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ji_referrers_name">Referrer's name</label></th>
				<td><input type="text" name="ji_referrers_name" id="ji_referrers_name" class="text" value="<?php echo form_prep(element('ji_referrers_name', $journey_info, '')); ?>" style="text-transform:capitalize;" /></td>
			</tr>
			
			<tr>
				<th><label for="ji_referrers_tel">Referrer's telephone number</label></th>
				<td><input type="text" name="ji_referrers_tel" id="ji_referrers_tel" class="text" value="<?php echo form_prep(element('ji_referrers_tel', $journey_info, '')); ?>" /></td>
			</tr>
			
			<tr>
				<th><label for="ci_pregnant">Pregnant</label></th>
				<td>
					<select name="ci_pregnant" id="ci_pregnant">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_pregnant']) && $code == $client['ci_pregnant']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_partner_pregnant">Is your partner or anyone you are living with pregnant?</label></th>
				<td>
					<select name="ci_partner_pregnant" id="ci_partner_pregnant">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_partner_pregnant']) && $code == $client['ci_partner_pregnant']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_relationship_status">Relationship status</label></th>
				<td>
					<select name="ci_relationship_status" id="ci_relationship_status">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['relationship_status_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_relationship_status']) && $code == $client['ci_relationship_status']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_parental_status">Parental status</label></th>
				<td>
					<select name="ci_parental_status" id="ci_parental_status">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['parental_status_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_parental_status']) && $code == $client['ci_parental_status']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_no_of_children">Number of children</label></th>
				<td><input type="text" name="ci_no_of_children" id="ci_no_of_children" class="text" size="2" maxlength="2" value="<?php echo form_prep(element('ci_no_of_children', $client, '')); ?>"></td>
			</tr>
			
			<tr>
				<th><label for="ci_access_to_children">Access to children</label></th>
				<td>
					<select name="ci_access_to_children" id="ci_access_to_children">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['access_to_children_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_access_to_children']) && $code == $client['ci_access_to_children']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_childrens_services">Are Children's Services involved?</label></th>
				<td>
					<select name="ci_childrens_services" id="ci_childrens_services">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_childrens_services']) && $code == $client['ci_childrens_services']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><label for="ci_kinship_carer">Kinship carer?</label></th>
				<td>
					<select name="ci_kinship_carer" id="ci_kinship_carer">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($client['ci_kinship_carer']) && $code == $client['ci_kinship_carer']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr class="vat">
				<th><label for="ci_outcome">Outcome</label></th>
				<td><textarea name="ci_outcome" id="ci_outcome" style="width:225px; height:100px;"><?php echo form_prep(element('ci_outcome', $client, '')); ?></textarea></td>
			</tr>
			
			<tr>
				<th><label for="ji_flagged_as_risk">Flag as risk?</label></th>
				<td>
					<select name="ji_flagged_as_risk" id="ji_flagged_as_risk">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($journey_info['ji_flagged_as_risk']) && $code == $journey_info['ji_flagged_as_risk']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
			<tr class="vat" id="ji_flagged_risk_summary_row">
				<th><label for="ji_flagged_risk_summary">Summary of risk</label></th>
				<td><textarea name="ji_flagged_risk_summary" id="ji_flagged_risk_summary" style="width:400px; height:150px;"><?php echo form_prep(element('ji_flagged_risk_summary', $journey_info, '')); ?></textarea></td>
			</tr>
					  
			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>
				
		</table>

	</form>

</div>