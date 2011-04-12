<?php 
ks_header();
if (have_posts()) :
	$post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<h2><?php ks_pagenum(); ?></h2>
<!--start paging-->
	<dl>
	<?php for ($count = 1; have_posts() ; $count++) :
		the_post();
		?><dt><?php 
		ks_ordered_link($count, 9, get_permalink(), get_the_title());
		echo ' ';
		ks_comments_link('<img localsrc="86" alt="[' . __('Comments') . '] " />', '0', '1', '%', '<img localsrc="61" alt="' . __('Off', 'ktai_style') . '" />', '<img localsrc="120" alt="?" />');
		?> <img localsrc="46" alt="@ " /><font color="<?php echo ks_option('ks_date_color'); ?>"><?php ks_time(); ?></font></dt>
	<?php endfor; ?>
	</dl>
	<div align="center"><?php ks_posts_nav_link(' | ', '', '<br />'); ks_posts_nav_dropdown(); ?></div>
<?php else : ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>