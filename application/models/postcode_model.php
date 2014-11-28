<?php

class Postcode_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_lat_lng($postcode)
	{
		// remove any spaces from postcode
		$postcode = str_replace(' ', '', $postcode);
		
		// check if postcode is already set in db
		$sql = 'SELECT
					pc_postcode
				FROM
					postcodes
				WHERE
					pc_postcode = ?';
		
		// if postcode does not exist in db			
		if( ! $result = $this->db->query($sql, $postcode)->row_array())
		{
			// if we successfully get lat/lng from goo-goo maps
			if($coords = $this->_get_coords($postcode))
			{
				
				// if the latitude of the coords is greater than 50 and less than 59
				// and the longitude of the coords is greater than -5 and less than 4
				// it is a valid set of coords
				if( $coords['lat'] > 50 && $coords['lat'] < 59 && $coords['lng'] > -5 && $coords['lng'] < 4)
				{
					// store postcode and coords in db
					$sql = 'INSERT INTO
							postcodes
						SET
							pc_postcode = ?,
							pc_lat = ?,
							pc_lng = ?';
							
					$this->db->query($sql, array($postcode,
											 $coords['lat'],
											 $coords['lng']));
											 
					return true;					 
				}
			}
		}
		
		return false;
	}
	
	// returns location details for all clients
	public function get_clients()
	{
		// if client coords cache exists and is valid
		if($client_coords = $this->cache->get('client_coords'))
			return $client_coords;
		
		$sql = 'SELECT
					c_id,
					pc_lat AS lat,
					pc_lng AS lng
				FROM
					clients,
					postcodes
				WHERE
					pc_postcode = c_post_code ';
		
		// get client coords json encoded		
		$client_coords = json_encode($this->db->query($sql)->result_array());
		
		// save client coords to cache for 10 mins
		$this->cache->save('client_coords', $client_coords, 3600);
		
		// return client coords
		return $client_coords;
	}
	
	// returns lat,lng coords for a given postcode
	protected function _get_coords($postcode)
	{		
	
		// set google maps api key
		$_key = 'ABQIAAAAKu8hk1iFe_qX4xW1WuGnkRQoLHFEvF6DEdn3dYzBFc08HABpNxSJbdYGFf916XfjY_Qnpy_rlSBHNA';
		
		// url for requests to api
		$_api_url = 'http://maps.google.co.uk/maps/geo?output=xml&key=' . $_key;
	
		// declare delay time
		$delay = 0;

		// set request pending
		$geocode_pending = true;
		
		// while a request is pending
		while($geocode_pending)
		{
			// create request url
			$request_url = $_api_url . "&q=" . urlencode($postcode);	
			
			if( ! $xml = simplexml_load_file($request_url))
			{ 
				// log error, couldn't load xml
			}
			
			// get status of request
			$status = $xml->Response->Status->code;
			
			// if status is ok
			if (strcmp($status, "200") == 0)
			{		
				// gecode no longer pending
				$geocode_pending = false;
					  
				// get lat,lng
				$coordinates = explode(",", $xml->Response->Placemark->Point->coordinates);

				// return lat,lng
				return array('lat' => $coordinates[1],
							 'lng' => $coordinates[0]);
			}
			elseif(strcmp($status, "620") == 0)
			{
				// sent geocodes too fast, set delay
				$delay += 100000;
			} 
			else
			{
				// failed to geocode postcode
				$geocode_pending = false;
			}
			
			// delay to throttle requests
			usleep($delay);
		}
	} // end get_lat_lng()
}