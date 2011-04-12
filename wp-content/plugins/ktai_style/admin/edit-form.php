<?php
/* ==================================================
 *   Ktai Admin Post Form
 *   based on wp-admin/edit-form.php of WP 2.3
   ================================================== */

if (! defined('ABSPATH')) {
	exit;
}
global $Ktai_Style, $KS_Admin;
?>
<form method="post" action="post.php">
<?php $KS_Admin->sid_field(); ks_fix_encoding_form();
if (0 == $post_ID) {
	$form_action = 'post';
	$temp_ID = -1 * time(); // don't change this formula without looking at wp_write_post()
	$form_extra = '<input type="hidden" name="temp_ID" value="' . $temp_ID . '" />';
	$slug_optional = ' ' . __('(Optional)', 'ktai_style');
	wp_nonce_field('add-post');
	if (! isset($checked_cats)) {
		$checked_cats = array(get_option('default_category'));
	}
} else {
	$form_action = 'editpost';
	$form_extra = '<input type="hidden" name="post_ID" value="' . intval($post_ID) . '" />';
	$slug_optional = '';
	wp_nonce_field('update-post_' .  $post_ID);
	if (! isset($checked_cats)) {
		$checked_cats = wp_get_post_categories($post_ID);
	}
}
$cat_names = array();
if (count($checked_cats)) {
	foreach ($checked_cats as $c) {
		$cat_names[] = wp_specialchars(apply_filters('the_category', get_the_category_by_ID($c)));
	}
}
$form_prevstatus = '<input type="hidden" name="prev_status" value="' . attribute_escape( $post->post_status ) . '" />';
$saveasdraft = '<input type="submit" name="save" value="' . attribute_escape( __('Save and Continue Editing') ) . '" tabindex="8" />';
if (empty($post->post_status)) $post->post_status = 'draft';
?>
<input type="hidden" name="action" value="<?php echo $form_action ?>" />
<input type="hidden" name="originalaction" value="<?php echo $form_action ?>" />
<input type="hidden" id="post_type" name="post_type" value="post" />
<?php echo $form_extra ?>
<div><?php _e('Title'); ?><br />
<input type="text" name="post_title" size="32" maxlength="999" value="<?php echo attribute_escape($post->post_title); ?>" tabindex="1" /><br />
<?php _e('Post Slug'); echo wp_specialchars($slug_optional); ?><br />
<input type="text" name="post_name" size="24" maxlength="999" value="<?php echo attribute_escape($post->post_name); ?>" tabindex="2" /><br />
<?php _e('Categories'); ?><br />
<input type="hidden" name="post_cats" value="<?php echo implode(',', $checked_cats); ?>" /><font color="green"><?php echo implode(', ', $cat_names); ?></font><input type="submit" name="selcats" value="<?php _e('Change', 'ktai_style'); ?>" tabindex="3" /><br />
<?php _e('Post'); ?><br />
<textarea rows="8" cols="100%" name="content" tabindex="4" ><?php echo attribute_escape($post->post_content); ?></textarea><br />
<?php if ($Ktai_Style->is_ktai() == 'KDDI' && strlen(mb_convert_encoding($post->post_content, $Ktai_Style->get('charset'), get_bloginfo('charset'))) >= 1024) { ?>
<font color="red"><img localsrc="1" alt="!" /><?php _e('The post is too big is chopped to fit in the form. If you save changes, part of the content may be lost. Please back to previous page.', 'ktai_style'); ?></font><br />
<?php }
if (function_exists('get_tags_to_edit')) {
_e('Tags'); ?>
<br /><input type="text" name="tags_input" value="<?php echo (isset($post->tags_input) ? $post->tags_input : get_tags_to_edit($post_ID)); ?>" size="48" tabindex="5" /><br />
<?php } ?>
<div><label><input type="checkbox" name="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> tabindex="6" /><?php _e('Allow Comments'); ?></label><br />
<label><input type="checkbox" name="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> tabindex="7" /> <?php _e('Allow Pings'); ?></label></div>
<?php echo $saveasdraft; ?>
<input type="submit" name="submit" value="<?php _e('Save'); ?>" tabindex="9" />
<?php
if ( !in_array( $post->post_status, array('publish', 'future') ) || 0 == $post_ID ) {
	if ( current_user_can('publish_posts') ) : ?>
<input type="submit" name="publish" value="<?php _e('Publish'); ?>" tabindex="10" />
<?php endif;
}
?>
<input name="referredby" type="hidden" value="<?php
if ($post_referredby) {
	echo clean_url($post_referredby);
} elseif (url_to_postid($KS_Admin->get_referer()) == $post_ID) {
	echo 'redo';
} else {
	echo clean_url(stripslashes($KS_Admin->get_referer()));
}
?>" />
<?php if ('edit' == $action) { ?>
<br /><img localsrc="61" /><a href="<?php echo $KS_Admin->add_sid("post.php?action=delete&post=$post_ID"); ?>"><?php ( 'draft' == $post->post_status ) ? _e('Delete this draft', 'ktai_style') : _e('Delete this post', 'ktai_style'); ?></a>
<?php } ?>
</div></form>