<?php
// Outcome count in PHP. Increment if outcomes already set.
// Javascript gets this later
$o_count = 1;
?>

<div class="header">
	<h2><?php echo (@$ocl ? 'Update' : 'Add'); ?> criteria list</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'acl_form')); ?>
        
    	<table class="horizontal_form" style="width: 100%">
        
        	<tr class="vat">
            	
				<td style="width: 33%">
                    
					<label for="acl_name">Name of list</label>
                    <input type="text" name="acl_name" id="acl_name" class="text" value="<?php echo form_prep(@$acl['acl_name']); ?>" />
					<br><br>
					
					<label for="acl_type">Type</label>
					<?php echo form_dropdown('acl_type', $this->config->item('acl_types'), element('acl_type', $acl, NULL)) ?>
					<br><br>
					
					<input type="image" src="/img/btn/<?php echo (@$acl ? 'update' : 'add-new'); ?>.png" alt="Save" />
					<a href="/admin/options/assessment-criteria"><img src="/img/btn/cancel.png" alt="Cancel" /></a>
					
                </td>
                
                
                <td style="width: 66%">
                    
                    <div class="grey">
                        
                        Outcomes <br><br>
                    
                        <table width="100%" id="criteria_outcomes">
                            
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Outcome</th>
                                    <th></th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                
                                <?php if (@$acl['acl_criteria']): ?>
                                
                                    <?php foreach ($acl['acl_criteria'] as $num => $title): ?>
                                    
                                    <tr class="current">
                                        <td class="num"><input type="text" name="acl_criteria[num][]" value="<?php echo $num ?>" /></td>
                                        <td class="outcome"><input type="text" name="acl_criteria[outcome][]" value="<?php echo $title ?>" /></td>
                                        <td class="remove"><a href="#"><img src="/img/icons/cross.png" title="Remove outcome" /></a></td>
                                    </tr>
                                    
                                    <?php $o_count++; ?>
                                    
                                    <?php endforeach; ?>
                                    
                                <?php endif; ?>
								
							</tbody>
							
							<tfoot>
                                
                                <tr id="blank-row">
                                    <td class="num"><input type="text" name="acl_criteria[num][]" /></td>
                                    <td class="outcome"><input type="text" name="acl_criteria[outcome][]" /></td>
                                    <td class="remove"></td>
                                </tr>
                                
                                <tr id="last-row">
                                    <td class="right" colspan="3">
                                        <br><br><a href="#" id="add_row">+ add outcome</a>
                                    </td>
                                </tr>
                                
                            </tfoot>
                            
                        </table>
                    
                    </div>

                </td>
                
            </tr>
            
        </table>
    
    <?php echo form_close(); ?>
	
</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if ($lists) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Name</th>
            <th>Type</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach ($lists as $list): ?>
        <tr class="row">
            <td><?php echo $list['acl_name']; ?></td>
            <td><?php echo $this->config->item($list['acl_type'], 'acl_types'); ?></td>
            <td class="action"><a href="/admin/options/assessment-criteria/<?php echo $list['acl_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="?delete=1&amp;acl_id=<?php echo $list['acl_id']; ?>" class="action" title="Are you sure you want to delete the criteria list <?php echo $list['acl_name']; ?>?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results">No results.</p>
    
    <?php endif; ?>

</div>



<script type="text/javascript">
var q_count = <?php echo $o_count; ?>;
</script>