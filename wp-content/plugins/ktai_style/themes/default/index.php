<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) :
	the_post();
	the_date('','<div align="center"><font color="' . ks_option('ks_date_color') . '">','</font></div>'); ?>
	<div align="right"><?php ks_pict_number(9, TRUE); ?><img localsrc="30" alt="<?php _e('v', 'ktai_style'); ?>" /><a href="#cont" accesskey="9"><?php _e('Skip the content', 'ktai_style'); ?></a></div>
	<h2><?php if (ks_is_front()) { ?>
		<img localsrc="334" alt="[new] " />
	<?php }
	the_title(); ?></h2>
	<div><img localsrc="68" alt="" /><font color="<?php echo ks_option('ks_author_color'); ?>"><?php the_author(); ?></font>
	<img localsrc="46" alt=" @ " /><font color="<?php echo ks_option('ks_author_color'); ?>"><?php the_time(); ?></font></div>
	<?php ks_content(__('(more...)'), 0 , '' , 1000); ks_link_pages(); ?>
	<div><img localsrc="354" alt="" /><font size="-1"><?php echo __('Categories') . ':'; ks_category(); ?></font><br />
	<?php if (function_exists('get_the_tags')) {
		ks_tags('<img localsrc="77" alt="" /><font size="-1">' . __('Tags') . ':', '</font><br />');
	}
	if (ks_option('ks_separate_comments')) {
		ks_comments_link(NULL, 
			__('No Comments/Trackbacks', 'ktai_style'), 
			__('One Comment/Trackback', 'ktai_style'), 
			__('% Comments and Trackbacks', 'ktai_style'));
	} else {
		ks_comments_link();
	} ?><br />
	<?php ks_comments_post_link(); ?></div>
	<?php if (have_posts()) : ?>
		<hr />
		<h2><a name="cont"><?php _e('Following posts', 'ktai_style'); ?></a></h2>
		<dl>
		<?php for ($count = 1; have_posts() ; $count++) :
			the_post(); ?>
			<dt><?php 
			ks_ordered_link($count, 8, get_permalink(), get_the_title());
			echo ' ';
			ks_comments_link(
				'<img localsrc="86" alt="[' . __('Comments') . '] " />', 
				'0', '1', '%', 
				'<img localsrc="61" alt="' . __('Off', 'ktai_style') . '" />', 
				'<img localsrc="120" alt="?" />');
			?> <img localsrc="46" alt="@ " /><font color="<?php echo ks_option('ks_date_color'); ?>"><?php ks_time(); ?></font></dt>
		<?php endfor; ?>
		</dl>
	<?php endif; // inner have_posts() ?>
	<div align="center"><?php ks_posts_nav_link(); ?></div>
<?php else: ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>