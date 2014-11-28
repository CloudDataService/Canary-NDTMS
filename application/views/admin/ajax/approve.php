<div id="approval" title="<?php echo 'Submit journey for approval'; ?>">
      
	<?php echo form_open(current_url() . '?j_id=' . $journey['j_id'], array('id' => 'approve_form')); ?>
       	
        
        <div class="valid">
            Are you sure you want to submit the journey for approval?
        </div>
    
        <table class="form">
        
            <tr class="vat">
                <th><label for="">Current status</label></th>
                <td>
					<?php echo $this->config->item($journey['j_status'], 'j_status_codes') ?>
                </td>
            </tr>
            <tr class="vat">
                <th><label for="">Status when approved</label></th>
                <td>
					<?php
					echo $next_status[1];
					echo form_hidden('j_new_status', $next_status[0]);
					?>
                </td>
            </tr>
			
            <tr class="vat">
                <th><label for="">Administrators to be notified</label></th>
                <td>
                    <?php
					foreach($approving_admins as $a)
					{
						echo $a['a_fullname'] . '<br />';
					}
                    ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/ok.png" alt="Save" /></td>
            </tr>
            
        </table>
    
    <?php echo form_close(); ?>
    
    <div class="clear"></div>

</div>