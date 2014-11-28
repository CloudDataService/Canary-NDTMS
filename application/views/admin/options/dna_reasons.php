<div class="header">
	<h2><?php echo (@$dr ? 'Update' : 'Add'); ?> DNA reason</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'dr_form')); ?>
        
    	<table class="horizontal_form">
        
        	<tr>
            	<td>
                    <label for="dr_name">Name of DNA reason</label>
                    <input type="text" name="dr_name" id="dr_name" class="text" value="<?php echo form_prep(@$dr['dr_name']); ?>" />
                </td>
                                 
                <td><a href="/admin/options/dna-reasons"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>
                    
            	<td><input type="image" src="/img/btn/<?php echo (@$dr ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
                
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

	<?php if($dna_reasons) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Name</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach($dna_reasons as $dna_reason) : ?>
        <tr class="row">
            <td><?php echo $dna_reason['dr_name']; ?></td>
            <td class="action"><a href="/admin/options/dna-reasons/<?php echo $dna_reason['dr_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="?delete=<?php echo $dna_reason['dr_id']; ?>" class="action" title="Are you sure you want to delete <?php echo $dna_reason['dr_name']; ?> as a DNA reason?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results">No results.</p>
    
    <?php endif; ?>

</div>