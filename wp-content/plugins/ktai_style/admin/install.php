<?php
if (! defined('ABSPATH')) {
	exit;
}

/* ==================================================
 *   Ktai Style Install class
   ================================================== */

class Ktai_Style_Install {

public function install() {
	global $wpdb;
	if (! current_user_can('activate_plugins')) {
		return;
	}
	$charset_collate = '';
	if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}
	$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ktaisession` (
		`sid` varchar(32) NOT NULL,
		`next_id` varchar(32) NULL default NULL,
		`expires` datetime NOT NULL default '0000-00-00 00:00:00',
		`user_login` varchar(60) NOT NULL default '',
		`user_pass` varchar(64) NOT NULL default '',
		`user_agent` varchar(255) NULL,
		`term_id` varchar(64) NULL,
		`sub_id` varchar(64) NULL,
		`data` text NULL,
		PRIMARY KEY (`sid`)
		) $charset_collate;";
	require_once ABSPATH . 'wp-admin/upgrade-functions.php';
	dbDelta($sql);
	return;
}

public function uninstall() {
	global $wpdb;
	if (! current_user_can('activate_plugins')) {
		return;
	}
	$sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}ktaisession`;";
	$wpdb->query($sql);
	return;
}

// ===== End of class ====================
}
?>