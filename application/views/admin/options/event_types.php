<div class="header">
	<h2><?php echo (@$et ? 'Update' : 'Add'); ?> event type</h2>
</div>

<div class="item">

	<?php echo form_open(
        current_url(),
        array('id' => 'et_form'),
        array(
            'target' => 'et',
            'et_id' => element('et_id', $et, NULL),
        )
    ); ?>
        
    	<table class="horizontal_form">
        
        	<tr>
                
                <td>
                    <label for="et_ec_id">Event category</label>
                    <select name="et_ec_id" id="et_ec_id">
                        <option value="">-- Please select --</option>
                        <?php foreach($event_categories as $event_category) : ?>
                        <option value="<?php echo $event_category['ec_id']; ?>" <?php if(@$event_category['ec_id'] == $et['et_ec_id']) echo 'selected="selected"'; ?>><?php echo form_prep($event_category['ec_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                
            	<td>
                    <label for="et_name">Name of event type</label>
                    <input type="text" name="et_name" id="et_name" class="text" value="<?php echo form_prep(@$et['et_name']); ?>" />
                </td>
                                                 
                <td><a href="/admin/options/event-types"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>
                
            	<td><input type="image" src="/img/btn/<?php echo (@$et ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
                
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

	<?php if($event_types) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Category</th>
            <th>Name</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach($event_types as $event_type) : ?>
        <tr class="row">
            <td><?php echo $event_type['et_ec_name']; ?></td>
            <td><?php echo $event_type['et_name']; ?></td>
            <td class="action"><a href="<?php echo current_url() ?>?et_id=<?php echo $event_type['et_id'] ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="<?php echo current_url() ?>?et_id=<?php echo $event_type['et_id'] ?>&amp;delete=1" class="action" title="Are you sure you want to delete <?php echo $event_type['et_name']; ?> as an event type?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results">No results.</p>
    
    <?php endif; ?>

</div>


<br>
<h2>Event Categories</h2>
<br>

<div class="header" id="cat">
    <h2>Add/update event category</h2>
</div>

<div class="item">
    
    <?php echo form_open(
        current_url(),
        array('id' => 'ec_form'),
        array(
            'target' => 'ec',
            'ec_id' => element('ec_id', $ec, NULL),
        )
    ); ?>
    
        <table class="horizontal_form">
            
            <tr>
                <td>
                    <label for="ec_name">Event category name</label>
                    <input type="text" name="ec_name" id="ec_name" class="text" value="<?php echo form_prep(@$ec['ec_name']); ?>" />
                </td>
                                    
                <td><input type="image" src="/img/btn/save.png" alt="Save" /></td>
                
                <td><a href="/admin/options/event-types"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>
            </tr>
            
        </table>
               
    <?php echo form_close(); ?>
</div>

<div class="item results">
    
    <?php if($event_categories) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Event Category Name</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    
        <?php foreach($event_categories as $event_category) : ?>
        <tr class="row">
            <td><?php echo $event_category['ec_name']; ?></td>
            <td class="action"><a href="/admin/options/event-types?ec_id=<?php echo $event_category['ec_id']; ?>#cat"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <?php if ($event_category['ec_id'] != EVENT_CAT_APPT): ?>
            <td class="action"><a href="/admin/options/event-types?ec_id=<?php echo $event_category['ec_id']; ?>&amp;delete=1" class="action" title="Click OK to delete this event category."><img src="/img/icons/cross.png" alt="Delete" /></a></td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>

    </table>
    
    <?php else : ?>
    
    <p class="no_results">No results.</p>
    
    <?php endif; ?>
    
</div>