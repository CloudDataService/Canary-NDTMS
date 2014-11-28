<h2>Appointments</h2>

<div class="row header">
	<span class="appt_meta">Info</span>
	<span class="appt_notes">Notes</span>
</div>

<?php foreach ($appointments as $appointment): ?>
	<div class="row">
		<span class="appt_meta">
			<strong>Offered: </strong><br><?php echo $appointment['ja_date_offered_format'] ?><br><br>
			<strong>Date/time: </strong><br><?php echo $appointment['ja_datetime_format'] ?><br><br>
			<strong>Keyworker: </strong><?php echo $appointment['ja_rc_name'] ?><br><br>
			<strong>Attended: </strong><?php echo $appointment['ja_attended'] . ($appointment['ja_length'] != '' ? " for " . $appointment['ja_length'] . " mins" : NULL) ?>
		</span>
		<span class="appt_notes"><?php echo $appointment['ja_notes'] ?></span>
	</div>
<?php endforeach; ?>