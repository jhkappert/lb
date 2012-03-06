<li id="slideshow" data-name="Slideshow">
	<?php if (count($slideshows)) : ?>
		<table class="list">
			<thead>
				<th>Name</th>
				<th class="shortcode">Shortcode</th>
				<th class="modified">Modified</th>
				<th class="actions"></th>
			</thead>
			<tbody>
			<?php foreach ($slideshows as $slideshow) : ?>
				<?php $edit = $this['system']->link(array('task' => 'edit_slideshow', 'id' => $slideshow->id)); ?>
				<?php $copy = $this['system']->link(array('task' => 'copy_slideshow', 'id' => $slideshow->id)); ?>
				<tr>
					<td><a href="<?php echo $edit; ?>"><?php echo $slideshow->name; ?></a></td>
					<td><code>[widgetkit id=<?php echo $slideshow->id; ?>]</code></td>
					<td><?php echo $slideshow->modified; ?></td>
					<td class="actions">
						<a class="action edit" href="<?php echo $edit; ?>">Edit</a>
						<a class="action copy" href="<?php echo $copy; ?>">Copy</a>
						<a class="action delete" href="#" data-id="<?php echo $slideshow->id;?>">Delete</a> 
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<a class="button-secondary" href="<?php echo $this['system']->link(array('task' => 'edit_slideshow')); ?>"><?php echo count($slideshows) ? 'Add New' : 'Create your first slideshow'; ?></a>
</li>