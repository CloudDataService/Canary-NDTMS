<div id="modality" title="Add new modality">

    <?php echo form_open(current_url() . '?mod_id=' . @$_GET['mod_id'], array('id' => 'modality_form')); ?>

        <div class="valid">
            You are about to add a new modality.
        </div>

        <table class="form" style='width: 624px;'>

            <tr>
                <th><label for="mod_cpdate">Care Plan Date</label></th>
                <td>
                    <input type="text" name="mod_cpdate" id="mod_cpdate" class="text datepicker" value="<?php echo @$modality['CPLANDT']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
                <th><label for="mod_treatment">Treatment</label></th>
                <td>
                    <select name="mod_treatment" id="mod_treatment">
                        <option value="">-- Please select --</option>
                        <?php foreach($modality_treatments as $key => $treatment) : ?>
                            <option value="<?php echo $key; ?>" <?php if(@$modality['MODAL'] == $key) echo 'selected="selected"'; ?>>
                                <?php echo form_prep($treatment); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="mod_refdate">Date Referred</label></th>
                <td>
                    <input type="text" name="mod_refdate" id="mod_refdate" class="text datepicker" value="<?php echo @$modality['REFMODDT']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
                <th><label for="mod_firstapptdate">Date of First Appointment Offered</label></th>
                <td>
                    <input type="text" name="mod_firstapptdate" id="mod_firstapptdate" class="text datepicker" value="<?php echo isset($modality['FAOMODDT']) ? $modality['FAOMODDT'] : date('d/m/Y'); ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
                <th><label for="mod_intsetting">Intervention Setting</label></th>
                <td>
                    <select name="mod_intsetting" id="mod_intsetting">
                        <?php foreach($intervention_setting as $key => $setting) : ?>
                            <option value="<?php echo $key; ?>" <?php if(@$modality['MODSET'] == $key) echo 'selected="selected"'; elseif(!isset($modality['MODSET']) && $key == 6) echo 'selected="selected"'; ?>>
                                <?php echo form_prep($setting); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="mod_start">Start Date</label></th>
                <td>
                    <input type="text" name="mod_start" id="mod_start" class="text datepicker" value="<?php echo @$modality['MODST']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
                <th><label for="mod_end">End Date</label></th>
                <td>
                    <input type="text" name="mod_end" id="mod_end" class="text datepicker" value="<?php echo @$modality['MODEND']; ?>" />
                    <small>(dd/mm/yyyy)</small>
                </td>
            </tr>

            <tr>
                <th><label for="mod_exit">Exit Status</label></th>
                <td>
                    <select name="mod_exit" id="mod_exit">
                        <option value="">-- Please select --</option>
                        <?php foreach($exit_status as $key => $status) : ?>
                            <option value="<?php echo $key; ?>" <?php if(@$modality['MODEXIT'] == $key) echo 'selected="selected"'; ?>>
                                <?php echo form_prep($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/<?php echo (@$modality ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
            </tr>

        </table>

    <?php echo form_close(); ?>

    <div class="clear"></div>

</div>
