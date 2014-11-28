

	<form action="" method="post" id="terms_and_conditions_form">
		
		<div class="header">
			<h2>Enable</h2>
		</div>
		
		<div class="item">
		
			<table class="form">
				<th><label>Enable terms and conditions</label></th>
				<td><input type="checkbox" name="on" id="on" value="1" <?php if($terms_and_conditions['on']) echo 'checked="checked"'; ?></td>
			</table>
		
		</div>

		<div class="header">
			<h2>Terms and conditions</h2>
		</div>
		
		<div class="item">
		
			<textarea name="value" id="value" style="width:100%; height:400px;"><?php echo $terms_and_conditions['value']; ?></textarea>
		
		</div>
		
		<div class="header">
			<h2>Changes</h2>
		</div>
		
		<div class="item">
		
			<p>Please detail a brief summary of any changes made to the terms and conditions, this will help existing service providers review the new terms and conditions more easily.</p>
			
			<textarea name="last_changes" id="last_changes" style="width:100%;"><?php echo $terms_and_conditions['last_changes']; ?></textarea>
		
		</div>
			
		<input type="image" src="/img/btn/save.png" alt="Save" id="save" style="float:right;" />
		
	</form>

	<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
