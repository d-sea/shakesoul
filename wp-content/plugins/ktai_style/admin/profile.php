<?php
/* ==================================================
 *   Ktai Admin Profile
 *   based on wp-admin/profile.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
$title = __('Profile');
$parent_file = 'profile.php';
include dirname(__FILE__) . '/admin-header.php';
$profileuser = get_user_to_edit($user_ID);
if ( isset($_GET['updated']) ) { ?>
<p><font color="blue"><?php echo ($Ktai_Style->check_wp_version('2.5', '>=') ? __('User updated.') : __('Profile updated.')); ?></font></p>
<?php } ?>
<h2><?php _e('Your Profile and Personal Options'); ?></h2>
<form action="profile-update.php" method="post">
<?php 
	$KS_Admin->sid_field();
	wp_nonce_field('update-profile_' . $user_ID);
	ks_fix_encoding_form();
?>
<input type="hidden" name="from" value="profile" />
<input type="hidden" name="checkuser_id" value="<?php echo intval($user_ID); ?>" />
<div><?php _e('Nickname:', 'ktai_style') ?><br />
<input type="text" name="nickname" size="100%" value="<?php echo attribute_escape($profileuser->nickname); ?>" /><br />
<?php _e('E-mail: (required)', 'ktai_style') ?><br />
<input type="text" name="email" size="100%" value="<?php echo attribute_escape($profileuser->user_email); ?>" /></div>
<p><input type="submit" value="<?php _e('Update') ?>" name="submit" /></p>
</form>
<?php include dirname(__FILE__) . '/admin-footer.php'; ?>