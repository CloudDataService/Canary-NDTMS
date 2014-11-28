<h2>Drugs</h2>

    <table class="form">

        <?php asort($this->config->config['drug_codes']); // sort array alphabeticaly ?>

        <tr>
            <th><label for="jd_substance_1">Problem substance 1</label></th>
            <td>

                <select name="jd_substance_1" id="jd_substance_1">
                    <option value="">- Select -</option>
                    <?php foreach($this->config->config['drug_codes'] as $code => $value) : ?>
                    <option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_substance_1']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
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
			<th><label for="jd_substance_1_age">Age of first use of problem substance 1</label></th>
			<td><input type="text" name="jd_substance_1_age" id="jd_substance_1_age" class="text" value="<?php echo form_prep(@$drugs_info['jd_substance_1_age']); ?>" style="width:50px;" maxlength="2"></td>
		</tr>

        <tr>
            <th><label for="jd_substance_2">Problem substance 2</label></th>
            <td>
                <select name="jd_substance_2" id="jd_substance_2">
                    <option value="">- Select -</option>
                    <?php foreach($this->config->config['drug_codes'] as $code => $value) : ?>
                    <option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_substance_2']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
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
            <th><label for="jd_prev_hep_b_infection">Previous hepatitis B infection?</label></th>
            <td>
                <select name="jd_prev_hep_b_infection" id="jd_prev_hep_b_infection">
                    <option value="">- Select -</option>
                    <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
                    <option value="<?php echo $code; ?>" <?php if($code == @$drugs_info['jd_prev_hep_b_infection']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
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
            <th><label for="jd_hep_c_test_date">Hepatitis C test date</label></th>
            <td>
            	<input type="text" name="jd_hep_c_test_date" id="jd_hep_c_test_date" class="text datepicker" value="<?php echo form_prep(@$drugs_info['jd_hep_c_test_date']); ?>" />
                <small>(dd/mm/yyyy)</small>
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

    </table>
