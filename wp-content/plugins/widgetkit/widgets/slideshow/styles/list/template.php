<?php 
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$content   = array();
	$nav       = ($settings['navigation']) ? 'nav-'.$settings['navigation'] : '';

?>

<div id="slideshow-<?php echo $widget_id; ?>" class="wk-slideshow wk-slideshow-list" data-widgetkit="slideshow" data-options='<?php echo json_encode($settings); ?>'>
	<div>
	
		<ul class="nav <?php echo $nav; ?> clearfix">
			<?php foreach ($widget->items as $key => $item) : ?>
			<?php $content[] = '<li><article class="wk-content clearfix">'.$item['content'].'</article></li>'; ?>
			<li>
				<span><?php echo $item['title']; ?></span>
			</li>
			<?php endforeach; ?>
		</ul>
		
		<div class="slides-container"><?php echo (count($content)) ? '<ul class="slides">'.implode('', $content).'</ul>' : '';?></div>
		
	</div>
</div>