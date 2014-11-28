<?php if($register) : ?>

<div class="search_results" style="position:relative; margin:0; width:100%;">

    <p>The following clients were registered to attend the last session for this activity.</p>
    
    <table class="results">
        
        <tr class="order">
            <th>Name</th>
            <th>Gender</th>
            <th>Date of birth</th>
            <th>Post code</th>
        </tr>
        
        <?php foreach($register as $c) : ?>
        
        <tr class="row">
            <td><?php echo $c['c_name']; ?></td>
            <td><?php echo $c['c_gender']; ?></td>
            <td><?php echo $c['c_date_of_birth']; ?></td>
            <td><?php echo $c['c_post_code']; ?></td>
        </tr>
        
        <?php endforeach; ?>
    
    </table>
    
</div>

<div style="text-align:right; margin-top:10px;">
    <a href="#" id="use_register">Use this register</a>
</div>

<?php else : ?>
<p>A register could not be suggested. Register suggestion works by using the last register of the previous session for this support group.</p>
<?php endif; ?>