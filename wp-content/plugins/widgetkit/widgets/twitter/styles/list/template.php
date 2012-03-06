<section class="wk-twitter wk-twitter-list">

	<?php foreach ($tweets as $tweet) : ?>
	<article>
		
		<?php if ($show_image) : ?>
		<a class="image" href="<?php echo $tweet->getLink(); ?>">
			<img src="<?php echo $tweet->image; ?>" width="<?php echo $image_size; ?>" height="<?php echo $image_size; ?>" alt="<?php echo $tweet->name; ?>"/>
		</a>
		<?php endif; ?>
	
		<p class="content"><?php echo $tweet->getText(); ?></p>
	
		<?php if ($show_author || $show_date) : ?>
		<p class="meta">

			<?php if ($show_author) : ?>
			<span class="author"><?php printf($this['system']->__('BY_X'), '<a href="'.$tweet->getLink().'">'.$tweet->name.'</a>'); ?></span>
			<?php endif; ?>
			
			<?php if ($show_date) : ?>
			<time datetime="<?php echo date(DATE_W3C, strtotime($tweet->created_at)); ?>" pubdate></time>
			<?php endif; ?>
			
		</p>
		<?php endif; ?>

	</article>
	<?php endforeach; ?>

</section>