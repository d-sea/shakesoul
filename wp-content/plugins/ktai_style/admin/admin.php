<?php
/* ==================================================
 *   based on wp-admin/admin.php, menu-header.php of WP 2.3
   ================================================== */

$wpload_error = 'Admin feature does not work if custom WP_PLUGIN_DIR is set.';
require dirname(dirname(__FILE__)) . '/wp-load.php';
nocache_headers();

global $Ktai_Style;
if (! isset($Ktai_Style) || ! $Ktai_Style->is_ktai() || ! class_exists('Ktai_Style_Admin')) {
	wp_redirect(get_bloginfo('wpurl') . '/wp-login.php');
	exit();
}

define ('KS_ADMIN_MODE', true);
$KS_Admin = new Ktai_Style_Admin();
$KS_Admin->auth_redirect();
$KS_Admin->renew_session();

require ABSPATH . 'wp-admin/admin-functions.php';
require dirname(dirname(__FILE__)) . '/tags.php';
if ($Ktai_Style->check_wp_version('2.3', '<') && file_exists(ABSPATH . 'wp-admin/admin-db.php')) {
	require ABSPATH . 'wp-admin/admin-db.php';
}
update_category_cache();
wp_get_current_user();
require dirname(__FILE__) . '/menu.php';
$page_charset = $Ktai_Style->get('charset');
$iana_charset = $Ktai_Style->get('iana_charset');
$mime_type    = 'text/html';
$Ktai_Style->ktai->set('mime_type', $mime_type); // don't use 'application/xhtml+xml'
?>