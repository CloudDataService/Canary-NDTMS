<?php $this->load->view('admin/journeys/journey_nav'); ?>

<div class="item">

	<?php echo form_open(current_url(), array('id' => 'client_form')); ?>

        <table class="form">
            
            <tr class="vat">
                <th>Client</th>
                <td>
                    <?php echo $client['c_fname'] . ' ' . $client['c_sname'] ?>
                    <br>DOB <?php echo $client['c_date_of_birth_format'] ?>
                </td>
            </tr>
            
            <tr>
                <th>is a ...</th>
                <td><?php echo form_dropdown('fc_rel_type', $this->config->item('relative_types')) ?>
            </tr>
            
            <tr class="vat">
                <th><label>... of this journey client</label></th>
                <td><?php echo $journey['ci_name'] ?><br>DOB <?php echo $journey['ci_date_of_birth_format'] ?></td>
            </tr>
            
            <tr>
                <th style="width: 150px; max-width: 150px;"><label>in journey ID</label></th>
                <td>
                    #<?php echo $journey['j_type'] . $journey['j_id'] ?>
                </td>
            </tr>
            
            <tr>
                <td></td>
                <td><input type="image" src="/img/btn/save.png" alt="Save" /></td>
            </tr>           
            
        </table>
                         
    <?php echo form_close(); ?>  

</div>