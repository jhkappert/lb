<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: OptionWidgetkitHelper
		Option helper class, store option data
*/
class OptionWidgetkitHelper extends WidgetkitHelper {

    /*
		Variable: prefix
			Option prefix.
    */
	protected $prefix;

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);

		// set prefix
		$this->prefix = 'widgetkit_';
	}

	/*
		Function: get
			Get a value from data

		Parameters:
			$name - String
			$default - Mixed
		Returns:
			Mixed
	*/
	public function get($name, $default = null) {
		return get_option($this->prefix.$name, $default);
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
		update_option($this->prefix.$name, $value);
	}

}