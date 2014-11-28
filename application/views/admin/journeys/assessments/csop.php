<style>
.chart_wrapper { page-break-inside: avoid;}
.csop_charts .header {
	min-width:300px;
}
</style>

<div class="csop_charts">

	<div class="functions">
		<a href="/admin/journeys/info/<?php echo element('j_id', $journey, ''); ?>" class="btn"><div class="btn_img">&larr; Back to Journey</div></a>
		<div class="clear"></div>
	</div>

	<?php if (count($scores) !== 0): ?>

	<?php
	foreach ($assessments as $title => $outcomes)
	{
		echo '<div class="chart_wrapper">'."\n";
		echo '<div class="header"><h2>' . $title . '</h2></div>'."\n";

		echo '<div class="item">'."\n";
		echo '<div class="csop_chart js-csop-chart" id="csop_chart_' . md5($title) . '" data-title="' . $title . '"></div>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
	}
	?>

	<script>
	var chart_data = <?php echo json_encode($assessments, JSON_NUMERIC_CHECK) ?>;
	</script>

	<?php else: ?>

	<div class="item results">
		<p class="no_results">No CSOP assessments have been completed for this journey yet.</p>
	</div>

	<?php endif; ?>

</div>