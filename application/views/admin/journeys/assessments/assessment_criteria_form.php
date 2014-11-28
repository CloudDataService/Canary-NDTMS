<style type="text/css">
.ui-datepicker {z-index:10100;}
</style>

<div id="assessment_criteria" title="<?php echo $journey['c_name'] ?>&rsquo;s custom assessment criteria" style="display:none;">
	
	<?php $o_count = 1; ?>
	
    <?php echo form_open('/admin/assessments/set_criteria/' . $journey['j_id'], array('id' => 'assessment_criteria_form')); ?>
        
        <input type="hidden" name="assessment_criteria_form" value="1" />
        
        <table width="100%" id="criteria_outcomes">
            
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Outcome</th>
					<th></th>
                </tr>
            </thead>
            
            <tbody>
                
			<?php if ($acl && ! empty($acl['acl_criteria'])): ?>
				
				<?php foreach ($acl['acl_criteria'] as $num => $title): ?>
				
				<tr class="current">
					<td class="num"><input type="text" name="acl_criteria[num][]" value="<?php echo $num ?>" /></td>
					<td class="outcome"><input type="text" name="acl_criteria[outcome][]" value="<?php echo $title ?>" size="40" /></td>
					<td class="remove"><a href="#" class="remove_row"><img src="/img/icons/cross.png" title="Remove outcome" /></a></td>
				</tr>
				
				<?php $o_count++; ?>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
				
			</tbody>
			
			<tfoot>
                
                <tr id="blank-row">
					<td class="num"><input type="text" name="acl_criteria[num][]" /></td>
					<td class="outcome"><input type="text" name="acl_criteria[outcome][]" /></td>
					<td class="remove"></td>
                </tr>
                
                <tr id="last-row">
                    <td class="right" colspan="5">
                        <br><br><a href="#" id="add_row">+ add outcome</a>
                    </td>
                </tr>
                
            </tfoot>
            
        </table>
        
        <input type="image" src="/img/btn/save.png" alt="Save" />
        <a href="<?php echo site_url('admin/assessments/index/' . $journey['j_id']) ?>" class="cancel_btn">
            <img src="/img/btn/cancel.png" alt="Cancel" />
        </a>
        
    </form>
    
</div>



<script type="text/javascript">
<?php
foreach ($acls as $acl)
{
	$all_acls[$acl['acl_id']] = $acl['acl_criteria'];
}
echo 'var acls = ' . json_encode($all_acls, JSON_NUMERIC_CHECK) .";\n";
echo 'var o_count = ' . $o_count . ';';
?>
</script>