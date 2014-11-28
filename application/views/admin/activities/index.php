<div class="functions">
   
    <a href="/admin/activities/set"><img src="/img/btn/add-new.png" alt="Add new" /></a>
	
	<a href="/admin/activities/groups_output"><img src="/img/btn/export-group-csv.png" alt="Export Group CSV" /></a>
    
    <div class="clear"></div>
</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if($support_groups) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th><a href="?order=sp_name<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sp_name') echo ' class="' . $_GET['sort'] . '"'; ?>>Name</a></th>
            <th><a href="?order=sp_default_day<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sp_default_day') echo ' class="' . $_GET['sort'] . '"'; ?>>Default day</a></th>
            <th><a href="?order=sp_default_time<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'sp_default_time') echo ' class="' . $_GET['sort'] . '"'; ?>>Default time</a></th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach($support_groups as $sp) : ?>
        <tr class="row">
            <td><a href="/admin/activities/info/<?php echo $sp['sp_id']; ?>"><?php echo $sp['sp_name']; ?></a></td> 
            <td><?php echo $sp['sp_default_day']; ?></td>
            <td><?php echo $sp['sp_default_time']; ?></td>
            <td class="action"><a href="/admin/activities/set/<?php echo $sp['sp_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="?delete=<?php echo $sp['sp_id']; ?>" class="action" title="Are you sure you want to delete this support group?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results">There are no listed activities.</p>
    
    <?php endif; ?>

</div>