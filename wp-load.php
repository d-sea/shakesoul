<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the wp-config.php file. The wp-config.php
 * file will then load the wp-settings.php file, which
 * will then set up the WordPress environment.
 *
 * If the wp-config.php file is not found then an error
 * will be displayed asking the visitor to set up the
 * wp-config.php file.
 *
 * Will also search for wp-config.php in WordPress' parent
 * directory to allow the WordPress directory to remain
 * untouched.
 *
 * @package WordPress
 */

/** Define ABSPATH as this files directory */
define( 'ABSPATH', dirname(__FILE__) . '/' );

error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);

if ( file_exists( ABSPATH . 'wp-config.php') ) {

	/** The config file resides in ABSPATH */
	require_once( ABSPATH . 'wp-config.php' );

} elseif ( file_exists( dirname(ABSPATH) . '/wp-config.php' ) ) {

	/** The config file resides one level below ABSPATH */
	require_once( dirname(ABSPATH) . '/wp-config.php' );

} else {

	// A config file doesn't exist

	// Set a path for the link to the installer
	if (strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false) $path = '';
	else $path = 'wp-admin/';

	// Die with an error message
	require_once( ABSPATH . '/wp-includes/classes.php' );
	require_once( ABSPATH . '/wp-includes/functions.php' );
	require_once( ABSPATH . '/wp-includes/plugin.php' );
	wp_die(sprintf(/*WP_I18N_NO_CONFIG*/'ファイルが見つかりません。インストールを開始するには <code>wp-config.php</code> ファイルが必要です。お困りでしたら<a href=\'http://wpdocs.sourceforge.jp/wp-config.php_%%E3%%81%%AE%%E7%%B7%%A8%%E9%%9B%%86\'>「wp-config.php の編集」</a> を参照してください。ウィザード形式で <code>wp-config.php</code> ファイルを作成することもできますが、すべてのサーバーにおいて正常に動作するわけではありません。最も安全な方法は手動でファイルを作成することです。</p><p><a href=\'%ssetup-config.php\' class=\'button\'><code>wp-config.php</code> ファイルを作成する</a>'/*/WP_I18N_NO_CONFIG*/, $path), /*WP_I18N_ERROR_TITLE*/'WordPress &rsaquo; エラー'/*/WP_I18N_ERROR_TITLE*/);

}

?>
