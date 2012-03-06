<li id="lightbox" data-name="Lightbox">

	<div class="info">
		
		The Lightbox allows you to view images, HTML and multi-media content on a dark dimmed overlay for without having to leave the current page. <a href="#" class="howtouse">How to use...</a>
		
		<div class="howtouse">
			<p>Use the HTML5 custom data attribute <code>data-lightbox</code> to activate the lightbox. For example:</p>

			<pre>&lt;a <code>data-lightbox="on"</code> href="image_lb.jpg"&gt;&lt;img src="image.jpg" width="180" height="120" alt="" /&gt;&lt;/a&gt;</pre>

			<p>If you want to create a group for your images or videos use the <code>group</code> parameter. For example:</p>

			<pre>&lt;a <code>data-lightbox="group:mygroup"</code> href="image1_lb.jpg"&gt;&lt;img src="image1.jpg" width="180" height="120" alt="" /&gt;&lt;/a&gt;
&lt;a <code>data-lightbox="group:mygroup"</code> href="image2_lb.jpg"&gt;&lt;img src="image2.jpg" width="180" height="120" alt="" /&gt;&lt;/a&gt;</pre>

			<p>You can set various other lightbox parameters to the data attribute. For example:</p>

			<pre>&lt;a <code>data-lightbox="transitionIn:elastic;transitionOut:elastic;"</code> href="http://www.google.com"&gt;Lightbox&lt;/a&gt;</pre>

			<p>Here is a list of common parameters:</p>

			<ul>
				<li><strong>titlePosition</strong> - How should the titlte show up? (<code>float</code>, <code>outside</code>, <code>inside</code> or <code>over</code>)</li>
				<li><strong>transitionIn</strong> - Set a opening transition. (<code>fade</code>, <code>elastic</code>, or <code>none</code>)</li>
				<li><strong>transitionOut</strong> - Set a closing transition (<code>fade</code>, <code>elastic</code>, or <code>none</code>)</li>
				<li><strong>overlayShow</strong> - Set to <code>true</code> or <code>false</code></li>
				<li><strong>width</strong> - Set a width in pixel</li>
				<li><strong>height</strong> - Set a height in pixel</li>
				<li><strong>padding</strong> - Set a padding in pixel</li>
			</ul>			
		</div>

	</div>

	<div class="config">
		<form method="post" action="<?php echo $this['system']->link(array('task' => 'config_lightbox', 'ajax' => true)); ?>">
			<ul class="properties">
				<li class="separator">Settings</li>

				<?php

					foreach ($xml->settings->setting as $setting) {

						$name    = (string) 'lightbox_'.$setting->attributes()->name;
						$type    = (string) $setting->attributes()->type;
						$label   = (string) $setting->attributes()->label;
						$value   = (string) $this['system']->options->has($name) ? $this['system']->options->get($name) : (string) $setting->attributes()->default;

						echo '<li>';
						echo '<div class="label">'.$label.'</div>';
						echo '<div class="field">'.$this['field']->render($type, $name, $value, $setting).'</div>';
						echo '<div class="description">'.$setting->attributes()->description.'</div>';
						echo '</li>';

					}

				?>

			</ul>
			<p>
				<input type="submit" value="Save changes" class="button-primary"/><span></span>
			</p>
		</form>
	</div>

</li>