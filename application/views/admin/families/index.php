<div class="functions">
    <a href="/admin/journeys/performance_output?<?php echo http_build_query($_GET); ?>"><img src="/img/btn/export-to-csv.png" alt="Export to CSV" /></a>
    <a href="/admin/journeys/new-journey?j_type=F"><img src="/img/btn/new-journey.png" alt="Add new journey" /></a>
	<a href="/admin/journeys/appointment_output?<?php echo http_build_query($_GET); ?>"><img src="/img/btn/appointment-csv.png" alt="Appointment CSV" /></a>
    <a href="/admin/journeys/outcomes_output?<?php echo http_build_query($_GET); ?>" class="btn"><div class="btn_img">Outcomes CSV</div></a>

    <div class="clear"></div>
</div>

<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/admin/families" method="get" id="filter_form">

        <table class="filter">

            <tr>
            	<th><label for="j_c_id">Client ID</label></th>
                <th><label for="c_sname">Client surname</label></th>
                <th><label for="date_from">Date from</label></th>
                <th><label for="date_to">Date to</label></th>
                <th><label for="c_catchment_area">Catchment area</label></th>
                <th><label for="j_rc_id">Keyworker</label></th>
            </tr>

            <tr>
            	<td><input type="text" name="j_c_id" id="j_c_id" class="text" value="<?php echo form_prep(@$_GET['j_c_id']); ?>" style="width:75px;" /></td>
                <td><input type="text" name="c_sname" id="c_sname" class="text" value="<?php echo form_prep(@$_GET['c_sname']); ?>" style="width:125px; text-transform:capitalize" /></td>
                <td><input type="text" name="date_from" id="date_from" class="text datepicker" value="<?php echo form_prep(@$_GET['date_from']); ?>" /></td>
                <td><input type="text" name="date_to" id="date_to" class="text datepicker" value="<?php echo form_prep(@$_GET['date_to']); ?>" /></td>
                <td>
                	<select name="c_catchment_area" id="c_catchment_area">
                    	<option value="">-- All --</option>
                        <?php foreach($this->config->config['catchment_area_codes'] as $catchment_area => $values) : ?>
                        <option value="<?php echo $catchment_area; ?>" <?php if($catchment_area == @$_GET['c_catchment_area']) echo 'selected="selected"'; ?>><?php echo $catchment_area; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>

				<td>
					<select name="j_rc_id">
						<option value="">-- Any --</option>
						<option value="0" <?php echo set_select('j_rc_id', 0, $this->input->get('j_rc_id') === '0') ?>>-- N/A --</option>
						<?php foreach ($key_workers as $kw): ?>
						<option value="<?php echo $kw['rc_id'] ?>" <?php echo set_select('j_rc_id', $kw['rc_id'], ($kw['rc_id'] == $this->input->get('j_rc_id'))) ?>><?php echo $kw['rc_name'] ?></option>
						<?php endforeach; ?>
					</select>
				</td>
            </tr>

            <tr>
                <th>Journey ID</th>
            	<th>Journey status</th>
                <th>Tier</th>
				<th colspan="3"></th>
            </tr>

            <tr>
                <td><input type="text" name="j_id" id="j_id" class="text" value="<?php echo form_prep(@$_GET['j_id']); ?>" style="width:75px;" /></td>
            	<td>
                	<select name="j_status">
                    	<option value="">-- All --</option>
                    	<?php foreach($this->config->config['j_status_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$_GET['j_status']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select name="j_tier">
                        <option value="">-- All --</option>
                        <?php foreach(array('1' => 'Brief intervention', '2' => 'Tier 2', '3' => 'Tier 3', '-1' => '- None -') as $key => $value) : ?>
                        <?php // foreach(array('2' => 'Tier 2', '3' => 'Tier 3', '-1' => '- None -') as $key => $value) : ?>
                            <option value="<?php echo $key; ?>" <?php if($key == @$_GET['j_tier']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>

				<td colspan="2"></td>
				<td>
					<input type="image" src="/img/btn/filter.png" alt="Filter" />
					<a href="/admin/families"><img src="/img/btn/clear.png" alt="Clear" /></a>
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
            <th><a href="?order=rc_name<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'rc_name') echo ' class="' . $_GET['sort'] . '"'; ?>>Keyworker</a></th>
            <th><a href="?order=ji_csop_due<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'ji_scop_due') echo ' class="' . $_GET['sort'] . '"'; ?>>CSOP Due</a></th>
            <th>Status</th>
            <th><a href="?order=last_seen<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'last_seen') echo ' class="' . $_GET['sort'] . '"'; ?>>Last Date Seen</th>
        </tr>

        <?php foreach($journeys as $j) : ?>
        <tr class="row">
            <td><a href="/admin/journeys/info/<?php echo $j['j_id']; ?>"><?php echo '#' . $j['j_c_id']; ?></a></td>
            <td><?php echo $j['j_date_of_referral_format']; ?></td>
            <td>
				<?php echo $j['c_name']; ?>
				<?php if($j['c_is_risk'] == 1) echo '<img src="/img/icons/exclamation.png" title="Risk: '. $j['ji_flagged_risk_summary'] .'" alt="" class="vat" />'; ?>
            </td>
            <td><?php echo $j['rc_name']; ?></td>
            <td><?php echo journey_ass_date($j['ji_csop_due'], 'd/m/Y') ?></td>
            <td><?php echo $j['j_status']; ?></td>
            <td><?php echo date_fmt($j['last_seen'], 'd/m/Y'); ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">Your search returned no journeys.</p>

    <?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
