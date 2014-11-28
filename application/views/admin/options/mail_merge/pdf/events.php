<h2>Events</h2>

<div class="row header">
	<span class="event_meta">Info</span>
	<span class="event_notes">Notes</span>
</div>

<?php foreach ($events as $event): ?>
	<div class="row">
		<span class="event_meta">
			<strong>Date/time: </strong><br><?php echo $event['je_datetime_format'] ?><br><br>
			<strong>Type: </strong><br><?php echo $event['je_et_name'] ?><br><br>
			<strong>Added by:</strong><br><?php echo $event['added_by'] ?>
		</span>
		<span class="event_notes"><?php echo $event['je_notes'] ?></span>
	</div>
<?php endforeach; ?>