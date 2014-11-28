<div class="header">
	<h2><?php echo (@$s ? 'Update' : 'Add'); ?> internal service</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'is_form')); ?>
        
    	<table class="horizontal_form">
        
        	<tr>
            	<td>
                    <label for="is_name">Service name</label>
                    <input type="text" name="is_name" id="is_name" class="text" value="<?php echo form_prep(@$is['is_name']); ?>" />
                </td>
                                 
                <td><a href="/admin/options/internal-services"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>
                    
            	<td><input type="image" src="/img/btn/<?php echo (@$is ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
                
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

	<?php if($internal_services) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Name</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach($internal_services as $service) : ?>
        <tr class="row">
            <td><?php echo $service['is_name']; ?></td>
            <td class="action"><a href="/admin/options/internal-services/<?php echo $service['is_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="?delete=<?php echo $service['is_id']; ?>" class="action" title="Are you sure you want to delete <?php echo $service['is_name']; ?> as an internal service?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results">No results.</p>
    
    <?php endif; ?>

</div>