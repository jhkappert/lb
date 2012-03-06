<span class="loc-data">
	Set location 
	<span>
	<?php 

		if (isset($item, $item['lat'])) {
			printf('(Lat: %s, Lng: %s)', $item['lat'],$item['lng']);
		}
	?>
	</span>
	<?php
		printf('<input %s />', $this['field']->attributes(array('type' => 'hidden', 'name' => str_replace("[{$node->attributes()->name}]", "[lat]", $name), 'value' => isset($item, $item['lat']) ? $item['lat'] : '')));
		printf('<input %s />', $this['field']->attributes(array('type' => 'hidden', 'name' => str_replace("[{$node->attributes()->name}]", "[lng]", $name), 'value' => isset($item, $item['lng']) ? $item['lng'] : '')));
	?>
</span>