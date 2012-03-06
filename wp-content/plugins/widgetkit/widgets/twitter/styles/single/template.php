<article class="wk-twitter wk-twitter-single <?php if ($show_image) echo 'image'; ?>">

	<p class="content"><?php echo $tweets[0]->getText(); ?></p>

	<?php if ($show_author || $show_date) : ?>
	<p class="meta">
	
		<?php if ($show_author) : ?>
		<span class="author"><?php printf($this['system']->__('BY_X'), '<a href="'.$tweets[0]->getLink().'">'.$tweets[0]->name.'</a>'); ?></span>
		<?php endif; ?>
		
			<?php if ($show_date) : ?>
			<time datetime="<?php echo date(DATE_W3C, strtotime($tweets[0]->created_at)); ?>" pubdate></time>
			<?php endif; ?>
		
	</p>
	<?php endif; ?>

</article>