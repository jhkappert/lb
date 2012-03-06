<li id="gallery" data-name="Gallery">
	<?php if (count($galleries)): ?>
		<table class="list">
			<thead>
				<th>Name</th>
				<th class="shortcode">Shortcode</th>
				<th class="modified">Modified</th>
				<th class="actions"></th>
			</thead>
			<tbody>
			<?php foreach ($galleries as $gallery) : ?>
				<?php $edit = $this['system']->link(array('task' => 'edit_gallery', 'id' => $gallery->id)); ?>
				<?php $copy = $this['system']->link(array('task' => 'copy_gallery', 'id' => $gallery->id)); ?>
				<tr>
					<td><a href="<?php echo $edit; ?>"><?php echo $gallery->name; ?></a></td>
					<td><code>[widgetkit id=<?php echo $gallery->id; ?>]</code></td>
					<td><?php echo $gallery->modified; ?></td>
					<td class="actions">
						<a class="action edit" href="<?php echo $edit; ?>">Edit</a>
						<a class="action copy" href="<?php echo $copy; ?>">Copy</a>
						<a class="action delete" href="#" data-id="<?php echo $gallery->id; ?>">Delete</a> 
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<a class="button-secondary" href="<?php echo $this['system']->link(array('task' => 'edit_gallery')); ?>"><?php echo count($galleries) ? 'Add New' : 'Create your first gallery'; ?></a>
</li>