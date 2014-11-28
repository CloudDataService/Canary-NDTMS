<div class="functions">
	
	<a href="/admin/mail-merge/set"><img src="/img/btn/add-new.png" alt="Add new" /></a>
    
    <div class="clear"></div>
</div>


<div class="header">
	<h2>Letter template</h2>
</div>
<div class="item results">
	<?php echo form_open_multipart(current_url(), array('id' => 'case_files_form')); ?>
		<table class="form">
				<tr><td colspan="3">Choose a file to upload and use as the background image for all your mail merge documents.<br />Please prepare an appropriate image, the mail-merged text will be added 250px from the top, with 50px space to the left and right of it.</td></tr>
				<tr id="userfile_row">
                    <th><label for="userfile">File</label></th>
                    <td><input type="file" name="userfile" id="userfile" /> <input type="submit" value="Upload new template" style="padding:3px;" /><?php if(@$upload_errors) echo $upload_errors; ?></td>
                    <?php 
                    if ($mmf)
                    {
                        echo '<td><span id="templatebg-view" style="text-align:right;"><a href="/mailmerge/images/'.$mmf['mmf_src'] .'">Download current background</a></span>';
                        echo '<td><a href="?delete_template=1" class="action" title="Are you sure you want to remove the current template?">Remove current background</a></td>';
                    }
                    else
                    {
                        echo '<td>&nbsp;</td>';
                    }
                    ?>
				</tr>
		</table>
	<?php echo form_close(); ?>

</div>


<div class="header">
	<h2>Mail merge documents</h2>
</div>
<div class="item results">
<?php if($mail_merges) : ?>

<table class="results">

	<tr class="order">
		<th>Mail merge</th>
        <th>Edit</th>
        <th>Delete</th>
	</tr>

	<?php foreach($mail_merges as $mm) : ?>
	<tr class="row">
		<td><?php echo $mm['mm_title']; ?></td>
        <td class="action"><a href="/admin/mail-merge/set/<?php echo $mm['mm_id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
        <td class="action"><a href="/admin/mail-merge/delete/<?php echo $mm['mm_id']; ?>"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
	</tr>
	<?php endforeach; ?>

</table>

<?php else : ?>

<p class="no_results">You have no listed mail merge documents.</p>

<?php endif; ?>
</div>