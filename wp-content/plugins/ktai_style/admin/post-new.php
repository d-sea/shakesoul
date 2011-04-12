<?php
/* ==================================================
 *   Ktai Admin Create Posts
 *   based on wp-admin/edit.php of WP 2.3
   ================================================== */

define ('KS_MAX_DRAFTS', 5);
require dirname(__FILE__) . '/admin.php';
$parent_file = 'post-new.php';
$title = __('Create New Post');
include dirname(__FILE__) . '/admin-header.php';
if ( ! current_user_can('edit_posts') ) { ?>
<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_option('admin_email')); ?></p>
<?php
} else {
if ( isset($_GET['posted']) && $_GET['posted'] ) : ?>
<p><font color="blue"><?php _e('Post saved.', 'ktai_style'); ?></font></p>
<?php
endif;
if ($drafts = get_users_drafts($user_ID)) {
	$drafts = array_slice($drafts, 0, KS_MAX_DRAFTS);
	$KS_Admin->show_drafts($drafts, __('Your Drafts:', 'ktai_style'));
	echo '<hr />';
}
// Show post form.
$post = get_default_post_to_edit();
$messages[1] = __('Post updated');
$messages[2] = __('Custom field updated');
$messages[3] = __('Custom field deleted.');
if (isset($_GET['message'])) {
	$_GET['message'] = (int) $_GET['message'];
	echo '<p><font color="blue">' . wp_specialchars($messages[$_GET['message']]) . '</font></p>';
}
include dirname(__FILE__) . '/edit-form.php';
} // current_user_can('edit_posts')
include dirname(__FILE__) . '/admin-footer.php';
?>