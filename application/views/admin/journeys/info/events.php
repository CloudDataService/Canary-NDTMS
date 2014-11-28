<div class="header">
	<h2>Events</h2>
</div>
<div class="item results">

    <?php if($journey['events']) : ?>

    <table class="results vat events paginated" data-items="10">

        <?php foreach($journey['events'] as $je) : ?>

        <tr class="row no_click">
        	<td class="event_meta">
        		<p class="event_datetime"><?php echo $je['je_datetime_format']; ?></p>
        		<p class="event_added">
        			<?php
        			echo ($je['je_created_datetime_format'] || $je['added_by'] ? 'Added ' : '');
        			echo ($je['je_created_datetime_format'] ? $je['je_created_datetime_format'] : '');
        			echo ($je['added_by'] ? ' by ' . $je['added_by'] : '');
        			?>
        		</p>
        	</td>
            <td>
            	<p class="event_type"><?php echo ($je['je_ec_name'] ? $je['je_ec_name'] . ' / ' : ''); echo $je['je_et_name']; ?></p>
            	<p class="event_notes"><?php echo $je['je_notes']; ?></p>
            </td>

			<?php if (can_edit_event($je)) : // if event is editable ?>
				<td class="action no_click"><a href="/admin/ajax/event/<?php echo $journey['j_id'] . '/' . $je['je_id']; ?>" class="event_btn"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
				<td class="action"><a href="?je_id=<?php echo $je['je_id']; ?>&amp;delete=1" class="action" title="Are you sure you want to delete this event?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
			<?php else: ?>
				<td></td>
				<td></td>
			<?php endif; ?>

        </tr>

        <?php endforeach; ?>

    </table>

    <?php else : ?>
    <p class="no_results">There have been no events recorded for this journey.</p>
    <?php endif; ?>

</div>
<p class="back_to_top">[<a href="#top">back to top</a>]</p>
