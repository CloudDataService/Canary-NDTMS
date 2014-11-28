<div class="header">
        <h2>Activity information</h2>
</div>

<div class="item">
    
    <table class="form">
        
        <tr>
            <th>Name</th>
            <td><?php echo $support_group['sp_name']; ?></td>
        </tr>
        
        <tr class="vat">
            <th>Description</th>
            <td><?php echo $support_group['sp_description']; ?></td>
        </tr>
        
        <tr>
            <th>Default date and time</th>
            <td><?php echo $support_group['sp_default_day'] . $support_group['sp_default_time']; ?></td>
        </tr>
                
    </table>

</div>

<div class="total"><?php echo $total; ?></div>

<div class="header">
	<h2>Sessions</h2>
</div>

<div class="item results">

	<?php if($sessions) : ?>
    
    <table class="results">
         
        <tr class="order">
            <th><a href="?order=sps_datetime<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sps_datetime') echo ' class="' . $_GET['sort'] . '"'; ?>>Date and time</a></th>
            <th><a href="?order=sps_location<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sps_location') echo ' class="' . $_GET['sort'] . '"'; ?>>Location</a></th>
            <th><a href="?order=sps_total_registered<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sps_total_registered') echo ' class="' . $_GET['sort'] . '"'; ?>>Total registered</a></th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach($sessions as $sps) : ?>
        <tr class="row">
            <td><?php echo $sps['sps_datetime_format']; ?></td> 
            <td><?php echo $sps['sps_location']; ?></td>
            <td><?php echo $sps['sps_total_registered']; ?></td>
            <td class="action"><a href="/admin/activities/session/<?php echo $support_group['sp_id'] . '/' . $sps['sps_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="/admin/activities/session/<?php echo $support_group['sp_id'] . '/' . $sps['sps_id']; ?>?delete=1" class="action" title="Are you sure you want to delete this activity?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    <p class="no_results">There are no listed sessions for this activity.</p>
    <?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>

<a href="/admin/activities/session/<?php echo $support_group['sp_id']; ?>" style="float:right;"><img src="/img/btn/add-new-session.png" alt="Add new session" /></a>

<a href="/admin/activities" ><img src="/img/btn/back.png" alt="Back" /></a>