<style type="text/css">
	div.body_content {
        width:900px;
    }
</style>
<label for="terms_and_conditions">Terms and conditions</label>
<?php if(@$_GET['new']) : ?>
<p>In order to use Escape IS, you must agree to the folowing Terms and Conditions set.</p>
<?php else : ?>
<p>The Terms and Conditions have changed since the last time you logged in. Please review any changes and agree to them in order to use Escape IS.</p>
<?php endif; ?>

<div class="item"><?php echo $terms_and_conditions['value']; ?></div>

<div class="item">
    The Escape Information System looks up the local authority of postcodes at certain points, this lookup is powered by <a href="http://mapit.mysociety.org/">MapIt</a>.
    This database contains Ordnance Survey data &copy; Crown copyright and database right 2010; NISRA data &copy; Crown copyright; Royal Mail data &copy; Royal Mail copyright and database right 2010 (Code-Point Open); Office for National Statistics data &copy; Crown copyright and database right 2010 (ONSPD) and &copy; Crown copyright 2004 (Super Output Areas).
</div>

<?php if( ! @$_GET['new']) : ?>
<label for="last_changes">Changes</label>
<p>Below is a summary of the changes made to the Terms and Conditions.</p>
<div class="item"><?php echo $terms_and_conditions['last_changes']; ?></div>
<?php endif; ?>

<div>
	<a href="?agree=0&amp;token=<?php echo $token; ?>" style="float:left;"><img src="/img/btn/disagree.png" alt="Disagree" /></a>
    
    <a href="?agree=1&amp;token=<?php echo $token; ?>" style="float:right;"><img src="/img/btn/agree.png" alt="Agree" /></a>
</div>