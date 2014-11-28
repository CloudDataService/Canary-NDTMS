<noscript>
<meta http-equiv="refresh" content="0; url=/home/javascript">
</noscript>
<style type="text/css">
    table.login {
    	width: 400px;
    	margin-left: auto;
    	margin-right: auto;
    }
	div.body_content {
		position:relative;
	}
</style>
<?php if(@$current_mismatch) : ?>

<div class="message error">
    <p style="background-position:top left;">Passwords do not match.</p>
</div>

<?php elseif (@$strength_error): ?>

<div class="message error">
    <p style="background-position:top left;">Passwords must be more than 12 characters long.</p>
</div>

<?php endif; ?>

<div class="message" style="text-align: center">
	<p>For security reasons, your password expires every <?php echo $this->config->item("password_expiry"); ?>. Please change your password here.</p>
	<br><hr><br>
</div>
<?php echo form_open('/admin/reset'); ?>

	<table class="login">

		<tr>
			<td><label for="password">New Password:</label></td>
			<td><input type="password" name="new_password" id="new_password" class="text" /></td>
		</tr>

		<tr>
			<td><label for="password">Password Again:</label></td>
			<td><input type="password" name="check_password" id="check_password" class="text" /></td>
		</tr>

		<tr>
			<td></td>
			<td><input type="submit" value="Reset Password"></td>
		</tr>
	</table>s


<?php echo form_close(); ?>
