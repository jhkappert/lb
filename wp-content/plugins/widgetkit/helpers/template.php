<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: TemplateWidgetkitHelper
		Template helper class, render layouts
*/
class TemplateWidgetkitHelper extends WidgetkitHelper {

	/* slots */
	protected $_slots = array();
    
	/*
		Function: render
			Render a layout file

		Parameters:
			$name - Layout name
			$args - Array of arguments

		Returns:
			String
	*/	
	function render($name, $args = array()) {
        
		$path = $this->widgetkit['path'];
		
		if (strpos($name, ':')===false) {
			$name = 'layouts:'.$name;
		}
		
		// load layout
		$__layout = $path->path($name.'.php');

		// render layout
		if ($__layout != false) {

			// import vars and get content
			extract($args);
			ob_start();
			include($__layout);
			return ob_get_clean();
		}
		
		trigger_error('<b>'.$name.'</b> not found in paths: ['.implode(', ',$path->getPaths('layouts')).']');
		
		return null;
	}
    
	/*
		Function: has
			Slot exists ?

		Parameters:
			$name - Slot name

		Returns:
			Boolean
	*/	
	function has($name) {
		return isset($this->_slots[$name]);
	}

	/*
		Function: get
			Retrieve a slot

		Parameters:
			$name - Slot name
			$default - Default content

		Returns:
			Mixed
	*/	
	function get($name, $default = false) {
		return isset($this->_slots[$name]) ? $this->slots[$name] : $default;
	}

	/*
		Function: set
			Set a slot

		Parameters:
			$name - Slot name
			$content - Content

		Returns:
			Void
	*/	
	function set($name, $content) {
		$this->_slots[$name] = $content;
	}

	/*
		Function: output
			Outputs slot content

		Parameters:
			$name - Slot name
			$default - Default content

		Returns:
			Boolean
	*/	
	function output($name, $default = false) {

		if (!isset($this->_slots[$name])) {
		
			if (false !== $default) {
				echo $default;
				return true;
			}

			return false;
		}

		echo $this->_slots[$name];
		return true;
	}

}