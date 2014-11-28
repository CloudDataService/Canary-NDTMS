<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>90</w:Zoom>"<w:DoNotOptimizeForBrowser/></w:WordDocument></xml><![endif]-->
<style type="text/css">
	body {
        margin:0;
        font-family:Verdana, Geneva, sans-serif;
    }
	p {
		margin-bottom:10px;
	}
    table {
        border: 1px solid #ccc;
        table-layout: fixed;
    }
        table tr {
            border-color: #ccc;
        }
        table th,
        table td {
            padding: 5px;
        }
</style>
</head>
<body>

<body>
<?php echo $this->mail_merge_model->parse_mail_merge($document, $data); ?>
</body>
</html>
