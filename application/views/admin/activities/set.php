<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'support_group_form')); ?>
    
    <table class="form">
        
        <tr>
            <th><label for="sp_name">Activity name</label></th>
            <td><input type="text" name="sp_name" id="sp_name" class="text" style="width:250px;" value="<?php echo form_prep(@$support_group['sp_name']); ?>" /></td>
        </tr>
        
        <tr class="vat">
            <th><label for="sp_description">Description</label></th>
            <td><textarea name="sp_description" id="sp_description" class="text" style="width:400px; height:175px;"><?php echo form_prep(@$support_group['sp_description']); ?></textarea></td>
        </tr>
    
    	<tr>
            <th><label for="sp_default_day">Default day</label></th>
            <td>
            	<select name="sp_default_day" id="sp_default_day">
                	<option value="">-- Please select --</option>
                    <?php foreach($this->config->config['day_codes'] as $day) : ?>
                    <?php echo '<option value="' . $day . '" ' . ($day == @$support_group['sp_default_day'] ? 'selected="selected"' : '') . '>' . $day . '</option>'; ?>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    
        <tr>
            <th><label for="time">Default time</label></th>
            <td>
                <select name="time_hour">
                    <?php 
                    for($i = 00; $i <= 23; $i++)
                    {
                        echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$support_group['time_hour'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                    }
                    ?>
                </select>
                :
                <select name="time_minute">
                    <?php 
                    for($i = 00; $i <= 59; $i = $i + 5)
                    {
                        echo '<option value="' . sprintf('%02u', $i) . '" ' . (@$support_group['time_minute'] == $i ? 'selected="selected"' : '') . '>' . sprintf('%02u', $i) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        
        <tr>
            <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/<?php echo (@$support_group ? 'update' : 'add-new'); ?>.png" alt="Save" /></td>
        </tr>
    
    </table>
    
    <?php echo form_close(); ?>

</div>