<?php

class Appointments_model extends MY_Model
{

	protected $_table = 'journey_appointments';
	protected $_primary = 'ja_id';

	protected $_filter_types = array(
		'where' => array('ja_id', 'ja_j_id', 'ja_rc_id'),
		'like' => array(),
		'in' => array(),
		'function' => array(),
	);


	public function __construct()
	{
		parent::__construct();
	}




}
