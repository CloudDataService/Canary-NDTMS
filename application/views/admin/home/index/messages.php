
<div class="header">
	<h2>Recent Messages</h2>
</div>

<div class="item results"> 
	<?php if($messages) : ?>
	<table class="results">    
		<tr class="order">
			<th>Date</th>
			<th>Journey</th>
			<th>Message</th>
			<th>Action</th>
		</tr>
		
		<?php foreach ($messages as $m): ?>
		
		<tr class="row vat">
			<td><?php echo $m['m_sent_date_format']; ?></td>
			<td>
				<?php
				if($m['m_j_id'] != null)
				{
					echo '<a href="' . site_url('admin/journeys/info/' .$m['m_j_id']) .'">#'. $m['m_j_id'] .'</a>';
				}
				else
				{
					echo 'N/A';
				}
				?>
			</td>
			<td><?php echo $m['m_text']; ?></td>
			<td>
				<?php
				if ($m['m_link'] !== NULL && $m['m_link_text'] !== NULL)
				{
					echo '<a href="'. $m['m_link'] .'">'. $m['m_link_text'] .'</a>';
				}
				else
				{
					echo 'none';
				}
				?>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
	</table>
	
	<?php else: ?>
	
	<p class="no_results">No unread messages.</p>
	
	<?php endif; ?>
	 
</div>
