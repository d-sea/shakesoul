<!--end paging-->
<?php ks_switch_inline_images('<hr color="red" /><div align="center">', '</div>'); ?><hr color="red" />
<a name="tail"></a>
<form method="get" action="<?php ks_blogurl(); ?>"><div>
	<input type="text" name="ks" value="<?php echo (function_exists('get_search_query') ? get_search_query() : ''); ?>" />
	<input type="submit" name="Submit" value="<?php _e('Search'); ?>" />
</div></form>
<dl><dt><?php $count = 5;
ks_ordered_link($count++, 10, ks_blogurl(false), get_option('show_on_front') == 'page' ? 
	__('Front Page', 'ktai_style') : 
	__('New Posts', 'ktai_style'));
?></dt><dt><?php 
ks_ordered_link($count++, 10, ks_blogurl(false) . '?menu=comments', ks_option('ks_separate_comments') ? __('Recent Comments/Trackbacks', 'ktai_style') : __('Recent Comments', 'ktai_style')); ?></dt><dt><?php 
ks_ordered_link($count++, 10, ks_blogurl(false) . '?menu=months', __('Archives')); ?></dt><dt><?php 
ks_ordered_link($count++, 10, ks_blogurl(false) . '?menu=cats', __('Categories')); ?><?php if (function_exists('get_tags')) { ?>
・<a href="<?php ks_blogurl(); ?>?menu=tags"><?php _e('Tags'); ?></a><?php } ?></dt><dt><?php 
ks_ordered_link($count++, 10, ks_blogurl(false) . '?menu=pages', __('Pages')); ?></dt><?php 
ks_login_link('<dt><img localsrc="120" alt="・" />', '</dt>'); ?></dl>
<div align="right">Redportal theme for Ktai Style.<?php ks_switch_pc_view(); ?></div>
<?php /* ks_wp_footer(); */ ?>
</body></html>