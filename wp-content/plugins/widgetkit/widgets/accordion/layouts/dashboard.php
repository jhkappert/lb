<li id="accordion" data-name="Accordion">
	<?php if (count($accordions)) : ?>
		<table class="list">
			<thead>
				<th>Name</th>
				<th class="shortcode">Shortcode</th>
				<th class="modified">Modified</th>
				<th class="actions"></th>
			</thead>
			<tbody>
			<?php foreach ($accordions as $accordion) : ?>
				<?php $edit = $this['system']->link(array('task' => 'edit_accordion', 'id' => $accordion->id)); ?>
				<?php $copy = $this['system']->link(array('task' => 'copy_accordion', 'id' => $accordion->id)); ?>
				<tr>
					<td><a href="<?php echo $edit; ?>"><?php echo $accordion->name; ?></a></td>
					<td><code>[widgetkit id=<?php echo $accordion->id; ?>]</code></td>
					<td><?php echo $accordion->modified; ?></td>
					<td class="actions">
						<a class="action edit" href="<?php echo $edit; ?>">Edit</a>
						<a class="action copy" href="<?php echo $copy; ?>">Copy</a>
						<a class="action delete" href="#" data-id="<?php echo $accordion->id;?>">Delete</a> 
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<a class="button-secondary" href="<?php echo $this['system']->link(array('task' => 'edit_accordion')); ?>"><?php echo count($accordions) ? 'Add New' : 'Create your first accordion'; ?></a>
</li>