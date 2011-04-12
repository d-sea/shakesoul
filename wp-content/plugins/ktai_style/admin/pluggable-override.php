<?php
/* ==================================================
 *   functions to override pluggable.php
   ================================================== */

// ==================================================
function auth_redirect() {
	global $Ktai_Style;
	nocache_headers();
	$uri = preg_replace('!^.*/wp-admin/!' , KS_ADMIN_DIR . '/', $_SERVER['REQUEST_URI']);
	wp_redirect($Ktai_Style->get('plugin_url') . 'login.php?redirect_to=' . urlencode($uri));
	exit();
}

// ==================================================
function check_admin_referer($action = -1) {
	global $Ktai_Style, $KS_Admin;
	$adminurl = strtolower($Ktai_Style->get('plugin_url') . KS_ADMIN_DIR . '/');
	$referer = strtolower($KS_Admin->get_referer());
	if (! wp_verify_nonce($_REQUEST['_wpnonce'], $action) &&
	    !(-1 == $action && strpos($referer, $adminurl) !== false)) {
		$KS_Admin->nonce_ays($action);
		exit();
	}
	do_action('check_admin_referer', $action);
}

// ==================================================
function get_currentuserinfo() {
	global $current_user;
	if (! empty($current_user)) {
		return;
	}
	$user_login = Ktai_Style_Admin::check_session();
	$user = get_userdatabylogin($user_login);
	if (! $user) {
		wp_set_current_user(0);
		return false;
	}
	wp_set_current_user($user->ID);
}

// ==================================================
function wp_setcookie($username, $password, $already_md5 = false, $home = '', $siteurl = '', $remember = false) {
	// do nothing
}

// ==================================================
function wp_set_auth_cookie($user_id, $remember = false) {
	// do nothing
}

?>