<div id="notes" title="<?php echo (@$note ? 'Update' : 'Add new'); ?> notes">
      
	<?php echo form_open(current_url() . '?jn_id=' . @$_GET['jn_id'], array('id' => 'notes_form')); ?>
       	
        <?php if(@$note) : ?>
        <div class="valid">
            You are about to update existing notes.
        </div>
        <input type="hidden" type="hidden" name="jn_id" id="jn_id" value="<?php echo $note['jn_id']; ?>" />
        <?php else : ?>
        <div class="valid">
            You are about to add new notes.
        </div>
        <?php endif; ?>
    
        <table class="form">
        
            <tr>
                <th><label for="jn_date">Date</label></th>
                <td>
                	<input type="text" name="jn_date" id="jn_date" class="text datepicker" value="<?php echo @$note['date_of_note']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>
             
           	<tr>
                <th><label for="jn_rc_id">Recovery coach</label></th>
                <td>
                    <select name="jn_rc_id" id="jn_rc_id">
                        <option value="">-- Please select --</option>
                        <?php foreach($recovery_coaches as $rc) : ?>
                        <option value="<?php echo $rc['rc_id']; ?>" <?php if(@$note['jn_rc_id'] == $rc['rc_id']) echo 'selected="selected"'; ?>><?php echo form_prep($rc['rc_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
                                                
            <tr class="vat">
                <th><label for="jn_notes">Notes</label></th>
                <td><textarea name="jn_notes" id="jn_notes" style="width:430px; height:450px;"><?php echo form_prep(@$note['jn_notes']); ?></textarea></td>
            </tr>
            
            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/<?php echo (@$note ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
            </tr>
            
        </table>
    
    <?php echo form_close(); ?>
    
    <div class="clear"></div>

</div>