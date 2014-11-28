<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library to handle all NDTMS stuff
 *
 * @package Escape
 * @subpackage Libraries
 * @author CR
 */

class Ndtms extends CI_Driver_Library
{
	
	
	public $CI;		// CodeIgntier object
	
	public $valid_drivers = array('ndtms_h', 'ndtms_k');		// valid ndtms standard drivers
	
	private $_dataset = 'k';
	
	
	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->set_dataset('k');
	}
	
	
	
	
	/**
	 * Specify which dataset to use for processing and data (if other than default)
	 */
	public function set_dataset($dataset = '')
	{
		$this->_dataset = $dataset;
		
		if (method_exists($this->{$dataset}, 'init'))
		{
			call_user_func(array($this->{$dataset}, 'init'));
		}
	}
	
	
	
	
	/**
	 * Get the dataset version that has been set
	 */
	public function get_dataset($dataset = '')
	{
		return $this->_dataset;
	}
	
	
	
	
	/**
	 * Get NDTMS CSV file from a result array.
	 *
	 * @param array $result		DB result array of all rows to export
	 * @param bool $direct_output		Specify whether or not the output should be directly outputted/streamed to the browser
	 * @return mixed		If $direct_output is FALSE, return value is the CSV data
	 */
	public function get_csv($result = array(), $direct_output = FALSE)
	{
		// Get the schema which details the CSV column header and where to get the value from
		$schema = $this->{$this->_dataset}->get_schema();
		
		// Get whitelist of fields (all others will be set to NULL)
		$whitelist = $this->{$this->_dataset}->get_whitelist();
		
		// Define the formatting to be used
		$enclosure = '"';
		$delim = ',';
		$newline = "\n";
		
		// Variable for final output
		$out = '';
		$line = '';
		
		// First generate the headings from the table column names
		foreach ($schema as $col_header => $field)
		{
			$line .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $col_header).$enclosure.$delim;
		}
		
		$line = rtrim($line);
		$line = rtrim($line, $delim);
		$line .= $newline;
		
		if ($direct_output)
		{
			echo $line;
		}
		else
		{
			$out .= $line;
		}
		
		// Next blast through the result array and build out the rows
		foreach ($result as $row)
		{
			$line = '';
			
			$use_whitelist = $this->{$this->_dataset}->use_whitelist($row);
			
			foreach ($schema as $col_header => $field)
			{
				// $field specifies the source data point that should be used as the value for this column
				// It should be NULL, an array key of $row, or a function.
				
				// If the whitelist should be used for this row AND this column is NOT a whitelisted field - it should be empty
				if ($use_whitelist && ! in_array($col_header, $whitelist))
				{
					$value = '';
				}
				else
				{
					if ($field === NULL)
					{
						// NULL value - get the null value characters
						$value = $this->{$this->_dataset}->get_null_value();
					}
					elseif (array_key_exists($field, $row))
					{
						// just get the value from the data array
						$value = element($field, $row, $this->{$this->_dataset}->get_null_value());
					}
					elseif (method_exists($this->{$this->_dataset}, 'field_' . $field))
					{
						// Call the field_ function specified with $row as the param 
						$value = call_user_func(array($this->{$this->_dataset}, 'field_' . $field), $row);
					}
					else
					{
						// Uh-oh.
						$value = '';	//'-ERR/' . $field;
					}
				}
				
				// Output the row
				$line .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $value).$enclosure.$delim;
			}
			
			$line = rtrim($line);
			$line = rtrim($line, $delim);
			$line .= $newline;
			
			if ($direct_output)
			{
				echo $line;
			}
			else
			{
				$out .= $line;
			}
		}
		
		if ($direct_output === FALSE)
		{
			return $out;
		}
		else
		{
			return TRUE;
		}
	
	}
	
	
	
	
	// ========================================================================
	// Validation
	// ========================================================================
	
	
	
	
	/**
	 * Determine if a journey is valid or not. Returns an array, with keys 'valid' and 'errors';
	 *
	 * @param int $j_id		ID of journey to validate
	 * @return array		Array. Key 'valid' will be boolean true/false; Key 'errors' will be array of error strings.
	 * @author CR
	 */
	public function validate_journey($j_id = 0)
	{
		$status = $this->{$this->_dataset}->validate_journey($j_id);
		
		if ($status['valid'] === TRUE)
		{
			// Journey is valid!
			$this->set_journey_valid($j_id, 1);
		}
		else
		{
			// Invalid
			$this->set_journey_valid($j_id, 2);
		}
		
		return $status;
	}
	
	
	
	
	/**
	 * Update the journey validity status in the database.
	 *
	 * @param int $j_id		ID of journey to update
	 * @param int $j_ndtms_valid		Journey validity - 1 Yes, 2 No.
	 * @author CR
	 */
	private function set_journey_valid($j_id = 0, $j_ndtms_valid = 2)
	{
		// Update journey validity status in the DB
		$sql = 'UPDATE journeys
				SET j_ndtms_valid = ?
				WHERE j_id = ?
				LIMIT 1';
		
		return $this->CI->db->query($sql, array($j_ndtms_valid, $j_id));
	}
	
	
	
	
}

/* End of file: ./application/libaries/Ndtms/Ndtms.php */