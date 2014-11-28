<div class="header">
	<h2><?php echo (@$rc ? 'Update' : 'Add'); ?> key worker</h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'rc_form')); ?>

    	<table class="horizontal_form">

        	<tr>
            	<td>
                    <label for="rc_name">Name of key worker</label>
                    <input type="text" name="rc_name" id="rc_name" class="text" value="<?php echo form_prep(@$rc['rc_name']); ?>" />
                </td>

                <td>
                    <label for="a2rc_a_id" style="margin-bottom: 5px">Administrator user</label>
                    <select name="a2rc_a_id">
                        <option value="">(None)</option>
                        <?php foreach ($admins as $admin): ?>
                        <option value="<?php echo $admin['a_id'] ?>" <?php echo set_select('a2rc_a_id', $admin['a_id'], ($admin['a_id'] == element('a2rc_a_id', $rc, FALSE))) ?>><?php echo $admin['a_fname'] . ' ' . $admin['a_sname'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>

                <td>
                    <label for="rc_jr_id" style="margin-bottom: 5px">Job Role</label>
                    <?php echo form_dropdown('rc_jr_id', $job_roles, element('rc_jr_id', $rc)) ?>
                </td>

                <td style="width: 100px">
                    <label for="rc_family_worker" style="margin-bottom: 5px">Family worker</label>
                    <?php
                    echo form_dropdown('rc_family_worker', $this->config->item('yes_no_codes'), element('rc_family_worker', $rc, 2));
                    ?>
                </td>

                <td><a href="/admin/options/key-workers"><img src="/img/btn/cancel.png" alt="Cancel" /></a></td>

            	<td><input type="image" src="/img/btn/<?php echo (@$rc ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>

            </tr>

        </table>

    <?php echo form_close(); ?>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if($recovery_coaches) : ?>

    <table class="results">

        <tr class="order">
            <th style="width: 16px; padding: 0;"></th>
            <th>Name</th>
            <th>Administrator</th>
            <th>Job Role</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>

        <?php foreach($recovery_coaches as $recovery_coach) : ?>
        <tr class="row">
            <td style="width: 16px; padding: 0 0 0 10px;">
                <?php if ($recovery_coach['rc_family_worker'] == 1): ?>
                <img src="/img/icons/family.png" alt="Family worker" title="Family worker" />
                <?php endif; ?>
            </td>
            <td><?php echo $recovery_coach['rc_name']; ?></td>
            <td><?php echo ($recovery_coach['a2rc_a_id'] ? $recovery_coach['a_fname'] . ' ' . $recovery_coach['a_sname'] : '-') ?></td>
            <td><?php echo $recovery_coach['jr_title'] ?></td>
            <td class="action"><a href="/admin/options/key-workers/<?php echo $recovery_coach['rc_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <td class="action"><a href="?delete=<?php echo $recovery_coach['rc_id']; ?>" class="action" title="Are you sure you want to delete <?php echo $recovery_coach['rc_name']; ?> as a key worker?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">No results.</p>

    <?php endif; ?>

</div>