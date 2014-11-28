<div class="header">
    <h2>Filter</h2>
</div>

<div class="item">

    <form action="/admin/journeys/keyworker" method="get" id="filter_form">

        <table class="filter" style="width: 100%">
			
            <tr>
                <th><label for="j_c_id">Client ID</label></th>
                <th><label for="c_sname">Client surname</label></th>
				<th><label for="j_status">Journey status</label></th>
				<th><label for="j_ndtms_valid">NDTMS valid</label></th>
                <th></th>
            </tr>
			
            <tr>
                <td><input type="text" name="j_c_id" id="j_c_id" class="text" value="<?php echo form_prep(@$_GET['j_c_id']); ?>" style="width:75px;" /></td>
                <td><input type="text" name="c_sname" id="c_sname" class="text" value="<?php echo form_prep(@$_GET['c_sname']); ?>" style="width:125px; text-transform:capitalize" /></td>
				<td>
					<select name="j_status">
						<option value="">-- All --</option>
						<?php foreach($this->config->config['j_status_codes'] as $code => $value) : ?>
						<?php if ($code == 2) continue; ?>
						<option value="<?php echo $code; ?>" <?php if($code == @$_GET['j_status']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<select name="j_ndtms_valid">
						<option value="">-- All --</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if($code == @$_GET['j_ndtms_valid']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
				
                <td align="right">
                    <input type="image" src="/img/btn/filter.png" alt="Filter" />
                    <a href="/admin/journeys/keyworker"><img src="/img/btn/clear.png" alt="Clear" /></a>
                </td>
            </tr>

        </table>

    </form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if($journeys) : ?>

    <table class="results">

        <tr class="order">
            <th><a href="?order=j_c_id<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'j_c_id') echo ' class="' . $_GET['sort'] . '"'; ?>>Client ID</a></th>
            <th><a href="?order=j_date_of_referral<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'j_date_of_referral') echo ' class="' . $_GET['sort'] . '"'; ?>>Date of referral</a></th>
            <th><a href="?order=c_sname<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'c_sname') echo ' class="' . $_GET['sort'] . '"'; ?>>Client name</a></th>
            <th><a href="?order=j_status<?php echo $sort ?>"<?php if($this->input->get('order') == 'j_status') echo 'class="' . $this->input->get('sort') . '"' ?>>Status</a></th>
            <th>CSOP Due</th>
            <th>CSOP Due Date</th>
            <th><a href="?order=j_ndtms_valid<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'j_ndtms_valid') echo ' class="' . $_GET['sort'] . '"'; ?>>NDTMS</th>
        </tr>

        <?php foreach($journeys as $j) : ?>
        <tr class="row">
            <td><a href="/admin/journeys/info/<?php echo $j['j_id']; ?>"><?php echo '#' . $j['j_c_id']; ?></a></td>
            <td><?php echo $j['j_date_of_referral_format']; ?></td>
            <td>
				<?php echo $j['c_name']; ?>
				<?php if($j['c_is_risk'] == 1) echo '<img src="/img/icons/exclamation.png" alt="" class="vat" />'; ?>
            </td>
            <td><?php echo $j['j_status']; ?></td>
			<td>-</td>
			<td><?php echo journey_ass_date($j['ji_csop_due'], 'd/m/Y') ?></td>
            <td class="action"><?php echo ($j['j_ndtms_valid'] == 'Yes' ? '<img src="/img/icons/tick.png" alt="Yes" />' : '<img src="/img/icons/cross.png" alt="No" />'); ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">Your search returned no journeys.</p>

    <?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>