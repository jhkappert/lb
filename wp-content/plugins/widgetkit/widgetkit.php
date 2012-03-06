<?php
/*
	Plugin Name: Widgetkit
	Plugin URI: http://www.yootheme.com
	Description: A widget toolkit by YOOtheme.
	Version: 1.0.1
	Author: YOOtheme
	Author URI: http://www.yootheme.com
	License: YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// check compatibility
if (version_compare(PHP_VERSION, '5.2.4', '>=')) {

	// load class
	require_once(dirname(__FILE__).'/classes/widgetkit.php');

	// get instance and init system
	$widgetkit = Widgetkit::getInstance();
	$widgetkit['system']->init();

}