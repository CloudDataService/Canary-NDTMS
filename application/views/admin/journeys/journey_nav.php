<?php
	if(isset($journey['j_id']))
	{
		if($journey['j_status'] == 3)
		{
			$journey_nav = array('Contact' => '/admin/journeys/contact/' . $journey['j_id']);
		}
		else if($journey['j_status'] == 4)
		{
			$journey_nav = array('Contact' => '/admin/journeys/contact/' . $journey['j_id'],
							 'Consultation' => '/admin/journeys/consultation/' . $journey['j_id']);
		}
		else
		{

			$journey_nav = array('Contact' => '/admin/journeys/contact/' . $journey['j_id'],
							 'Consultation' => '/admin/journeys/consultation/' . $journey['j_id'],
							 'Assessment' => '/admin/journeys/assessment/' . $journey['j_id'],
						//  'Journey' => '/admin/journeys/journey/' . $journey['j_id'],
						//  'Client' => '/admin/journeys/client/' . $journey['j_id'],
							 'Outcomes' => '/admin/assessments/index/' . $journey['j_id'],
							 'Journey' => '/admin/journeys/journey/' . $journey['j_id']
						//	 'Risks' => '/admin/risks/index/' . $journey['j_id'],
						//	 'Substances' => '/admin/substances/index/' . $journey['j_id'],
						//	 'Offending' => '/admin/offending/index/' . $journey['j_id']);
						);
		}
	}
	else
	{
		$journey_nav = array('Contact' => '/admin/journeys/contact/j_type=' . element('j_type', $journey, ''));
	}
?>

<?php if (isset($journey['j_ndtms_valid'])): ?>
	<div class="functions">

		<?php /* <a href="/admin/ajax/appointment/<?php echo element('j_id', $journey, ''); ?>" class="appointment_btn"><img src="/img/btn/make-appointment.png" alt="Make appointment" /></a> */ ?>

		<?php if($journey['j_published'] == 0) : ?>
			<a href="/admin/ajax/event/<?php echo element('j_id', $journey, ''); ?>" class="event_btn"><img src="/img/btn/add-event.png" alt="Add event" /></a>
			<?php /* <a href="/admin/ajax/note/<?php echo element('j_id', $journey, ''); ?>" class="note_btn"><img src="/img/btn/add-notes.png" alt="Add notes" /></a> */ ?>
		<?php endif; ?>

		<a href="/admin/ajax/modality/<?php echo element('j_id', $journey, ''); ?>" class="btn modality_btn">
			<div class="btn_img">Add Modality</div>
		</a>
		<?php
				echo '<a href="/admin/ajax/family/'. element('j_id', $journey, '') .'" class="btn family_btn">
							<div class="btn_img">Family</div>
					  </a>';
		?>


		<?php if($journey['j_published'] == 0) : ?>
			<a href="/admin/journeys/info/<?php echo element('j_id', $journey, ''); ?>/?publish=1" class="btn publish_btn publish-journey-confirm"><div class="btn_img">Publish Journey</div></a>
		<?php elseif($journey['j_published'] == 1 && $this->session->userdata('a_master') ) : ?>
			<a href="/admin/journeys/info/<?php echo element('j_id', $journey, ''); ?>/?unpublish=1" class="btn publish_btn"><div class="btn_img">Unpublish Journey</div></a>
		<?php endif; ?>


		<?php if(($journey['j_type'] == 'C' && $permissions['apt_can_approve_client'] == 1)
				|| ($journey['j_type'] == 'F' && $permissions['apt_can_approve_family'] == 1)) : ?>
			<a href="/admin/ajax/status/<?php echo element('j_id', $journey, ''); ?>" class="btn status_btn"><div class="btn_img">Approve status change</div></a>
		<?php elseif($journey['j_status'] == 3 || $journey['j_status'] == 4) : ?>
			<a href="/admin/ajax/approve/<?php echo element('j_id', $journey, ''); ?>" class="btn approve_btn"><div class="btn_img">Submit for Approval</div></a>
		<?php endif; ?>

		<?php if ($journey['j_type'] == 'C'): ?>

			<?php if($journey['j_ndtms_valid'] == 'Yes') : ?>
			<div class="ndtms_status status_valid">
				<p>This journey is NDTMS valid</p>
			</div>
			<?php else : ?>
			<div class="ndtms_status status_invalid">
				<p>This journey is not NDTMS valid. <a href="/admin/journeys/ndtms_valid/<?php echo $journey['j_id']; ?>" id="ndtms_valid_btn" style="float:none; margin:0px;">Find out why</a></p>
			</div>
			<?php endif; ?>

		<?php endif; ?>

		<div class="clear"></div>
</div>
<?php endif; ?>

<div class="journey_nav">
	<ul>
	<?php
	foreach($journey_nav as $title => $href)
	{
		echo '<li class="' . ($title == $this->layout->get_last_title() ? 'selected' : '') . '"><a href="' . $href . '">' . $title . '</a></li>';
	}
	?>
	</ul>
	<div class="clear"></div>
</div>
