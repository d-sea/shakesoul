<?php
/* ==================================================
 *   Patches for other plugins
   ================================================== */

/* ==================================================
 * Keep access to admin screen if not exists ktai style admin directory
 */
if (file_exists(PLUGINDIR . '/wphone') || file_exists(PLUGINDIR . '/mobileadmin')) {
	define ('KS_KEEP_ADMIN_ACESS', true);
}

/* ==================================================
 * Erase Location URL for Ktai Location
 */
function ks_erase_location_url($content) {
	return preg_replace('!\s*<div class="([-. \w]+ )?location([-. \w]+ )?">.*?</div>!se', '"$1$2" ? "<div class=\"$1$2\"> </div>" : ""', $content);
}
add_filter('the_content', 'ks_erase_location_url', 88);

/* ==================================================
 * Disable WP-SpamFree
 */
if (! class_exists('wpSpamFree')):
class wpSpamFree {
	public function __construct() {
		return;
	}
}
endif;

/* ==================================================
 * Shrink FireStats Images
 */
if (defined('FS_WORDPRESS_PLUGIN_VER')):
global $KS_Flags, $KS_Browsers;
$KS_Flags = array(
	'jp' => 237, 'us' => 90,  'es' => 366, 'ru' => 367, 'fr' => 499,
	'de' => 700, 'it' => 701, 'gb' => 702, 'cn' => 703, 'kr' => 704, 
);
/*
$KS_Browsers = array(
	'macos' => 434, 'linux' => 252, 'debian' => 190, 'java' => 93,
	'docomo' => 'd109',
); */
function ks_shrink_firestat_images($return) {
	global $Ktai_Style, $KS_Flags, $KS_Browsers;
	if ($Ktai_Style->is_ktai() == 'Unknown') {
		return $return;
	}
	if (preg_match("|<img src='[^']*plugins/firestats/img/flags/(\w+)\.png' alt='([^']*)' [^>]*class='fs_flagicon' ?/>|", $return, $match) && isset($KS_Flags[$match[1]])) {
		$return = str_replace($match[0], '<img localsrc="' . $KS_Flags[$match[1]] . '" alt="' . $match[2] . '" />', $return);
	}
/*
	if (preg_match("|<img src='[^']*plugins/firestats/img/browsers/(\w+)\.png' alt='([^']*)' [^>]*class='fs_browsericon' ?/>|", $return, $match) && isset($KS_Browsers[$match[1]])) {
		$return = str_replace($match[0], '<img localsrc="' . $KS_Browsers[$match[1]] . '" alt="' . $match[2] . '" />', $return);
	}
*/
	return $return;
}
add_filter('get_comment_author_link', 'ks_shrink_firestat_images', 101);
endif;

/* ==================================================
 * Disable title-replace by All in One SEO Pack 
 */
global $aiosp;
if (isset($aiosp) && is_object($aiosp)) {
	remove_filter('wp_head', array($aiosp, 'wp_head'));
	remove_filter('template_redirect', array($aiosp, 'template_redirect'));
}

?>