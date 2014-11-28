<div id="status" title="<?php echo 'Approve journey'; ?>">
	
	<?php echo form_open(current_url() . '?j_id=' . $journey['j_id'], array('id' => 'status_form')); ?>
       	
        
        <div class="valid">
            Are you sure you want to approve the change of journey status?
        </div>
    
        <table class="form">
        
            <tr class="vat">
                <th><label for="">Current status</label></th>
                <td>
                        <?php echo $status_codes[ $journey['j_status'] ]; ?>
                </td>
            </tr>
            <tr class="vat">
                <th><label for="">Next Status</label></th>
                <td><?php echo $next_status[1]; ?>
                </td>
            </tr>
                                                
            <tr class="vat">
                <th><label for="j_status">Change status to</label></th>
                <td>
                    <select name="j_status" id="j_status">
                        <?php foreach($status_codes as $k => $v) : ?>
                        <option value="<?php echo $k; ?>" <?php if(isset($next_status) && $next_status[0] == $k) echo 'selected="selected"'; ?>><?php echo form_prep($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
			
            <tr class="vat">
                <th><label for="j_tier">Tier</label></th>
                <td>
                    <select name="j_tier" id="j_tier">
                        <option value="">Not set</option>
                        <?php foreach($tiers as $tier) : ?>
                        <option value="<?php echo $tier; ?>" <?php if(isset($journey['j_tier']) && $journey['j_tier'] == $tier) echo 'selected="selected"'; ?>><?php echo form_prep($tier); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/ok.png" alt="Save" /></td>
            </tr>
            
        </table>
    
    <?php echo form_close(); ?>
    
    <div class="clear"></div>

</div>