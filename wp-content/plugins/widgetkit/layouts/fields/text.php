<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// set attributes
$attributes = array();
$attributes['type']  = 'text';
$attributes['name']  = $name;
$attributes['value'] = $value;
$attributes['class'] = isset($class) ? $class : '';

printf('<input %s />', $this['field']->attributes($attributes, array('label', 'description', 'default')));