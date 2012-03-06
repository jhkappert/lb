<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

printf('<select %s>', $this['field']->attributes(compact('name')));

foreach ($node->children() as $option) {

	// set attributes
	$attributes = array('value' => $option->attributes()->value);
	
	// is checked ?
	if ($option->attributes()->value == $value) {
		$attributes = array_merge($attributes, array('selected' => 'selected'));
	}

	printf('<option %s>%s</option>', $this['field']->attributes($attributes), (string)$option);
}

printf('</select>');