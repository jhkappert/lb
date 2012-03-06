<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: WidgetkitData
		Read/Write data in various formats.
*/
class WidgetkitData implements ArrayAccess, Countable {

    /*
		Variable: _data
			Data array.
    */
	protected $_data;
	
	/*
		Function: __construct
			Constructor
	*/	
	public function __construct($data = array()) {
		$this->_data = $data;
	}

	/*
		Function: has
			Has a key ?

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function has($name) {
		return isset($this->_data[$name]);
	}
	
	/*
		Function: get
			Get a value

		Parameters:
			$name - String
			$default - Mixed

		Returns:
			Mixed
	*/
	public function get($name, $default = null) {
		
		if (isset($this->_data[$name])) {
			return $this->_data[$name];
		}
		
		return $default;
	}

 	/*
		Function: set
			Set a value

		Parameters:
			$name - String
			$value - Mixed
			
		Returns:
			Void
	*/
	public function set($name, $value) {
		$this->_data[$name] = $value;
	}
	
	/*
		Function: remove
			Remove a value

		Parameters:
			$name - String
			
		Returns:
			Void
	*/
	public function remove($name) {
		unset($this->_data[$name]);
	}
	
 	/*
		Function: offsetExists
			(implements ArrayAccess interface)

		Parameters:
			$name - String
			
		Returns:
			Boolean
	*/
	public function offsetExists($name) {
		return $this->has($name);
	}

 	/*
		Function: offsetGet
			(implements ArrayAccess interface)

		Parameters:
			$name - String
			
		Returns:
			Mixed
	*/
	public function offsetGet($name) {
		return $this->get($name);
	}

 	/*
		Function: offsetSet
			(implements ArrayAccess interface)

		Parameters:
			$name - String
			$value - Mixed
			
		Returns:
			Void
	*/
	public function offsetSet($name, $value) {
		$this->set($name);
	}

 	/*
		Function: offsetUnset
			(implements ArrayAccess interface)

		Parameters:
			$name - String
			
		Returns:
			Void
	*/
	public function offsetUnset($name) {
		$this->remove($name);
	}

 	/*
		Function: count
			(implements Countable interface)
			
		Returns:
			Int
	*/
	public function count() {
		return count($this->_data);
	}

	/*
		Function: __isset
			Has a key ? (via magic method)

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function __isset($name) {
		return $this->has($name);
	}

	/*
		Function: __get
			Get a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->get($name);
	}

 	/*
		Function: __set
			Set a value (via magic method)

		Parameters:
			$name - String
			$value - Mixed
			
		Returns:
			Void
	*/
	public function __set($name, $value) {
		$this->set($name, $value);
	}

 	/*
		Function: __unset
			Unset a value (via magic method)

		Parameters:
			$name - String
			
		Returns:
			Void
	*/
	public function __unset($name) {
		$this->remove($name);
	}

 	/*
		Function: __toString
			Get string (via magic method)
			
		Returns:
			String
	*/
    public function __toString() {
        return empty($this->_data) ? '' : $this->_write($this->_data);
    }

	/*
		Function: _read
			Read array
	*/	
	protected function _read($array = array()) {
		return $array;
	}

	/*
		Function: _write
			Serialize array
	*/
	protected function _write($data) {
		return serialize($data);
	}

}

/*
	Class: JSONWidgetkitData
		Read/Write data in JSON format.
*/
class JSONWidgetkitData extends WidgetkitData {

    /*
		Variable: _assoc
			Returned object's will be converted into associative array's.
    */
	protected $_assoc = true;

	/*
		Function: __construct
			Constructor
	*/	
	public function __construct($data = array()) {
		
		// decode JSON string
		if (is_string($data)) {
			$data = $this->_read($data);
		}
		
		parent::__construct($data);
	}

	/*
		Function: _read
			Decode JSON string
	*/	
	protected function _read($json = '') {
		return json_decode($json, $this->_assoc);
	}

	/*
		Function: _write
			Encode JSON string
	*/
	protected function _write($data) {
		return json_encode($data);
	}
	
}