<div id="widgetkit" class="wrap">
	
	<h2>Widgetkit</h2>
	<p>Manage all your widgets right here. To display widgets, please visit the <a href="widgets.php">widgets settings</a> screen and apply the Widgetkit to a widget position of your theme.</p>

	<?php if ($this['check']->notices()): ?>
	<div id="wk-systemcheck">
		<strong>Critical Issues</strong>
		<ul>
			<?php foreach($this['check']->get_notices() as $notice): ?>
			<li class="<?php echo $notice['type']; ?>"><?php echo $notice['message']; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>

	<div class="dashboard">
		<ul id="tabs" data-wkversion="<?php echo $this->widgetkit["version"];?>">
			<?php $this['event']->trigger('dashboard'); ?>
		</ul>
	</div>												

</div>