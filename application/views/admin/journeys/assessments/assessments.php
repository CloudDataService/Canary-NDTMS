<?php 
$no = count($assessments);
?>

<?php foreach ($assessments as $jas): ?>

<div class="grey">
    
    <?php if ($this->session->userdata('a_master')) : // if master admin is logged in ?>
        <a href="?delete=<?php echo $jas['jas_id']; ?>" class="action" title="Are you sure you want to delete assessment <?php echo $no; ?> from <?php echo $journey['c_name'] . '&rsquo;s'; ?> journey?" style="float:right;"><img src="/img/btn/delete.png" alt="Delete" /></a>
    <?php endif; ?>
	
    <span class="assessment_no">Assessment <?php echo $no; ?></span>
    <span class="assessment_date"><?php echo $jas['jas_date_format']; ?></span>
	<span class="assessment_criteria"><?php echo $jas['acl_name']; ?></span>
	
    <div class="clear"></div>
	
    <table class="assessment_table" style="float:left;width:375px">
        
        <thead>
            <tr>
                <th style="width: 40px">Key</th>
                <th style="">Outcome</th>
                <th style="width: 40px">Score</th>
            </tr>
        </thead>
        
        <?php 
        $i = 1;
        foreach ($jas['scores'] as $score):
        $colour = (@$colour ? false : true);
        ?>
        <tr <?php echo ($colour ? 'class="color"' : ''); ?>>
            <td><?php echo $score['jacs_num'] ?>.</td>
            <td style="text-align:left;"><?php echo $score['jacs_title'] ?></th>
            <td><?php echo $score['jacs_score']; ?></td>
        </tr>
        <?php
        $i++;
        endforeach;
        ?>
        
    </table>

    <div style="float:left; margin-left:75px;">
        <img src="<?php echo $this->charts_model->get_score_chart($jas); ?>" />
    </div>

    <div class="clear"></div>

    <?php if($jas['jas_notes']) : ?>
    <div class="notes">
        <?php echo nl2br($jas['jas_notes']); ?>
    </div>
    <?php endif; ?>

    <div class="clear"></div>

</div>

<?php 
$no--;
endforeach;
?>