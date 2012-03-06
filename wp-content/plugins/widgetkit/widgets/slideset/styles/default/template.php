<?php 
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$sets      = array();
	$nav       = array();

	if (is_numeric($settings['items_per_set'])) {
		
		$sets = array_chunk($widget->items, $settings['items_per_set']);

		foreach (array_keys($sets) as $s) {
			$nav[] = '<li><span></span></li>';
		}

	} else {
	
		foreach ($widget->items as $key => $item) {
			
			if (!isset($sets[$item['set']])) {
				$sets[$item['set']] = array();
			}

			$sets[$item['set']][] = $item;
		}

		foreach (array_keys($sets) as $s) {
			$nav[] = '<li><span>'.$s.'</span></li>';
		}

	}

	$i = 0;
?>

<div id="slideset-<?php echo $widget_id;?>" class="wk-slideset wk-slideset-default" data-widgetkit="slideset" data-options='<?php echo json_encode($settings); ?>'>
	<div>
		<div class="sets">
			<?php foreach ($sets as $set => $items) : ?>
				<ul class="set">
					<?php foreach ($items as $item) : ?>
					<?php 
						/* Lazy Loading */
						$item["content"] = ($i==$settings['index']) ? $item["content"] : $this['image']->prepareLazyload($item["content"]);
					?>
					<li>
						<article class="wk-content"><?php echo $item['content']; ?></article>
						<?php if($settings['title']): ?>
						<strong class="title"><?php echo $item['title']; ?></strong>
						<?php endif; ?>
					</li>
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