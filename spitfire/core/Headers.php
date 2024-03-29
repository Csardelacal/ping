<?php namespace spitfire\core;

use spitfire\core\Environment;

class Headers
{
	private $headers = Array(
	    'Content-type' => 'text/html;charset=utf-8',
	    'x-Powered-By' => 'Spitfire',
	    'x-version'    => '0.1 Beta'
	);
	
	private $states = Array(
		 200 => '200 OK',
		 206 => '206 Partial Content',
		 301 => '301 Moved Permanently',
		 302 => '302 Found',
		 400 => '400 Invalid request',
		 401 => '401 Unauthorized',
		 403 => '403 Forbidden',
		 404 => '404 Not Found',
		 410 => '410 Gone',
		 416 => '416 Range not satisfiable',
		 500 => '500 Server Error'
	);
	
	public function set ($header, $value) {
		$this->headers[$header] = $value;
	}
	
	public function get($header) {
		return $this->headers[$header];
	}
	
	public function send () {
		foreach ($this->headers as $header => $value) {
			
			#Special condition for status headers
			if (strtolower($header) == 'status') {
				http_response_code((int)$value);
				continue;
			}
			
			header("$header: $value");
		}
	}
	
	/**
	 * This method allows the application to define the content type it wishes to
	 * send with the response. It prevents the user from having to define the 
	 * headers manually and automatically sets an adequate charset depending on
	 * the app's settings.
	 * 
	 * @param string $str
	 */
	public function contentType($str) {
		
		#Check if we have a defined environment that provides a legit encoding
		if (class_exists('\spitfire\core\Environment')) { $encoding = Environment::get('system_encoding'); }
		else                                            { $encoding = 'utf8'; }
		
		switch ($str) {
			case 'php':
			case 'html':
				$this->set('Content-type', 'text/html;charset=' . $encoding);
				break;
			case 'xml':
				$this->set('Content-type', 'application/xml;charset=' . $encoding);
				break;
			case 'json':
				$this->set('Content-type', 'application/json;charset=' . $encoding);
				break;
			default:
				$this->set('Content-type', $str);
		}
	}
	
	public function status($code= 200) {
		#Check if the call was valid
		if (!is_numeric($code)) { throw new \BadMethodCallException('Invalid argument. Requires a number', 1509031352); }
		if (!isset($this->states[$code])) { throw new \BadMethodCallException('Invalid status code', 1509031353); }
		
		$this->set('Status', $code);
	}
	
	public function redirect($location, $status = 302) {
		$this->set('Location', $location);
		$this->set('Expires', date("r", time()));
		$this->set('Cache-Control', 'no-cache, must-revalidate');
		$this->set('Status', $status);
	}
	
}
