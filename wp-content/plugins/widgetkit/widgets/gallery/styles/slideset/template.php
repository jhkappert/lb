<?php 
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$images    = $this['gallery']->images($widget);
	$sets      = array();
	$nav       = array();
	
	$sets = array_chunk($images, $settings['items_per_set']);

	foreach (array_keys($sets) as $s) {
		$nav[] = '<li><span></span></li>';
	}

	unset($settings["width"]);
	unset($settings["height"]);

	$i = 0;
?>

<div id="slideset-<?php echo $widget_id;?>" class="wk-slideset wk-slideset-default" data-widgetkit="slideset" data-options='<?php echo json_encode($settings); ?>'>
	<div>
		<div class="sets">
			<?php foreach ($sets as $set => $items) : ?>
				<ul class="set">
					<?php foreach ($items as $image) : 

						$lightbox = '';

						/* Prepare Lightbox */
						if ($settings['lightbox'] && !$image['link']) {
							$lightbox = 'data-lightbox="group:'.$widget_id.'"';
						}

						/* Prepare Image */
						$content = '<img src="'.$image['cache_url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.$image['filename'].'" />';
					
						/* Lazy Loading */
						$content = ($i==0) ? $content : $this['image']->prepareLazyload($content);
					?>
					<?php if ($settings['lightbox'] || $image['link']) : ?>
						<li>
							<article class="wk-content"><a class="" href="<?php echo $image['link'] ? $image['link'] : $image['url']; ?>" <?php echo $lightbox; ?>><?php echo $content; ?></a></article>
						</li>
					<?php else : ?>		
						<li>
							<article class="wk-content"><?php echo $content; ?></article>
						</li>
					<?php endif; ?>
					
					<?php endforeach; ?>
				</ul>
				<?php $i=$i+1;?>
			<?php endforeach; ?>
		</div>
		<?php if ($settings['buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
	</div>
	<?php if ($settings['navigation'] && count($nav)) : ?>
	<ul class="nav <?php echo (is_numeric($settings['items_per_set'])) ? 'icon' : 'text'; ?>"><?php echo implode('', $nav); ?></ul>
	<?php endif; ?>
</div>