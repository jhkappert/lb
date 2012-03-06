<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

$widget_id = $widget->id.'-'.uniqid();
$settings  = $widget->settings;
$zoom      = in_array($settings['effect'], array('zoom', 'polaroid')) ? 1.4 : 1;
$images    = $this['gallery']->images($widget, array('width' => $settings['width'] * $zoom, 'height' => $settings['height'] * $zoom));

$css_classes  = ($settings['corners'] == 'round') ? 'round ' : '';
$css_classes .= ($settings['effect'] == 'zoom') ? 'zoom ' : '';
$css_classes .= ($settings['effect'] == 'polaroid') ? 'polaroid ' : '';
$css_classes .= ($settings['margin']) ? 'margin ' : '';

?>

<?php if (count($images)) : ?>
<div class="wk-gallery wk-gallery-wall clearfix <?php echo $css_classes; ?>">

	<?php foreach ($images as $image) : ?>
	
		<?php
	
			$lightbox  = '';
			$spotlight = '';
			$overlay   = '';

			/* Prepare Lightbox */
			if ($settings['lightbox'] && !$image['link']) {
				$lightbox = 'data-lightbox="group:'.$widget_id.'"';

				$image['caption'] = strip_tags($image['caption']);
				if ($settings['lightbox_caption']) {
					$lightbox .= (strlen($image['caption'])) ? ' title="'.$image['caption'].'"' : ' title="'.$image['filename'].'"';
				}
			}

			/* Prepare Spotlight */
			if ($settings['effect'] == 'spotlight') {
				if ($settings['spotlight_effect'] && strlen($image['caption'])) {
					$spotlight = 'data-spotlight="effect:'.$settings['spotlight_effect'].'"';
					$overlay = '<div class="overlay">'.$image['caption'].'</div>';
				} elseif (!$settings['spotlight_effect']) {
					$spotlight = 'data-spotlight="on"';
				}
			}

			/* Prepare Polaroid */
			if ($settings['effect'] == 'polaroid') {
				$overlay = (strlen($image['caption'])) ? '<p class="title">'.$image['caption'].'</p>' : '<p class="title">'.$image['filename'].'</p>';
			}
			
			/* Prepare Image */
			$content = '<img src="'.$image['cache_url'].'" width="'.$settings['width'].'" height="'.$settings['height'].'" alt="'.$image['filename'].'" />'.$overlay;
	
			$content = ($settings['effect'] == 'polaroid') ? '<div>'.$content.'</div>' : $content ;

		?>
	
		<?php if ($settings['lightbox'] || $image['link']) : ?>
			<a class="" href="<?php echo $image['link'] ? $image['link'] : $image['url']; ?>" <?php echo $lightbox; ?> <?php echo $spotlight; ?>><?php echo $content; ?></a>
		<?php elseif ($settings['effect'] == 'spotlight') : ?>
			<div <?php echo $spotlight; ?>><?php echo $content; ?></div>
		<?php else : ?>		
			<?php echo $content; ?>
		<?php endif; ?>
		
	<?php endforeach; ?>

</div>

<?php else : ?>
	<?php echo "No images found."; ?>
<?php endif; ?>