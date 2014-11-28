<style>
.problem_substance {
	display: block;
	width: 50%;
	float: left;
}
</style>

<?php if ($report['description']): ?>
	<p class="report_description"><?php echo $report['description'] ?></p>
<?php endif; ?>

<div class="problem_substance" data-report="problem_substance_1" id="problem_substance_1"></div>
<div class="problem_substance" data-report="problem_substance_2" id="problem_substance_2"></div>
<div class="clear"></div>