<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/
	
	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$init      = array();
	$adresses  = array();
	
	foreach ($widget->items as $item) {
		$item['popup'] = strlen(trim($item['popup'])) ? '<div class="wk-content">'.$item['popup'].'</div>': '';
		if (!count($init)) {
			$init = $item;
			$init['text'] = $item['popup'];
			$init['mainIcon'] = $item['icon'];
		} else {
			$adresses[] = $item;
		}
	}
	
	$width = $settings['width'] == "auto" ? "100%": $settings['width']."px";
?>
<div id="map-<?php echo $widget_id; ?>" class="wk-map wk-map-default" style="height: <?php echo $settings['height']; ?>px; width:<?php echo $width; ?>;" data-widgetkit="googlemaps" data-options='<?php echo str_replace("'","\u0027",json_encode(array_merge($init, $settings, array("adresses"=>$adresses)))); ?>'></div>