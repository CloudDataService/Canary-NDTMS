<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'journey_form')); ?>

		<table class="form">
        
            <tr>
            	<th><label for="j_date_of_referral">Date of referral</label></th>
                <td>
                	<input type="text" name="j_date_of_referral" id="j_date_of_referral" class="text datepicker" value="<?php echo form_prep($journey_info['j_date_of_referral_format']); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
            
            <tr>
            	<th><label for="ji_date_referral_received">Date referral received</label></th>
                <td>
                	<input type="text" name="ji_date_referral_received" id="ji_date_referral_received" class="text datepicker" value="<?php echo form_prep($journey_info['ji_date_referral_received_format']); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
            
            <tr>
            	<th><label for="ji_referral_received_by">Referral received by</label></th>
                <td>
                	<select name="ji_referral_received_by" id="ji_referral_received_by">
                    	<option value="">- Select -</option>
                        <?php foreach($staff as $s) : ?>
                        <option value="<?php echo $s['s_id']; ?>" <?php if($s['s_id'] == $journey_info['ji_referral_received_by']) echo 'selected="selected"'; ?>><?php echo $s['s_name'];?></option>
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
                        <option value="<?php echo $rs['rs_id']; ?>" <?php if($rs['rs_id'] == $journey_info['ji_rs_id']) echo 'selected="selected"'; ?>><?php echo $rs['rs_name'];?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ji_referrers_name">Referrer's name</label></th>
                <td><input type="text" name="ji_referrers_name" id="ji_referrers_name" class="text" value="<?php echo form_prep($journey_info['ji_referrers_name']); ?>" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
                <th><label for="ji_referrers_tel">Referrer's telephone number</label></th>
                <td><input type="text" name="ji_referrers_tel" id="ji_referrers_tel" class="text" value="<?php echo form_prep($journey_info['ji_referrers_tel']); ?>" /></td>
            </tr>
            
            <tr>
            	<th><label for="j_rc_id">Case worker</label></th>
                <td>
                	<select name="j_rc_id" id="j_rc_id">
                    	<option value="">- Select -</option>
                        <?php foreach($recovery_coaches as $rc) : ?>
                        <option value="<?php echo $rc['rc_id']; ?>" <?php if($rc['rc_id'] == $journey_info['j_rc_id']) echo 'selected="selected"'; ?>><?php echo $rc['rc_name_family_worker'];?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
            	<th><label for="ji_date_rc_allocated">Date case worker allocated</label></th>
                <td>
                	<input type="text" name="ji_date_rc_allocated" id="ji_date_rc_allocated" class="text datepicker" value="<?php echo form_prep($journey_info['ji_date_rc_allocated_format']); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
            	<th><label for="ji_rc_allocated_by">Case worker allocated by</label></th>
                <td>
                	<select name="ji_rc_allocated_by" id="ji_rc_allocated_by">
                    	<option value="">- Select -</option>
                        <?php foreach($staff as $s) : ?>
                        <option value="<?php echo $s['s_id']; ?>" <?php if($s['s_id'] == $journey_info['ji_rc_allocated_by']) echo 'selected="selected"'; ?>><?php echo $s['s_name'];?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <?php
            /*
            <tr>
            	<th><label for="j_family_or_carer_involved">Is there a family or carer involved?</label></th>
                <td>
                	<select name="j_family_or_carer_involved" id="j_family_or_carer_involved">
                    	<option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $journey_info['j_family_or_carer_involved']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr> 
            */
            ?>
            
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
            
            <tr class="vat">
                <th><label for="ji_summary_of_needs">Summary of needs</label></th>
                <td><textarea name="ji_summary_of_needs" id="ji_summary_of_needs" style="width:400px; height:150px;"><?php echo form_prep($journey_info['ji_summary_of_needs']); ?></textarea></td>
            </tr>
            
            <tr class="vat">
                <th>
                	<label for="ji_additional_information">Additional information</label>
                    <small>Please provide as much details as possible about the client's circumstances.</small>
                </th>
                <td><textarea name="ji_additional_information" id="ji_additional_information" style="width:400px; height:150px;"><?php echo form_prep($journey_info['ji_additional_information']); ?></textarea></td>
            </tr>
            
            <tr class="vat">
                <th>
                	<label for="ji_medication">Medication</label>
                    <small>Any prescribed/non prescribed medication used at present?</small>
                </th>
                <td><textarea name="ji_medication" id="ji_medication" style="width:400px; height:100px;"><?php echo form_prep($journey_info['ji_medication']); ?></textarea></td>
            </tr>
            
            <tr>
            	<th><label for="j_status">Journey status</label></th>
                <td>
                	<select name="j_status" id="j_status">
                        <?php foreach($this->config->config['j_status_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $journey_info['j_status']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
            	<th><label for="j_closed_date">Date journey closed</label></th>
                <td>
                	<input type="text" name="j_closed_date" id="j_closed_date" class="text datepicker" value="<?php echo form_prep($journey_info['j_closed_date_format']); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr> 
            
            <tr>
            	<th><label for="ji_exit_status">Exit status</label></th>
                <td>
                	<select name="ji_exit_status" id="ji_exit_status">
                    	<option value="">- Select -</option>
                        <?php foreach($this->config->config['exit_status_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $journey_info['ji_exit_status']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr> 
            
            <tr>
            	<th><label for="ji_discharge_reason">Discharge reason</label></th>
                <td>
                	<select name="ji_discharge_reason" id="ji_discharge_reason">
                    	<option value="">- Select -</option>
                        <?php foreach($this->config->config['discharge_reason_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $journey_info['ji_discharge_reason']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
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