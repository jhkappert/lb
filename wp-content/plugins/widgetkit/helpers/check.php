<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: CheckWidgetkitHelper
		System helper class
*/
class CheckWidgetkitHelper extends WidgetkitHelper {
	
	// includes a html-list of the detected problems
	private $notice_list = null;

	/*
		Function: notices
			Checks the system for compatability with the widgetkit
		
		Returns:
			true 	if everything is okay
			false 	if problems were detected
	*/
	public function notices() {

		if ( !$this->notice_list ) {
			$this->run_tests();
		}

		return count($this->notice_list);
	}

	/*
		Function: get_notices
			Runs some compatibility checks on the system

		Returns:
			a list of errormessages as strings
	*/
	public function get_notices() {
		if ( !$this->notice_list ) {
			$this->run_tests();
		}
		return $this->notice_list;
	}

	/*
		function: run_tests
			Performs all tests on the system
	*/
	private function run_tests() {
		
		$this->notice_list = array();

		// cache writable ?
		if (!file_exists($this['path']->path('cache:')) || !is_writable($this['path']->path('cache:'))) {
			
			$this->notice_list[] = array(
				'type'    => 'critical',
				'message' => 'Widgetkit cache folder is not writable! Please check directory permissions '.$this['path']->path('cache:')
			);
		}

		// check if php version >= 5.2.4
		$needed_version = "5.2.4";
		if (version_compare($needed_version, phpversion(), ">")) {

			$this->notice_list[] = array(
				'type'    => 'critical',
				'message' => 'Your PHP version '.phpversion().' is too old. Make sure to install '.$needed_version.' or newer.'
			);
		}

		// check if installation supports json
		if (!function_exists('json_decode')) {

			$this->notice_list[] = array(
				'type'    => 'critical',
				'message' => 'No JSON support available.'
			);
		}

		// Check GD library support
		if ( !($this['image']->check()) ) {
			
			$this->notice_list[] = array(
				'type'    => 'critical',
				'message' => 'No GD library available.'
			);
		}

	}

}