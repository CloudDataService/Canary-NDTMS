<?php

class CI_Download
{
	
	public function get($file, $dir)
		{
		
			if ( ! @require_once(APPPATH.'config/mimes'.EXT))
			{
				throw new Exception('Could not load mime types');
			}
			
			$mimes = array(	'hqx'	=>	'application/mac-binhex40',
							'cpt'	=>	'application/mac-compactpro',
							'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
							'bin'	=>	'application/macbinary',
							'dms'	=>	'application/octet-stream',
							'lha'	=>	'application/octet-stream',
							'lzh'	=>	'application/octet-stream',
							'exe'	=>	'application/octet-stream',
							'class'	=>	'application/octet-stream',
							'psd'	=>	'application/x-photoshop',
							'so'	=>	'application/octet-stream',
							'sea'	=>	'application/octet-stream',
							'dll'	=>	'application/octet-stream',
							'oda'	=>	'application/oda',
							'pdf'	=>	array('application/pdf', 'application/x-download'),
							'ai'	=>	'application/postscript',
							'eps'	=>	'application/postscript',
							'ps'	=>	'application/postscript',
							'smi'	=>	'application/smil',
							'smil'	=>	'application/smil',
							'mif'	=>	'application/vnd.mif',
							'xls'	=>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
							'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
							'wbxml'	=>	'application/wbxml',
							'wmlc'	=>	'application/wmlc',
							'dcr'	=>	'application/x-director',
							'dir'	=>	'application/x-director',
							'dxr'	=>	'application/x-director',
							'dvi'	=>	'application/x-dvi',
							'gtar'	=>	'application/x-gtar',
							'gz'	=>	'application/x-gzip',
							'php'	=>	'application/x-httpd-php',
							'php4'	=>	'application/x-httpd-php',
							'php3'	=>	'application/x-httpd-php',
							'phtml'	=>	'application/x-httpd-php',
							'phps'	=>	'application/x-httpd-php-source',
							'js'	=>	'application/x-javascript',
							'swf'	=>	'application/x-shockwave-flash',
							'sit'	=>	'application/x-stuffit',
							'tar'	=>	'application/x-tar',
							'tgz'	=>	'application/x-tar',
							'xhtml'	=>	'application/xhtml+xml',
							'xht'	=>	'application/xhtml+xml',
							'zip'	=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
							'mid'	=>	'audio/midi',
							'midi'	=>	'audio/midi',
							'mpga'	=>	'audio/mpeg',
							'mp2'	=>	'audio/mpeg',
							'mp3'	=>	array('audio/mpeg', 'audio/mpg'),
							'aif'	=>	'audio/x-aiff',
							'aiff'	=>	'audio/x-aiff',
							'aifc'	=>	'audio/x-aiff',
							'ram'	=>	'audio/x-pn-realaudio',
							'rm'	=>	'audio/x-pn-realaudio',
							'rpm'	=>	'audio/x-pn-realaudio-plugin',
							'ra'	=>	'audio/x-realaudio',
							'rv'	=>	'video/vnd.rn-realvideo',
							'wav'	=>	'audio/x-wav',
							'bmp'	=>	'image/bmp',
							'gif'	=>	'image/gif',
							'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
							'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
							'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
							'png'	=>	array('image/png',  'image/x-png'),
							'tiff'	=>	'image/tiff',
							'tif'	=>	'image/tiff',
							'css'	=>	'text/css',
							'html'	=>	'text/html',
							'htm'	=>	'text/html',
							'shtml'	=>	'text/html',
							'txt'	=>	'text/plain',
							'text'	=>	'text/plain',
							'log'	=>	array('text/plain', 'text/x-log'),
							'rtx'	=>	'text/richtext',
							'rtf'	=>	'text/rtf',
							'xml'	=>	'text/xml',
							'xsl'	=>	'text/xml',
							'mpeg'	=>	'video/mpeg',
							'mpg'	=>	'video/mpeg',
							'mpe'	=>	'video/mpeg',
							'qt'	=>	'video/quicktime',
							'mov'	=>	'video/quicktime',
							'avi'	=>	'video/x-msvideo',
							'movie'	=>	'video/x-sgi-movie',
							'doc'	=>	'application/msword',
							'docx'	=>	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
							'xlsx'	=>	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							'word'	=>	array('application/msword', 'application/octet-stream'),
							'xl'	=>	'application/excel',
							'eml'	=>	'message/rfc822'
						);		
						
				
			$file = basename(urldecode($file));
			
			$ext = strtolower(substr(strrchr($file,'.'),1));
			
			if($ext && array_key_exists($ext,$mimes)){
			
				$mime = $mimes[$ext];
				
				if(is_array($mime))
					$mime = $mime[0];
				
			} else {
				exit;
			}
						
			// change this depending on location of protected directory.
			$file_dir = $dir.$file;
						
			if(file_exists("$file_dir")) {
						
				if(is_readable("$file_dir")){
					$size=filesize("$file_dir");
					 // open the file for reading
					if($fp=@fopen("$file_dir",'r')){
						// send the headers
						header("Content-type: $mime");
						header("Content-Length: $size");
						header("Content-Disposition: attachment; filename=\"$file\"");
						// send the file content
						fpassthru($fp);
						// close the file
						fclose($fp);
						// and quit
						exit;
					}
		
				} // end if is_readable
			
			} // end if file_exists
			else {
				throw new Exception('Cannot find ' . $file_dir);
			}		
		
		} // end construct
	
}