<div class="functions">
	<a href="/admin/journeys/new-journey/<?php echo $client['c_id']; ?>"><img src="/img/btn/new-journey.png" alt="Add new journey" /></a>
    
    <a href="/admin/clients/set/<?php echo $client['c_id']; ?>"><img src="/img/btn/update.png" alt="Update" /></a>
    
    <?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
    <a href="/admin/clients/delete-client/<?php echo $client['c_id']; ?>" class="action" title="Are you sure you want to permanently delete this client? All associated journey information will also be deleted. This action cannot be undone."><img src="/img/btn/delete.png" alt="Delete journey" /></a>    
    <?php endif; ?>
    
    <div class="clear"></div>
</div>

<div style="float:left; width:465px; margin-right:30px;">

    <div class="header">
        <h2>Client information</h2>
    </div>
    
    <div class="item" style="height:320px;">
        
        <table class="form">
        
            <tr>
                <th>Name</th>
                <td>
                    <?php echo $client['c_fname'] . ' ' . $client['c_sname']; ?>
                    <?php if($client['c_is_risk'] == 1) echo '<img src="/img/icons/exclamation.png" alt="" class="vat" />'; ?>
                </td>
            </tr>
            
            <tr>
                <th>Gender</th>
                <td><?php echo $client['c_gender']; ?></td>
            </tr>        
            
            <tr>
                <th>Date of birth</th>
                <td><?php echo $client['c_date_of_birth_format'] . ' (age ' . $client['c_age'] . ')'; ?></td>
            </tr>
            
            <tr class="vat">
                <th>Address</th>
                <td><?php echo nl2br($client['c_address']); ?></td>
            </tr>
            
            <tr>
                <th>Post code</th>
                <td><?php echo $client['c_post_code']; ?></td>
            </tr>
            
            <tr>
                <th>Catchment area</th>
                <td><?php echo $client['c_catchment_area']; ?></td>
            </tr>
            
            <tr>
                <th>Home telephone</th>
                <td><?php echo $client['c_tel_home']; ?></td>
            </tr>
            
            <tr>
                <th>Mobile telephone</th>
                <td><?php echo $client['c_tel_mob']; ?></td>
            </tr>       
        
        </table>
        
    </div>

</div>

<div style="float:left;">

    <input type="hidden" name="coords" id="coords" value='<?php echo json_encode($coords); ?>' />
    
    <div class="header">
        <h2>Map</h2>
    </div>
    
    <div id="map" style="width:400px; height:350px;">
    
    </div>

</div>

<div class="clear"></div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Journeys</h2>
</div>

<div class="item results">

	<?php if($journeys) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Journey ID</th>
            <th>Date of referral</th>
            <th>First assessment</th>
            <th>Key Worker</th>
            <th>Status</th>
        </tr>
        
        <?php foreach($journeys as $j) : ?>
        <tr class="row">
            <td><a href="/admin/journeys/info/<?php echo $j['j_id']; ?>"><?php echo '#' . $j['j_type'] . $j['j_id']; ?></a></td> 
            <td><?php echo $j['j_date_of_referral_format']; ?></td>
            <td><?php echo $j['j_date_first_assessment_format']; ?></td>            
            <td><?php echo $j['rc_name']; ?></td>
            <td><?php echo $j['j_status']; ?></td>
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results"><?php echo $client['c_fname']; ?> has not engaged in any journeys.</p>
    
    <?php endif; ?>

</div>

<div class="clear"></div>

<div class="total">
	<?php echo $total_sg; ?>
</div>

<div class="header">
	<h2>Activities</h2>
</div>

<div class="item results">

	<?php if($support_groups) : ?>
    
    <table class="results">
        
        <tr class="order">
        	<th>Activity</th>
            <th>Total sessions attended</th>
            <th>View group</th>
        </tr>
        
        <?php foreach($support_groups as $sg) : ?>
        <tr class="row">
        	<td><?php echo $sg['sp_group']; ?></td>
            <td><?php echo $sg['total']; ?></td>
            <td><a href="/admin/activities/info/<?php echo $sg['sp_id']; ?>"><img src="/img/icons/magnifier.png" alt="View group" /></a></td>   
        </tr>
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results"><?php echo $client['c_fname']; ?> has not attended any activities.</p>
    
    <?php endif; ?>

</div>



<div class="header">
    <h2>Family</h2>
</div>

<div class="item results">

    <?php if ($family_clients) : ?>
    
    <table class="results">
        
        <tr class="order">
            <th>Name</th>
            <th>Date of birth</th>
            <th>Relationship to client</th>
            <th>View</th>
        </tr>
        
        <?php foreach ($family_clients as $fc) : ?>
        
        <tr class="row">
            <td><?php echo $fc['c_fname'] . ' ' . $fc['c_sname'] ?></td>
            <td><?php echo $fc['c_date_of_birth_format'] ?></td>
            <td>
                <?php
                if ($fc['type'] == 'primary')
                {
                    echo $this->config->item($fc['fc_rel_type'], 'relative_types');
                }
                else
                {
                    echo $this->config->item($this->config->item($fc['fc_rel_type'], 'relative_types_opposite'), 'relative_types');
                }
                ?>
            </td>
            <td><a href="/admin/clients/info/<?php echo $fc['c_id']; ?>"><img src="/img/icons/magnifier.png" alt="View client" /></a></td>   
        </tr>
        
        <?php endforeach; ?>
    
    </table>
    
    <?php else : ?>
    
    <p class="no_results"><?php echo $client['c_fname']; ?> has no linked family members.</p>
    
    <?php endif; ?>

</div>