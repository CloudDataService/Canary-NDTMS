<?php

	class Twitter
	{
		
		public function __construct()
		{
		}
		
		// get a specific user's timeline
		public function get_user_timeline($screen_name, $tweet_count = 5)
		{			
			$twitter_data = $this->_api_call('1/statuses/user_timeline', array('screen_name' => $screen_name, 'count' => $tweet_count));
			
			return $this->_parse_timeline($twitter_data);
		}
		
		protected function _api_call($twitter_method, $options = array(), $http_method = 'get', $format = 'json')
		{
			// initialise curl
			$curl_handle = curl_init();
   			
			// set api url
			$api_url = sprintf('http://api.twitter.com/%s.%s', $twitter_method, $format);
			
			// if the http method is get, add any options to api_url
			if (($http_method == 'get') && (count($options) > 0)) {
				
				$api_url .= '?';
				
				foreach($options as $key => $value)
				{
					$api_url .= $key . '=' . $value .'&';
				}
   			}
			
			// set the options to the curl handle
			curl_setopt($curl_handle, CURLOPT_URL, $api_url);

			// if the http method is post, add any options
			if ($http_method == 'post') {
				
				curl_setopt($curl_handle, CURLOPT_POST, true);
      			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($options));
				
			}
			
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
			
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
			
			$twitter_data = curl_exec($curl_handle);
			
			$this->http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
			
			$this->last_api_call = $api_url;
			
			curl_close($curl_handle);
			
			return $twitter_data;
		}	
		
		protected function _parse_timeline($twitter_data)
		{		
			return json_decode($twitter_data);
		}
		
		
	}