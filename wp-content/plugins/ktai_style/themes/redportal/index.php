<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) : ?>
	<div align="center"><?php 
	if (ks_is_front()) { ?>
		<img localsrc="334" alt="[new] " />新着記事
	<?php } else {
		ks_posts_nav_link();
	} ?></div>
	<?php for ($count = 0 ; have_posts() ; $count++) :
		the_post();
		if (! ks_is_front() && $count > 0) { ?>
			<hr color="red" width="95%" />
		<?php } ?>
		<img localsrc="508" alt="" /><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
		<img localsrc="46" alt="@ " /><?php 
		if (ks_is_front()) {
			 ?><font color="red"><?php 
		}
		the_time('Y.m.d H:i');
		if (ks_is_front()) {
			 ?></font><br /><?php 
		}
	endfor;
	if (ks_is_front()) { ?>
		<div align="right">→ <?php ks_next_posts_link('もっと見る'); ?></div>
	<?php } else { ?>
		<div align="center"><?php ks_posts_nav_link(); ?></div>
	<?php }
else: ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>
