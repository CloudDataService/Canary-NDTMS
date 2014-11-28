<div class="header">
	<h2><?php echo $title; ?></h2>
</div>
<div class="item">

	<?php echo form_open(current_url(), array('id' => 'client_form')); ?>

        <table class="form">
            <tr>
                <th><label for="c_fname">Forename</label></th>
                <td><input type="text" name="c_fname" id="c_fname" class="text" value="<?php echo form_prep(@$client['c_fname']); ?>" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
                <th><label for="c_sname">Surname</label></th>
                <td><input type="text" name="c_sname" id="c_sname" class="text" value="<?php echo form_prep(@$client['c_sname']); ?>" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
                <th><label for="c_gender">Gender</label></th>
                <td>
                    <select name="c_gender" id="c_gender">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['gender_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$client['c_gender']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="c_date_of_birth">Date of birth</label></th>
                <td>
                    <input type="text" name="c_date_of_birth" id="c_date_of_birth" class="text datepicker" value="<?php echo form_prep(@$client['c_date_of_birth']); ?>" />
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
            	<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>
            
        </table>
    
    <?php echo form_close(); ?>
    
</div>