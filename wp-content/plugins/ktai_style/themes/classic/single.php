<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) :
	the_post();
	if (! ks_is_comments() ) :
		the_date('', '<u>', '</u><br />'); ?>
		[<font color="#008800"><?php the_title(); ?></font>]<br />
		<?php ks_content(__('(more...)')); ks_link_pages(); ?>
		<div><?php _e('Author'); the_author(); ?><br />
		<font size="-1"><?php echo __('Categories') . ':'; ks_category(); ?></font><br />
		<?php if (function_exists('get_the_tags')) {
			ks_tags('<font size="-1">' . __('Tags') . ':', '</font><br />');
		}
		the_time(); ?></div>
		<hr />
		<?php if (ks_option('ks_separate_comments')) {
			ks_comments_link('', 
				__('No Comments/Trackbacks', 'ktai_style'), 
				__('One Comment/Trackback', 'ktai_style'), 
				__('% Comments and Trackbacks', 'ktai_style'));
		} else {
			ks_comments_link();
		}
		?> <?php ks_comments_post_link(NULL, '', '', ''); ?>
		<hr />
		<div><?php ks_previous_post_link(__('*.Prev:%link', 'ktai_style')) ?><br />
		<?php ks_next_post_link(__('#.Next:%link', 'ktai_style')) ?></div>
	<?php else : // ks_is_comment() ?>
		[<a href="<?php the_permalink(); ?>"><font color="#008800"><?php the_title(); ?></font></a>]<br />
		<?php comments_template();
	endif;
else : ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>