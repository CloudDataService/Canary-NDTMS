<?php if($this->session->userdata('tooltips')) : // if tooltips are turned on ?>

<div class="tooltip">
	
    <?php if ($this->input->get('j_type') == 'F'): ?>
    <p>You are about to start a new journey for a family member. Use this form to collect basic information about the family member and journey. Once you have completed and saved this form, a new journey will be started for the client where you will be able to add more information.</p>
    <?php else: ?>
    <p>You are about to start a new journey. Use this form to collect basic information about the client and journey. Once you have completed and saved this form, a new journey will be started for the client where you will be able to add more information.</p>
    <?php endif; ?>

</div>

<?php endif; ?>


<div class="header">
	<h2>Start new <?php echo strtolower($this->config->item($this->input->get('j_type'), 'types')) ?> journey</h2>
</div>

<div class="item">
    
    <?php
    $hidden = array(
        'j_type' => element('j_type', $_GET),
        'c_id' => element('c_id', $client, NULL),
        'from_j_id' => element('from_j_id', $_GET, NULL),
    );
    
    echo form_open(current_url(), array('id' => 'new_journey_form'), $hidden);
    ?>
    	
        <div id="search_results" class="search_results"></div>

		<table class="form">
            
            <?php if ( ! element('j_type', $_GET)): ?>
            <tr>
                <th><label for="j_type">Journey type</label></th>
                <td>
                    <select name="j_type" id="j_type">
                        <option value="C">Client</option>
                        <option value="F">Family</option>
                    </select>
                </td>
            </tr>
            <?php endif; ?>
        
        	<tr>
            	<th><label for="c_fname">Forename</label></th>
                <td><input type="text" name="c_fname" id="c_fname" class="text" value="<?php echo element('c_fname', $_GET, form_prep(@$client['c_fname'])); ?>" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
            	<th><label for="c_sname">Surname</label></th>
                <td><input type="text" name="c_sname" id="c_sname" class="text" value="<?php echo element('c_sname', $_GET, form_prep(@$client['c_sname'])); ?>" style="text-transform:capitalize;" /></td>
            </tr>
            
            
            <?php if (isset($from_journey)): ?>
            
            <tr class="vat">
                <th><label for="fc_rel_type">Relation to<br><?php echo $from_journey['ci_name'] ?></label></th>
                <td><?php echo form_dropdown('fc_rel_type', $this->config->item('relative_types'), $this->input->get('fc_rel_type')) ?>
            </tr>
            
            <?php endif; ?>
            
            
            <tr>
            	<th><label for="c_gender">Gender</label></th>
                <td>
                	<select name="c_gender" id="c_gender">
                    	<option value="">- Select -</option>
                        <?php foreach($this->config->config['gender_codes'] as $code => $gender) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$client['c_gender']) echo 'selected="selected"'; ?>><?php echo $gender;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
            	<th><label for="c_date_of_birth">Date of birth</label></th>
                <td>
                    <input type="text" name="c_date_of_birth" id="c_date_of_birth" class="text datepicker" value="<?php echo element('c_date_of_birth', $_GET, form_prep(@$client['c_date_of_birth'])); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
            
            <tr class="vat">
            	<th><label for="c_address">Address</label></th>
                <td><textarea name="c_address" id="c_address" style="width:225px; height:100px;"><?php echo form_prep(@$client['c_address']); ?></textarea></td>
            </tr>
            
            <tr>
            	<th><label for="c_post_code">Post code</label></th>
                <td><input type="text" name="c_post_code" id="c_post_code" class="text" value="<?php echo form_prep(@$client['c_post_code']); ?>" style="width:75px; text-transform:uppercase;" /></td>
            </tr>
            
            <tr>
            	<th><label for="c_tel_home">Home telelphone</label></th>
                <td><input type="text" name="c_tel_home" id="c_tel_home" class="text" value="<?php echo form_prep(@$client['c_tel_home']); ?>" /></td>
            </tr>
            
            <tr>
            	<th><label for="c_tel_mob">Mobile telelphone</label></th>
                <td><input type="text" name="c_tel_mob" id="c_tel_mob" class="text" value="<?php echo form_prep(@$client['c_tel_mob']); ?>" /></td>
            </tr>
            
            <tr>
            	<th><label for="j_date_of_referral">Date of referral</label></th>
                <td>
                	<input type="text" name="j_date_of_referral" id="j_date_of_referral" class="text datepicker" value="<?php echo date('d/m/Y'); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
            
            <tr>
            	<th><label for="j_rs_id">Referral source</label></th>
                <td>
                	<select name="j_rs_id" id="j_rs_id">
                    	<option value="">- Select -</option>
                        <?php foreach($referral_sources as $rs) : ?>
                        <option value="<?php echo $rs['rs_id']; ?>"><?php echo $rs['rs_name'];?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
            	<th><label for="j_rc_id">Referral received by</label></th>
                <td>
                	<select name="j_rc_id" id="j_rc_id">
                    	<option value="">- Select -</option>
                        <?php foreach($recovery_coaches as $rc) : ?>
                        <option value="<?php echo $rc['rc_id']; ?>" <?php echo set_select('j_rc_id', $rc['rc_id'], ($rc['rc_id'] == $this->session->userdata('rc_id'))) ?>><?php echo $rc['rc_name_family_worker'];?> </option>
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
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>"><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr> 
            */
            ?>
            
            <tr>
            	<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>
            
        </table>

	</form>

</div>