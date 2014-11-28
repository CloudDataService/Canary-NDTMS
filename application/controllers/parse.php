<?php

class Parse extends MY_controller {


	public function __construct()
	{
		parent::__construct();
	}


	public function index()
	{
	}


	public function cache_journeys($j_id = 0)
	{
		$this->load->model('journeys_model');

		if ($j_id == 0)
		{
			$sql = 'SELECT j_id FROM journeys';
			$result = $this->db->query($sql)->result_array();

			header("Content-Type: text/plain");

			foreach ($result as $row)
			{
				echo "Caching journey {$row['j_id']} ...\n";
				$this->journeys_model->cache_journey($row['j_id']);
			}

			echo 'Cached ' . count($result) . ' journeys';
		}
		else
		{
			$this->journeys_model->cache_journey($j_id);
			echo "Cached journey $j_id.";
		}
	}

	public function anon()
	{
		if($handle = fopen(FCPATH . 'randomdata.csv', 'r'))
		{
			$c_id = 1;

			$i = 0;

			while(! feof($handle))
			{
				$i++;

				$data = fgetcsv($handle);

				if($i != 1)
				{
					$sql = 'SELECT
								c_id
							FROM
								clients
							WHERE
								c_id = (SELECT MIN(c_id) FROM clients WHERE c_id > "' . $c_id . '");';

					$row = $this->db->query($sql)->row_array();

					$c_id = $row['c_id'];

					$sql = 'UPDATE
								clients
							SET
								c_fname = ?,
								c_sname = ?,
								c_address = ?,
								c_date_of_birth = ?
							WHERE
								c_id = "' . (int)$c_id . '";';

					$this->db->query($sql, array($data[0], $data[1], $data[2], $data[3]));
				}

				$i++;
			}

			echo $i;
		}

	}


}