<div id="risk_type" title="<?php echo (@$risk_type ? 'Update' : 'Add new'); ?> risk">

	<?php echo form_open(current_url() . '?rt_id=' . @$_GET['rt_id'], array('id' => 'risk_type_form')); ?>

	<input type="hidden" name="risk_type_form" value="1" />

    <?php if(@$risk_type) : ?>
    <input type="hidden" name="cr_rt_id" id="cr_rt_id" value="<?php echo $risk_type['cr_rt_id']; ?>" />
    <?php endif; ?>

    <table class="form">

        <tr>
            <th><label for="rt_id">Risk type</label></th>
            <td>
                <select name="rt_id" id="rt_id">
                    <option value="">-- Please select --</option>
                    <?php foreach($risk_types as $group => $rt) : ?>
                    <optgroup label="<?php echo $group; ?>">
						<?php foreach($rt as $risk) : ?>
							<?php if( ! $risk['has_risk'] || $risk['rt_id'] == @$risk_type['cr_rt_id']) : ?>
                            <option value="<?php echo $risk['rt_id']; ?>" <?php if($risk['rt_id'] == @$risk_type['cr_rt_id']) echo 'selected="selected"'; ?>><?php echo $risk['rt_name']; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="impact_score">Impact score</label></th>
            <td>
                <select name="impact_score" id="impact_score">
                    <?php for($i = 1; $i <= 5; $i++) : ?>
                    <option value="<?php echo $i; ?>" <?php if($i == @$risk_type['cr_impact_score']) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="likelihood_score">Likelihood score</label></th>
            <td>
                <select name="likelihood_score" id="likelihood_score">
                    <?php for($i = 1; $i <= 5; $i++) : ?>
                    <option value="<?php echo $i; ?>" <?php if($i == @$risk_type['cr_likelihood_score']) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>

        <tr class="vat">
            <th><label for="risk_to_whom">Risk to whom</label></th>
            <td><textarea name="risk_to_whom" id="risk_to_whom" style="height:100px;"><?php echo form_prep(@$risk_type['cr_risk_to_whom']); ?></textarea></td>
        </tr>

        <tr class="vat">
            <th><label for="protective_factors">Protective factors</label></th>
            <td><textarea name="protective_factors" id="protective_factors" style="height:100px;"><?php echo form_prep(@$risk_type['cr_protective_factors']); ?></textarea></td>
        </tr>

        <tr>
            <td colspan="2" style="text-align:right;">
                <a href="<?php echo current_url(); ?>"><img src="/img/btn/cancel.png" /></a>
                <input type="image" src="/img/btn/<?php echo (@$risk_type ? 'update' : 'add-new'); ?>.png" alt="Save" />
            </td>
        </tr>

    </table>

    <?php echo form_close(); ?>

</div>
