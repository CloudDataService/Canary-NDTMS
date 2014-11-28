<?php

class Charts_model extends CI_Model
{
	
	protected $_url = 'http://chart.apis.google.com/chart?';
	
	public function __construct()
	{
		parent::__construct();
	}	
	
	// returns formatted url string for radar chart based on case scores
	public function get_score_chart($jas)
	{		
		$params = array();
		
		// set chart type
		$params['cht'] = 'r';
		
		// chart size
		$params['chs'] = '300x300';
		
		$chd = 't:';
		
		$chxl = '0:|';
		
		$i = 1;
		
		foreach ($jas['scores'] as $row)
		{
			$chd .=  $this->_divide($row['jacs_score']) . ',';
			
			$chxl .= $row['jacs_num'] . '|';
			
			$i++;
		}
						
		// chart data
		$params['chd'] = rtrim($chd, ',');
			
		// chart axis
		$params['chxt'] = 'x';
		
		// fill colours
		$params['chm'] = 'B,FF000080,0,0,0';
		
		// line colours
		$params['chco'] = 'FF0000';
		
		$params['chf'] = 'bg,s,efefef';
				
		// chart labels
		$params['chxl'] = rtrim($chxl, '|');
		
		// get url
		$url = $this->_build_url($params);
			
		// md5 url and append .png - name of image
		$img = md5($url) . '.png';
				
		// set full directory to image
		$dir = FCPATH . 'img/assessments/' . $img;
		
		// if we don't have a cache of the chart
		if( ! file_exists($dir))
		{
			// create image
			$chart_png = imagecreatefrompng($url);
			
			// cache the image  
			imagepng($chart_png, $dir);
		}
				
		// return path to image on server
		return '/img/assessments/' . $img;
	}
	
	
	public function get_avg_scores($sp_id = 0)
	{
		$sql = 'SELECT
					AVG(phq9_first_score) AS phq9_first_score,
					AVG(phq9_last_score) AS phq9_last_score,
					AVG(gad7_first_score) AS gad7_first_score,
					AVG(gad7_last_score) AS gad7_last_score,
					AVG(wsas_first_score) AS wsas_first_score,
					AVG(wsas_last_score) AS wsas_last_score,
					AVG(core_first_score) AS core_first_score,
					AVG(core_last_score) AS core_last_score
				FROM
					cases ';
					
		if($sp_id)
			$sql .= ' WHERE c_sp_id = "' . (int)$sp_id . '" ';
			
		$scores = $this->db->query($sql)->row_array();		
		
		return $this->get_score_chart($scores);
	}
	
	
	protected function  _divide($score)
	{
		return ($score * 100) / 10;
	}
	
	protected function _build_url($params)
	{
		$url = $this->_url;
		
		foreach($params as $key => $value)
		{
			$url .= $key . '=' . $value . '&';		
		}
		
		return $url;
	}
	
}