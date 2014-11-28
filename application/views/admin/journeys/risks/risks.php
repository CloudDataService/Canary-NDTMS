<?php echo form_open(current_url(), array('id' => 'risk_summaries_form')); ?>

    <input type="hidden" name="risk_summaries_form" value="1" />

    <h3>Risk summary</h3>
    <textarea name="crs_summary" class="summary text"><?php echo @$risk_summaries['crs_summary']; ?></textarea>

    <?php foreach($risk_types as $group => $risks) : ?>

        <?php if(isset($clients_risks[$group])) : // if client has risks in this group ?>

        <div class="header">
            <h2><?php echo $group; ?> risks</h2>
        </div>
        <div class="item results">
            <table class="results">

                <tr class="order">
                    <th>Risk</th>
                    <th>Impact</th>
                    <th>Likelihood</th>
                    <th>Risk score</th>
                    <th>Risk level</th>
                    <th>Risk to whom</th>
                    <th>Protective factors</th>
                    <th>Edit</th>
                    <?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
                    <th>Delete</th>
                    <?php endif; ?>
                </tr>

                <?php foreach($clients_risks[$group] as $rt) : ?>
                <tr class="row vat">
                    <td><?php echo $rt['rt_name']; ?></td>
                    <td><?php echo $rt['cr_impact_score']; ?></td>
                    <td><?php echo $rt['cr_likelihood_score']; ?></td>
                    <td><?php echo $rt['cr_risk_score']; ?></td>
                    <td><?php echo $rt['cr_risk_level']; ?></td>
                    <td><?php echo $rt['cr_risk_to_whom']; ?></td>
                    <td><?php echo $rt['cr_protective_factors']; ?></td>
                    <td class="action"><a href="?rt_id=<?php echo $rt['cr_rt_id']; ?>"><img src="/img/icons/edit.png" alt="Edit"></a></td>
                    <?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
                    <td class="action"><a href="?rt_id=<?php echo $rt['cr_rt_id']; ?>&amp;delete=1" class="action" title="Are you sure you want to delete <?php echo $rt['rt_name']; ?> as a risk?"><img src="/img/icons/cross.png" alt="Delete"></a></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>

            </table>

        </div>

        <h3><?php echo $group; ?> risks summary</h3>
        <textarea name="crs_<?php echo $group_name = str_replace(' ', '_', strtolower($group)) . '_risks'; ?>" class="summary text"><?php echo @$risk_summaries['crs_' . $group_name]; ?></textarea>

        <?php endif; ?>

    <?php endforeach; ?>


    <div style="text-align:right;">

        <label for="is_risk">Flag <?php echo $journey['c_name']; ?> as a potential risk?</label>
        <select name="is_risk" id="is_risk">
            <option value="">-- Please select --</option>
            <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
            <option value="<?php echo $code; ?>" <?php if($code == $journey['c_is_risk']) echo 'selected="selected"'; ?>><?php echo $value; ?></option>
            <?php endforeach; ?>
        </select>

        <div class="clear"></div>

        <input type="image" src="/img/btn/save.png" alt="Save" style="margin-top:15px;" />

    </div>

<?php echo form_close(); ?>
