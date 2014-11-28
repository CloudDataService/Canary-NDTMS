<div class="functions">

    <a href="/admin/clients/set/"><img src="/img/btn/add-new.png" alt="Add new" /></a>

    <div class="clear"></div>
</div>

<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/admin/clients" method="get" id="filter_form">

        <table class="filter">

            <tr>
                <th><label for="c_id">Client ID</label></th>
                <th><label for="c_fname">Forename</label></th>
                <th><label for="c_sname">Surname</label></th>
                <th><label for="c_date_of_birth">Date of birth</label></th>
                <th><label for="c_post_code">Post code</label></th>
                <th><label for="pp">Results per page</label></th>
                <td rowspan="2" style="text-align: right">
                    <input type="image" src="/img/btn/filter.png" alt="Filter" />
                    <a href="/admin/clients"><img src="/img/btn/clear.png" alt="Clear" /></a>
                </td>
            </tr>

            <tr>
                <td><input type="text" name="c_id" id="c_id" class="text" value="<?php echo form_prep(@$_GET['c_id']); ?>" style="width:60px;" /></td>
                <td><input type="text" name="c_fname" id="c_fname" class="text" value="<?php echo form_prep(@$_GET['c_fname']); ?>" style="width:125px; text-transform:capitalize" /></td>
                <td><input type="text" name="c_sname" id="c_sname" class="text" value="<?php echo form_prep(@$_GET['c_sname']); ?>" style="width:125px; text-transform:capitalize" /></td>
                <td><input type="text" name="c_date_of_birth" id="c_date_of_birth" class="text datepicker" value="<?php echo form_prep(@$_GET['c_date_of_birth']); ?>" /></td>
                <td><input type="text" name="c_post_code" id="c_post_code" class="text" value="<?php echo form_prep(@$_GET['c_post_code']); ?>" style="width:80px; text-transform:uppercase;" /></td>
                <td>
                    <select name="pp" id="pp" style="width: 100px">
                        <?php foreach($pp as $pp) : ?>
                            <option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="j_type">Client Type</label></th>
            </tr>
            <tr>
                <td>
                    <select name="j_type" id="j_type">
                        <?php foreach(array('F' => "Family", 'C' => 'Service User') as $j_type_key => $j_type_value ) : ?>
                            <option value="<?=$j_type_key?>" <?php if(@$_GET['j_type'] == $j_type_key) echo 'selected="selected"'; ?>><?=$j_type_value?></option>
                        <?php endforeach; ?>
                    </select>
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

	<?php if($clients) : ?>

    <table class="results">

        <tr class="order">
            <th><a href="?order=c_id<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'c_id') echo ' class="' . $_GET['sort'] . '"'; ?>>Client ID</a></th>
            <th><a href="?order=j_type<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'j_type') echo ' class="' . $_GET['sort'] . '"'; ?>>Client Type</a></th>
            <th><a href="?order=c_sname<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'c_sname') echo ' class="' . $_GET['sort'] . '"'; ?>>Client name</a></th>
            <th><a href="?order=c_date_of_birth<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'c_date_of_birth') echo ' class="' . $_GET['sort'] . '"'; ?>>Date of birth</a></th>
            <th><a href="?order=c_post_code<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'c_post_code') echo ' class="' . $_GET['sort'] . '"'; ?>>Post code</a></th>
        </tr>

        <?php foreach($clients as $c) : ?>
        <tr class="row">
            <td><a href="/admin/clients/info/<?php echo $c['c_id']; ?>"><?php echo '#' . $c['c_id']; ?></a></td>
            <td><?php echo (trim($c['j_type']) == 'F' ? 'Family' : 'Service User'); ?></td>
            <td>
				<?php echo $c['c_name']; ?>
                <?php if($c['c_is_risk'] == 1) echo '<img src="/img/icons/exclamation.png" alt="" class="vat" />'; ?>
            </td>
            <td><?php echo $c['c_date_of_birth_format']; ?> <em>(age <?php echo $c['c_age']; ?>)</em></td>
            <td><?php echo $c['c_post_code']; ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">Your search returned no clients.</p>

    <?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
