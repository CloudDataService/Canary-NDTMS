<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php echo $this->layout->get_css(); ?>
<?php echo $this->layout->get_js(); ?>
<link rel="shortcut icon" href="/img/favicon.ico" />
<title><?php echo $this->layout->get_title(); ?></title>
</head>

    <body>

		<div class="container">

       		<img src="/img/style/logo-home.png" alt="<?php echo $this->config->item('site_name'); ?>" class="logo" />
            
            <!-- <h1 class="logo"><?php echo $this->config->item('site_name'); ?></h1> -->

            <div class="body_content">
                
                <?php $this->load->view($this->layout->get_view()); ?>
                 
                <div class="clear"></div>
                
            </div>
            
            <div class="body_foot_logos">
                <img src="/img/style/logos-vital-cds.png" alt="Created &amp; supported by: Cloud Data Service; Vital" class="logos" />
            </div>

            
    	</div>
            
    </body>

</html>
