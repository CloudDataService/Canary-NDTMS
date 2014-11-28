<div class="header">
	<h2>Notes</h2>
</div>
<div class="item results">
	
    <?php if($journey['notes']) : ?>
    
    <table class="results vat">
    
    	<tr class="order">
        	<th>Date and time</th>
            <th>Recovery coach</th>
            <th>Notes</th>
            <th>Edit</th>
            <?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
            <th>Delete</th>
            <?php endif; ?>
        </tr>
                
        <?php foreach($journey['notes'] as $jn) : ?>
        <tr class="row no_click">
        	<td><?php echo $jn['jn_date_format']; ?></td>
            <td><?php echo $jn['jn_rc_name']; ?></td>
            <td><?php echo $jn['jn_notes']; ?>
            <td class="action no_click"><a href="/admin/ajax/note/<?php echo $journey['j_id'] . '/' . $jn['jn_id']; ?>" class="note_btn"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
            <?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
            <td class="action"><a href="?jn_id=<?php echo $jn['jn_id']; ?>&amp;delete=1" class="action" title="Are you sure you want to delete this note?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    
    </table>
        
    <?php else : ?>
    <p class="no_results">There have been no notes left for this journey.</p>
    <?php endif; ?>
    
</div>
<p class="back_to_top">[<a href="#top">back to top</a>]</p>