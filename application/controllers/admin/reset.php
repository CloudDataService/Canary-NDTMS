<?php

class Reset extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library("auth");
		$this->load->model("admins_model");

		// you should only be here if you have to be.
		if (! $this->auth->check_password_expired())
			redirect("/admin");
	}

	public function index()
	{

		if ($_POST)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('new_password', '', 'required|callback__reset');
			$this->form_validation->set_rules('check_password', '', 'required');

			if (! $this->form_validation->run())
			{
				// failed to reset password
			}
		}

		$this->layout->set_view('/admin/reset/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

	public function _reset()
	{
		$new = $this->input->post("new_password");
		$check = $this->input->post("check_password");

		if ($new !== $check)
		{
			$this->data["password_mismatch"] = true;
			return false;
		}
		else if (strlen($new) < 12)
		{
			$this->data["strength_error"] = true;
			return false;
		}

		$this->admins_model->update_admin_password($this->session->userdata('a_id'), $new);
		redirect("/admin");
	}
}
