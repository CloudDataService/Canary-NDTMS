<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/admin/options/usage-log" method="get" id="filter_form">

        <table class="filter">

            <tr>
                <th><label for="date_from">Date from</label></th>
                <th><label for="date_to">Date to</label></th>
                <th><label for="c_id">Client ID</label></th>
                <th><label for="j_id">Journey ID</label></th>
            </tr>

            <tr>
                <td><input type="text" name="date_from" id="date_from" class="text datepicker" value="<?php echo form_prep(@$_GET['date_from']); ?>" /></td>
                <td><input type="text" name="date_to" id="date_to" class="text datepicker" value="<?php echo form_prep(@$_GET['date_to']); ?>" /></td>
                <td><input type="text" name="c_id" id="c_id" class="text" value="<?php echo form_prep(@$_GET['c_id']); ?>" /></td>
                <td><input type="text" name="j_id" id="j_id" class="text" value="<?php echo form_prep(@$_GET['j_id']); ?>" /></td>
                <td><input type="image" src="/img/btn/filter.png" alt="Filter" /></td>
                <td><a href="/admin/options/usage-log"><img src="/img/btn/clear.png" alt="Clear" /></a></td>
            </tr>

        </table>

    </form>

</div>

<div class="header">
	<h2>Usage log</h2>
</div>
<div class="item results">
	<?php if($log) : ?>

    <table class="results">

        <tr class="order">
            <th><a href="?order=l_a_id<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'l_a_id') echo ' class="' . $_GET['sort'] . '"'; ?>>Admin</a></th>
            <th><a href="?order=l_datetime<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'l_datetime') echo ' class="' . $_GET['sort'] . '"'; ?>>Date and time</a></th>
            <th>Description</th>
            <th>Client Name</th>
            <th>Journey ID</th>
            <th>Client ID</th>
        </tr>

        <?php foreach($log as $l) : ?>
        <tr class="row">
            <td><?php echo $l['a_name']; ?></td>
            <td><?php echo $l['l_datetime_format']; ?></td>
            <td><?php echo $l['l_description']; ?></td>
            <td><?php echo $l['l_client_name']; ?></td>
            <td>
                <a href="/admin/journeys/info/<?php echo $l['l_journey']; ?>">
                    #<?php echo $l['l_journey']; ?>
                </a>
            </td>
            <td>#<?php echo $l['l_client']; ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">Your search returned no log information.</p>

    <?php endif; ?>
</div>

<?php echo $this->pagination->create_links(); ?>
