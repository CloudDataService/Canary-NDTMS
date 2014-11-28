<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $this->layout->get_css(); ?>
    <?php echo $this->layout->get_js(); ?>
    <style type="text/css">
    	body {
            background:none;
            background-color:#fff;
            margin-top:20px;
        }
        div.container {
            width:800px;
        }
        h1 {
            margin-bottom:15px;
        }
    </style>
    <script type="text/javascript">
		window.print();
	</script>
    <title><?php echo $this->layout->get_title(); ?></title>
</head>

<body>
    
	<div class="container">

        	<?php $this->load->view($this->layout->get_view()); ?>
            
        <div class="clear"></div>
    </div>

</body>
</html>