<li id="mediaplayer" data-name="Media Player">

	<div class="info">

		The Widgetkit Media Player is a HTML5 audio and video player completely built HTML and CSS. A Flash player fallback is included for all unsupported browsers. <a href="#" class="howtouse">How to use...</a>
		
		<div class="howtouse">
			<p>Use the HTML5 <code>video</code> element to embed video in your website. For example:</p>

<pre>&lt;<code>video</code> src="video.mp4" width="320" height="240"&gt;&lt;/<code>video</code>&gt;</pre>

			<p>You can also provide multiple sources, to add support for the different video formats like h.264, WebM or Ogg:</p>

<pre>&lt;<code>video</code> width="320" height="240"&gt;
	&lt;source type="video/mp4"  src="video.mp4" /&gt;
	&lt;source type="video/webm" src="video.webm" /&gt;
	&lt;source type="video/ogg"  src="video.ogv" /&gt;
&lt;/<code>video</code>&gt;
</pre>

			<p>Use the HTML5 <code>audio</code> element to embed MP3 files in your website. For example:</p>
			
			<pre>&lt;<code>audio</code> src="audio.mp3" type="audio/mp3"&gt;&lt;/<code>audio</code>&gt;</pre>
		</div>

	</div>

	<div class="config">
		<form method="post" action="<?php echo $this['system']->link(array('task' => 'config_mediaplayer', 'ajax' => true)); ?>">
			<ul class="properties">
				<li class="separator">Settings</li>

				<?php

					foreach ($xml->settings->setting as $setting) {

						$name    = (string) 'mediaplayer_'.$setting->attributes()->name;
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