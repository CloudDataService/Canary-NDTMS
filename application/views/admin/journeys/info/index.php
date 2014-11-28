<div class="functions">
	<a href="/admin/journeys/journey/<?php echo $journey['j_id']; ?>"><img src="/img/btn/update.png" alt="Update" /></a>

	<?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
	<a href="/admin/journeys/delete-journey/<?php echo $journey['j_id']; ?>" class="action" title="Are you sure you want to permanently delete this journey? This action cannot be undone."><img src="/img/btn/delete.png" alt="Delete journey" /></a>
	<?php endif; ?>

	<?php /* <a href="/admin/ajax/appointment/<?php echo element('j_id', $journey, ''); ?>" class="appointment_btn"><img src="/img/btn/make-appointment.png" alt="Make appointment" /></a> */ ?>

	<?php if($journey['j_published'] == 0) : ?>
		<a href="/admin/ajax/event/<?php echo element('j_id', $journey, ''); ?>" class="event_btn"><img src="/img/btn/add-event.png" alt="Add event" /></a>
		<?php /* <a href="/admin/ajax/note/<?php echo element('j_id', $journey, ''); ?>" class="note_btn"><img src="/img/btn/add-notes.png" alt="Add notes" /></a> */ ?>
	<?php endif; ?>


	<a href="/admin/assessments/index/<?php echo element('j_id', $journey, ''); ?>" class="btn outcome_ass_btn" id="assessment_btn"><div class="btn_img">Add Outcome Assessment</div></a>

	<a href="/admin/ajax/modality/<?php echo element('j_id', $journey, ''); ?>" class="btn modality_btn">
		<div class="btn_img">Add Modality</div>
	</a>


	<?php if($journey['j_published'] == 0) : ?>
		<a href="/admin/journeys/info/<?php echo element('j_id', $journey, ''); ?>/?publish=1" class="btn publish_btn publish-journey-confirm"><div class="btn_img">Publish Journey</div></a>
	<?php elseif($journey['j_published'] == 1 && ($this->session->userdata('a_master') || $permissions['apt_can_unpublish'] == 1)): ?>
		<a href="/admin/journeys/info/<?php echo element('j_id', $journey, ''); ?>/?unpublish=1" class="btn publish_btn"><div class="btn_img">Unpublish Journey</div></a>
	<?php endif; ?>


	<?php if(($journey['j_type'] == 'C' && $permissions['apt_can_approve_client'] == 1)
			|| ($journey['j_type'] == 'F' && $permissions['apt_can_approve_family'] == 1)) : ?>
		<a href="/admin/ajax/status/<?php echo element('j_id', $journey, ''); ?>" class="btn status_btn"><div class="btn_img">Approve status change</div></a>
	<?php elseif($journey['j_status'] == 3 || $journey['j_status'] == 4) : ?>
		<a href="/admin/ajax/approve/<?php echo element('j_id', $journey, ''); ?>" class="btn approve_btn"><div class="btn_img">Submit for Approval</div></a>
	<?php endif; ?>

	<?php if ($journey['j_type'] == 'F'): ?>
		<a href="/admin/assessments/csop/<?php echo element('j_id', $journey, ''); ?>" class="btn"><div class="btn_img">CSOP Report</div></a>
	<?php endif; ?>


	<?php if ($journey['j_type'] == 'C'): ?>

			<?php if($journey['j_ndtms_valid'] == 'Yes') : ?>
			<div class="ndtms_status status_valid">
				<p>This journey is NDTMS valid</p>
			</div>
			<?php else : ?>
			<div class="ndtms_status status_invalid">
				<p>This journey is not NDTMS valid. <a href="/admin/journeys/ndtms_valid/<?php echo $journey['j_id']; ?>" id="ndtms_valid_btn" style="float:none; margin:0px;">Find out why</a></p>
			</div>
			<?php endif; ?>

	<?php endif; ?>

	<div class="clear"></div>
</div>

<div class="total"><?php echo 'Last updated ' . $journey['j_datetime_last_update_format'] . ' by ' . $journey['j_last_update_by']; ?></div>

<div class="header">
	<h2>Client information</h2>
</div>

<div class="item">

	<table class="form vat column">

		<tr>
			<th>Client ID</th>
			<td>#<?php echo $journey['j_c_id']; ?></td>
		</tr>

		<tr>
			<th>Name</th>
			<td>
				<?php echo $journey['ci_name']; ?>
				<?php if($journey['ci_is_risk'] == 1) echo '<img src="/img/icons/exclamation.png" alt="" style="vertical-align:sub;" />'; ?>
			</td>
		</tr>

		<tr>
			<th>Gender</th>
			<td><?php echo $journey['ci_gender']; ?></td>
		</tr>

		<tr>
			<th>Date of birth</th>
			<td><?php echo $journey['ci_date_of_birth_format'] . ' (age ' . $journey['ci_age'] . ')'; ?></td>
		</tr>

		<tr>
			<th>Address</th>
			<td><?php echo nl2br($journey['ci_address']); ?></td>
		</tr>

		<tr>
			<th>Post code</th>
			<td><?php echo $journey['ci_post_code']; ?></td>
		</tr>

		<tr>
			<th>GP code</th>
			<td><?php echo $journey['ci_gp_code']; ?></td>
		</tr>

		<tr>
			<th>GP name</th>
			<td><?php echo $journey['ci_gp_name']; ?></td>
		</tr>

		<tr>
			<th>Catchment area</th>
			<td><?php echo $journey['ci_catchment_area']; ?></td>
		</tr>

		<tr>
			<th>Home telephone</th>
			<td><?php echo $journey['ci_tel_home']; ?></td>
		</tr>

		<tr>
			<th>Mobile telephone</th>
			<td><?php echo $journey['ci_tel_mob']; ?></td>
		</tr>

		<tr>
			<th>Nationality</th>
			<td><?php echo ($journey['ci_nationality'] == NULL ? "N/A" : $this->config->config['country_codes'][$journey['ci_nationality']]); ?></td>
		</tr>

		<tr>
			<th>Ethnicity</th>
			<td><?php echo ($journey['ci_ethnicity'] == NULL ? "N/A" : $this->config->config['ethnicity_codes'][$journey['ci_ethnicity']]); ?></td>
		</tr>

		<tr>
			<th>Pregnant</th>
			<td><?php echo $journey['ci_pregnant']; ?></td>
		</tr>

		<tr>
			<th>CAF completed</th>
			<td><?php echo $journey['ci_caf_completed']; ?></td>
		</tr>

		<tr>
			<th>Relationship status</th>
			<td><?php echo ($journey['ci_relationship_status'] == NULL ? "N/A" : $this->config->config['relationship_status_codes'][$journey['ci_relationship_status']]); ?></td>
		</tr>

		<tr>
			<th>Sexuality</th>
			<td><?php echo ($journey['ci_sexuality'] == NULL ? "N/A" : $this->config->config['sexuality_codes'][$journey['ci_sexuality']]); ?></td>
		</tr>

	</table>

	<table class="form vat column">

		<tr>
			<th>Mental health issues</th>
			<td><?php echo $journey['ci_mental_health_issues']; ?></td>
		</tr>

		<tr>
			<th>Learning difficulties</th>
			<td><?php echo $journey['ci_learning_difficulties']; ?></td>
		</tr>

		<tr>
			<th>Disabilities</th>
			<td>
			<?php
				if($journey['disabilities'] == array())
				{
					echo 'N/A';
				}
				else
				{
					echo '<ul style="margin-left: 10px; width:300px">';
					foreach($journey['disabilities'] as $k => $v)
					{
						echo '<li>'. $v .'</li>';
					}
					echo '</ul>';
				}
			?>
			</td>
		</tr>

		<tr>
			<th>Consents to data sharing</th>
			<td><?php echo $journey['ci_consents_to_ndtms']; ?></td>
		</tr>

		<tr>
			<th>Parental status</th>
			<td>
				<?php echo ($journey['ci_parental_status'] == NULL ? "N/A" : $this->config->config['parental_status_codes'][$journey['ci_parental_status']]); ?>
				<?php echo ($journey['ci_no_of_children'] != NULL ? '<small class="metadata">' . ($journey['ci_no_of_children'] == 1 ? '1 child' : $journey['ci_no_of_children'] . ' children') . '</small>' : ""); ?>
			</td>
		</tr>

		<tr>
			<th>Access to children</th>
			<td><?php echo ($journey['ci_access_to_children'] == NULL ? "N/A" : $this->config->config['access_to_children_codes'][$journey['ci_access_to_children']]); ?></td>
		</tr>

		<tr>
			<th>Accommodation status</th>
			<td><?php echo ($journey['ci_accommodation_status'] == NULL ? "N/A" : $this->config->config['accommodation_status_codes'][$journey['ci_accommodation_status']]); ?></td>
		</tr>

		<tr>
			<th>Accommodation need</th>
			<td><?php echo ($journey['ci_accommodation_need'] == NULL ? "N/A" : $this->config->config['accommodation_need_codes'][$journey['ci_accommodation_need']]); ?></td>
		</tr>

		<tr>
			<th>Employment status</th>
			<td><?php echo ($journey['ci_employment_status'] == NULL ? "N/A" : $this->config->config['employment_status_codes'][$journey['ci_employment_status']]); ?></td>
		</tr>

		<tr>
			<th>Smoker</th>
			<td><?php echo $journey['ci_smoker']; ?></td>
		</tr>

		<tr>
			<th>Client contact</th>
			<td>
				<em>During work:</em> <?php echo ( ! @$journey['ci_contact']['during'] ? "N/A" : $this->config->config['yes_no_codes'][$journey['ci_contact']['during']]); ?>
				<em>After work:</em> <?php echo ( ! @$journey['ci_contact']['after'] ? "N/A" : $this->config->config['yes_no_codes'][$journey['ci_contact']['after']]); ?>
			</td>
		</tr>

		<tr>
			<th>Next of kin details</th>
			<td>
				<p style="margin-bottom:10px;">
					<em>Kin 1</em><br />
					<?php echo ( ! @$journey['ci_next_of_kin_details'][1] ? "N/A" : nl2br($journey['ci_next_of_kin_details'][1])); ?>
				</p>
				<p>
					<em>Kin 2</em><br />
					<?php echo ( ! @$journey['ci_next_of_kin_details'][1] ? "N/A" : nl2br($journey['ci_next_of_kin_details'][2])); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th>Additional information</th>
			<td><?php echo nl2br($journey['ci_additional_information']); ?></td>
		</tr>

	</table>

	<div class="clear"></div>

	<p style="text-align:right;"><a href="/admin/journeys/client/<?php echo $journey['j_id']; ?>"><img src="/img/btn/edit.png" alt="Edit" /></a></p>

</div>
<p class="back_to_top">[<a href="#top">back to top</a>]

<div class="header">
	<h2>Journey information</h2>
</div>

<div class="item">

	<table class="form vat column">

		<tr>
			<th>Journey ID</th>
			<td>#<?php echo $journey['j_type'] . $journey['j_id']; ?></td>
		</tr>

		<tr>
			<th>Date of referral</th>
			<td>
			<?php
				echo $journey['j_date_of_referral_format'];
				echo '<small class="metadata">received by ' . $journey['ji_referral_received_by'] . ' on ' . $journey['ji_date_referral_received_format'] . '</small>';
			?>
			</td>
		</tr>

		<tr>
			<th>Referral source</th>
			<td>
			<?php
				if($journey['ji_referral_source'] == "N/A")
				{
					echo "N/A";
				}
				else
				{
					echo $journey['ji_referral_source'] . '<small class="metadata">' . @$this->config->config['referral_source_codes'][$journey['ji_rs_type']] . '</small>';
				}
			?>
			</td>
		</tr>

		<tr>
			<th>Referrer's name</th>
			<td><?php echo $journey['ji_referrers_name']; ?></td>
		</tr>

		<tr>
			<th>Referrer's contact telephone</th>
			<td><?php echo $journey['ji_referrers_tel']; ?></td>
		</tr>

		<tr>
			<th>Keyworker</th>
			<td>
			<?php
			if($journey['j_rc_name'] == NULL)
			{
				echo 'N/A';
			}
			else
			{
				echo $journey['j_rc_name'];
				echo '<small class="metadata">allocated by ' . $journey['ji_rc_allocated_by'] . ' on ' . $journey['ji_date_rc_allocated_format'] . '</small>';
			}
			?></td>
		</tr>

		<tr>
			<th>First assessment date</th>
			<td><?php echo $journey['j_date_first_assessment_format']; ?></td>
		</tr>

		<tr>
			<th>Last assessment date</th>
			<td><?php echo $journey['j_date_last_assessment_format']; ?></td>
		</tr>

		<tr>
			<th>Previously treated</th>
			<td><?php echo $journey['ji_previously_treated']; ?></td>
		</tr>

		<tr>
			<th>Family or carer involved</th>
			<td><?php echo $journey['j_family_or_carer_involved']; ?></td>
		</tr>

	</table>

	<table class="form vat column">

		<tr>
			<th>Medication</th>
			<td><?php echo $journey['ji_medication']; ?></td>
		</tr>

		<tr>
			<th>Summary of needs</th>
			<td><?php echo $journey['ji_summary_of_needs']; ?></td>
		</tr>

		<tr>
			<th>Additional information</th>
			<td><?php echo $journey['ji_additional_information']; ?></td>
		</tr>

		<tr>
			<th>Tier</th>
			<td>
				<?php echo $journey['j_tier']; ?>
			</td>
		</tr>

		<tr>
			<th>Status</th>
			<td>
				<?php echo $journey['j_status']; ?>
				<?php if($journey['j_closed_date_format']) echo '<small class="metadata">' . $journey['j_closed_date_format'] . '</small>'; ?>
			</td>
		</tr>

		<tr>
			<th>Exit status</th>
			<td><?php echo ($journey['ji_exit_status'] == NULL ? "N/A" : $this->config->config['exit_status_codes'][$journey['ji_exit_status']]); ?></td>
		</tr>

		<tr>
			<th>Discharge reason</th>
			<td><?php echo ($journey['ji_discharge_reason'] == NULL ? "N/A" : $this->config->config['discharge_reason_codes'][$journey['ji_discharge_reason']]); ?></td>
		</tr>

	</table>

	<div class="clear"></div>

	<p style="text-align:right;"><a href="/admin/journeys/journey/<?php echo $journey['j_id']; ?>"><img src="/img/btn/edit.png" alt="Edit" /></a></p>

</div>
<p class="back_to_top">[<a href="#top">back to top</a>]</p>



<div class="header">
		<h2>Journey files</h2>
</div>

<div class="item results">

		<?php if (isset($journey_files)): ?>
		<table class="results">
				<tr class="order">
						<th>Name</th>
						<th>Filename</th>
						<th>Type</th>
						<th>Size</th>
						<th>Download</th>
						<th>Delete</th>
				</tr>

				<?php foreach($journey_files as $t) : ?>
						<tr class="row no_click">
								<td><?php echo $t['ju_name']; ?></td>
								<td><?php echo $t['ju_src']; ?></td>
								<td><?php echo '<img src="'. site_url('img/icons/'. $t['ju_ext'] .'.png') .'" alt="" class="ext" />'; ?></td>
								<td><?php echo $t['ju_size']; ?>kb</td>
								<td class="action"><a href="/admin/download?path=journeyfiles/<?php echo $t['ju_src']; ?>"><img src="<?php echo site_url('/img/icons/download.png'); ?>" alt="Download" /></a></td>
								<td class="action"><a href="?ju_id=<?php echo $t['ju_id']; ?>&amp;deletefile=1"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
						</tr>
				<?php endforeach; ?>
		</table>

		<?php else: ?>
				<p class="no_results">No files have been uploaded for this journey.</p>
		<?php endif; ?>

		<?php echo form_open_multipart(current_url(), array('id' => 'journey_files_form')); ?>
		<table class="form">
				<tr>
						<th><label for="ju_name">File name/description</label></th>
						<td><input type="text" name="ju_name" id="cf_name" /></td>
				</tr>
				<tr id="userfile_row">
						<th><label for="userfile">File</label></th>
						<td><input type="file" name="userfile" id="userfile" /> <input type="submit" value="Add" /><?php if(@$upload_errors) echo $upload_errors; ?></td>
				</tr>
		</table>
		<?php echo form_close(); ?>

</div>
<p class="back_to_top">[<a href="#top">back to top</a>]</p>


<?php echo $this->load->view('admin/journeys/info/appointments'); ?>

<?php echo $this->load->view('admin/journeys/info/events'); ?>

<?php echo $this->load->view('admin/journeys/info/modalities'); ?>

<?php // echo $this->load->view('admin/journeys/info/notes'); ?>



<div class="header">
		<h2>Mail merge</h2>
</div>
	<div class="item">
		<table class="form">
			<input type="hidden" name="j_id" id="j_id" value="<?php echo (int)$journey['j_id']; ?>" />

			<tr>
				<th><label for="mm_id">Document</label></th>
				<td>
					<select name="mm_id" id="mm_id" style="width:300px;">
					<?php foreach($mail_merges as $mm) : ?>
					<?php echo '<option value="' . $mm['mm_id'] . '">' . form_prep($mm['mm_title']) . '</option>'; ?>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<th>
					 <input type="image" src="/img/btn/mail-merge.png" alt="Create" id="pdf_btn">
					 <img src="/img/style/loading.gif" class="hidden" id="loading" />
				</th>
				<td><div id="pdf_links"></div></td>
			</tr>

		</table>
	</div>
</div>

	<?php $this->load->view('admin/journeys/assessments/assessment_form'); ?>

<script type="text/javascript">
<?php
foreach ($acls as $acl)
{
	$all_acls[$acl['acl_id']] = $acl['acl_criteria'];
}
echo 'var acls = ' . json_encode($all_acls, JSON_NUMERIC_CHECK) .";\n";
echo 'var o_count = ' . (count($acls) +1) . ';';
?>
</script>
