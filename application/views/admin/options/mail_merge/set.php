<style type="text/css">
	textarea.text, input.text {
		text-transform:none;
	}
</style>
<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

    <?php echo form_open(current_url(), array('id' => 'mail_merge_form')); ?>

    <table class="form">

    	<tr>
        	<th>Title</th>
            <td><input type="text" name="mm_title" id="mm_title" class="text" value="<?php echo form_prep(@$mail_merge['mm_title']); ?>" style="width:700px;" /></td>
        </tr>

        <tr class="vat">
        	<th>Body</th>
            <td><textarea name="mm_body" id="mm_body" class="text" style="width:700px; height:300px;"><?php echo @$mail_merge['mm_body']; ?></textarea></td>
        </tr>

        <tr class="vat">
        	<th>Tags</th>
            <td class="tags">
                <div>
                    <?php
                    foreach($mail_merge_aliases as $alias)
					{
                    	echo "<span>{$alias->alias}</span> ";
                    }
                    ?>

                </div>
            </td>
        </tr>

        <tr>
        	<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
        </tr>

    </table>

    <?php echo form_close(); ?>

</div>

<a href="#" id="preview">Preview</a>

<div id="preview_div">

</div>