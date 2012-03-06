<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: DataWidgetkitHelper
		Data helper class.
*/
class DataWidgetkitHelper extends WidgetkitHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);

		// load class
		require_once($this['path']->path('classes:data.php'));
	}

	/*
		Function: create
			Retrieve a data object

		Parameters:
			$data - Data
			$format - Data format

		Returns:
			Mixed
	*/
	public function create($data = array(), $format = 'json') {
		
		// load data class
		$class = $format.'WidgetkitData';

		return new $class($data);
	}
	
}