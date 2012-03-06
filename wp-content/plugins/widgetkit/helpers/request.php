<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: RequestWidgetkitHelper
		Helper for managing/retrieving request variables.
*/
class RequestWidgetkitHelper extends WidgetkitHelper {
	
		protected $_POST;
		protected $_GET;
		protected $_COOKIE;
		protected $_REQUEST;
	
	
	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);
		
		
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			$this->_POST      = array_map('stripslashes_deep', $_POST);
			$this->_GET       = array_map('stripslashes_deep', $_GET);
			$this->_COOKIE    = array_map('stripslashes_deep', $_COOKIE);
			$this->_REQUEST   = array_map('stripslashes_deep', $_REQUEST);
		} else {
			$this->_POST    = $_POST;
			$this->_GET     = $_GET;
			$this->_COOKIE  = $_COOKIE ;
			$this->_REQUEST = $_REQUEST;
		}		
	}
	
	/*
		Function: get
			Retrieve a value from a request variable

		Parameters:
			$var - Variable name (hash:name)
			$type - Variable type (string, int, float, bool, array)
			$default - Default value

		Returns:
			Mixed
	*/	
    public function get($var, $type, $default = null) {
		
		// parse variable name
		extract($this->_parse($var));

		// get hash array, if name is empty
		if ($name == '') {
			return $this->_hash($hash);
		}
		
		// access a array value ?
		if (strpos($name, '.') !== false) {

			$parts = explode('.', $name);
			$array = $this->_get(array_shift($parts), $default, $hash, $type);

			foreach ($parts as $part) {

				if (!is_array($array) || !isset($array[$part])) {
					return $default;
				}

				$array =& $array[$part];
			}

			return $array;
		}

		return $this->_get($name, $default, $hash, $type);
    }
	
	/*
		Function: _get
			Get variable from http request.

		Returns:
			Mixed
	*/	
	protected function _get($name, $default = null, $hash = 'default', $type = 'none') {
		
		$input = $this->_hash($hash);
		$var   = (isset($input[$name]) && $input[$name] !== null) ? $input[$name] : $default;

		if (in_array($type, array('string', 'int', 'float', 'bool', 'array'))) {
			settype($var, $type);
		}

		return $var;
	}

	/*
		Function: _hash
			Get hash from http request.

		Returns:
			Mixed
	*/	
	protected function _hash($hash) {
		
		switch (strtoupper($hash)) {
			case 'GET' :
				$input = $this->_GET;
				break;
			case 'POST' :
				$input = $this->_POST;
				break;
			case 'COOKIE' :
				$input = $this->_COOKIE;
				break;
			default:
				$input = $this->_REQUEST;
				break;
		}

		return $input;
	}
	
	/*
		Function: _parse
			Parse variable string.

		Parameters:
			$var - Variable

		Returns:
			String
	*/
	protected function _parse($var) {
	    
	    // init vars
		$parts = explode(':', $var, 2);
		$count = count($parts);
		$name  = '';
		$hash  = 'default';

		// parse variable name
		if ($count == 1) {
			list($name) = $parts;
		} elseif ($count == 2) {
			list($hash, $name) = $parts;
		}
		
		return compact('hash', 'name');
    }
	
}