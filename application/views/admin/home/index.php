<style type="text/css">
div.header {
	width:auto;
}
</style>


<div class="header">
	<h2>Quick links</h2>
</div>
<div class="item quick_links">
	<p style="background-image:url(/img/icons/plus.png);">
		<a href="/admin/journeys/new-journey">Start new journey</a>
		Start a brand new recovery journey
	</p>
	
	<p style="background-image:url(/img/icons/magnifier_large.png);">
		<a href="/admin/clients">Find existing client</a>
		Search for an exisiting client's information.
	</p>
	
	<p style="background-image:url(/img/icons/account.png);">
		<a href="/admin/options/my-account">My account</a>
		Change your account details
	</p> 
	
	<div class="clear"></div>       
</div>


<?php if (isset($newly_assigned) && ! empty($newly_assigned)): ?>

	<!-- Keyworker dashboard -->
	<div class="panel_left" style="width: 475px;">
		<?php $this->load->view('admin/home/index/journeys') ?>
		<?php $this->load->view('admin/home/index/messages') ?>
	</div>
	
	<div class="panel_right" style="width: 395px;">
		<?php $this->load->view('admin/home/index/newly_assigned') ?>
	</div>

<?php else: ?>

	<!-- Regular dashboard -->
	<div class="panel_left" style="width: 475px;">
		<?php $this->load->view('admin/home/index/journeys') ?>
		
	</div>
	
	<div class="panel_right" style="width: 395px;">
		<?php $this->load->view('admin/home/index/messages') ?>
	</div>

<?php endif; ?>

<div class="clear"></div>

<div class="header">
	<h2>Clients</h2>
</div>
<div id="map">
</div>