<div class="header">
    <h2>Session information</h2>
</div>

<div class="item">

    <?php echo form_open(current_url(), array('id' => 'session_form')); ?>
        
        <table class="form">
        
            <tr class="vat">
                <th><label for="sps_date">Date</label></th>
                <td><input type="text" name="sps_date" id="sps_date" class="text datepicker" value="<?php echo form_prep(@$session['sps_date']); ?>" /></td>
                
                <th rowspan="3">Notes</th>
                <td rowspan="3"><textarea name="sps_notes" id="sps_notes" class="text" style="width:500px; height:110px;"><?php echo form_prep(@$session['sps_notes']); ?></textarea></td>
                
            </tr>
            
            <tr>
                <th><label for="time">Time</label></th>
                <td>
                    <select name="time_hour">
                        <?php 
                        for($i = 00; $i <= 23; $i++)
                        {
                            echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$session['time_hour'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                        }
                        ?>
                    </select>
                    :
                    <select name="time_minute">
                        <?php 
                        for($i = 00; $i <= 59; $i = $i + 5)
                        {
                            echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$session['time_minute'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="sps_location">Location</label></th>
                <td><input type="text" name="sps_location" id="sps_location" class="text" style="text-transform:capitalize;" value="<?php echo form_prep(@$session['sps_location']); ?>" /></td>
            </tr>

            </tr>                                     
                <td colspan="4" style="text-align:right;"><input type="image" src="/img/btn/<?php echo (@$session ? 'update' : 'add-new'); ?>.png" alt="Save" /></td> 
            </tr>
            
        </table>
    
    <?php echo form_close(); ?>
    
</div>

<div class="total"><?php echo $total; ?></div>

<div class="header">
	<h2>Register</h2>
</div>

<div class="item results">
	
    <?php if($session) : ?>
    
		<?php if($register) : ?>
        
        <table class="results" id="register">
        
            <tr class="order">
                <th>Name</th>
                <th>Gender</th>
                <th>Date of birth</th>
                <th>Post code</th>
                <th>Remove</th>
            </tr>
            
            <?php foreach($register as $client) : ?>
            <tr class="row no_click">
                <td><a href="/admin/clients/info/<?php echo $client['c_id']; ?>"><?php echo $client['c_name']; ?></a></td> 
                <td><?php echo $client['c_gender']; ?></td>
                <td><?php echo $client['c_date_of_birth']; ?></td>
                <td><?php echo $client['c_post_code']; ?></td>
                <td class="action"><a href="?remove_client=<?php echo $client['c_id']; ?>" class="action" title="Are you sure you want to remove <?php echo $client['c_name']; ?> from the register"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
            </tr>
            <?php endforeach; ?>
        
        </table>
        
        <?php else : ?>
        <p class="no_results">There are no clients registered to attend this session. <br /><a href="/admin/activities/get_last_register/<?php echo $session['sps_sp_id'] . '/' . $session['sps_id']; ?>" id="suggest_a_register">Suggest a register</a></p>
        <?php endif; ?>
        
    <?php else : ?>
    <p class="no_results">You can add clients to the register once you have saved information about the session.</p>
    <?php endif; ?>
    
</div>
<?php if($session) : ?><a href="#" id="add_client" style="float:right; display:block;"><img src="/img/btn/add-client.png" alt="Add client" /></a><?php endif; ?>

<a href="/admin/activities/info/<?php echo $support_group['sp_id']; ?>" ><img src="/img/btn/back.png" alt="Back" /></a>

<div id="client_dialog" style="display:none;" title="Add client to register">

	<?php echo form_open(current_url(), array('id' => 'client_form')); ?>

		<input type="hidden" name="ajax" id="ajax" value="1" />

		<table class="form">
        
        	<tr>
            	<th><label for="c_fname">Forename</label></th>
                <td><input type="text" name="c_fname" id="c_fname" class="text" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
            	<th><label for="c_sname">Surname</label></th>
                <td><input type="text" name="c_sname" id="c_sname" class="text" style="text-transform:capitalize;" /></td>
            </tr>
            
            <tr>
            	<th><label for="c_gender">Gender</label></th>
                <td>
                	<select name="c_gender" id="c_gender">
                    	<option value="">- Select -</option>
                        <?php foreach($this->config->config['gender_codes'] as $code => $gender) : ?>
                        <option value="<?php echo $code; ?>"><?php echo $gender;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
            	<th><label for="c_date_of_birth">Date of birth</label></th>
                <td>
                    <input type="text" name="c_date_of_birth" id="c_date_of_birth" class="text datepicker" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
            	<th><label for="c_post_code">Post code</label></th>
                <td><input type="text" name="c_post_code" id="c_post_code" class="text" style="width:75px; text-transform:uppercase;" /></td>
            </tr>
            
            <tr>
            	<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/add-new.png" alt="Add" /></td>
            </tr>
            
        </table>
        
	<?php echo form_close(); ?>
    
    <div id="search_results" class="search_results" style="position:relative; margin:0; width:100%; display:none;">

    </div>
    
</div>