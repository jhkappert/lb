<?php 
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

	$widget_id      = $widget->id.'-'.uniqid();
	$settings       = $widget->settings;
	$captions       = array();
	$images         = $this['gallery']->images($widget);
	$thumbs         = $this['gallery']->images($widget, array('width' => $settings['thumb_width'], 'height' => $settings['thumb_height']));
	$sets           = array_chunk($thumbs, $settings['items_per_set']);

	foreach (array_keys($sets) as $s) {
		$nav[] = '<li><span></span></li>';
	}

	$i = 0;
?>

<?php if (count($images)) : ?>
<div id="gallery-<?php echo $widget_id; ?>" class="wk-gallery-showcase" data-widgetkit="showcase" data-options='<?php echo json_encode($settings); ?>'>

	<div id="slideshow-<?php echo $widget_id; ?>" class="wk-slideshow">
		<div class="slides-container">
			<ul class="slides">

				<?php foreach ($images as $image) : ?>
				
					<?php
						$captions[]   = '<li>'.(strlen($image['caption']) ? $image['caption']:"").'</li>';
	
						/* Prepare Image */
						$content = '<img src="'.$image['cache_url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.$image['filename'].'" />';
					
						/* Lazy Loading */
						$content = ($i==$settings['index']) ? $content : $this['image']->prepareLazyload($content);
					?>

					<?php if ($image['link']) : ?>
						<li><a class="" href="<?php echo $image['link'] ? $image['link'] : $image['url']; ?>"><?php echo $content; ?></a></li>
					<?php else : ?>		
						<li><?php echo $content; ?></li>
					<?php endif; ?>
					
					<?php $i=$i+1;?>
				<?php endforeach; ?>

			</ul>
			<?php if ($settings['buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
			<div class="caption"></div><ul class="captions"><?php echo implode('', $captions);?></ul>
		</div>
	</div>

	<div id="slideset-<?php echo $widget_id;?>" class="wk-slideset <?php if (!$settings['slideset_buttons']) echo 'no-buttons'; ?>">
		<div>
			<div class="sets">
				<?php foreach ($sets as $set => $items) : ?>
				<ul class="set">
					<?php foreach ($items as $thumb) : ?>
					
					<?php
	
						/* Prepare Image */
						$content = '<img src="'.$thumb['cache_url'].'" width="'.$thumb['width'].'" height="'.$thumb['height'].'" alt="'.$thumb['filename'].'" />';
						
					?>
					
					<li><?php echo $content; ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endforeach; ?>
			</div>
			<?php if ($settings['slideset_buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
		</div>
	</div>
	
</div>

<?php else : ?>
	<?php echo "No images found."; ?>
<?php endif; ?>