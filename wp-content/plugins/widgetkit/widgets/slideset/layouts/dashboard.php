<li id="slideset" data-name="Slideset">
	<?php if (count($slidesets)) : ?>
		<table class="list">
			<thead>
				<th>Name</th>
				<th class="shortcode">Shortcode</th>
				<th class="modified">Modified</th>
				<th class="actions"></th>
			</thead>
			<tbody>
			<?php foreach ($slidesets as $slideset) : ?>
				<?php $edit = $this['system']->link(array('task' => 'edit_slideset', 'id' => $slideset->id)); ?>
				<?php $copy = $this['system']->link(array('task' => 'copy_slideset', 'id' => $slideset->id)); ?>
				<tr>
					<td><a href="<?php echo $edit; ?>"><?php echo $slideset->name; ?></a></td>
					<td><code>[widgetkit id=<?php echo $slideset->id; ?>]</code></td>
					<td><?php echo $slideset->modified; ?></td>
					<td class="actions">
						<a class="action edit" href="<?php echo $edit; ?>">Edit</a>
						<a class="action copy" href="<?php echo $copy; ?>">Copy</a>
						<a class="action delete" href="#" data-id="<?php echo $slideset->id;?>">Delete</a> 
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<a class="button-secondary" href="<?php echo $this['system']->link(array('task' => 'edit_slideset')); ?>"><?php echo count($slidesets) ? 'Add New' : 'Create your first slideset'; ?></a>
</li>