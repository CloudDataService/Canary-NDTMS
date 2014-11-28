<?php if(isset($newly_assigned) && ! empty($newly_assigned)) : ?>

<div class="header">
	<h2>Newly-assigned needing assessment</h2>
</div>

<div class="item results">
	
	<table class="results">
		
		<tr class="order">
			<th>Client ID</th>
			<th>Referral</th>
			<th>Client</th>
		</tr>
		
		<?php foreach ($newly_assigned as $j): ?>
		
		<tr class="row">
			<td><a href="/admin/journeys/info/<?php echo $j['j_id']; ?>"><?php echo '#' . $j['j_c_id']; ?></a></td> 
			<td><?php echo $j['j_date_of_referral_format']; ?></td>
			<td><?php echo $j['c_name']; ?> <?php if($j['c_is_risk'] == 1) echo '<img src="/img/icons/exclamation.png" alt="" class="vat" />'; ?></td>
		</tr>
		
		<?php endforeach; ?>
	
	</table>
	
	<div class="functions">
		<a href="<?php echo site_url('admin/journeys/keyworker?j_status=1') ?>" class="btn">
			<div class="btn_img">View all my open journeys</div>
		</a>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	
</div>

<?php endif; ?>