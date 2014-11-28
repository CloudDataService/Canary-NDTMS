<table class="tabular_report">
	<thead>
		<tr>
			<th style="width: 80%">Outcome</th>
			<th style="width: 20%">Total</th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach ($config as $key => $title)
		{
			echo '<tr>';
			echo '<td>' . $title . '</td>';
			echo '<td>' . element($key, $result['data'], '-') . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>