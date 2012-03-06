<?php

// init vars
$path = dirname(dirname(__FILE__));

// load classes
require_once($path.'/classes/helper.php');
require_once($path.'/helpers/path.php');

/*
	Class: Widgetkit
		Widgetkit class.
*/
class Widgetkit implements ArrayAccess {

	/* helpers */
	protected $_helpers = array();

	/* instances */
	protected static $_instance;

	/*
		Function: __construct
			Class Constructor.

		Returns:
			Plugin
	*/
	public function __construct() {

        // set defaults
        $path = dirname(dirname(__FILE__));
        $this->addHelper(new PathWidgetkitHelper($this));

		// register paths
        $this['path']->register($path, 'widgetkit');
        $this['path']->register($path.'/classes', 'classes');
        $this['path']->register($path.'/helpers', 'helpers');
        $this['path']->register($path.'/layouts', 'layouts');
    }

	/*
		Function: getInstance
			Retrieve instance

		Returns:
			Widgetkit
	*/
	public static function getInstance() {
	
		// add instance, if not exists
		if (!isset(self::$_instance)) {
			self::$_instance = new Widgetkit();
		}

		return self::$_instance;
	}

	/*
		Function: getHelper
			Retrieve a helper

		Parameters:
			$name - Helper name
	*/
	public function getHelper($name) {

		// try to load helper, if not found
		if (!isset($this->_helpers[$name])) {
		    $this->loadHelper($name);
		}

		// get helper
		if (isset($this->_helpers[$name])) {
			return $this->_helpers[$name];
		}
		
		return null;
	}

	/*
		Function: addHelper
			Adds a helper

		Parameters:
			$helper - Helper object
			$alias - Helper alias (optional)
	*/
	public function addHelper($helper, $alias = null) {

		// add to helpers
		$name = $helper->getName();
		$this->_helpers[$name] = $helper;

		// add alias
		if (!empty($alias)) {
			$this->_helpers[$alias] = $helper;
		}

	}

	/*
		Function: loadHelper
			Load helper from path

		Parameters:
			$helpers - Helper names
			$suffix - Helper class suffix
	*/
	public function loadHelper($helpers, $suffix = 'WidgetkitHelper') {
		$helpers = (array) $helpers;

		foreach ($helpers as $name) {
			$class = $name.$suffix;

			// autoload helper class
			if (!class_exists($class) && ($file = $this['path']->path('helpers:'.$name.'.php'))) {
                require_once($file);
			}

			// add helper, if not exists
			if (!isset($this->_helpers[$name])) {
				$this->addHelper(new $class($this));
			}
		}
	}
	
	/* ArrayAccess interface implementation */

	public function offsetGet($name)	{
		return $this->getHelper($name);
	}

	public function offsetSet($name, $helper) {
		$this->_helpers[$name] = $helper;
	}

	public function offsetUnset($name) {
		unset($this->_helpers[$name]);
	}

	public function offsetExists($name) {
		return !empty($this[$name]);
	}

}