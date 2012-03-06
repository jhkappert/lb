<li id="map" data-name="Map">
	<?php if (count($maps)) : ?>
		<table class="list">
			<thead>
				<th>Name</th>
				<th class="shortcode">Shortcode</th>
				<th class="modified">Modified</th>
				<th class="actions"></th>
			</thead>
			<tbody>
			<?php foreach ($maps as $map) : ?>
				<?php $edit = $this['system']->link(array('task' => 'edit_map', 'id' => $map->id)); ?>
				<?php $copy = $this['system']->link(array('task' => 'copy_map', 'id' => $map->id)); ?>
				<tr>
					<td><a href="<?php echo $edit; ?>"><?php echo $map->name; ?></a></td>
					<td><code>[widgetkit id=<?php echo $map->id; ?>]</code></td>
					<td><?php echo $map->modified; ?></td>
					<td class="actions">
						<a class="action edit" href="<?php echo $edit; ?>">Edit</a>
						<a class="action copy" href="<?php echo $copy; ?>">Copy</a>
						<a class="action delete" href="#" data-id="<?php echo $map->id;?>">Delete</a> 
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<a class="button-secondary" href="<?php echo $this['system']->link(array('task' => 'edit_map')); ?>"><?php echo count($maps) ? 'Add New' : 'Create your first map'; ?></a>
</li>