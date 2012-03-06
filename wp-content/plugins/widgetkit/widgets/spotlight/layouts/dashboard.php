<li id="spotlight" data-name="Spotlight">

	<div class="info">

		The Spotlight allows you to add an overlay to your images which fades or moves in on mouse hover. The overlay can be an image or HTML content. <a href="#" class="howtouse">How to use...</a>
		
		<div class="howtouse">
			<p>Use the HTML5 custom data attribute <code>data-spotlight</code> to activate the spotlight. For example:</p>
		
			<pre>&lt;a <code>data-spotlight="on"</code> href="mypage.html"&gt;&lt;img src="image.jpg" width="180" height="120" alt="" /&gt;&lt;/a&gt;</pre>

			<p>To create a custom overlay use a div element with the CSS class <code>overlay</code>. You can set the effect parameter to the data attribute. For example:</p>

			<pre>&lt;a <code>data-spotlight="effect:bottom;"</code> href="mypage.html"&gt;
		&lt;img src="image.jpg" width="180" height="120" alt="" /&gt;
		&lt;div <code>class="overlay"</code>&gt;Custom Overlay&lt;/div&gt;
	&lt;/a></pre>

			<p>You can set the effect parameter to <code>fade</code>, <code>bottom</code>, <code>top</code>, <code>right</code> or <code>left</code>.
		</div>

	</div>

	<div class="config">
		<form method="post" action="<?php echo $this['system']->link(array('task' => 'config_spotlight', 'ajax' => true)); ?>">
			<ul class="properties">
				<li class="separator">Settings</li>

				<?php

					foreach ($xml->settings->setting as $setting) {

						$name    = (string) 'spotlight_'.$setting->attributes()->name;
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