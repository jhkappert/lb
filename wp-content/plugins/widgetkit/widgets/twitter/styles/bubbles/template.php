<section class="wk-twitter wk-twitter-bubbles grid-block">

	<?php foreach ($tweets as $tweet) : ?>
	<article class="grid-box <?php echo 'width'.intval(100 / count($tweets)); ?>">
		
		<p class="content"><?php echo $tweet->getText(); ?></p>

		<?php if ($show_author || $show_date) : ?>
		<p class="meta">
		
			<?php if ($show_image) : ?>
			<a class="image" href="<?php echo $tweet->getLink(); ?>">
				<img src="<?php echo $tweet->image; ?>" width="<?php echo $image_size; ?>" height="<?php echo $image_size; ?>" alt="<?php echo $tweet->name; ?>"/>
			</a>
			<?php endif; ?>
		
			<?php if ($show_author) : ?>
			<span class="author"><a href="<?php echo $tweet->getLink(); ?>"><?php echo $tweet->name; ?></a></span>
			<?php endif; ?>
			
			<?php if ($show_date) : ?>
			<time datetime="<?php echo date(DATE_W3C, strtotime($tweet->created_at)); ?>" pubdate></time>
			<?php endif; ?>
			
		</p>
		<?php endif; ?>

	</article>
	<?php endforeach; ?>

</section>