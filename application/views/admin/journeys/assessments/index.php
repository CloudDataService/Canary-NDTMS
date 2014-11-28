<style type="text/css">
	table.form tr th {
		max-width:300px;
	}
	label.error {
		display:block;
	}
</style>
<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<div class="functions">
		<a href="#" id="assessment_criteria_btn"><img src="/img/btn/assessment-criteria.png" alt="Assessment criteria" /></a>
		<a href="#" id="assessment_btn"><img src="/img/btn/new-assessment.png" alt="New assessment" /></a>
		
		<?php
		if ( ! $window_csop['valid']) echo '<div class="ndtms_status status_invalid"><p>CSOP can be taken on or after ' .$window_csop['start']->format('d/m/Y') . '.</p></div>';
		if ( ! $window_top['valid']) echo '<div class="ndtms_status status_invalid"><p>TOP can be taken on or after ' .$window_top['start']->format('d/m/Y') . '.</p></div>';
		?>
		
		<div class="clear"></div>
	</div>

	<?php $this->load->view('admin/journeys/assessments/assessment_criteria_form'); ?>
	
	<?php $this->load->view('admin/journeys/assessments/assessment_form'); ?>
	
	<?php $this->load->view('admin/journeys/assessments/assessments'); ?>

</div>