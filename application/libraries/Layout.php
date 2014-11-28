<?php

class Layout
{
	

	protected $_template; // location and filename of template
	protected $_view; // location and filename of view 
	protected $_title = array(); // value of <title></title>
	protected $_breadcrumbs = array(); // breadcrumbs
	protected $_js = array(); // array of javascript files to load in view
	protected $_css = array(); // array of css files to load in view
	protected $_nav = array(); // array of navigation links
	
	protected $_ts;
	
	
	public function __construct()
	{
		$this->_ts = config_item('version');
	}

		

	// sets the value of $_template
	public function set_template($template)
	{
		$this->_template = 'templates/' . $template;	
	}

	

	

	// returns the value of $_template
	public function get_template()
	{
		return $this->_template;	
	}

	

	

	// sets the value of $_view
	public function set_view($view)
	{
		$this->_view = $view;	
	}

	

	

	// returns the vale of $_view
	public function get_view()
	{
		return $this->_view;	
	}

	

	

	// set array element to $_title
	public function set_title($title)
	{
		// is $title an array?
		if(is_array($title))
		{
			// loop through each title element and add it to $_title
			foreach($title as $title_element)
			{
				$this->_title[] = $title_element;
			}
		}
		else
		{
			$this->_title[] = $title;	
		}
	}

	

	

	// takes $_title and returns it as a formatted string ready to use in <title></title> 

	public function get_title($delimiter = '-')
	{
		// puts the array in reverse order so it reads like a breadcrumb
		$title_elements = array_reverse($this->_title);
		// glues $_title together and places the value of $delimiter in between each element of the array
		return implode(' ' . $delimiter . ' ', $title_elements);		
	}

	

	

	// gets the last title in the $_title array
	public function get_last_title()
	{
		return end($this->_title);		
	}

	

	
	// set array element to $_js
	function set_js($js, $dir = '/scripts/')
	{
		// is $js an array?
		if(is_array($js))
		{
			// loop through each $js element and add it to $_js
			foreach($js as $js_element)
			{
				$this->_js[] = $dir . $js_element . '.js?t=' . $this->_ts;
			}
		}
		else
		{
			$this->_js[] = $dir . $js . '.js?t=' . $this->_ts;
		}
	}
	
	function set_external_js($js)
	{	
		$this->_js[] = $js;
	}
	
	// returns html used to load javascript files in view
	function get_js()
	{		
		$html = '';
	
		foreach($this->_js as $js_element)
		{
			$html .= '<script type="text/javascript" src="' . $js_element .'"></script>' . "\n";
		}
		
		return $html;		
	}
	
	
	// sets the js array to a blank array
	function clear_js()
	{
		$this->_js = array();	
	}
		

	// set array element to $_css

	function set_css($css)
	{
		// is $css an array?
		if(is_array($css))
		{
			// loop through each $css element and add it to $_css
			foreach($css as $css_element)
			{
				$this->_css[] = $css_element;
			}
		}
		else
		{
			$this->_css[] = $css;
		}
	}

	

	

	// returns html used to load css files in view
	function get_css($dir = '/css/')
	{
		$html = '';
		foreach($this->_css as $css_element)
		{
			$html .= '<link href="' . $dir . $css_element . '.css?t=' . $this->_ts . '" rel="stylesheet" type="text/css" />' . "\n";
		}
		return $html;		
	}

	

	

	// clears $_css array
	function clear_css()
	{
		// sets $_css to blank array
		$this->_css = array();
		// returns layout object
		return $this;
	}

	

	

	// set array element to $_breadcrumb

	public function set_breadcrumb($breadcrumb, $link = '')
	{
		// is $breadcrumb an array?
		if(is_array($breadcrumb))
		{
			foreach($breadcrumb as $title => $link)
			{
				$this->_breadcrumbs[$title] = $link;
			}
		}
		else
		{
			$this->_breadcrumbs[$breadcrumb] = $link;	
		}
	}

	

	

	// takes $_breadcrumbs and formats them into a string for view

	public function get_breadcrumbs($delimiter = '»')
	{
		$html = '';
		
		// loop over each breadcrumb and add it to string
		foreach($this->_breadcrumbs as $title => $link)
		{
			// if it is the last breadcrumb in the array or if there is only 1 breadcrumb
			if($link == '' || count($this->_breadcrumbs) == 1)
			{
				// simply return title
				$html .= $title;
			}
			else
			{
				// return formatted string
				$html .= '<a href="' . $link . '">' . $title . '</a> ' . $delimiter . ' ';		
			}
		}
		return $html;
	}

	

	

	// set navigation

	public function set_nav($title, $link = '')
	{
		// is $title an array?
		if(is_array($title))
		{
			// if it is, add all links to $_nav
			foreach($title as $title => $link)
			{
				$this->_nav[$title] = $link;	
			}
		}
		else
		{
			$this->_nav[$title] = $link;	
		}	
		
		// return object
		return $this;
	}

	

	// takes $_nav, formats it and returns it as a string
	public function get_nav()
	{
		// begin by creating first part of html string
		$html = '<ul class="tabs">';
		
		// go over each element in the $_nav array
		foreach($this->_nav as $title => $href)
		{
			
			// create the first listed item in the navigation unordered list. Add classes where appropriate.			
			$html .= '<li class="' . (in_array($title, $this->_title) ? 'selected ' : '') . (is_array($href) ? 'has_more ' : '') . '">';
				
			// if the link contains a url, it is just a stand alone link, if it is an array, then we have a sub nav
			$html .= ( ! is_array($href) ? '<a href="' . $href . '">' . $title . '</a>' : '<span>' . $title . '</span>');
			
			// if we have a sub nav
			if(is_array($href))
			{
				// create sub nav html
				$html .= '<ul class="sub_nav">';
				
				foreach($href as $title => $href)
				{
					$html .= '<li>
								<a href="' . $href . '">' . $title . '</a>
								</li>';
				}
				
				$html .= '</ul>';
				
			}
			
			// close listed item
			$html .= '</li>';
		}
	
		// close unordered list
		$html .= '</ul>';
		
		// return full html
		return $html;
	}


	

}