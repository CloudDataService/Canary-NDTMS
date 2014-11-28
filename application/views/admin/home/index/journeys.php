<div class="header">
	<h2>Latest journeys</h2>
</div>
<div class="item results">
	
	<?php if ($journeys): ?>
	
	<table class="results">
		
		<tr class="order">
			<th>Client ID</th>
			<th>Referral</th>
			<th>Client</th>
			<th>Keyworker</th>
		</tr>
		
		<?php foreach($journeys as $j) : ?>
		
		<tr class="row">
			<td><a href="/admin/journeys/info/<?php echo $j['j_id']; ?>"><?php echo '#' . $j['j_c_id']; ?></a></td> 
			<td><?php echo $j['j_date_of_referral_format']; ?></td>
			<td><?php echo $j['c_name']; ?> <?php if($j['c_is_risk'] == 1) echo '<img src="/img/icons/exclamation.png" alt="" class="vat" />'; ?></td>
			<td><?php echo $j['rc_name']; ?></td>
		</tr>
		
		<?php endforeach; ?>
	
	</table>
	
	<div class="functions">
		<a href="<?php echo site_url('admin/journeys') ?>" class="btn"><div class="btn_img">View Service User</div></a>
		<a href="<?php echo site_url('admin/families') ?>" class="btn"><div class="btn_img">View Family</div></a>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	
	<?php else : ?>
	
	<p class="no_results">No results</p>
	
	<?php endif; ?>
	
</div>