<div id="event" title="<?php echo (@$event ? 'Update' : 'Add new'); ?> event">

	<?php echo form_open(current_url() . '?je_id=' . @$_GET['je_id'], array('id' => 'event_form')); ?>

        <?php if ( ! empty($appt)) echo form_hidden('ja_id', $appt['ja_id']) ?>

        <?php if(@$event) : ?>
        <div class="valid">
            You are about to update information about an existing event.
        </div>
        <input type="hidden" type="hidden" name="je_id" id="je_id" value="<?php echo $event['je_id']; ?>" />
        <?php else : ?>
        <div class="valid">
            You are about to add information about a new event.
        </div>
        <?php endif; ?>

        <table class="form">

            <tr>
                <th><label for="et_ec_id">Event category</label></th>
                <td>
                    <select name="et_ec_id" id="et_ec_id">
                        <option value="">-- Please Select --</option>
                        <option value="other" <?php echo ($event['je_ec_id'] == 0 ? 'selected="selected"' : '') ?>>Other</option>
                        <?php foreach($event_categories as $event_category) : ?>
                        <option value="<?php echo $event_category['ec_id']; ?>" <?php if(@$event_category['ec_id'] == $event['je_ec_id']) echo 'selected="selected"'; ?>><?php echo form_prep($event_category['ec_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="je_et_id">Event type</label></th>
                <td>
                    <select name="je_et_id" id="je_et_id">
                        <option value="">-- Please select --</option>
                        <?php foreach($event_types as $event_type) : ?>
                        <option value="<?php echo $event_type['et_id']; ?>" class="<?php echo ($event_type['et_ec_id'] ? $event_type['et_ec_id'] : 'other'); ?>" <?php if(@$event['je_et_id'] == $event_type['et_id']) echo 'selected="selected"'; ?>><?php echo form_prep($event_type['et_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <?php echo form_hidden('je_rc_id', 0); ?>

            <tr data-cat="<?php echo EVENT_CAT_APPT ?>">
                <th><label for="ja_date_offered">Date offered</label></th>
                <td><input type="text" name="ja_date_offered" id="ja_date_offered" class="text datepicker" value="<?php echo @$appt['ja_date_offered']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
                <th><label for="je_date">Date</label></th>
                <td>
                	<input type="text" name="je_date" id="je_date" class="text datepicker" value="<?php echo @$event['date_of_event']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
                <th><label for="time">Time</label></th>
                <td>
                    <select name="time_hour">
                        <?php
                        for($i = 00; $i <= 23; $i++)
                        {
                            echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$event['hour_of_event'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                        }
                        ?>
                    </select>
                    :
                    <select name="time_minute">
                        <?php
                        for($i = 00; $i <= 59; $i = $i + 5)
                        {
                            echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$event['minute_of_event'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="vat">
                <th><label for="je_notes">Notes</label></th>
                <td><textarea name="je_notes" id="je_notes" style="width:450px; height:105px;"><?php echo form_prep(@$event['je_notes']); ?></textarea></td>
            </tr>

			<tr data-cat="<?php echo EVENT_CAT_APPT ?>">
				<th><label for="ja_attended">Attended</label></th>
				<td>
					<select name="ja_attended" id="ja_attended">
						<option value="">-- Please select --</option>
						<?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
						<option value="<?php echo $code; ?>" <?php if($code == @$appt['ja_attended']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr id="ja_dr_tr" style="display:none;">
				<th><label for="ja_dr_id">DNA reason</label></th>
				<td>
					<select name="ja_dr_id" id="ja_dr_id">
						<option value="">-- Please select --</option>
						<?php foreach($dna_reasons as $dr) : ?>
						<option value="<?php echo $dr['dr_id']; ?>" <?php if(@$appt['ja_dr_id'] == $dr['dr_id']) echo 'selected="selected"'; ?>><?php echo form_prep($dr['dr_name']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr class="showhide" id="ja_length_tr" style="display:none;">
				<th><label for="ja_length">Length</label></th>
				<td><input type="text" name="ja_length" id="ja_length" class="text" size="3" maxlength="3" value="<?php echo form_prep(@$appt['ja_length']); ?>" style="text-align:right;" /> mins</td>
			</tr>

            <tr>
                <td colspan="2" style="text-align:right;">
                	<button type="submit" name="submit" value="save" class="btn"><div class="btn_img"><?php echo (@$event ? 'Update' : 'Add New'); ?></div></button>
                    <button type="submit" name="submit" value="publish" class="btn modality_btn"><div class="btn_img"><?php echo (@$event ? 'Update' : 'Add New'); ?> and Publish</div></button>
                </td>
            </tr>

        </table>

    <?php echo form_close(); ?>

    <div class="clear"></div>

</div>
