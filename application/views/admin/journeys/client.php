<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'client_form')); ?>

        <table class="form">
            
            <tr>
                <th><label for="ci_fname">Forename</label></th>
                <td><input type="text" name="ci_fname" id="ci_fname" class="text" value="<?php echo form_prep($client['ci_fname']); ?>" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
                <th><label for="ci_sname">Surname</label></th>
                <td><input type="text" name="ci_sname" id="ci_sname" class="text" value="<?php echo form_prep($client['ci_sname']); ?>" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
                <th><label for="ci_gender">Gender</label></th>
                <td>
                    <select name="ci_gender" id="ci_gender">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['gender_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_gender']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ci_date_of_birth">Date of birth</label></th>
                <td>
                	<input type="text" name="ci_date_of_birth" id="ci_date_of_birth" class="text datepicker" value="<?php echo form_prep($client['ci_date_of_birth']); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
            
            <tr class="vat">
                <th><label for="ci_address">Address</label></th>
                <td><textarea name="ci_address" id="ci_address" style="width:225px; height:100px;"><?php echo form_prep($client['ci_address']); ?></textarea></td>
            </tr>
            
            <tr>
                <th><label for="ci_post_code">Post code</label></th>
                <td><input type="text" name="ci_post_code" id="ci_post_code" class="text" value="<?php echo form_prep($client['ci_post_code']); ?>" style="width:75px; text-transform:uppercase;" /></td>
            </tr>
            
            <tr>
                <th><label for="ci_tel_home">Home telelphone</label></th>
                <td><input type="text" name="ci_tel_home" id="ci_tel_home" class="text" value="<?php echo form_prep($client['ci_tel_home']); ?>" /></td>
            </tr>
            
            <tr>
                <th><label for="ci_tel_mob">Mobile telelphone</label></th>
                <td><input type="text" name="ci_tel_mob" id="ci_tel_mob" class="text" value="<?php echo form_prep($client['ci_tel_mob']); ?>" /></td>
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
                <th><label for="ci_ethnicity">Ethnicity</label></th>
                <td>
                    <select name="ci_ethnicity" id="ci_ethnicity">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['ethnicity_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_ethnicity']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
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
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_nationality']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ci_pregnant">Pregnant</label></th>
                <td>
                    <select name="ci_pregnant" id="ci_pregnant">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_pregnant']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ci_caf_completed">CAF completed</label></th>
                <td>
                    <select name="ci_caf_completed" id="ci_caf_completed">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_caf_completed']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
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
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_relationship_status']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
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
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_sexuality']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ci_mental_health_issues">Mental health issues</label></th>
                <td>
                    <select name="ci_mental_health_issues" id="ci_mental_health_issues">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_mental_health_issues']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ci_learning_difficulties">Learning difficulties</label></th>
                <td>
                    <select name="ci_learning_difficulties" id="ci_learning_difficulties">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_learning_difficulties']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr class="vat">
            	<th><label for="ci_disabilities">Disabilities</label></th>
                <td><textarea name="ci_disabilities" id="ci_disabilities" style="width:225px; height:75px;"><?php echo form_prep(@$client['ci_disabilities']); ?></textarea></td>
            </tr>
            
            <tr>
                <th><label for="ci_consents_to_ndtms">Consents to data sharing</label></th>
                <td>
                    <select name="ci_consents_to_ndtms" id="ci_consents_to_ndtms">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_consents_to_ndtms']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
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
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_parental_status']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ci_no_of_children">Number of children</label></th>
                <td><input type="text" name="ci_no_of_children" id="ci_no_of_children" class="text" size="2" maxlength="2" value="<?php echo form_prep($client['ci_no_of_children']); ?>"></td>
            </tr>
            
            <tr>
                <th><label for="ci_access_to_children">Access to children</label></th>
                <td>
                    <select name="ci_access_to_children" id="ci_access_to_children">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['access_to_children_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_access_to_children']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ci_accommodation_status">Accommodation status</label></th>
                <td>
                    <select name="ci_accommodation_status" id="ci_accommodation_status">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['accommodation_status_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_accommodation_status']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
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
                <th><label for="ci_smoker">Smoker</label></th>
                <td>
                    <select name="ci_smoker" id="ci_smoker">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == $client['ci_smoker']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th>
                	<label for="ci_contact">Contact</label>
                    <small>Is it ok to contact client during and after work?</small>
                </th>
                <td>
                	During
                    <select name="ci_contact[during]" id="ci_contact_during">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$client['ci_contact']['during']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    After
                    <select name="ci_contact[after]" id="ci_contact[after]">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $answer) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$client['ci_contact']['after']) echo 'selected="selected"'; ?>><?php echo $answer;?></option>
                        <?php endforeach; ?>
                    </select>
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
			
            <tr class="vat">
                <th>
                	<label for="ci_additional_information">Additonal information</label>
                    <small>Any additional information you would like to include about <?php echo $client['ci_fname']; ?>.</small>
                </th>
                <td><textarea name="ci_additional_information" id="ci_additional_information" style="width:400px; height:100px;"><?php echo form_prep($client['ci_additional_information']); ?></textarea></td>
            </tr>
                      
            <tr>
            	<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>
                
    	</table>

	</form>

</div>