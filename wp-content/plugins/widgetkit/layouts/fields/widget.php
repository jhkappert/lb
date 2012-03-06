<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// set attributes
$attributes = array();
$attributes['name']  = $name;
$attributes['class'] = 'widgets '.(isset($class) ? $class : '');

// set id attribute
if (isset($id)) {
	$attributes['id'] = $id;
}

// get widget options
$options = array();
foreach ($this['widget']->all() as $widget) {

	if (!isset($options[$widget->type])) {
		$options[$widget->type] = array();
	}

	$options[$widget->type][] = $widget;
}

printf('<select %s><option value="">Please select a widget...</option>', $this['field']->attributes($attributes, array('label', 'description', 'default')));

foreach ($options as $type => $widgets) {
	printf('<optgroup label="%s">', $type);
	
	foreach ($widgets as $widget) {

		// set attributes
		$attributes = array('value' => $widget->id);

		// is checked ?
		if ($widget->id == $value) {
			$attributes = array_merge($attributes, array('selected' => 'selected'));
		}

		printf('<option %s>%s</option>', $this['field']->attributes($attributes), $widget->name);
	}

	printf('</optgroup>');
}

printf('</select>');