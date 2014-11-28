<div class="header">
	<h2>Appointments</h2>
</div>
<div class="item results">

	<?php if($journey['appointments']) : ?>

	<table class="results vat paginated" data-items="4">

		<thead>

			<tr class="order">
				<th>Date offered</th>
				<th>Date and time</th>
				<th>Client attended</th>
				<th>Notes</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach($journey['appointments'] as $ja) : ?>
				<tr class="row no_click">
					<td><?php echo $ja['ja_date_offered_format']; ?></td>
					<td><?php echo $ja['ja_datetime_format']; ?></td>
					<td>
						<?php echo $ja['ja_attended']; ?>
						<?php if($ja['dr_name']) echo '<small class="metadata">' . $ja['dr_name'] . '</small>'; ?>
						<?php if($ja['ja_length']) echo '<small class="metadata">' . $ja['ja_length'] . ' mins</small>'; ?>
					</td>
					<td><p class="event_notes"><?php echo ($ja['ja_notes'] ? nl2br($ja['ja_notes']) : 'N/A'); ?></p></td>
					<?php if (can_edit_event($ja)): ?>
						<td class="action no_click"><a href="/admin/ajax/event/<?php echo $journey['j_id'] . '/' . $ja['ja_je_id']; ?>" class="event_btn"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
						<td class="action"><a href="?ja_id=<?php echo $ja['ja_id']; ?>&amp;delete=1" class="action" title="Are you sure you want to delete this appointment?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
					<?php else: ?>
						<td></td>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>

	</table>

	<?php else : ?>
	<p class="no_results">There have been no appointments made for this journey.</p>
	<?php endif; ?>

</div>
<p class="back_to_top">[<a href="#top">back to top</a>]</p>
