<?php

class Mail_merge extends MY_controller {

	public $mailmerge_template_shell = '
<html xmlns:o="urn:schemas-microsoft-com:office:office"
   xmlns:w="urn:schemas-microsoft-com:office:word"
   xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="ProgId" content="Word.Document">
<meta name="Generator" content="EscapeInformationSystem 0.4">
<meta name="Originator" content="EscapeInformationSystem 0.4">
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
w\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
<style>
<!--
/* Style Definitions */
@page Section1
   {size: 595.35pt 841.995pt;
   mso-page-orientation: portrait;
   margin: 3cm 2.5cm 3cm 2.5cm;
   mso-header-margin: 36pt;
   mso-footer-margin: 36pt;
   mso-paper-source: 0;}
div.Section1
  {page: Section1;}

p.normalText, li.normalText, div.normalText{
   mso-style-parent: "";
   margin: 0cm;
   margin-bottom: 6pt;
   mso-pagination: widow-orphan;
   font-size: 12pt;
   font-family: "Arial";
   mso-fareast-font-family: "Arial";
}

table.normalTable{
   mso-style-name: "Tabela com grade";
   mso-tstyle-rowband-size: 0;
   mso-tstyle-colband-size: 0;
   border-collapse: collapse;
   mso-border-alt: solid windowtext 0.5pt;
   mso-yfti-tbllook: 480;
   mso-padding-alt: 0cm 5.4pt 0cm 5.4pt;
   mso-border-insideh: 0.5pt solid windowtext;
   mso-border-insidev: 0.5pt solid windowtext;
   mso-para-margin: 0cm;
   mso-para-margin-bottom: .0001pt;
   mso-pagination: widow-orphan;
   font-size: 12pt;
   font-family: "Arial";
}
table.normalTable td{
   border: solid windowtext 1.0pt;
   border-left: none;
   mso-border-left-alt: solid windowtext .5pt;
   mso-border-alt: solid windowtext .5pt;
   padding: 0cm 5.4pt 0cm 5.4pt;
}

table.tableWithoutGrid{
   mso-style-name: "Tabela sem grade";
   mso-tstyle-rowband-size: 0;
   mso-tstyle-colband-size: 0;
   border-collapse: collapse;
   border: none;
   mso-border-alt: none;
   mso-yfti-tbllook: 480;
   mso-padding-alt: 0cm 5.4pt 0cm 5.4pt;
   mso-border-insideh: 0.5pt solid windowtext;
   mso-border-insidev: 0.5pt solid windowtext;
   mso-para-margin: 0cm;
   mso-para-margin-bottom: .0001pt;
   mso-pagination: widow-orphan;
   font-size: 12pt;
   font-family: "Arial";
}


-->
</style>
</head>
<body lang="PT-BR" style="tab-interval: 35.4pt">
<div class="Section1">
<table>
<tr><td>
<!--[if gte vml 1]><v:shapetype id="_x0000_t75" coordsize="21600,21600"
		   o:spt="75" o:preferrelative="t" position="t" path="m@4@5l@4@11@9@11@9@5xe" filled="f"
		   stroked="f">
		   <v:stroke joinstyle="miter"/>
		   <v:formulas>
			<v:f eqn="if lineDrawn pixelLineWidth 0"/>
			<v:f eqn="sum @0 1 0"/>
			<v:f eqn="sum 0 0 @1"/>
			<v:f eqn="prod @2 1 2"/>
			<v:f eqn="prod @3 21600 pixelWidth"/>
			<v:f eqn="prod @3 21600 pixelHeight"/>
			<v:f eqn="sum @0 0 1"/>
			<v:f eqn="prod @6 1 2"/>
			<v:f eqn="prod @7 21600 pixelWidth"/>
			<v:f eqn="sum @8 21600 0"/>
			<v:f eqn="prod @7 21600 pixelHeight"/>
			<v:f eqn="sum @10 21600 0"/>
		   </v:formulas>
		   <v:path o:extrusionok="f" gradientshapeok="t" o:connecttype="rect"/>
		   <o:lock v:ext="edit" aspectratio="t"/>
		  </v:shapetype><v:shape id="_x0000_i1021" type="#_x0000_t75" style="width:225.00pt;
		   height:150.00pt; margin-left:0.00pt;margin-top:0.00pt;" >
		   <v:imagedata src="{site_url}/header.jpg" o:title="accessibilityIssues"/>
		  </v:shape><![endif]--><!--[if !vml]--><img width="300" height="200" src="{site_url}/header.jpg" v:shapes="_x0000_i1021"><!--[endif]-->
</td>
<td>
{address}
</td>
</tr></table>{body}
<!--[if gte vml 1]><v:shape id="_x0000_i1022" type="#_x0000_t75" style="width:112.50pt;
		   height:75.00pt; margin-left:0.00pt;margin-top:0.00pt;" >
		   <v:imagedata src="{site_url}/banner-1.jpg" o:title="accessibilityIssues"/>
		  </v:shape><![endif]--><![if !vml]><img width="150" height="100" src="{site_url}/banner-1.jpg" v:shapes="_x0000_i1022"><![endif]><!--[if gte vml 1]><v:shape id="_x0000_i1023" type="#_x0000_t75" style="width:112.50pt;
		   height:75.00pt; margin-left:0.00pt;margin-top:0.00pt;"" >
		   <v:imagedata src="{site_url}/banner-2.jpg" o:title="accessibilityIssues"/>
		  </v:shape><![endif]--><![if !vml]><img width="150" height="100" src="{site_url}/banner-2.jpg" v:shapes="_x0000_i1023"><![endif]><!--[if gte vml 1]><v:shape id="_x0000_i1024" type="#_x0000_t75" style="width:112.50pt;
		   height:75.00pt; margin-left:0.00pt;margin-top:0.00pt;" >
		   <v:imagedata src="{site_url}/banner-3.jpg" o:title="accessibilityIssues"/>
		  </v:shape><![endif]--><![if !vml]><img width="150" height="100" src="{site_url}/banner-3.jpg" v:shapes="_x0000_i1024"><![endif]><!--[if gte vml 1]><v:shape id="_x0000_i1025" type="#_x0000_t75" style="width:112.50pt;
		   height:75.00pt; margin-left:0.00pt;margin-top:0.00pt;"S >
		   <v:imagedata src="{site_url}/banner-4.jpg" o:title="accessibilityIssues"/>
		  </v:shape><![endif]--><![if !vml]><img width="150" height="100" src="{site_url}/banner-4.jpg" v:shapes="_x0000_i1025"><![endif]></div>

</body>
</html>';

	public function __construct()
	{
		parent::__construct();

		// check the sps is logged in
		$this->auth->check_admin_logged_in();

		//$this->auth->check_master_admin_logged_in();

		$this->load->model('mail_merge_model');

		$this->layout->set_breadcrumb(array('Options' => '/admin/options', 'Mail merge' => '/admin/mail-merge'));
	}


	public function index($page = 0)
	{
		$this->_check_permission();

		// if uploading a file
		if($_FILES)
		{
			// if file exists
			if(@$_FILES['userfile']['name'])
			{
				$config['upload_path'] = APPPATH . '../public_html/mailmerge/images';	//APPPATH . '/storage/mailmergebgs';
				$config['allowed_types'] = 'jpg|jpeg|png';

				$this->load->library('upload', $config);

				// if file does not upload ok
				if ( ! $this->upload->do_upload())
				{
					// get upload errors
					$this->data['upload_errors'] = $this->upload->display_errors('<label class="error">', '</label>');
				}
				else
				{
					// get upload data
					$data = $this->upload->data();
				}
			}

			// if upload worked ok
			if(@$data)
			{
				// add template to database
				$this->mail_merge_model->upload_template_file($data);

				// redirect to current info page
				redirect(current_url());
			}

		}
		// if attempting to delete template
		elseif(@$_GET['delete_template'])
		{
			// delete template
			$this->mail_merge_model->delete_template($this->session->userdata('a_id'));

			// "refresh" page
			redirect(current_url());
		}

		$this->data['mmf'] = $this->mail_merge_model->get_background_file();

		// get all mail merges service provider has set
		$this->data['mail_merges'] = $this->mail_merge_model->get_mail_merges();

		$this->layout->set_js(array('plugins/jquery.validate'));
		$this->layout->set_view('/admin/options/mail_merge/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function set($mm_id = 0)
	{
		$this->_check_permission();

		// attempt to get mail merge document
		if($mail_merge = $this->mail_merge_model->get_mail_merge($mm_id))
		{
			$title = 'Update mail merge'; // set update title
		}
		else
		{
			$title = 'Create new mail merge'; // set create new title
		}

		// if attempting to post form
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set case rules
			$this->form_validation->set_rules('mm_title', '', 'required|strip_tags')
								  ->set_rules('mm_body', '', '');

			// if form validation runs ok
			if($this->form_validation->run())
			{
				// save mail merge document
				$this->mail_merge_model->set_mail_merge(@$mail_merge['mm_id']);

				// redirect to mail merge index
				redirect('/admin/mail-merge');
			}
		}

		$this->data['mail_merge'] =& $mail_merge;
		$this->data['mail_merge_aliases'] = $this->mail_merge_model->get_aliases();
		$this->data['title'] =& $title;

		// set layout
		$this->layout->set_js(array('tiny_mce/jquery.tinymce', 'plugins/jquery.validate', 'views/admin/mail_merge/set'));
		$this->layout->set_title($title);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_view('/admin/options/mail_merge/set');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

	public function delete($mm_id)
	{
		$this->_check_permission();

	// if attempting to delete mail merge document
		$this->mail_merge_model->delete_mail_merge($mm_id);

			// redirect to mail merge index
		redirect('/admin/mail-merge');
	}

	public function preview()
	{
		// set mail merge docment text
		$this->data['document'] = $_POST['mm_body'];

		// set some static preview data
		$this->data['data'] = array(
			'j_id' => '1',
			'c_date_of_birth' => '16/04/1987',
			'c_fname' => 'OLIVER',
			'c_sname' => 'DUNN',
			'c_address' => '123 LOWSTREET<br />SOMETOWN<br />BIGCITY',
			'c_post_code' => 'NE1 5DW',
			'c_catchment_area' => 'Berwick',
			'c_gender' => 'Male',
			'c_tel_home' => '0191 2111975',
			'c_tel_mob' => '07777 998990',
			'j_date_current' => date('d/m/Y'),
			'date_referral_made' => '31/07/2012',
			'date_referral_received' => '01/08/2012',
			'date_referral_accepted' => '02/08/2012',
			'j_date_of_referral' => '24/10/2011',
			'j_date_of_triage' => '05/11/2011',
			'ji_referral_source' => 'Adult Services',
			'ji_referrers_name' => 'Lee Tom',
			'j_date_first_appointment' => '29/12/2012',
			'j_date_first_appointment_offered' => '28/12/2012',
			'j_date_first_assessment' => '07/09/2012',
			'j_date_last_assessment' => '07/10/2012',
			'j_type' => 'Family',
			'appointments' => array(
				array(
					'ja_id'                  => '16',
					'ja_date_offered_format' => '29/12/2012',
					'ja_datetime_format'     => '29/12/2012 at 16:04',
					'ja_rc_name'             => 'Julie Aitman',
					'ja_notes'               => 'This appointment was a good one.',
					'dr_name'                => '',
					'ja_attended'            => 'Yes',
					'ja_dr_name'             => '',
					'ja_length'              => '90',
				),
			),
			'events' => array(
				array(
					'je_id'              => '4',
					'je_datetime_format' => '29/12/2012 at 13:37',
					'je_et_name'         => 'First Assessment',
					'je_notes'           => 'These notes concern the first assessment of this person.',
					'added_by'           => 'Example',
					'updated_by'         => 'N/A',
				),
			),
		);

		$this->load->vars($this->data);
		$this->load->view('/admin/options/mail_merge/create');
	}

	public function create()
	{
		//createWordDoc();
		//make pdf is called by ajax
	}

	/**
	 * Generate a PDF file merge mail letter
	 *
	 * @param segment $slug		Mail merge template to use
	 */
	public function make_pdf($slug)
	{
		// Hold any error messages
		$err = FALSE;

		// Get logged in user service provider ID
		$sp_id = $this->session->userdata('sps_sp_id');

		// Information about the PDF file
		$file_name = "Letter_{$slug}_" . uniqid();
		$file_path = FCPATH . 'pdf';
		@mkdir($file_path);		// attempt to make directory
		$file_path .= '/' . $file_name . '.pdf';	// system path
		$file_url = site_url('/pdf' .'/' . $file_name . '.pdf');		// public accessible URL

		// Load the report

		// Check if report exists
		if( ! $mail_merge = $this->mail_merge_model->get_mail_merge($slug))
		{
			return $this->_json_exit("Mail merge template ('$slug') not found.");
		}
		$pdf_title = $mail_merge['mm_title'];


		// get case information
		if( ! $data = $this->mail_merge_model->get_journey($_GET['j_id']))
			return $this->_json_exit("Case information not found.");

		//merge the mail
		$processed = $this->mail_merge_model->parse_mail_merge($mail_merge['mm_body'], $data);

		$site_url = base_url() . "mailmerge";
		$site_url = str_replace("https", "http", $site_url);
		//$body = str_replace('{site_url}', $site_url, $this->mailmerge_template_shell );


		//data for the pdf template to use
		$this->data['bodyhtml'] = $processed;
		$this->data['address'] = nl2br($this->config->item('service_address'));
		$this->data['bgimg'] = '/mailmerge/images/'. $mail_merge['mmf_src'];

		$this->load->vars($this->data);
		$html = $this->load->view('admin/options/mail_merge/pdf', NULL, TRUE);

		// Include the PDF library and generate it
		require_once(APPPATH . 'third_party/dompdf/dompdf_config.inc.php');
		$dompdf = new DOMPDF();
		$dompdf->set_base_path(APPPATH . '/pdf');
		$dompdf->load_html($html);
		$dompdf->render();
		$pdf = $dompdf->output();

		file_put_contents($file_path, $pdf);

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode(array(
			'status' => 'ok',
			'file_url' => $file_url,
			'file_title' => $pdf_title,
			'err' => $err,
		)));


	}

	public function createWordDoc()
	{
		$this->load->library('Worddoc');

		// get mail merge document, if it doesn't exist, show error
		if( ! $mail_merge = $this->mail_merge_model->get_mail_merge($_GET['mm_id']))
			show_error('No mail merge document was found');

		$document =& $mail_merge['mm_body'];


		// get case information
		if( ! $data = $this->mail_merge_model->get_journey($_GET['j_id']))
			show_error('No case information was found');

		$processed = $this->mail_merge_model->parse_mail_merge($document, $data);

		$site_url = base_url() . "mailmerge";

		$site_url = str_replace("https", "http", $site_url);

		$body = str_replace('{site_url}', $site_url, $this->mailmerge_template_shell );

		$body = str_replace('{body}', $processed, $body );

		$address = nl2br($this->config->item('service_address'));

		$body = str_replace('{address}', $address, $body );


		$this->worddoc->setBody($body);

		$dir = base_url();

		$this->worddoc->output(str_replace(' ', '_', $mail_merge['mm_title']) . '.doc');

	}


	/**
	 * Allows immediate of execution and responds with the error in JSON format
	 *
	 * Useful in functons that are called via AJAX and has multiple possible failure points.
	 *
	 * @param string $text		Error text to include in the JSON response
	 * @return void
	 */
	private function _json_exit($text = '')
	{
		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode(array(
			'status' => 'err',
			'msg' => $text,
		)));
	}




	private function _check_permission()
	{
		if( $this->session->userdata['permissions']['apt_can_manage_options'] != 1)
		{
			show_error('You do not have permission to configure options.');
		}
	}

}
