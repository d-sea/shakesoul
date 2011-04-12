<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) : ?>
	<dl>
	<?php for ($count = 1; have_posts() ; $count++) :
		the_post(); ?>
		<dt><?php 
		ks_ordered_link($count, 9, get_permalink(), get_the_title()); ?> <?php 
		ks_comments_link(
			'<img localsrc="86" alt="[' . __('Comments') . '] " />', 
			'0', '1', '%', 
			'<img localsrc="61" alt="' . __('Off', 'ktai_style') . '" />', 
			'<img localsrc="120" alt="?" />');
		?> <img localsrc="46" alt="@ " /><font color="<?php echo ks_option('ks_date_color'); ?>"><?php ks_time(); ?></font></dt>
	<?php endfor; ?>
	</dl>
	<div align="center"><?php ks_posts_nav_link(); ?></div>
<?php else: ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>