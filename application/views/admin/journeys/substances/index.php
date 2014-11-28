<style type="text/css">
	table.form tr th {
		max-width:185px;
	}
</style>
<?php $this->load->view('admin/journeys/journey_nav'); ?>


<?php echo form_open(current_url(), array('id' => 'alcohol_form')); ?>

<div class="item">
    
    <div class="grey">
    	<?php $this->load->view('admin/journeys/substances/drugs'); ?>
    </div>
    
    <div class="grey">
	<?php $this->load->view('admin/journeys/substances/alcohol'); ?>
	</div>
    
    <div style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></div>
    
</div>

<?php echo form_close(); ?>