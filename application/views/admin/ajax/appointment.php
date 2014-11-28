<div id="appointment" title="<?php echo (@$appt ? 'Update' : 'Make new'); ?> appointment">

	<?php echo form_open(current_url() . '?ja_id=' . @$_GET['ja_id'], array('id' => 'appointment_form')); ?>
		
        <?php if(@$appt) : ?>
        <input type="hidden" name="ja_id" id="ja_id" value="<?php echo $appt['ja_id'] ;?>" />
        <div class="valid">
        	<p>You are about to update information about an exisiting appointment.</p>
        </div>      
        <?php else : ?>
        <div class="valid">
        	<p>You are about to add information about a new appointment.</p>
        </div>        
        <?php endif; ?>
        
   		<table class="form">
        	
            <tr>
            	<th><label for="ja_date_offered">Date offered</label></th>
                <td><input type="text" name="ja_date_offered" id="ja_date_offered" class="text datepicker" value="<?php echo @$appt['ja_date_offered']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
        
            <tr>
                <th><label for="ja_date">Date</label></th>
                <td>
                	<input type="text" name="ja_date" id="ja_date" class="text datepicker" value="<?php echo @$appt['date_of_appt']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
            
            <tr>
                <th><label for="time">Time</label></th>
                <td>
                    <select name="time_hour">
                        <?php 
                        for($i = 00; $i <= 23; $i++)
                        {
                            echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$appt['hour_of_appt'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                        }
                        ?>
                    </select>
                    :
                    <select name="time_minute">
                        <?php 
                        for($i = 00; $i <= 59; $i = $i + 5)
                        {
                            echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$appt['minute_of_appt'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="ja_rc_id">Keyworker</label></th>
                <td>
                    <select name="ja_rc_id" id="ja_rc_id">
                        <option value="">-- Please select --</option>
                        <?php foreach($recovery_coaches as $rc) : ?>
                        <option value="<?php echo $rc['rc_id']; ?>" <?php if(@$appt['ja_rc_id'] == $rc['rc_id']) echo 'selected="selected"'; ?>><?php echo form_prep($rc['rc_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr class="vat">
                <th><label for="ja_notes">Notes</label></th>
                <td><textarea name="ja_notes" id="ja_notes" style="width:450px; height:105px;"><?php echo form_prep(@$appt['ja_notes']); ?></textarea></td>
            </tr>
            
            <tr>
            	<th><label for="ja_attended">Attended</label></th>
                <td>
                	<select name="ja_attended" id="ja_attended">
                        <option value="">-- Please select --</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$appt['ja_attended']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr id="ja_dr_tr" style="display:none;">
            	<th><label for="ja_dr_id">DNA reason</label></th>
                <td>
                	<select name="ja_dr_id" id="ja_dr_id">
                    	<option value="">-- Please select --</option>
                        <?php foreach($dna_reasons as $dr) : ?>
                        <option value="<?php echo $dr['dr_id']; ?>" <?php if(@$appt['ja_dr_id'] == $dr['dr_id']) echo 'selected="selected"'; ?>><?php echo form_prep($dr['dr_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr class="showhide" id="ja_length_tr" style="display:none;">
            	<th><label for="ja_length">Length</label></th>
                <td><input type="text" name="ja_length" id="ja_length" class="text" size="3" maxlength="3" value="<?php echo form_prep(@$appt['ja_length']); ?>" style="text-align:right;" /> mins</td>
            </tr>
                         
            <tr>
                <td colspan="2" style="text-align:right;">
                    <input type="image" src="/img/btn/<?php echo (@$appt ? 'update' : 'add-new'); ?>.png" alt="Save" />
                </td>
            </tr>
            
        </table>
    
    <?php echo form_close(); ?>

    <div class="clear"></div>

</div>