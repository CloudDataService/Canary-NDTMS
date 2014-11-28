<div class="header">
	<h2>My details</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'my_account_form')); ?>
    
        <table class="form">
        
            <tr>
                <th><label for="email">Email</label></th>
                <td><input type="text" name="email" id="email" value="<?php echo form_prep($this->session->userdata('a_email')); ?>" class="text" /></td>
                <td class="e"></td>
            </tr>
            
            <tr>
                <th><label for="email_confirmed">Confirm email</label></th>
                <td><input type="text" name="email_confirmed" id="email_confirmed" value="<?php echo form_prep($this->session->userdata('a_email')); ?>" class="text" /></td>
                <td class="e"></td>
            </tr>
            
            <tr>
                <th><label for="fname">First name</label></th>
                <td><input type="text" name="fname" id="fname" value="<?php echo form_prep($this->session->userdata('a_fname')); ?>" class="text" style="width:auto;" /></td>
                <td class="e"></td>
            </tr>
            
            <tr>
                <th><label for="sname">Surname</label></th>
                <td><input type="text" name="sname" id="sname" value="<?php echo form_prep($this->session->userdata('a_sname')); ?>" class="text" style="width:auto;" /></td>
                <td class="e"></td>
            </tr>
            
            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/update.png" alt="Update" /></td>
            </tr>
            
        </table>
        
    </form>
    
</div>

<div class="header">
	<h2>My password</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'my_account_password_form')); ?>
    
        <table class="form">
        
            <tr>
                <th><label for="current_password">Current password</label></th>
                <td><input type="password" name="current_password" id="current_password" class="text" /></td>
                <td class="e"></td>
            </tr>
            
            <tr>
                <th><label for="new_password">New password</label></th>
                <td><input type="password" name="new_password" id="new_password" class="text" /></td>
                <td class="e"></td>
            </tr>
            
            <tr>
                <th><label for="new_password_confirmed">Confirm new password</label></th>
                <td><input type="password" name="new_password_confirmed" id="new_password_confirmed" class="text" /></td>
                <td class="e"></td>
            </tr>
            
            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/update.png" alt="Update" /></td>
            </tr>
        
        </table>
    
    </form>

</div>

<div class="header">
	<h2>Options</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'my_account_options_form')); ?>

		<input type="hidden" name="options[]" value="1" />

        <table class="form">
            
            <tr>
                <th>
                    <label for="tooltips">Tooltips</label>
                    <small>Show tooltip information</small>               	
                </th>
                <td><input type="checkbox" name="options[tooltips]" id="tooltips" value="1" <?php if($this->session->userdata('tooltips')) echo 'checked="checked"'; ?> /></td>
            </tr>
            
            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/update.png" alt="Update" /></td>
            </tr>
        
        </table>
    
    </form>

</div>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>