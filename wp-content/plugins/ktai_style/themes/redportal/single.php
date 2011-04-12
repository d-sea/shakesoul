<?php ks_header(); ?>
<!--start paging-->
<?php if (have_posts()) :
	the_post();
	global $id;
	if (! ks_is_comments() ) : ?>
		<div align="center"><?php ks_previous_post_link('*.%link', '前へ'); 
		?>|<?php ks_next_post_link('%link.#', '次へ'); ?></div>
		<img localsrc="508" alt="" /><?php the_title(); ?><br />
		<font color="red"><?php the_time('Y.m.d H:i'); ?></font><br />
		<font size="-1"><?php echo __('Categories') . ':'; ks_category(); ?></font>
		<hr color="red" />
		<?php ks_content(__('(more...)')); ks_link_pages(); ?>
		<hr color="red" />
		<?php ks_comments_link(ks_pict_number(1), 'コメントを見る(0)', 'コメントを見る(1)', 'コメントを見る(' . intval(get_comments_number($id)) . ')', '<img localsrc="61" alt="×" />コメント停止中', NULL, '1'); ?><br />
		<?php ks_comments_post_link('コメントを書く', '', '', ks_pict_number(2), '2'); ?>
		<hr color="red" />
		<div align="left"><?php ks_previous_post_link() ?></div>
		<div align="right"><?php ks_next_post_link() ?></div>
		<?php if (function_exists('get_the_tags')) :
			ks_tags('<hr color="red" /><img localsrc="77" alt="" />関連ワード<br />');
		endif;
	else : // ks_is_comment()
		comments_template();
	endif;
else : ?>
	<h2><?php _e('Not Found', 'ktai_style'); ?></h2>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
ks_footer(); ?>