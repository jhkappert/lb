<?php 
/**
* @package   Widgetkit Bonus Styles
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$content   = array();
	$nav       = ($settings['navigation']) ? 'nav-'.$settings['navigation'] : '';

?>

<div id="slideshow-<?php echo $widget_id; ?>" class="wk-slideshow wk-slideshow-tabsbar-balance" data-widgetkit="slideshow" data-options='<?php echo json_encode($settings); ?>'><div>
	
	<div class="nav-container <?php echo $nav; ?> clearfix">
		<ul class="nav">
			<?php foreach ($widget->items as $key => $item) : ?>
			<?php $content[] = '<li><article class="wk-content clearfix">'.$item['content'].'</article></li>'; ?>
			<li>
				<span><?php echo $item['title']; ?></span>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	
	<div class="slides-container"><?php echo (count($content)) ? '<ul class="slides">'.implode('', $content).'</ul>' : '';?></div>
	
</div></div>