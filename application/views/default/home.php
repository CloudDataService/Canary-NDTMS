<noscript>
<meta http-equiv="refresh" content="0; url=/home/javascript">
</noscript>
<style type="text/css">
	div.body_content {
        width:350px;
    }
	div.body_content {
		position:relative;
	}
</style>
<?php if(@$banned) : ?>

<div class="message error">
    <p style="background-position:top left;">You have failed to login 3 times. You will not be able to attempt another login for 10 minutes (<?php echo $banned['datetime_set_format']; ?>).
</div>

<?php elseif(@$failed_login) : ?>

<div class="message error">
	<p>Sorry, but your login attempt failed.</p>
</div>

<?php elseif(@$_GET['timeout']) : ?>

<div class="message action">
	<p>For security reasons you were timed out of your session.</p>
</div>

<?php elseif(@$_GET['logged_out']) : ?>

<div class="message action">
	<p>You have been successfully logged out.</p>
</div>

<?php elseif(@$_GET['auth_failed']) : ?>

<div class="message action">
	<p>Please login to access this page.</p>
</div>

<?php elseif(@$_GET['verified']) : ?>

<div class="message action">
	<p>Your account has been verified and actived.</p>
</div>

<?php endif; ?>
	
<?php echo form_open('/'); ?>
	
	<table class="login">
	
		<tr>
			<td><label for="email">Email:</label> <input type="text" name="email" id="email" class="text" value="" autocomplete="off" /></td>
		</tr>
		
		<tr>
			<td><label for="password">Password:</label> <input type="password" name="password" id="password" class="text" value="" autocomplete="off" /></td>
		</tr>
		
		<tr>
			<td style="text-align:right;"><input type="image" src="/img/btn/login.png" alt="Login" /></td>
		</tr>
	
	</table>

<?php echo form_close(); ?>