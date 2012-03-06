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
$attributes['class'] = isset($node->attributes()->class) ? (string)$node->attributes()->class : '';

printf('<textarea %s>%s</textarea>', $this['field']->attributes($attributes, array('label', 'description', 'default')), $value);