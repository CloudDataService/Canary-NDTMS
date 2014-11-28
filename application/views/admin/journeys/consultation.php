<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'client_form')); ?>

		<table class="form">

			<tr>
				<th><label for="j_date_of_triage">Consultation Date</label></th>
				<td>
					<input type="text" name="j_date_of_triage" id="j_date_of_triage" class="text datepicker" value="<?php echo form_prep($journey_info['j_date_of_triage_format']); ?>" />
					<small>(dd/mm/yyyy)</small>
				</td>
			</tr>

			<tr>
				<th><label for="ji_triage_completed_by">Triage completed by</label></th>
				<td>
					<select name="ji_triage_completed_by" id="ji_referral_received_by">
						<option value="">- Select -</option>
						<?php foreach($staff as $s) : ?>
						<option value="<?php echo $s['s_id']; ?>" <?php if($s['s_id'] == $journey_info['ji_triage_completed_by']) echo 'selected="selected"'; ?>><?php echo $s['s_name'];?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<th><label for="ci_gp_code">GP code</label></th>
				<td><input type="text" name="ci_gp_code" id="ci_gp_code" class="text" value="<?php echo form_prep($client['ci_gp_code']); ?>" style="width:400px;" /></td>
			</tr>

			<tr>
				<th><label for="ci_gp_name">GP name</label></th>
				<td><input type="text" name="ci_gp_name" id="ci_gp_name" class="text" value="<?php echo form_prep($client['ci_gp_name']); ?>" /></td>
			</tr>

			<tr>
				<th><label for="ji_date_of_drug_treatment">Drug Treatment Date</label></th>
				<td>
					<input type="text" name="ji_date_of_drug_treatment" id="ji_date_of_drug_treatment" class="text datepicker" value="<?php echo form_prep($journey_info['ji_date_of_drug_treatment_format']); ?>" />
					<small>(dd/mm/yyyy)</small>
				</td>
			</tr>

			<tr>
				<th><label for="ci_accommodation_need">Accommodation need</label></th>
				<td>
					<select name="ci_accommodation_need" id="ci_accommodation_need">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['accommodation_need_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if($code == $client['ci_accommodation_need']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<th><label for="ci_employment_status">Employment status</label></th>
				<td>
					<select name="ci_employment_status" id="ci_employment_status">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['employment_status_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if($code == $client['ci_employment_status']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<th><label for="ci_mental_health_issues">Dual Diagnosis</label></th>
				<td>
					<select name="ci_mental_health_issues" id="ci_mental_health_issues">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if($code == $client['ci_mental_health_issues']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>


	<?php //echo $this->load->view('admin/journeys/risks/risk_type_form'); ?>

	<?php //echo $this->load->view('admin/journeys/risks/risks'); ?>


			<tr>
				<th><label for="ji_previously_treated">Previously treated</label></th>
				<td>
					<select name="ji_previously_treated" id="ji_previously_treated">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if($code == $journey_info['ji_previously_treated']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

		<tr>
			<th><label for="jd_substance_1_route">Route of administration of problem substance number 1</label></th>
			<td>
				<select name="jd_substance_1_route" id="jd_substance_1_route">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['route_of_admin_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_substance_1_route']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><label for="jd_substance_3">Problem substance 3</label></th>
			<td>
				<select name="jd_substance_3" id="jd_substance_3">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['drug_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_substance_3']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><label for="jd_injecting">Injecting status</label></th>
			<td>
				<select name="jd_injecting" id="jd_injecting">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['injecting_status_codes'] as $code => $value) : ?>
					<option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_injecting']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

			<tr>
				<th><label for="jd_injected_in_last_month">Injected in last 28 days?</label></th>
				<td>
					<select name="jd_injected_in_last_month" id="jd_injected_in_last_month">
						<option value="">- Select -</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
						<option value="<?php echo $code; ?>" <?php if(isset($drugs_info['jd_injected_in_last_month']) && $code == $drugs_info['jd_injected_in_last_month']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

		<tr>
			<th><label for="jd_substance_1_age">Age of first use of problem substance 1</label></th>
			<td><input type="text" name="jd_substance_1_age" id="jd_substance_1_age" class="text" value="<?php echo form_prep(@$drugs_info['jd_substance_1_age']); ?>" style="width:50px;" maxlength="2"></td>
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
			<th><label for="jal_avg_daily_units">Average daily unit intake</label></th>
			<td><input type="text" name="jal_avg_daily_units" id="jal_avg_daily_units" class="text" value="<?php echo form_prep(@$alcohol_info['jal_avg_daily_units']); ?>" style="width:50px;" maxlength="3"></td>
		</tr>

		<!-- Agencies Involved -->
		<tr class="vat">
			<th><label for="agencies_involved">Add new agency involved</label></th>
			<td>
				<select name="agencies_involved" id="agencies_involved" class="multi_add_choice">
					<option value="">- Select -</option>
					<?php foreach($list_of_agencies as $ag_choice) : ?>
					<option value="<?php echo $ag_choice['ag_id']; ?>"><?php echo $ag_choice['ag_name'];?></option>
					<?php endforeach; ?>
				</select>
				<input type="text" name="ag_start_date" id="ag_start_date" class="text datepicker" value="" />
				<small>(dd/mm/yyyy)</small>
				<button type="button" class="small button action-add-agency-involved">Add</button>
			</td>
		</tr>

			<tr class="vat">
				<th><label for="j2ag">Agencies involved</label></th>
				<td>
					<ul class="multi-add j2ag">
					<?php
					if ( ! empty($journey_agencies_involved))
					{
						foreach ($journey_agencies_involved as $jag)
						{
							echo '<li data-id="' . $jag['ag_id'] . '">';
							echo '<img src="/img/style/x14.png" title="Remove" class="action-remove">';
							echo '<span>' . $jag['ag_name'] . ', added to recovery on '. $jag['j2ag_date_format'] .'</span>';
							echo form_hidden('j2ag['. $jag['ag_id'] .']', $jag['j2ag_date_format']);
							echo '</li>';
						}
					}
					?>
					</ul>
				</td>
			</tr>

	   <tr class="vat">
			<th><label for="ci_next_of_kin_details">Next of kin details</label></th>
			<td>
				<label for="ci_next_of_kin_details_1" style="display:block;">Next of kin 1</label>
				<textarea name="ci_next_of_kin_details[1]" id="ci_next_of_kin_details_1" style="width:225px; height:75px;"><?php echo form_prep(@$client['ci_next_of_kin_details'][1]); ?></textarea>

				<label for="ci_next_of_kin_details_2" style="display:block; margin-top:15px;">Next of kin 2</label>
				<textarea name="ci_next_of_kin_details[2]" id="ci_next_of_kin_details_2" style="width:225px; height:75px;"><?php echo form_prep(@$client['ci_next_of_kin_details'][2]); ?></textarea>
			</td>
		</tr>

		<tr>
			<th><label for="ci_escape_are_top_responsible">Are Escape Responsible for TOP Care Coordination?</th>
			<td>
				<select name="ci_escape_are_top_responsible" id="ci_escape_are_top_responsible">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_escape_are_top_responsible']) && $code == $client['ci_escape_are_top_responsible']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><label for="ci_pbr_client">Client is a PbR client</th>
			<td>
				<select name="ci_pbr_client" id="ci_pbr_client">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_pbr_client']) && $code == $client['ci_pbr_client']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><label for="ci_lasar_complexity">Complexity at LASAR Assessment</th>
			<td>
				<select name="ci_lasar_complexity" id="ci_lasar_complexity">
					<option value="">- Select -</option>
					<?php foreach($this->config->config['lasar_complexity_levels'] as $code => $answer) : ?>
					<option value="<?php echo $code; ?>" <?php if(isset($client['ci_lasar_complexity']) && $code == $client['ci_lasar_complexity']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><label for="ci_external_client_id">External System Client ID</label></th>
			<td><input type="text" name="ci_external_client_id" id="ci_external_client_id" class="text" value="<?php echo form_prep($client['ci_external_client_id']); ?>" /></td>
		</tr>


		<tr class="vat">
			<th>
				<label for="ji_additional_information">Additional information</label>
				<small>Please provide as much details as possible about the client's circumstances.</small>
			</th>
			<td><textarea name="ji_additional_information" id="ji_additional_information" style="width:400px; height:150px;"><?php echo form_prep($journey_info['ji_additional_information']); ?></textarea></td>
		</tr>

		<!-- Approved By -->

		<!-- Approved Date -->

		<!-- Internal service -->
		<tr class="vat">
			<th><label for="internal_services">Add new internal service</label></th>
			<td>
				<select name="internal_services" id="internal_services" class="multi_add_choice">
					<option value="">- Select -</option>
					<?php foreach($list_of_internal_services as $is_choice) : ?>
					<option value="<?php echo $is_choice['is_id']; ?>"><?php echo $is_choice['is_name'];?></option>
					<?php endforeach; ?>
				</select>
				<button type="button" class="small button action-add-internal-service">Add</button>
			</td>
		</tr>

			<tr class="vat">
				<th><label for="j2is">Internal services</label></th>
				<td>
					<ul class="multi-add j2is">
					<?php
					if ( ! empty($journey_internal_services))
					{
						foreach ($journey_internal_services as $jis)
						{
							echo '<li data-id="' . $jis['is_id'] . '">';
							echo '<img src="/img/style/x14.png" title="Remove" class="action-remove">';
							echo '<span>' . $jis['is_name'] . '</span>';
							echo form_hidden('j2is[]', $jis['is_id']);
							echo '</li>';
						}
					}
					?>
					</ul>
				</td>
			</tr>

		<!-- Medication? -->

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>
