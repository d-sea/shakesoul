<?php
/* ==================================================
 *   Ktai Admin Updating Profile
 *   based on wp-admin/profile.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
require_once ABSPATH . WPINC . '/registration.php';
$parent_file = 'profile.php';
$submenu_file = 'profile.php';

if (! $_POST) {
	Ktai_Style::ks_die(__('No post?'));
}
check_admin_referer('update-profile_' . $user_ID);
$_POST['nickname'] = ks_mb_get_form('nickname');
do_action('personal_options_update');
$errors = edit_user($user_ID);
if ( is_wp_error( $errors ) ) {
	Ktai_style::ks_die(implode('<br />', $errors->get_error_messages()));
}
$KS_Admin->redirect('profile.php?updated=true');
exit;
?>
