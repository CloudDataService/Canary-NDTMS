
<div class="item">

   	<h2>Family members</h2>

    <div class="grey">

        <?php if ($this->session->userdata('tooltips')): ?>

        <p>Search for existing clients by client ID, part of their name or date of birth.</p><br>

        <?php endif; ?>

        <?php echo form_open('/admin/family/add_family_member', array('id' => 'family_member_form')); ?>

    		<input type="hidden" name="j_id" id="j_id" value="<?php echo $journey['j_id']; ?>" />
            <input type="hidden" name="j_c_id" id="j_c_id" value="<?php echo $journey['j_c_id']; ?>" />
			
            <table class="horizontal_form">
                
				<tr>
					<td><label for="c_id">Client ID</label></td>
					<td><label for="f_fname">First name</label></td>
                    <td><label for="f_sname">Surname</label></td>
                    <td><label for="f_date_of_birth">Date of birth</label></td>
                    <td><label for="f_rel_type">Relationship Type</label></td>
					<td>&nbsp;</td>
                </tr>
				
				<tr>
					<td><input type="text" name="c_id" id="c_id" class="text" style="width:50px; text-transform:capitalize" value="" /></td>
					<td><input type="text" name="f_fname" id="f_fname" class="text" style="width:125px; text-transform:capitalize" value="" /></td>
					<td><input type="text" name="f_sname" id="f_sname" class="text" style="width:125px; text-transform:capitalize" value="" /></td>
					<td><input type="text" name="f_date_of_birth" id="f_date_of_birth" class="text datepicker" /></td>
					<td style="vertical-align: middle"><?php echo form_dropdown('f_rel_type', $this->config->item('relative_types'), NULL, 'id="f_rel_type" style="height: 32px"') ?></td>
                    <td>
						<input type="image" src="/img/btn/add-new.png" id="add_family_member" alt="Add new" style="margin: 0" />
						<img src="/img/icons/ajax.gif" alt="Loading..." id="loading" style="display:none;" />
					</td>
                </tr>
				
            </table>
			
        </form>
		
        <div id="search_results" class="search_results" style="visibility: hidden"></div>
		
    </div>

    <div class="item results">

        <table class="results" id="family_members">

            <tr class="order">
                <th>Client ID</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Date of birth</th>
                <th>Relation to client</th>
                <th>Manage</th>
                <th>Delete</th>
            </tr>

            <?php $family_member_count = 0; ?>

            <?php if ($family_clients): ?>

                <!-- Client family members -->

                <?php foreach ($family_clients as $fc): ?>

                <tr class="row no_click">
                    <td><?php echo $fc['c_id'] ?></td>
                    <td><?php echo $fc['c_fname'] ?></td>
                    <td><?php echo $fc['c_sname'] ?></td>
                    <td><?php echo $fc['c_date_of_birth_format'] ?></td>
                    <td><?php echo $this->config->item($fc['fc_rel_type'], 'relative_types') ?></td>
                    <td><a href="/admin/clients/info/<?php echo $fc['c_id'] ?>"><img src="/img/icons/magnifier.png" alt="View client details" title="View client details" /></a></td>
                    <td><a href="/admin/family/delete_client/<?php echo $journey['j_id'] . '/' . $fc['fc_c_id']; ?>" class="delete"><img src="/img/icons/cross.png" alt="Delete" title="Remove client from famly" /></a></td>
                </tr>

                <?php $family_member_count++; ?>

                <?php endforeach; ?>

            <?php endif; ?>


            <tr class="no_click">
                <td colspan="8"><hr size="1" color="#dddddd"></td>
            </tr>


            <?php if ($family_info['jf_family_members']): ?>

                <!-- Other family members -->

                <?php foreach($family_info['jf_family_members'] as $key => $family_member): ?>
				
				<?php $family_member['rel_type'] = (array_key_exists('rel_type', $family_member) ? $family_member['rel_type'] : NULL) ?>

                <tr class="row no_click">
                    <td></td>
                    <td><?php echo $family_member['fname']; ?></td>
                    <td><?php echo $family_member['sname']; ?></td>
                    <td><?php echo $family_member['date_of_birth']; ?></td>
                    <td><?php echo $this->config->item($family_member['rel_type'], 'relative_types') ?></td>
                    <td><a href="/admin/family/promote_member/<?php echo $journey['j_id'] . '/' . $key ?>"><img src="/img/icons/forward_green.png" alt="Start own journey" title="Start own journey"/></a></td>
                    <td><a href="/admin/family/delete_family_member/<?php echo $journey['j_id'] . '/' . $key; ?>" class="delete"><img src="/img/icons/cross.png" alt="Delete" title="Delete family member"/></a></td>
                </tr>

                <?php $family_member_count++; ?>

                <?php endforeach; ?>

            <?php endif; ?>


            <?php if ($family_member_count == 0): ?>

            <tr class="row no_click" id="no_family_members">
                <td colspan="7" style="text-align: center;">No family members have been listed</td>
            </tr>

            <?php endif; ?>

        </table>

    </div>

    <?php echo form_open(current_url(), array('id' => 'notes_form')); ?>

        <h3>Additional information</h3>
        <textarea name="jf_notes" id="jf_notes" class="summary text"><?php echo form_prep(@$family_info['jf_notes']); ?></textarea>

    	<div style="text-align:right;">
        	<input type="image" src="/img/btn/save.png" alt="Save" />
        </div>

    <?php echo form_close(); ?>

</div>
