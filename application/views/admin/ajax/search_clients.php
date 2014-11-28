<?php if($clients) : ?>

<p>Do you want...</p>

<table class="results">
	
    <tr class="order">
    	<th>Name</th>
        <th>Date of birth</th>
        <th>Post code</th>
        <th></th>
	</tr>
    
	<?php foreach($clients as $c) : ?>
    
    <?php
    $url = $_GET['url'] . $c['c_id'];
    if (element('j_type', $_GET, NULL))
    {
        $url .= '?j_type=' . $_GET['j_type'];
    }
    ?>
    
    <tr class="row">
        <td><?php echo $c['c_name']; ?></td>
        <td><?php echo $c['c_date_of_birth']; ?></td>
        <td><?php echo $c['c_post_code']; ?></td>
        <td><a href="<?php echo $url ?>"><img src="/img/btn/right.png" alt="Select" /></a></td>
    </tr>
    
    <?php endforeach; ?>

</table>

<?php endif; ?>