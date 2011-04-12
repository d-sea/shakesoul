<!--end paging-->
<?php ks_switch_inline_images(); ?>
<hr />
<div><a name="tail" href="<?php ks_blogurl(); ?>"><?php 
echo (get_option('show_on_front') == 'page' ? 
	__('Front Page', 'ktai_style') : 
	__('New Posts', 'ktai_style')); 
?></a> | <a href="<?php ks_blogurl(); ?>?menu=comments"><?php
if (ks_option('ks_separate_comments')) {
	_e('Recent Comments/Trackbacks', 'ktai_style');
} else {
	_e('Recent Comments', 'ktai_style');
}
?></a> | <a href="<?php ks_blogurl(); ?>?menu=months"><?php _e('Archives'); 
?></a> | <a href="<?php ks_blogurl(); ?>?menu=cats"><?php _e('Categories'); 
?></a><?php
if (function_exists('get_tags')) {
	?> | <a href="<?php ks_blogurl(); ?>?menu=tags"><?php _e('Tags'); ?></a><?php 
}
ks_login_link(); ?></div>
<form method="get" action="<?php ks_blogurl(); ?>"><div>
	<input type="text" name="ks" value="<?php echo (function_exists('get_search_query') ? get_search_query() : ''); ?>" />
	<input type="submit" name="Submit" value="<?php _e('Search'); ?>" />
</div></form>
<?php ks_pages_menu(' | ', '<div>', '</div>'); ?>
<div align="right">Converted by Ktai Style plugin.<?php ks_switch_pc_view(); ?></div>
<?php /* ks_wp_footer(); */ ?>
</body></html>