<?php

class Gps_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}

	// get total active GPs
	public function get_total_gps()
	{
		$sql = 'SELECT
					COUNT(gp_code) AS total
				FROM
					gps
				WHERE
					gp_active = 1';

		$result = $this->db->query($sql)->row_array();

		return $result['total'];
	}

	// get active GPs
	public function get_gps($start = 0, $limit = 0)
	{
		$sql = 'SELECT
					gp_code,
					gp_name,
					gp_surgery,
					gp_telephone
				FROM
					gps
				WHERE
					gp_active = 1 ';

		if($limit)
			$sql .= ' LIMIT ?, ?';

		return $this->db->query($sql, array($start, $limit))->result_array();
	}

	// get gps for a select element
	public function get_gps_select()
	{
		$sql = 'SELECT
					gp_code,
					gp_name
				FROM
					gps
				WHERE
					gp_active = 1
				ORDER BY
					gp_code ASC ';

		return $this->db->query($sql)->result_array();
	}

	// returns
	public function get_gps_ajax()
	{
		$sql = 'SELECT
					CONCAT(gp_name, " (#", gp_code, ")") AS gp
				FROM
					gps
				WHERE
					gp_name LIKE ?
					OR
					gp_code LIKE ?
				LIMIT 10 ';

		return $this->db->query($sql, array("%{$_GET['q']}}%", "%{$_GET['q']}}%"))->result_array();
	}

	// get single GP information
	function get_gp($gp_code)
	{
		if($gp_code)
		{
			$sql = 'SELECT
						*
					FROM
						gps
					WHERE
						gp_code = ?
						AND gp_active = 1';

			return $this->db->query($sql, array($gp_code))->row_array();
		}

		return false;
	}


	public function parse_csv($file_name)
	{
		// clear gps table
		$sql = 'DELETE FROM
					gps ';

		// commit query
		$this->db->query($sql);

		// open handle to csv file
		$handle = fopen(APPPATH . $file_name, 'r');

		// declare row
		$row = 0;

		// while lines still in csv file
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			// if this isn't the header row
			if($row != 0)
			{
				// set gp code
				$gp['code'] = $data[0];

				// set gp name
				$gp['name'] = $data[1];

				// declare address lines with address line 1
				$address_lines = array($data[4]);

				// if address line 2 is not empty
				if($data[5] != '')
					$address_lines[] = $data[5];

				// if address line 3 is not empty
				if($data[6] != '')
					$address_lines[] = $data[6];

				// if address line 4 is not empty
				if($data[7] != '')
					$address_lines[] = $data[7];

				// if address line 5 is not empty
				if($data[8] != '')
					$address_lines[] = $data[8];

				// set gp surgery address
				$gp['surgery'] = implode("\n", $address_lines);

				// set gp telephone number
				$gp['telephone'] = $data[15];

				// set gp postcode
				$gp['postcode'] = $data[9];

				// insert gp into gps table
				$sql = 'INSERT INTO
							gps
						SET
							gp_code = ?,
							gp_name = ?,
							gp_surgery = ?,
							gp_telephone = ?,
							gp_postcode = ?,
							gp_active = 1';

				// commit query
				$this->db->query($sql, $gp);
			}

			// incremenet rows
			$row++;
		}
	}


}
