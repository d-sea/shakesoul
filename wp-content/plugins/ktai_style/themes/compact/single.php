<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) :
	the_post();
	if (! ks_is_comments()) :
		the_date('','<div align="center"><font color="' . ks_option('ks_date_color') . '">','</font></div>'); ?>
		<h2><?php the_title(); ?></h2>
		<div><img localsrc="68" alt="" /><font color="<?php echo ks_option('ks_author_color'); ?>"><?php the_author(); ?></font>
		<img localsrc="46" alt=" @ " /><font color="<?php echo ks_option('ks_author_color'); ?>"><?php the_time(); ?></font></div>
		<?php ks_content(__('(more...)')); ks_link_pages(); ?>
		<div><img localsrc="354" alt="" /><font size="-1"><?php echo __('Categories') . ':'; ks_category(); ?></font><br /><?php 
		if (function_exists('get_the_tags')) {
			ks_tags('<img localsrc="77" alt="" /><font size="-1">' . __('Tags') . ':', '</font><br />');
		}
		if (ks_option('ks_separate_comments')) {
			ks_comments_link(NULL, 
				__('No Comments/Trackbacks', 'ktai_style'), 
				__('One Comment/Trackback', 'ktai_style'), 
				__('% Comments and Trackbacks', 'ktai_style'));
		} else {
			ks_comments_link();
		} ?>
		<br />
		<?php ks_comments_post_link()?></div>
		<hr />
		<div align="left"><?php ks_previous_post_link() ?></div>
		<div align="right"><?php ks_next_post_link() ?></div>
	<?php else : // ks_is_comment() ?>
		<h2><?php printf(__('Comments for <a href="%1s">%2s</a>', 'ktai_style'), get_permalink(), get_the_title()); ?></h2>
		<?php comments_template();
	endif;
else : ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>