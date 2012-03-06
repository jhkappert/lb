<div id="widgetkit" class="gallery wrap">

	<?php echo $this['template']->render('title', array('title' => ($widget->id ? 'Edit' : 'Add').' Gallery')); ?>

	<form id="form" method="post" action="<?php echo $this['system']->link(array('task' => 'save_gallery')); ?>">

		<div class="sidebar">
		
			<div class="box">
				<h3>Folders</h3>
				<div class="content">
					<div id="finder" class="finder"></div>
					<p>
						<button class="add-folder button-secondary" type="button">Add to Photo Gallery</button>
					</p>
				</div>
			</div>
		
			<div class="box">
				<h3>Settings</h3>
				<div class="content">
					<?php
						
						$settings = array();

						foreach (array($xml, $style_xml) as $x) {
							if ($setting = $x->xpath('settings/setting')) {
								$settings = array_merge($settings, $setting);
							}
						}

						foreach ($settings as $setting) {

							$name    = (string) $setting->attributes()->name;
							$type    = (string) $setting->attributes()->type;
							$label   = (string) $setting->attributes()->label;
							$value   = isset($widget->settings[$name]) ? $widget->settings[$name] : (string) $setting->attributes()->default;

							echo '<div class="option">';
							echo '<h4>'.$label.'</h4>';
							echo '<div class="value">';
							echo $this['field']->render($type, 'settings['.$name.']', $value, $setting);
							echo '<span class="description">'.$setting->attributes()->description.'</span>';
							echo '</div>';
							echo '</div>';

						}

					?>
				</div>
			</div>
			
		</div>

		<div class="form">
			<input type="hidden" value="<?php echo $widget->id; ?>" name="id" />
			<input type="text" size="40" value="<?php echo $widget->name; ?>" name="name" placeholder="Enter name here..." class="name" required />
		
			<div id="gallery"></div>

			<p class="actions">
				<input type="submit" value="Save changes" class="button-primary action save"/><span></span>
			</p>
		</div>

	</form>

</div>

<script type="text/javascript">
	
	jQuery(function($) {

		var paths = <?php echo json_encode($widget->paths); ?>;
		var captions = <?php echo json_encode($widget->captions); ?>;
		var links = <?php echo json_encode($widget->links); ?>;

		$('#gallery').data('url', '<?php echo $this['path']->url("media:"); ?>');

		$.each(paths, function(i, path) {
			$('#gallery').trigger('add', [path]);
		});

		$('#gallery').bind('update', function() {
			$('input[name^=captions]', this).val(function(i, value) {
				var path = $(this).attr('name').replace(/^captions\[/i, '').replace(/\]$/i, '');
				if (captions[path]) value = captions[path];
				return value;
			});
			$('input[name^=links]', this).val(function(i, value) {
				var path = $(this).attr('name').replace(/^links\[/i, '').replace(/\]$/i, '');
				if (links[path]) value = links[path];
				return value;
			});
		});

	});
	
</script>