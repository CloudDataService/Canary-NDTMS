<style type="text/css">
	div.item {
		margin-bottom:5px;
	}
</style>
<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<div class="functions">
	
    	<a href="#" id="risk_type_btn"><img src="/img/btn/add-risk.png" alt="Add risk" /></a>

		<div class="clear"></div>
	</div>
    
	<?php echo $this->load->view('admin/journeys/risks/risk_type_form'); ?>
	
	<?php echo $this->load->view('admin/journeys/risks/risks'); ?>

</div>