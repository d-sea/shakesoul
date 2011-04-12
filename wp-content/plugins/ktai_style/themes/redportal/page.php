<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) :
	the_post(); ?>
	<h2><?php the_title(); ?></h2>
	<?php ks_content(__('(more...)')); ks_link_pages();
	ks_posts_nav_link(' | ', '<hr /><div align="center">', '</div>');
else: ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>