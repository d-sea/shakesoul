<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) :
	for ($count = 1; have_posts() ; $count++) :
		the_post();
		the_date('','<u>','</u><br/>');
		ks_ordered_link($count, 9, get_permalink(), get_the_title());
		echo ' ';
		ks_comments_link('', '[0]', '[1]', '[%]', '[X]', '[?]');
		?> <font color="<?php echo ks_option('ks_date_color'); ?>"><?php the_time('H:i'); ?></font><br />
	<?php endfor; ?>
	<div align="center"><?php ks_posts_nav_link(); ?></div>
<?php else: ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>