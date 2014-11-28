<div class="header">
	<h2><?php echo (@$dr ? 'Update' : 'Add'); ?> referral source</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'rs_form')); ?>
        
    	<table class="horizontal_form">
        
        	<tr>
            	<td>
                    <label for="rs_name">Name of referral source</label>
                    <input type="text" name="rs_name" id="rs_name" class="text" value="<?php echo form_prep(@$rs['rs_name']); ?>" style="width:350px;" />
                </td>
                
                <td>
                	<label for="rs_type">Type of referral source</label>
                    <select name="rs_type" id="rs_type">
                    	<option value="">-- Please select --</option> 
                        <?php foreach($rs_types as $code => $rs_type) : ?>  
                        <option value="<?php echo $code; ?>" <?php if($code == @$rs['rs_type']) echo 'selected="selected"'; ?>><?php echo $rs_type; ?></option>
                        <?php endforeach; ;?>             
                    </select>
                </td>
                   
                <td><a href="/admin/options/referral-sources"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>
                    
            	<td><input type="image" src="/img/btn/<?php echo (@$rc ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
                
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

	<?php if($referral_sources) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Name</th>
            <th>Type</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach($referral_sources as $referral_source) : ?>
        <tr class="row">
            <td><?php echo $referral_source['rs_name']; ?></td>
            <td><?php echo $rs_types[$referral_source['rs_type']]; ?>
            <td class="action"><a href="/admin/options/referral-sources/<?php echo $referral_source['rs_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="?delete=<?php echo $referral_source['rs_id']; ?>" class="action" title="Are you sure you want to delete <?php echo $referral_source['rs_name']; ?> as a referral source?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results">No results.</p>
    
    <?php endif; ?>

</div>