<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $this->layout->get_css(); ?>
    <link href="/css/print.css" rel="stylesheet" type="text/css" media="print" />
    <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <link rel="shortcut icon" href="/img/favicon.ico" />
    <title><?php echo $this->layout->get_title(); ?></title>
</head>

<body>

	<div class="nav_container" id="top">

		<div class="nav">

       		<img src="/img/style/logo-default.png" alt="<?php echo $this->config->item('site_name'); ?>" class="logo" />

        	<?php echo $this->layout->get_nav(); ?>

        	<div class="clear"></div>

        </div>

    </div>

	<div class="container">

        <div class="banner">
        	<p class="sp_name"><?php echo $this->session->userdata('sp_name'); ?></p>
        </div>

        <div class="breadcrumbs">
			<?php if($this->session->flashdata('action')) : ?>
            <div class="action">
            	<p><?php echo $this->session->flashdata('action'); ?></p>
            </div>

            <?php endif; ?>
        	<h1><?php echo $this->layout->get_last_title(); ?></h1>

        	<p><?php echo $this->layout->get_breadcrumbs(); ?></p>
        </div>

        <div class="body_content">
        	<?php $this->load->view($this->layout->get_view()); ?>

            <div class="clear"></div>
        </div>

    </div>


	<div class="page_footer">
        <div id="version"><?php echo $this->config->item('site_name'); ?> v<?php echo $this->config->item('site_version') ?> r<?php echo config_item('version') ?></div>
    </div>

</div>

<?php echo $this->layout->get_js(); ?>

</body>
</html>
