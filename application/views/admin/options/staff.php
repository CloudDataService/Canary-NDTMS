<div class="header">
	<h2><?php echo (@$s ? 'Update' : 'Add'); ?> staff member</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 's_form')); ?>
        
    	<table class="horizontal_form">
        
        	<tr>
            	<td>
                    <label for="s_name">Staff member</label>
                    <input type="text" name="s_name" id="s_name" class="text" value="<?php echo form_prep(@$s['s_name']); ?>" />
                </td>
                                 
                <td><a href="/admin/options/staff"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>
                    
            	<td><input type="image" src="/img/btn/<?php echo (@$s ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
                
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

	<?php if($staff) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Name</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach($staff as $staff_member) : ?>
        <tr class="row">
            <td><?php echo $staff_member['s_name']; ?></td>
            <td class="action"><a href="/admin/options/staff/<?php echo $staff_member['s_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="?delete=<?php echo $staff_member['s_id']; ?>" class="action" title="Are you sure you want to delete <?php echo $staff_member['s_name']; ?> as a staff member?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results">No results.</p>
    
    <?php endif; ?>

</div>