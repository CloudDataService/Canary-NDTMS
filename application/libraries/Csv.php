<?php

class Csv
{
	
	protected $_export_schema; // export schema that is going to be used to build CSV file
	protected $_data; // the actual data we are creating the csv file with
	protected $_file_name; // the file name of the csv file
	protected $_CI; // current instance of CI
	
	public function __construct()
	{
		// set current instance of CI
		$this->_CI =& get_instance();
	}
	
	// inialises class by setting values and invoking other methods
	public function create_csv_file($export_schema, $data, $file_name, $dir = '/application/csv')
	{
		$this->_export_schema = $export_schema;
		$this->_data = $data;
		$this->_file_name = $file_name;
		
		// get array of lines for csv file
		$csv = $this->_parse_csv();
		
		// build csv file and return file path
		$csv_file_dir = $this->_create_file($csv);
		
		// return file path to csv file
		return $csv_file_dir;
	}
	
	// returns array that can be looped over to create lines of a csv file
	protected function _parse_csv()
	{
		// declare csv array
		$csv = array();
		
		// counter for csv lines
		$i = 0;		
		
		// loop through export schema to get each field name and title of that field name
		foreach($this->_export_schema as $field_name => $title)
		{
			// first line of csv file will contain the field name titles
			$csv[$i][$field_name] = $title;
		}
		
		// increment csv line by one
		$i++;
					
		// loop through data
		foreach($this->_data as $data)
		{

			// loop through field names of export schema and value to current csv line
			foreach($this->_export_schema as $field_name => $title)
			{				
				// add value of data csv line
				$csv[$i][$field_name] = @$data[$title];
			}
			// increment csv line by one
			
			$i++;
		}
		
		// return the formatted csv array
		return $csv;
	}
	
	// creates csv file on server and returns it on 
	protected function _create_file($csv)
	{			
		// check to see if .csv is appended at end of $_file_name
		$ext = (substr(strrchr($this->_file_name,'.'), 1) == 'csv' ? '' : '.csv');
		
		// build file path where new csv file is going to be stored
		$file_path = APPPATH . 'csv/' . $this->_file_name . $ext;

		// if we can open a file handler for our new csv file
		if($file = fopen($file_path, "w"))
		{
			// loop over each line in csv array
			foreach ($csv as $line)
			{
				// add it to the csv file
				fputcsv($file, $line);
			}
			
			// close the file handler
			fclose($file);
			
			// return file path to our new csv file
			return $file_path;
		}
	}
}