<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'offending_form')); ?>

        <table class="form">
            
            <tr>
                <th><label for="jo_shop_theft">Shop theft</label></th>
                <td>
                    <select name="jo_shop_theft" id="jo_shop_theft">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$offending_info['jo_shop_theft']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr> 
            
            <tr>
                <th><label for="jo_drug_selling">Drug selling</label></th>
                <td>
                    <select name="jo_drug_selling" id="jo_drug_selling">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$offending_info['jo_drug_selling']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr> 
           	
            <tr>
                <th><label for="jo_other_theft">Other theft</label></th>
                <td>
                    <select name="jo_other_theft" id="jo_other_theft">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$offending_info['jo_other_theft']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="jo_assault_violence">Assault</label></th>
                <td>
                    <select name="jo_assault_violence" id="jo_assault_violence">
                        <option value="">- Select -</option>
                        <?php foreach($this->config->config['yes_no_codes'] as $code => $value) : ?>
                        <option value="<?php echo $code; ?>" <?php if($code == @$offending_info['jo_assault_violence']) echo 'selected="selected"'; ?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr class="vat">
                <th><label for="jo_notes">Additional information</label></th>
                <td><textarea name="jo_notes" id="jo_notes" style="width:430px; height:150px;"><?php echo form_prep(@$offending_info['jo_notes']); ?></textarea></td>
            </tr> 
            
            <tr>
                <td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>           
            
        </table>
                         
    <?php echo form_close(); ?>  

</div>